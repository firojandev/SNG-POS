<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * @return View
     */

    public function index(): View
    {
        $data['title'] = 'Dashboard';
        $data['menu'] = 'dashboard';

        // Check if user has view_dashboard permission
        if (!auth()->user()->can('view_dashboard')) {
            // User doesn't have permission - show welcome page
            $data['user'] = auth()->user();
            return view('admin.welcome', $data);
        }

        // User has permission - show full dashboard
        // Get current user's store_id
        $storeId = auth()->user()->store_id;

        // Get current month and year
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // ========== Summary Statistics ==========

        // Customer count (has global scope)
        $data['total_customers'] = Customer::count();

        // Supplier count (has global scope)
        $data['total_suppliers'] = Supplier::count();

        // Total products count (has global scope)
        $data['total_products'] = Product::count();

        // Total sales (invoices) - current month
        $data['total_sales'] = Invoice::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('payable_amount');

        // Total invoice count - current month
        $data['total_orders'] = Invoice::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        // Total purchases - current month
        $data['total_purchases'] = Purchase::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('total_amount');

        // Calculate actual profit from sold items (using revenue column from invoice_items)
        $actual_profit_from_sales = InvoiceItem::join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.store_id', $storeId)
            ->whereMonth('invoices.date', $currentMonth)
            ->whereYear('invoices.date', $currentYear)
            ->whereNull('invoices.deleted_at')
            ->sum('invoice_items.revenue');

        // Total income and expenses
        $total_income = Income::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $total_expenses = Expense::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $data['total_income'] = $total_income;
        $data['total_expenses'] = $total_expenses;

        // Total revenue (profit from sales + other income - expenses)
        $data['total_revenue'] = $actual_profit_from_sales + $total_income - $total_expenses;

        // Calculate profit margin
        $data['profit_margin'] = $data['total_sales'] > 0
            ? (($data['total_revenue'] / $data['total_sales']) * 100)
            : 0;

        // Low stock products (stock < 10)
        $data['low_stock_count'] = Product::where('stock_quantity', '<', 10)
            ->where('stock_quantity', '>', 0)
            ->count();

        // Out of stock products
        $data['out_of_stock_count'] = Product::where('stock_quantity', 0)->count();

        // Total stock value
        $data['total_stock_value'] = Product::select(DB::raw('SUM(stock_quantity * purchase_price) as total'))
            ->value('total') ?? 0;

        // ========== Charts Data ==========

        // Monthly sales data for chart (last 12 months)
        $monthlySales = Invoice::select(
                DB::raw('MONTH(date) as month'),
                DB::raw('YEAR(date) as year'),
                DB::raw('SUM(payable_amount) as total')
            )
            ->where('date', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Monthly revenue (profit) data for chart (last 12 months)
        $monthlyRevenue = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->select(
                DB::raw('MONTH(invoices.date) as month'),
                DB::raw('YEAR(invoices.date) as year'),
                DB::raw('SUM(invoice_items.revenue) as total_revenue')
            )
            ->where('invoices.store_id', $storeId)
            ->where('invoices.date', '>=', now()->subMonths(11)->startOfMonth())
            ->whereNull('invoices.deleted_at')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $data['monthly_sales'] = $monthlySales;
        $data['monthly_revenue'] = $monthlyRevenue;

        // Sales by status for radial chart
        $data['active_sales'] = Invoice::where('status', 'active')->sum('payable_amount');
        $data['returned_sales'] = Invoice::where('status', 'returned')->sum('payable_amount');
        $data['cancelled_sales'] = Invoice::where('status', 'cancelled')->sum('payable_amount');

        // ========== Recent Data ==========

        // Recent invoices (last 10)
        $data['recent_invoices'] = Invoice::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent purchases (last 10)
        $data['recent_purchases'] = Purchase::with('supplier')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // ========== Top Products ==========

        // Top selling products (by quantity)
        $data['top_selling_products'] = InvoiceItem::select(
                'products.id',
                'products.name',
                'products.sku',
                'products.stock_quantity',
                DB::raw('SUM(invoice_items.quantity) as total_quantity'),
                DB::raw('SUM(invoice_items.unit_total) as total_sales'),
                DB::raw('COUNT(DISTINCT invoice_items.invoice_id) as order_count')
            )
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.store_id', $storeId)
            ->where('products.store_id', $storeId)
            ->whereNull('invoices.deleted_at')
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.stock_quantity')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        // Top revenue generating products
        $data['top_revenue_products'] = InvoiceItem::select(
                'products.id',
                'products.name',
                'products.sku',
                'products.purchase_price',
                'products.sell_price',
                DB::raw('SUM(invoice_items.quantity) as total_quantity'),
                DB::raw('SUM(invoice_items.unit_total) as total_sales'),
                DB::raw('SUM(invoice_items.revenue) as total_profit')
            )
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.store_id', $storeId)
            ->where('products.store_id', $storeId)
            ->whereNull('invoices.deleted_at')
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.purchase_price', 'products.sell_price')
            ->orderBy('total_profit', 'desc')
            ->limit(10)
            ->get();

        // Low stock products
        $data['low_stock_products'] = Product::where('stock_quantity', '<', 10)
            ->where('stock_quantity', '>=', 0)
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        // ========== Payment Status ==========

        // Total due from customers
        $data['total_due_from_customers'] = Invoice::sum('due_amount');

        // Total due to suppliers
        $data['total_due_to_suppliers'] = Purchase::sum('due_amount');

        return view('admin.dashboard', $data);
    }
}
