<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PaymentFromCustomer;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentFromCustomerController extends Controller
{
    /**
     * Apply permission middleware
     */
    public function __construct()
    {
        $this->middleware('permission:manage_payment');
    }

    /**
     * Display a listing of payments from customers
     */
    public function index()
    {
        $data['title'] = 'Received from Customer';
        $data['menu'] = 'payment-from-customer';
        return view('admin.PaymentFromCustomer.index', $data);
    }

    /**
     * Get payment data for DataTable with date range filter
     */
    public function getData(Request $request): JsonResponse
    {
        try {
            // Get date range (default to last 15 days)
            $startDate = $request->input('start_date', now()->subDays(15)->format('Y-m-d'));
            $endDate = $request->input('end_date', now()->format('Y-m-d'));

            // Query payments with date range filter (store filter applied via global scope)
            $query = PaymentFromCustomer::with(['customer', 'invoice'])
                ->whereBetween('payment_date', [$startDate, $endDate]);

            // Search functionality
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function($q) use ($searchValue) {
                    $q->whereHas('customer', function($cq) use ($searchValue) {
                        $cq->where('name', 'like', "%{$searchValue}%");
                    })
                    ->orWhereHas('invoice', function($iq) use ($searchValue) {
                        $iq->where('invoice_number', 'like', "%{$searchValue}%");
                    })
                    ->orWhere('amount', 'like', "%{$searchValue}%")
                    ->orWhere('note', 'like', "%{$searchValue}%");
                });
            }

            // Get total and filtered counts (store filter applied via global scope)
            $totalRecords = PaymentFromCustomer::count();
            $filteredRecords = $query->count();

            // Sorting
            if ($request->has('order') && count($request->order) > 0) {
                $orderColumnIndex = $request->order[0]['column'];
                $orderDirection = $request->order[0]['dir'];

                $columns = ['payment_date', 'customer_name', 'invoice_number', 'amount', 'note'];
                if (isset($columns[$orderColumnIndex])) {
                    $orderColumn = $columns[$orderColumnIndex];

                    if ($orderColumn === 'customer_name') {
                        $query->leftJoin('customers', 'payment_from_customers.customer_id', '=', 'customers.id')
                              ->orderBy('customers.name', $orderDirection)
                              ->select('payment_from_customers.*');
                    } elseif ($orderColumn === 'invoice_number') {
                        $query->leftJoin('invoices', 'payment_from_customers.invoice_id', '=', 'invoices.id')
                              ->orderBy('invoices.invoice_number', $orderDirection)
                              ->select('payment_from_customers.*');
                    } else {
                        $query->orderBy('payment_from_customers.' . $orderColumn, $orderDirection);
                    }
                }
            } else {
                $query->orderBy('payment_from_customers.payment_date', 'desc')
                      ->orderBy('payment_from_customers.created_at', 'desc');
            }

            // Pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $payments = $query->skip($start)->take($length)->get();

            // Format data for DataTable
            $data = $payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'payment_date' => $payment->payment_date ? $payment->payment_date->format(get_option('date_format', 'Y-m-d')) : '-',
                    'customer_name' => $payment->customer ? $payment->customer->name : '-',
                    'invoice_number' => $payment->invoice ? $payment->invoice->invoice_number : '-',
                    'amount' => $payment->amount,
                    'formatted_amount' => get_option('app_currency', '$') . number_format($payment->amount, 2),
                    'note' => $payment->note ?? '-',
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Payment from customer getData error: ' . $e->getMessage());
            return response()->json([
                'draw' => intval($request->input('draw', 0)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export payments to CSV
     */
    public function exportCsv(Request $request)
    {
        try {
            // Get date range
            $startDate = $request->input('start_date', now()->subDays(15)->format('Y-m-d'));
            $endDate = $request->input('end_date', now()->format('Y-m-d'));

            // Query payments (store filter applied via global scope)
            $payments = PaymentFromCustomer::with(['customer', 'invoice'])
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->orderBy('payment_date', 'desc')
                ->get();

            // Generate CSV filename
            $filename = 'payments_from_customers_' . date('Y-m-d_His') . '.csv';

            // Set headers for CSV download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            // Create CSV content
            $callback = function() use ($payments) {
                $file = fopen('php://output', 'w');

                // Add CSV headers
                fputcsv($file, ['Payment Date', 'Customer Name', 'Invoice Number', 'Amount', 'Note']);

                // Add data rows
                foreach ($payments as $payment) {
                    fputcsv($file, [
                        $payment->payment_date ? $payment->payment_date->format(get_option('date_format', 'Y-m-d')) : '-',
                        $payment->customer ? $payment->customer->name : '-',
                        $payment->invoice ? $payment->invoice->invoice_number : '-',
                        $payment->amount,
                        $payment->note ?? '-',
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    /**
     * Store payment from customer for an invoice
     */
    public function storeInvoicePayment(Request $request, Invoice $invoice): JsonResponse
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $amount = $request->amount;
            $dueAmount = $invoice->due_amount;

            // Validate payment amount
            if ($amount > $dueAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount cannot exceed due amount'
                ], 400);
            }

            // Create payment record
            $payment = PaymentFromCustomer::create([
                'store_id' => Auth::user()->store_id,
                'customer_id' => $invoice->customer_id,
                'invoice_id' => $invoice->id,
                'payment_date' => $request->payment_date,
                'amount' => $amount,
                'note' => $request->note ?? null,
            ]);

            // Update invoice paid and due amounts
            $newPaidAmount = $invoice->paid_amount + $amount;
            $newDueAmount = $invoice->payable_amount - $newPaidAmount;

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'due_amount' => $newDueAmount,
            ]);

            // Update customer balance (decrease by payment amount)
            $customer = Customer::findOrFail($invoice->customer_id);
            if (\Illuminate\Support\Facades\Schema::hasColumn('customers', 'balance')) {
                $customer->decrement('balance', $amount);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment received successfully',
                'data' => [
                    'payment' => $payment,
                    'invoice' => $invoice->fresh(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
