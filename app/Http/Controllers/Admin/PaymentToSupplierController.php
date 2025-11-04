<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentToSupplierStoreRequest;
use App\Http\Requests\PaymentToSupplierUpdateRequest;
use App\Models\PaymentToSupplier;
use App\Models\Supplier;
use App\Http\Resources\PaymentToSupplierResource;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentToSupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $data['title'] = 'Payment to Supplier List';
        $data['menu'] = 'payment-to-supplier';
        return view('admin.PaymentToSupplier.index', $data);
    }



    /**
     * Get all payments for DataTable with server-side pagination and date filter.
     */
    public function getData(Request $request): JsonResponse
    {
        try {
            // Get date range (default to last 15 days)
            $startDate = $request->input('start_date', now()->subDays(15)->format('Y-m-d'));
            $endDate = $request->input('end_date', now()->format('Y-m-d'));

            // Query payments with date range filter
            $query = PaymentToSupplier::with('supplier')
                ->whereBetween('payment_date', [$startDate, $endDate]);

            // Search functionality
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function($q) use ($searchValue) {
                    $q->whereHas('supplier', function($sq) use ($searchValue) {
                        $sq->where('name', 'like', "%{$searchValue}%");
                    })
                    ->orWhere('amount', 'like', "%{$searchValue}%")
                    ->orWhere('note', 'like', "%{$searchValue}%");
                });
            }

            // Get total and filtered counts
            $totalRecords = PaymentToSupplier::count();
            $filteredRecords = $query->count();

            // Sorting
            if ($request->has('order') && count($request->order) > 0) {
                $orderColumnIndex = $request->order[0]['column'];
                $orderDirection = $request->order[0]['dir'];

                $columns = ['payment_date', 'supplier_name', 'amount', 'note'];
                if (isset($columns[$orderColumnIndex])) {
                    $orderColumn = $columns[$orderColumnIndex];

                    if ($orderColumn === 'supplier_name') {
                        $query->leftJoin('suppliers', 'payment_to_suppliers.supplier_id', '=', 'suppliers.id')
                              ->orderBy('suppliers.name', $orderDirection)
                              ->select('payment_to_suppliers.*');
                    } else {
                        $query->orderBy('payment_to_suppliers.' . $orderColumn, $orderDirection);
                    }
                }
            } else {
                $query->orderBy('payment_to_suppliers.payment_date', 'desc')
                      ->orderBy('payment_to_suppliers.created_at', 'desc');
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
                    'supplier_name' => $payment->supplier ? $payment->supplier->name : '-',
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
            \Log::error('Payment to supplier getData error: ' . $e->getMessage());
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

            // Query payments
            $payments = PaymentToSupplier::with('supplier')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->orderBy('payment_date', 'desc')
                ->get();

            // Generate CSV filename
            $filename = 'payments_to_suppliers_' . date('Y-m-d_His') . '.csv';

            // Set headers for CSV download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            // Create CSV content
            $callback = function() use ($payments) {
                $file = fopen('php://output', 'w');

                // Add CSV headers
                fputcsv($file, ['Payment Date', 'Supplier Name', 'Amount', 'Note']);

                // Add data rows
                foreach ($payments as $payment) {
                    fputcsv($file, [
                        $payment->payment_date ? $payment->payment_date->format(get_option('date_format', 'Y-m-d')) : '-',
                        $payment->supplier ? $payment->supplier->name : '-',
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

}
