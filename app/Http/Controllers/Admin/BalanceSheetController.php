<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Debt;
use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PaymentFromCustomer;
use App\Models\PaymentToSupplier;
use App\Models\Expense;
use App\Models\Income;
use App\Services\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BalanceSheetController extends Controller
{
    protected $salesReportService;

    public function __construct(SalesReportService $salesReportService)
    {
        $this->middleware('permission:view_balance_sheet');
        $this->salesReportService = $salesReportService;
    }

    /**
     * Display the balance sheet report
     */
    public function index(Request $request)
    {
        $storeId = Auth::user()->store_id;

        // Get date filter (default to current date for balance sheet "as of" date)
        $asOfDate = $request->input('as_of_date', now()->format(get_option('date_format', 'Y-m-d')));

        // Parse the date
        $parsedDate = $this->salesReportService->parseDateRange(
            $asOfDate,
            $asOfDate,
            'today'
        );

        // Calculate Assets
        $assets = $this->calculateAssets($storeId, $parsedDate['end']);

        // Calculate Liabilities
        $liabilities = $this->calculateLiabilities($storeId, $parsedDate['end']);

        // Get Bank Balance breakdown
        $bankBalanceBreakdown = $this->getBankBalanceBreakdown($storeId, $parsedDate['end']);

        // Calculate totals
        $totalAssets = array_sum(array_column($assets, 'amount'));
        $totalLiabilities = array_sum(array_column($liabilities, 'amount'));
        $netAssets = $totalAssets - $totalLiabilities;
        $title = 'Balance Sheet';

        return view('admin.Reports.balance-sheet', compact(
            'assets',
            'liabilities',
            'totalAssets',
            'totalLiabilities',
            'netAssets',
            'asOfDate',
            'bankBalanceBreakdown',
            'title'
        ))->with('menu', 'balance-sheet');
    }

    /**
     * Calculate all assets
     */
    private function calculateAssets($storeId, $asOfDate)
    {
        $assets = [];

        // 1. Fixed Assets (Total Security/Asset)
        $fixedAssets = Asset::where('store_id', $storeId)
            ->whereDate('created_at', '<=', $asOfDate)
            ->sum('amount');

        $assets[] = [
            'name' => 'Total Security/Asset',
            'amount' => $fixedAssets,
            'note' => 'Fixed assets and security deposits'
        ];

        // 2. Inventory Value (from all purchases up to the date)
        $inventoryValue = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->where('purchases.store_id', $storeId)
            ->whereDate('purchases.date', '<=', $asOfDate)
            ->whereNull('purchases.deleted_at')
            ->whereNull('purchase_items.deleted_at')
            ->sum(DB::raw('purchase_items.quantity * purchase_items.unit_price'));

        $assets[] = [
            'name' => 'Inventory Value',
            'amount' => $inventoryValue,
            'note' => 'Total value of all purchased items'
        ];

        // 3. Client's Dues (Accounts Receivable)
        $clientDues = Invoice::where('store_id', $storeId)
            ->where('status', 'active')
            ->whereDate('date', '<=', $asOfDate)
            ->sum('due_amount');

        $assets[] = [
            'name' => "Client's Dues",
            'amount' => $clientDues,
            'note' => 'Outstanding receivables from customers'
        ];

        // 4. Bank Balance (Cash in hand)
        $bankBalance = $this->calculateBankBalance($storeId, $asOfDate);

        $assets[] = [
            'name' => 'Bank Balance',
            'amount' => $bankBalance,
            'note' => 'Cash in hand (see breakdown below)'
        ];

        return $assets;
    }

    /**
     * Calculate all liabilities
     */
    private function calculateLiabilities($storeId, $asOfDate)
    {
        $liabilities = [];

        // 1. Supplier's Dues (Accounts Payable)
        $supplierDues = Purchase::where('store_id', $storeId)
            ->whereDate('date', '<=', $asOfDate)
            ->sum('due_amount');

        $liabilities[] = [
            'name' => "Supplier's Dues",
            'amount' => $supplierDues,
            'note' => 'Outstanding payables to suppliers'
        ];

        // 2. Bank Loan (Debts - Unpaid)
        $bankLoan = Debt::where('store_id', $storeId)
            ->where('status', 'Unpaid')
            ->whereDate('date', '<=', $asOfDate)
            ->sum('amount');

        $liabilities[] = [
            'name' => 'Bank Loan',
            'amount' => $bankLoan,
            'note' => 'Unpaid loans and debts'
        ];

        return $liabilities;
    }

    /**
     * Get detailed breakdown of bank balance components
     */
    private function getBankBalanceBreakdown($storeId, $asOfDate)
    {
        // Total payments received from customers
        $paymentsReceived = PaymentFromCustomer::where('store_id', $storeId)
            ->whereDate('payment_date', '<=', $asOfDate)
            ->sum('amount');

        // Total payments made to suppliers
        $paymentsMade = PaymentToSupplier::where('store_id', $storeId)
            ->whereDate('payment_date', '<=', $asOfDate)
            ->sum('amount');

        // Total expenses
        $totalExpenses = Expense::where('store_id', $storeId)
            ->whereDate('date', '<=', $asOfDate)
            ->sum('amount');

        // Other income
        $otherIncome = Income::where('store_id', $storeId)
            ->whereDate('date', '<=', $asOfDate)
            ->sum('amount');

        return [
            'payments_received' => $paymentsReceived,
            'other_income' => $otherIncome,
            'payments_made' => $paymentsMade,
            'expenses' => $totalExpenses,
            'total' => $paymentsReceived + $otherIncome - ($paymentsMade + $totalExpenses)
        ];
    }

    /**
     * Calculate bank balance (cash in hand)
     * This is calculated as: Payments Received - Payments Made - Expenses + Other Income
     */
    private function calculateBankBalance($storeId, $asOfDate)
    {
        // Total payments received from customers
        $paymentsReceived = PaymentFromCustomer::where('store_id', $storeId)
            ->whereDate('payment_date', '<=', $asOfDate)
            ->sum('amount');

        // Total payments made to suppliers
        $paymentsMade = PaymentToSupplier::where('store_id', $storeId)
            ->whereDate('payment_date', '<=', $asOfDate)
            ->sum('amount');

        // Total expenses
        $totalExpenses = Expense::where('store_id', $storeId)
            ->whereDate('date', '<=', $asOfDate)
            ->sum('amount');

        // Other income
        $otherIncome = Income::where('store_id', $storeId)
            ->whereDate('date', '<=', $asOfDate)
            ->sum('amount');

        // Calculate bank balance
        $bankBalance = $paymentsReceived + $otherIncome - ($paymentsMade + $totalExpenses) ;

        return $bankBalance;
    }

    /**
     * Export balance sheet to CSV
     */
    public function exportCsv(Request $request)
    {
        $storeId = Auth::user()->store_id;
        $asOfDate = $request->input('as_of_date', now()->format('Y-m-d'));

        $parsedDate = $this->salesReportService->parseDateRange(
            $asOfDate,
            $asOfDate,
            'today'
        );

        // Calculate data
        $assets = $this->calculateAssets($storeId, $parsedDate['end']);
        $liabilities = $this->calculateLiabilities($storeId, $parsedDate['end']);
        $totalAssets = array_sum(array_column($assets, 'amount'));
        $totalLiabilities = array_sum(array_column($liabilities, 'amount'));
        $netAssets = $totalAssets - $totalLiabilities;

        // Generate CSV filename
        $filename = 'balance_sheet_' . $asOfDate . '.csv';

        // Create CSV content
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($assets, $liabilities, $totalAssets, $totalLiabilities, $netAssets, $asOfDate) {
            $file = fopen('php://output', 'w');

            // Title
            fputcsv($file, ['Balance Sheet']);
            fputcsv($file, ['As of Date:', $asOfDate]);
            fputcsv($file, []);

            // Assets section
            fputcsv($file, ['ASSETS']);
            fputcsv($file, ['Item', 'Amount']);
            foreach ($assets as $asset) {
                fputcsv($file, [$asset['name'], number_format($asset['amount'], 2)]);
            }
            fputcsv($file, ['Total Assets', number_format($totalAssets, 2)]);
            fputcsv($file, []);

            // Liabilities section
            fputcsv($file, ['LIABILITIES']);
            fputcsv($file, ['Item', 'Amount']);
            foreach ($liabilities as $liability) {
                fputcsv($file, [$liability['name'], number_format($liability['amount'], 2)]);
            }
            fputcsv($file, ['Total Liabilities', number_format($totalLiabilities, 2)]);
            fputcsv($file, []);

            // Net Assets
            fputcsv($file, ['Net Assets (Assets - Liabilities)', number_format($netAssets, 2)]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
