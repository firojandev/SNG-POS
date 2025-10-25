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
        $data['suppliers'] = Supplier::orderBy('name')->get(['id','name']);
        return view('admin.PaymentToSupplier.index', $data);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentToSupplierStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Create payment
            $payment = PaymentToSupplier::create($request->validated());

            // Deduct amount from supplier balance
            $supplier = Supplier::findOrFail($request->supplier_id);
            $supplier->decrement('balance', $request->amount);

            $payment->load('supplier');

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Payment created successfully', 'data' => new PaymentToSupplierResource($payment)]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create payment'], 500);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentToSupplier $paymentToSupplier): JsonResponse
    {
        try {
            $paymentToSupplier->load('supplier');
            return response()->json(['success' => true, 'data' => new PaymentToSupplierResource($paymentToSupplier)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentToSupplierUpdateRequest $request, PaymentToSupplier $paymentToSupplier): JsonResponse
    {
        try {
            DB::beginTransaction();

            $oldAmount = $paymentToSupplier->amount;
            $oldSupplierId = $paymentToSupplier->supplier_id;
            $newAmount = $request->amount;
            $newSupplierId = $request->supplier_id;

            // If supplier changed, add back to old supplier and deduct from new supplier
            if ($oldSupplierId != $newSupplierId) {
                // Add back old amount to old supplier
                $oldSupplier = Supplier::findOrFail($oldSupplierId);
                $oldSupplier->increment('balance', $oldAmount);

                // Deduct new amount from new supplier
                $newSupplier = Supplier::findOrFail($newSupplierId);
                $newSupplier->decrement('balance', $newAmount);
            } else {
                // Same supplier, adjust the difference
                $difference = $newAmount - $oldAmount;
                $supplier = Supplier::findOrFail($newSupplierId);

                if ($difference > 0) {
                    // New amount is higher, deduct the difference
                    $supplier->decrement('balance', $difference);
                } elseif ($difference < 0) {
                    // New amount is lower, add back the difference
                    $supplier->increment('balance', abs($difference));
                }
            }

            $paymentToSupplier->update($request->validated());
            $paymentToSupplier->refresh()->load('supplier');

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Payment updated successfully', 'data' => new PaymentToSupplierResource($paymentToSupplier)]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update payment'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentToSupplier $paymentToSupplier): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Add back the amount to supplier balance
            $supplier = Supplier::findOrFail($paymentToSupplier->supplier_id);
            $supplier->increment('balance', $paymentToSupplier->amount);

            $paymentToSupplier->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Payment deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete payment'], 500);
        }
    }

    /**
     * Get all payments for DataTable with server-side pagination.
     */
    public function getData(Request $request): JsonResponse
    {
        try {
            // Get total records count (before filtering)
            $totalRecords = PaymentToSupplier::count();

            // Start building query
            $query = PaymentToSupplier::query();

            // Search functionality
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function($q) use ($searchValue) {
                    $q->whereHas('supplier', function($sq) use ($searchValue) {
                        $sq->where('name', 'like', "%{$searchValue}%");
                    })
                    ->orWhere('amount', 'like', "%{$searchValue}%")
                    ->orWhere('payment_date', 'like', "%{$searchValue}%")
                    ->orWhere('note', 'like', "%{$searchValue}%");
                });
            }

            // Get filtered records count
            $filteredRecords = $query->count();

            // Sorting
            $joinedWithSupplier = false;
            if ($request->has('order') && count($request->order) > 0) {
                $orderColumnIndex = $request->order[0]['column'];
                $orderDirection = $request->order[0]['dir'];

                // Map column index to database column
                $columns = ['supplier_name', 'amount', 'payment_date', 'note'];
                if (isset($columns[$orderColumnIndex])) {
                    $orderColumn = $columns[$orderColumnIndex];

                    if ($orderColumn === 'supplier_name') {
                        $query->leftJoin('suppliers', 'payment_to_suppliers.supplier_id', '=', 'suppliers.id')
                              ->orderBy('suppliers.name', $orderDirection)
                              ->select('payment_to_suppliers.*');
                        $joinedWithSupplier = true;
                    } else {
                        $query->orderBy('payment_to_suppliers.' . $orderColumn, $orderDirection);
                    }
                }
            } else {
                // Default sorting: latest payment_date first
                $query->orderBy('payment_to_suppliers.payment_date', 'desc')
                      ->orderBy('payment_to_suppliers.created_at', 'desc');
            }

            // Fallback if joined but no additional sorting applied
            if ($joinedWithSupplier) {
                $query->orderBy('payment_to_suppliers.payment_date', 'desc');
            }

            // Pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $payments = $query->with('supplier')->skip($start)->take($length)->get();

            // Format data for DataTable
            $data = [];
            foreach ($payments as $payment) {
                $data[] = [
                    'id' => $payment->id,
                    'supplier' => $payment->supplier ? $payment->supplier->name : '',
                    'amount' => $payment->amount,
                    'payment_date' => optional($payment->payment_date)->format(get_option('date_format', 'Y-m-d')),
                    'note' => $payment->note ?? '',
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Payment getData error: ' . $e->getMessage());
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
     * Get supplier balance.
     */
    public function getSupplierBalance(Request $request): JsonResponse
    {
        try {
            $supplierId = $request->supplier_id;
            $paymentId = $request->payment_id; // For edit mode

            $supplier = Supplier::findOrFail($supplierId);
            $balance = $supplier->balance;

            // If editing, add back the current payment amount to show available balance
            if ($paymentId) {
                $payment = PaymentToSupplier::find($paymentId);
                if ($payment && $payment->supplier_id == $supplierId) {
                    $balance += $payment->amount;
                }
            }

            return response()->json([
                'success' => true,
                'balance' => $balance,
                'formatted_balance' => get_option('app_currency', '$') . number_format($balance, 2)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Supplier not found'], 404);
        }
    }
}
