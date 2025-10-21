<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    protected $purchaseService;
    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }
    public function index(): View
    {
        $data['title'] = 'Purchase';
        $data['menu'] = 'purchase';
        return view('admin.Purchase.index', $data);
    }

    public function getData(): JsonResponse
    {
        try {
            $purchases = Purchase::with(['supplier'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $purchases
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load purchases',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function create(): View
    {
        $data['title'] = 'Create Purchase';
        $data['menu'] = 'create-purchase';
        $data['suppliers'] = $this->purchaseService->getSuppliersForPurchase();
        $data['categories'] = $this->purchaseService->getCategoriesForPurchase();
        return view('admin.Purchase.create', $data);
    }


    public function store(PurchaseRequest $request)
    {
        try {
            $data = $request->validated();
            $purchase = $this->purchaseService->createPurchase($data);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase created successfully',
                    'data' => $purchase,
                    'redirect' => route('purchase.show', $purchase)
                ], 201);
            }

            return redirect()->route('purchase.show', $purchase)
                ->with('success', 'Purchase created successfully');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create purchase',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create purchase: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        // Check if PDF download is requested
        if (request()->has('download') && request()->get('download') === 'pdf') {
            return $this->downloadPdf($purchase);
        }

        $data['title'] = 'Purchase Details';
        $data['menu'] = 'purchase';
        $data['purchase'] = $purchase->load(['supplier', 'items.product.category', 'items.product.unit']);

        return view('admin.Purchase.show', $data);
    }

    public function edit(Purchase $purchase): View
    {
        // TODO: Implement edit functionality
        $data['title'] = 'Edit Purchase';
        $data['menu'] = 'purchase';
        $data['purchase'] = $purchase;

        return view('admin.Purchase.edit', $data);
    }

    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        // TODO: Implement update functionality
        return redirect()->back()->with('info', 'Update functionality will be implemented soon');
    }

    public function destroy(Purchase $purchase)
    {
        // TODO: Implement delete functionality
        return response()->json([
            'success' => false,
            'message' => 'Delete functionality will be implemented soon'
        ]);
    }

    private function downloadPdf(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product.category', 'items.product.unit']);

        $pdf = \PDF::loadView('admin.Purchase.invoice-pdf', ['purchase' => $purchase]);

        return $pdf->download('purchase-' . $purchase->invoice_number . '.pdf');
    }

    public function getProducts(Request $request): JsonResponse
    {
        try {
            $products = $this->purchaseService->getProductsForPurchase(
                $request->get('search'),
                $request->get('category_id'),
                $request->get('page', 1),
                12
            );

            return response()->json([
                'success' => true,
                'data' => $products['data'],
                'current_page' => $products['current_page'],
                'last_page' => $products['last_page'],
                'per_page' => $products['per_page'],
                'total' => $products['total'],
                'has_more' => $products['has_more']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while loading products.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function calculateUnitTotal(Request $request): JsonResponse
    {
        try {
            $response = $this->purchaseService->calculateUnitTotal(
                $request->get('unit_price'),
                $request->get('quantity'),
                $request->get('tax_id')
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'subtotal' => $response['subtotal'],
                    'tax_amount' => $response['tax_amount'],
                    'unit_total' => $response['unit_total'],
                    'tax_percentage' => $response['tax_percentage'],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while calculating unit total.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



}
