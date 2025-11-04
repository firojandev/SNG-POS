<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\Customer;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index(): View
    {
        $data['title'] = 'Sales Invoice';
        $data['menu'] = 'invoice';
        return view('admin.Invoice.index', $data);
    }

    public function getData(): JsonResponse
    {
        try {
            $invoices = Invoice::with(['customer'])
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'data' => InvoiceResource::collection($invoices)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load invoices',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function create(): View
    {
        $data['title'] = 'Create Invoice';
        $data['menu'] = 'create-invoice';
        $data['customers'] = $this->invoiceService->getCustomersForInvoice();
        $data['categories'] = $this->invoiceService->getCategoriesForInvoice();
        return view('admin.Invoice.create', $data);
    }

    public function store(InvoiceRequest $request)
    {
        try {
            $data = $request->validated();
            $invoice = $this->invoiceService->createInvoice($data);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice created successfully',
                    'data' => $invoice,
                    'redirect' => route('invoice.show', $invoice)
                ], 201);
            }

            return redirect()->route('invoice.show', $invoice)
                ->with('success', 'Invoice created successfully');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create invoice',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Invoice $invoice)
    {
        // Check if PDF download is requested
        if (request()->has('download') && request()->get('download') === 'pdf') {
            return $this->downloadPdf($invoice);
        }

        $data['title'] = 'Invoice Details';
        $data['menu'] = 'invoice';
        $data['invoice'] = $invoice->load(['customer', 'items.product.category', 'items.product.unit']);

        return view('admin.Invoice.show', $data);
    }

    public function edit(Invoice $invoice): View
    {
        $data['title'] = 'Edit Invoice';
        $data['menu'] = 'invoice';
        $data['invoice'] = $invoice->load(['customer', 'items.product']);
        $data['customers'] = $this->invoiceService->getCustomersForInvoice();
        $data['categories'] = $this->invoiceService->getCategoriesForInvoice();

        return view('admin.Invoice.edit', $data);
    }

    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        try {
            $data = $request->validated();
            $updatedInvoice = $this->invoiceService->updateInvoice($invoice, $data);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice updated successfully',
                    'data' => $updatedInvoice,
                    'redirect' => route('invoice.show', $updatedInvoice)
                ], 200);
            }

            return redirect()->route('invoice.show', $updatedInvoice)
                ->with('success', 'Invoice updated successfully');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update invoice',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Invoice $invoice)
    {
        try {
            // Check if invoice can be deleted
            if ($invoice->status !== 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only cancelled invoices can be deleted.'
                ], 400);
            }

            $invoice->delete();

            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function returnInvoice(Invoice $invoice)
    {
        try {
            $returnedInvoice = $this->invoiceService->returnInvoice($invoice);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice returned successfully',
                    'data' => new InvoiceResource($returnedInvoice)
                ]);
            }

            return redirect()->route('invoice.show', $invoice)
                ->with('success', 'Invoice returned successfully');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function cancelInvoice(Invoice $invoice)
    {
        try {
            $cancelledInvoice = $this->invoiceService->cancelInvoice($invoice);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice cancelled successfully',
                    'data' => new InvoiceResource($cancelledInvoice)
                ]);
            }

            return redirect()->route('invoice.show', $invoice)
                ->with('success', 'Invoice cancelled successfully');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    private function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product.category', 'items.product.unit']);

        $pdf = \PDF::loadView('admin.Invoice.invoice-pdf', ['invoice' => $invoice]);

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function getProducts(Request $request): JsonResponse
    {
        try {
            $products = $this->invoiceService->getProductsForInvoice(
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
            $response = $this->invoiceService->calculateUnitTotal(
                $request->get('unit_price'),
                $request->get('quantity'),
                $request->get('vat_id')
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'vat_amount' => $response['vat_amount'],
                    'unit_total' => $response['unit_total'],
                    'vat_percentage' => $response['vat_percentage'],
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
