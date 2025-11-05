<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    /**
     * Display sales summary report
     */
    public function summary(Request $request)
    {
        $menu = 'sales-report-summary';
        $title = 'Sales Summary Report';

        // Get date range from request or default to current month
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Parse dates
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

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
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $invoices = Invoice::with(['customer', 'items.product'])
            ->whereBetween('date', [$start, $end])
            ->orderBy('date', 'desc')
            ->get();

        $filename = 'sales_summary_' . $startDate . '_to_' . $endDate . '.csv';

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
}
