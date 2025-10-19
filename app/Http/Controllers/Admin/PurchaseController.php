<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseRequest;
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

    public function create(): View
    {
        $data['title'] = 'Create Purchase';
        $data['menu'] = 'create-purchase';
        $data['suppliers'] = $this->purchaseService->getSuppliersForPurchase();
        $data['categories'] = $this->purchaseService->getCategoriesForPurchase();
        return view('admin.Purchase.create', $data);
    }


    public function store(PurchaseRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $purchase = $this->purchaseService->createPurchase($data);

            return response()->json([
                'message' => 'Purchase created successfully',
                'data' => $purchase
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to create purchase',
                'error' => $e->getMessage()
            ], 500);
        }
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
