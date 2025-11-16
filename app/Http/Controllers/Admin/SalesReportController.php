<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use App\Services\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    protected $salesReportService;

    public function __construct(SalesReportService $salesReportService)
    {
        $this->salesReportService = $salesReportService;
        $this->middleware('permission:view_sale_reports');
    }
    /**
     * Display sales summary report
     */
    public function summary(Request $request)
    {
        $menu = 'sales-report-summary';
        $title = 'Sales Summary Report';

        // Parse date range using service
        $dateRange = $this->salesReportService->parseDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            'current_month'
        );

        $start = $dateRange['start'];
        $end = $dateRange['end'];
        $startDate = $dateRange['startDate'];
        $endDate = $dateRange['endDate'];

        // Get summary statistics
        $summary = $this->getSalesSummary($start, $end);

        // Get daily sales data for chart
        $dailySales = $this->getDailySales($start, $end);

        // Get top selling products
        $topProducts = $this->getTopSellingProducts($start, $end, 10);

        // Get top customers
        $topCustomers = $this->getTopCustomers($start, $end, 10);

        // Get sales by status
        $salesByStatus = $this->getSalesByStatus($start, $end);

        // Get recent invoices
        $recentInvoices = Invoice::with('customer')
            ->whereBetween('date', [$start, $end])
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        return view('admin.Reports.sales-summary', compact(
            'menu',
            'title',
            'summary',
            'dailySales',
            'topProducts',
            'topCustomers',
            'salesByStatus',
            'recentInvoices',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get sales summary statistics
     */
    private function getSalesSummary($start, $end)
    {
        $invoices = Invoice::whereBetween('date', [$start, $end])->get();

        return [
            'total_sales' => $invoices->where('status', '!=', 'cancelled')->sum('payable_amount'),
            'total_paid' => $invoices->where('status', '!=', 'cancelled')->sum('paid_amount'),
            'total_due' => $invoices->where('status', '!=', 'cancelled')->sum('due_amount'),
            'total_discount' => $invoices->where('status', '!=', 'cancelled')->sum('discount_amount'),
            'total_vat' => $invoices->where('status', '!=', 'cancelled')->sum('total_vat'),
            'total_invoices' => $invoices->where('status', '!=', 'cancelled')->count(),
            'active_invoices' => $invoices->where('status', 'active')->count(),
            'returned_invoices' => $invoices->where('status', 'returned')->count(),
            'cancelled_invoices' => $invoices->where('status', 'cancelled')->count(),
            'items_sold' => InvoiceItem::whereHas('invoice', function($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end])
                      ->where('status', '!=', 'cancelled');
            })->sum('quantity'),
        ];
    }

    /**
     * Get daily sales data
     */
    private function getDailySales($start, $end)
    {
        return Invoice::select(
                DB::raw('DATE(date) as sale_date'),
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(payable_amount) as total_amount'),
                DB::raw('SUM(paid_amount) as paid_amount'),
                DB::raw('SUM(due_amount) as due_amount')
            )
            ->whereBetween('date', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'asc')
            ->get();
    }

    /**
     * Get top selling products
     */
    private function getTopSellingProducts($start, $end, $limit = 10)
    {
        return InvoiceItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(unit_total) as total_amount'),
                DB::raw('COUNT(DISTINCT invoice_id) as order_count')
            )
            ->with(['product' => function($query) {
                $query->select('id', 'name', 'sku', 'sell_price');
            }])
            ->whereHas('invoice', function($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end])
                      ->where('status', '!=', 'cancelled');
            })
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top customers
     */
    private function getTopCustomers($start, $end, $limit = 10)
    {
        return Invoice::select(
                'customer_id',
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(payable_amount) as total_purchase'),
                DB::raw('SUM(paid_amount) as total_paid'),
                DB::raw('SUM(due_amount) as total_due')
            )
            ->with(['customer' => function($query) {
                $query->select('id', 'name', 'email', 'phone');
            }])
            ->whereBetween('date', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->groupBy('customer_id')
            ->orderBy('total_purchase', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get sales by status
     */
    private function getSalesByStatus($start, $end)
    {
        return Invoice::select(
                'status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(payable_amount) as total_amount')
            )
            ->whereBetween('date', [$start, $end])
            ->groupBy('status')
            ->get();
    }

    /**
     * Export sales summary as CSV
     */
    public function exportCsv(Request $request)
    {
        // Parse date range using service
        $dateRange = $this->salesReportService->parseDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            'current_month'
        );

        $start = $dateRange['start'];
        $end = $dateRange['end'];
        $startDate = $dateRange['startDate'];
        $endDate = $dateRange['endDate'];

        $invoices = Invoice::with(['customer', 'items.product'])
            ->whereBetween('date', [$start, $end])
            ->orderBy('date', 'desc')
            ->get();

        $filename = $this->salesReportService->createCsvFilename('sales_summary', $startDate, $endDate);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($invoices) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Invoice Number',
                'Date',
                'Customer',
                'Phone',
                'Status',
                'Unit Total',
                'VAT',
                'Discount',
                'Payable Amount',
                'Paid Amount',
                'Due Amount',
                'Items Count'
            ]);

            // Data rows
            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->invoice_number,
                    $invoice->date->format('Y-m-d'),
                    $invoice->customer->name ?? 'N/A',
                    $invoice->customer->phone ?? 'N/A',
                    ucfirst($invoice->status),
                    number_format($invoice->unit_total, 2),
                    number_format($invoice->total_vat, 2),
                    number_format($invoice->discount_amount, 2),
                    number_format($invoice->payable_amount, 2),
                    number_format($invoice->paid_amount, 2),
                    number_format($invoice->due_amount, 2),
                    $invoice->items->count()
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display product-wise sales report
     */
    public function productWise(Request $request)
    {
        $menu = 'sales-report-product-wise';
        $title = 'Product Wise Sales Report';

        // Parse date range using service
        $dateRange = $this->salesReportService->parseDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            'current_month'
        );

        $start = $dateRange['start'];
        $end = $dateRange['end'];
        $startDate = $dateRange['startDate'];
        $endDate = $dateRange['endDate'];

        // Get product sales data
        $productSales = InvoiceItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity_sold'),
                DB::raw('SUM(unit_total) as total_sales'),
                DB::raw('SUM(vat_amount) as total_vat'),
                DB::raw('COUNT(DISTINCT invoice_id) as order_count'),
                DB::raw('AVG(unit_price) as avg_unit_price'),
                DB::raw('MIN(unit_price) as min_unit_price'),
                DB::raw('MAX(unit_price) as max_unit_price')
            )
            ->with(['product' => function($query) {
                $query->select('id', 'name', 'sku', 'category_id', 'sell_price', 'stock_quantity')
                      ->with(['category' => function($q) {
                          $q->select('id', 'name');
                      }]);
            }])
            ->whereHas('invoice', function($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end])
                      ->where('status', '!=', 'cancelled');
            })
            ->groupBy('product_id')
            ->orderBy('total_sales', 'desc')
            ->get();

        // Calculate summary
        $summary = [
            'total_products_sold' => $productSales->count(),
            'total_quantity_sold' => $productSales->sum('total_quantity_sold'),
            'total_sales' => $productSales->sum('total_sales'),
            'total_vat' => $productSales->sum('total_vat'),
            'total_orders' => $productSales->sum('order_count'),
        ];

        // Get category-wise breakdown
        $categoryBreakdown = $productSales->groupBy(function($item) {
            return $item->product->category->name ?? 'Uncategorized';
        })->map(function($items, $category) {
            return [
                'category' => $category,
                'product_count' => $items->count(),
                'total_quantity' => $items->sum('total_quantity_sold'),
                'total_sales' => $items->sum('total_sales'),
            ];
        })->sortByDesc('total_sales')->values();

        return view('admin.Reports.product-wise-sales', compact(
            'menu',
            'title',
            'productSales',
            'summary',
            'categoryBreakdown',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export product-wise sales as CSV
     */
    public function exportProductWiseCsv(Request $request)
    {
        // Parse date range using service
        $dateRange = $this->salesReportService->parseDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            'current_month'
        );

        $start = $dateRange['start'];
        $end = $dateRange['end'];
        $startDate = $dateRange['startDate'];
        $endDate = $dateRange['endDate'];

        $productSales = InvoiceItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity_sold'),
                DB::raw('SUM(unit_total) as total_sales'),
                DB::raw('SUM(vat_amount) as total_vat'),
                DB::raw('COUNT(DISTINCT invoice_id) as order_count'),
                DB::raw('AVG(unit_price) as avg_unit_price')
            )
            ->with(['product' => function($query) {
                $query->select('id', 'name', 'sku', 'category_id', 'sell_price', 'stock_quantity')
                      ->with(['category' => function($q) {
                          $q->select('id', 'name');
                      }]);
            }])
            ->whereHas('invoice', function($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end])
                      ->where('status', '!=', 'cancelled');
            })
            ->groupBy('product_id')
            ->orderBy('total_sales', 'desc')
            ->get();

        $filename = $this->salesReportService->createCsvFilename('product_wise_sales', $startDate, $endDate);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($productSales) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Product Name',
                'SKU',
                'Category',
                'Quantity Sold',
                'Total Sales',
                'Total VAT',
                'Order Count',
                'Avg Unit Price',
                'Current Sell Price',
                'Current Stock'
            ]);

            // Data rows
            foreach ($productSales as $item) {
                fputcsv($file, [
                    $item->product->name ?? 'N/A',
                    $item->product->sku ?? 'N/A',
                    $item->product->category->name ?? 'Uncategorized',
                    number_format($item->total_quantity_sold, 0),
                    number_format($item->total_sales, 2),
                    number_format($item->total_vat, 2),
                    $item->order_count,
                    number_format($item->avg_unit_price, 2),
                    number_format($item->product->sell_price ?? 0, 2),
                    number_format($item->product->stock_quantity ?? 0, 0)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get data for AJAX requests
     */
    public function getData(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $invoices = Invoice::with('customer')
            ->whereBetween('date', [$start, $end])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'data' => $invoices->map(function($invoice) {
                return [
                    'invoice_number' => $invoice->invoice_number,
                    'date' => $invoice->date->format('Y-m-d'),
                    'customer' => $invoice->customer->name ?? 'N/A',
                    'status' => $invoice->status,
                    'payable_amount' => $invoice->formatted_payable_amount,
                    'paid_amount' => $invoice->formatted_paid_amount,
                    'due_amount' => $invoice->formatted_due_amount,
                    'actions' => route('invoice.show', $invoice->uuid)
                ];
            })
        ]);
    }

    /**
     * Display revenue report (profit analysis)
     */
    public function revenue(Request $request)
    {
        $menu = 'sales-report-revenue';
        $title = 'Revenue Report';

        // Parse date range using service
        $dateRange = $this->salesReportService->parseDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            'current_month'
        );

        $start = $dateRange['start'];
        $end = $dateRange['end'];
        $startDate = $dateRange['startDate'];
        $endDate = $dateRange['endDate'];

        // Get revenue summary
        $summary = $this->getRevenueSummary($start, $end);

        // Get daily revenue data for chart
        $dailyRevenue = $this->getDailyRevenue($start, $end);

        // Get revenue by category
        $categoryRevenue = $this->getRevenueByCategory($start, $end);

        // Get top profitable products
        $topProfitableProducts = $this->getTopProfitableProducts($start, $end, 10);

        // Get product revenue details
        $productRevenue = $this->getProductRevenue($start, $end);

        return view('admin.Reports.revenue', compact(
            'menu',
            'title',
            'startDate',
            'endDate',
            'summary',
            'dailyRevenue',
            'categoryRevenue',
            'topProfitableProducts',
            'productRevenue'
        ));
    }

    /**
     * Get revenue summary statistics
     */
    private function getRevenueSummary($start, $end)
    {
        $invoiceItems = InvoiceItem::whereHas('invoice', function($query) use ($start, $end) {
            $query->whereBetween('date', [$start, $end])
                  ->where('status', 'active');
        })->get();

        $totalSales = $invoiceItems->sum('unit_total');
        $totalRevenue = $invoiceItems->sum('revenue');
        $totalCost = $invoiceItems->sum(function($item) {
            $product = Product::find($item->product_id);
            return $product && $product->purchase_price
                ? $product->purchase_price * $item->quantity
                : 0;
        });

        $revenueMargin = $totalSales > 0 ? ($totalRevenue / $totalSales) * 100 : 0;
        $totalItemsSold = $invoiceItems->sum('quantity');

        return [
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'revenue_margin' => $revenueMargin,
            'items_sold' => $totalItemsSold,
            'avg_revenue_per_sale' => $totalItemsSold > 0 ? $totalRevenue / $totalItemsSold : 0
        ];
    }

    /**
     * Get daily revenue data for chart
     */
    private function getDailyRevenue($start, $end)
    {
        return InvoiceItem::select(
                DB::raw('DATE(invoices.date) as date'),
                DB::raw('SUM(invoice_items.unit_total) as total_sales'),
                DB::raw('SUM(invoice_items.revenue) as total_revenue')
            )
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->whereBetween('invoices.date', [$start, $end])
            ->where('invoices.status', 'active')
            ->groupBy(DB::raw('DATE(invoices.date)'))
            ->orderBy('date')
            ->get()
            ->map(function($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d'),
                    'sales' => (float) $item->total_sales,
                    'revenue' => (float) $item->total_revenue
                ];
            });
    }

    /**
     * Get revenue by category
     */
    private function getRevenueByCategory($start, $end)
    {
        return InvoiceItem::select(
                'categories.name as category',
                DB::raw('SUM(invoice_items.unit_total) as total_sales'),
                DB::raw('SUM(invoice_items.revenue) as total_revenue'),
                DB::raw('COUNT(DISTINCT invoice_items.product_id) as product_count'),
                DB::raw('SUM(invoice_items.quantity) as total_quantity')
            )
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('invoices.date', [$start, $end])
            ->where('invoices.status', 'active')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get()
            ->map(function($item) {
                $revenueMargin = $item->total_sales > 0
                    ? ($item->total_revenue / $item->total_sales) * 100
                    : 0;

                return [
                    'category' => $item->category ?? 'Uncategorized',
                    'total_sales' => (float) $item->total_sales,
                    'total_revenue' => (float) $item->total_revenue,
                    'revenue_margin' => $revenueMargin,
                    'product_count' => $item->product_count,
                    'total_quantity' => $item->total_quantity
                ];
            });
    }

    /**
     * Get top profitable products
     */
    private function getTopProfitableProducts($start, $end, $limit = 10)
    {
        return InvoiceItem::select(
                'products.name',
                'products.sku',
                'categories.name as category',
                DB::raw('SUM(invoice_items.quantity) as total_quantity'),
                DB::raw('SUM(invoice_items.unit_total) as total_sales'),
                DB::raw('SUM(invoice_items.revenue) as total_revenue')
            )
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('invoices.date', [$start, $end])
            ->where('invoices.status', 'active')
            ->groupBy('products.id', 'products.name', 'products.sku', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get detailed product revenue
     */
    private function getProductRevenue($start, $end)
    {
        return InvoiceItem::select(
                'products.id',
                'products.name',
                'products.sku',
                'products.stock_quantity',
                'categories.name as category',
                DB::raw('SUM(invoice_items.quantity) as total_quantity'),
                DB::raw('SUM(invoice_items.unit_total) as total_sales'),
                DB::raw('SUM(invoice_items.revenue) as total_revenue'),
                DB::raw('AVG(invoice_items.unit_price) as avg_price'),
                DB::raw('COUNT(DISTINCT invoice_items.invoice_id) as order_count')
            )
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('invoices.date', [$start, $end])
            ->where('invoices.status', 'active')
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.stock_quantity', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();
    }

    /**
     * Export revenue report to CSV
     */
    public function exportRevenueCsv(Request $request)
    {
        // Parse date range using service
        $dateRange = $this->salesReportService->parseDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            'current_month'
        );

        $start = $dateRange['start'];
        $end = $dateRange['end'];
        $startDate = $dateRange['startDate'];
        $endDate = $dateRange['endDate'];

        $productRevenue = $this->getProductRevenue($start, $end);

        $filename = $this->salesReportService->createCsvFilename('revenue', $startDate, $endDate);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($productRevenue) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Product Name',
                'SKU',
                'Category',
                'Quantity Sold',
                'Total Sales',
                'Total Revenue (Profit)',
                'Revenue Margin %',
                'Avg Price',
                'Orders',
                'Current Stock'
            ]);

            // Add data rows
            foreach ($productRevenue as $item) {
                $revenueMargin = $item->total_sales > 0
                    ? round(($item->total_revenue / $item->total_sales) * 100, 2)
                    : 0;

                fputcsv($file, [
                    $item->name,
                    $item->sku,
                    $item->category ?? 'Uncategorized',
                    $item->total_quantity,
                    number_format($item->total_sales, 2),
                    number_format($item->total_revenue, 2),
                    $revenueMargin . '%',
                    number_format($item->avg_price, 2),
                    $item->order_count,
                    $item->stock_quantity ?? 0
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
