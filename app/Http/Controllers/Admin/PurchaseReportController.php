<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseReportController extends Controller
{
    protected $reportService;

    public function __construct(SalesReportService $reportService)
    {
        $this->reportService = $reportService;
        $this->middleware('permission:view_purchase_reports');
    }

    /**
     * Display purchase summary report
     */
    public function index(Request $request)
    {
        $menu = 'purchase-report';
        $title = 'Purchase Reports';

        // Parse date range using service
        $dateRange = $this->reportService->parseDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            'current_month'
        );

        $start = $dateRange['start'];
        $end = $dateRange['end'];
        $startDate = $dateRange['startDate'];
        $endDate = $dateRange['endDate'];

        // Get summary statistics
        $summary = $this->getPurchaseSummary($start, $end);

        // Get daily purchase data for chart
        $dailyPurchases = $this->getDailyPurchases($start, $end);

        // Get top suppliers
        $topSuppliers = $this->getTopSuppliers($start, $end, 10);

        // Get top purchased products
        $topProducts = $this->getTopPurchasedProducts($start, $end, 10);

        // Get recent purchases
        $recentPurchases = Purchase::with('supplier')
            ->whereBetween('date', [$start, $end])
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        // Get product-wise purchase data
        $productPurchases = $this->getProductPurchases($start, $end);

        return view('admin.Reports.purchase-report', compact(
            'menu',
            'title',
            'summary',
            'dailyPurchases',
            'topSuppliers',
            'topProducts',
            'recentPurchases',
            'productPurchases',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get purchase summary statistics
     */
    private function getPurchaseSummary($start, $end)
    {
        $purchases = Purchase::whereBetween('date', [$start, $end])->get();

        return [
            'total_purchases' => $purchases->sum('total_amount'),
            'total_paid' => $purchases->sum('paid_amount'),
            'total_due' => $purchases->sum('due_amount'),
            'total_count' => $purchases->count(),
            'total_tax' => PurchaseItem::whereHas('purchase', function($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end]);
            })->sum('tax_amount'),
            'items_purchased' => PurchaseItem::whereHas('purchase', function($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end]);
            })->sum('quantity'),
            'unique_products' => PurchaseItem::whereHas('purchase', function($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end]);
            })->distinct()->count('product_id'),
            'unique_suppliers' => $purchases->unique('supplier_id')->count(),
            'avg_purchase_value' => $purchases->count() > 0 ? $purchases->avg('total_amount') : 0,
        ];
    }

    /**
     * Get daily purchase data
     */
    private function getDailyPurchases($start, $end)
    {
        return Purchase::select(
                DB::raw('DATE(date) as purchase_date'),
                DB::raw('COUNT(*) as purchase_count'),
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(paid_amount) as paid_amount'),
                DB::raw('SUM(due_amount) as due_amount')
            )
            ->whereBetween('date', [$start, $end])
            ->groupBy('purchase_date')
            ->orderBy('purchase_date', 'asc')
            ->get();
    }

    /**
     * Get top suppliers by purchase amount
     */
    private function getTopSuppliers($start, $end, $limit = 10)
    {
        return Purchase::select(
                'supplier_id',
                DB::raw('COUNT(*) as purchase_count'),
                DB::raw('SUM(total_amount) as total_purchase'),
                DB::raw('SUM(paid_amount) as total_paid'),
                DB::raw('SUM(due_amount) as total_due')
            )
            ->with(['supplier' => function($query) {
                $query->select('id', 'name', 'email', 'phone');
            }])
            ->whereBetween('date', [$start, $end])
            ->groupBy('supplier_id')
            ->orderBy('total_purchase', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top purchased products
     */
    private function getTopPurchasedProducts($start, $end, $limit = 10)
    {
        return PurchaseItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(unit_total) as total_amount'),
                DB::raw('COUNT(DISTINCT purchase_id) as order_count'),
                DB::raw('AVG(unit_price) as avg_price')
            )
            ->with(['product' => function($query) {
                $query->select('id', 'name', 'sku', 'purchase_price');
            }])
            ->whereHas('purchase', function($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end]);
            })
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get detailed product purchase data
     */
    private function getProductPurchases($start, $end)
    {
        return PurchaseItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity_purchased'),
                DB::raw('SUM(unit_total) as total_purchase_cost'),
                DB::raw('COUNT(DISTINCT purchase_id) as order_count'),
                DB::raw('AVG(unit_price) as avg_purchase_price'),
                DB::raw('MIN(unit_price) as min_purchase_price'),
                DB::raw('MAX(unit_price) as max_purchase_price')
            )
            ->with(['product' => function($query) {
                $query->select('id', 'name', 'sku', 'category_id', 'purchase_price', 'stock_quantity')
                      ->with(['category' => function($q) {
                          $q->select('id', 'name');
                      }]);
            }])
            ->whereHas('purchase', function($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end]);
            })
            ->groupBy('product_id')
            ->orderBy('total_purchase_cost', 'desc')
            ->get();
    }

    /**
     * Export purchase report as CSV
     */
    public function exportCsv(Request $request)
    {
        // Parse date range using service
        $dateRange = $this->reportService->parseDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            'current_month'
        );

        $start = $dateRange['start'];
        $end = $dateRange['end'];
        $startDate = $dateRange['startDate'];
        $endDate = $dateRange['endDate'];

        $purchases = Purchase::with(['supplier', 'items.product'])
            ->whereBetween('date', [$start, $end])
            ->orderBy('date', 'desc')
            ->get();

        $filename = $this->reportService->createCsvFilename('purchase_report', $startDate, $endDate);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($purchases) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Invoice Number',
                'Date',
                'Supplier',
                'Phone',
                'Total Amount',
                'Paid Amount',
                'Due Amount',
                'Items Count'
            ]);

            // Data rows
            foreach ($purchases as $purchase) {
                fputcsv($file, [
                    $purchase->invoice_number,
                    $purchase->date->format('Y-m-d'),
                    $purchase->supplier->name ?? 'N/A',
                    $purchase->supplier->phone ?? 'N/A',
                    number_format($purchase->total_amount, 2),
                    number_format($purchase->paid_amount, 2),
                    number_format($purchase->due_amount, 2),
                    $purchase->items->count()
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export product-wise purchase report as CSV
     */
    public function exportProductWiseCsv(Request $request)
    {
        // Parse date range using service
        $dateRange = $this->reportService->parseDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            'current_month'
        );

        $start = $dateRange['start'];
        $end = $dateRange['end'];
        $startDate = $dateRange['startDate'];
        $endDate = $dateRange['endDate'];

        $productPurchases = $this->getProductPurchases($start, $end);

        $filename = $this->reportService->createCsvFilename('product_wise_purchase', $startDate, $endDate);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($productPurchases) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Product Name',
                'SKU',
                'Category',
                'Quantity Purchased',
                'Total Cost',
                'Order Count',
                'Avg Price',
                'Min Price',
                'Max Price',
                'Current Stock'
            ]);

            // Data rows
            foreach ($productPurchases as $item) {
                fputcsv($file, [
                    $item->product->name ?? 'N/A',
                    $item->product->sku ?? 'N/A',
                    $item->product->category->name ?? 'Uncategorized',
                    number_format($item->total_quantity_purchased, 0),
                    number_format($item->total_purchase_cost, 2),
                    $item->order_count,
                    number_format($item->avg_purchase_price, 2),
                    number_format($item->min_purchase_price, 2),
                    number_format($item->max_purchase_price, 2),
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

        $purchases = Purchase::with('supplier')
            ->whereBetween('date', [$start, $end])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'data' => $purchases->map(function($purchase) {
                return [
                    'invoice_number' => $purchase->invoice_number,
                    'date' => $purchase->date->format('Y-m-d'),
                    'supplier' => $purchase->supplier->name ?? 'N/A',
                    'total_amount' => $purchase->formatted_total_amount,
                    'paid_amount' => $purchase->formatted_paid_amount,
                    'due_amount' => $purchase->formatted_due_amount,
                    'actions' => route('purchase.show', $purchase->uuid)
                ];
            })
        ]);
    }
}
