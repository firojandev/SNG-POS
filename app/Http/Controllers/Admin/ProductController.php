<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\ImportProductRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Tax;
use App\Models\Vat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductController extends Controller
{
    /**
     * Apply permission middleware
     */
    public function __construct()
    {
        $this->middleware('permission:manage_product');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $data['title'] = 'Products';
        $query = Product::with(['category', 'unit', 'tax', 'vat'])
            ->byStore(Auth::user()->store_id);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $data['products'] = $query->latest()->paginate(12);
        $data['categories'] = Category::all();

        return view('admin.products.index', $data)
            ->with('menu', 'products');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $data['title'] = 'Create Product';
        $data['categories'] = Category::all();
        $data['units'] = Unit::all();
        $data['taxes'] = Tax::all();
        $data['vats'] = Vat::all();

        return view('admin.products.create', $data)
            ->with('menu', 'products');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {

        $data = $request->all();
        $data['store_id'] = Auth::user()->store_id;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        Product::create($data);

        notyf()->success('Product created successfully!');
        return redirect()->route('admin.products.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'unit', 'tax', 'vat']);
        $data['product'] = $product;
        $data['title'] = $product->name;
        return view('admin.products.show', $data)
            ->with('menu', 'products');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        $data['categories'] = Category::all();
        $data['units'] = Unit::all();
        $data['taxes'] = Tax::all();
        $data['vats'] = Vat::all();
        $data['product'] = $product;
        $data['title'] = $product->name;

        return view('admin.products.edit', $data)
            ->with('menu', 'products');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);

        notyf()->success('Product updated successfully!');
        return redirect()->route('admin.products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            // Store product name for response
            $productName = $product->name;

            // Soft delete the product (image will be kept for potential restore)
            $product->delete();

            // Check if request expects JSON (AJAX request)
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Product '{$productName}' has been deleted successfully!"
                ]);
            }

            notyf()->success('Product deleted successfully!');
            return redirect()->route('admin.products.index');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete product: ' . $e->getMessage()
                ], 500);
            }

            notyf()->error('Failed to delete product!');
            return redirect()->route('admin.products.index');
        }
    }

    /**
     * Export products to CSV
     */
    public function export()
    {
        $products = Product::with(['category', 'unit', 'tax'])
            ->byStore(Auth::user()->store_id)
            ->get();

        $filename = 'products_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Name', 'SKU', 'Purchase Price', 'Sell Price',
                'Stock Quantity', 'Category', 'Unit', 'Tax', 'Description'
            ]);

            // CSV data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->sku,
                    $product->purchase_price,
                    $product->sell_price,
                    $product->stock_quantity,
                    $product->category?->name ?? '',
                    $product->unit?->name ?? '',
                    $product->tax?->name ?? '',
                    $product->description ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        $data['title'] = 'Import Products';
        return view('admin.products.import', $data)
            ->with('menu', 'products');
    }

    /**
     * Import products from CSV
     */
    public function import(ImportProductRequest $request)
    {

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        // Remove header row
        $header = array_shift($data);

        $imported = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $productData = [
                    'name' => $row[0] ?? '',
                    'sku' => $row[1] ?? '',
                    'purchase_price' => $row[2] ?? 0,
                    'sell_price' => $row[3] ?? 0,
                    'stock_quantity' => $row[4] ?? 0,
                    'description' => $row[9] ?? null,
                    'store_id' => Auth::user()->store_id,
                ];

                // Find category by name
                if (!empty($row[5])) {
                    $category = Category::where('name', $row[5])->first();
                    $productData['category_id'] = $category?->id;
                }

                // Find unit by name
                if (!empty($row[6])) {
                    $unit = Unit::where('name', $row[6])->first();
                    $productData['unit_id'] = $unit?->id;
                }

                // Find tax by name
                if (!empty($row[7])) {
                    $tax = Tax::where('name', $row[7])->first();
                    $productData['tax_id'] = $tax?->id;
                }

                // Find VAT by name
                if (!empty($row[8])) {
                    $vat = Vat::where('name', $row[8])->first();
                    $productData['vat_id'] = $vat?->id;
                }

                Product::create($productData);
                $imported++;

            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        $message = "Successfully imported {$imported} products.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        if (!empty($errors)) {
            notyf()->error($message);
        } else {
            notyf()->success($message);
        }
        return redirect()->route('admin.products.index');
    }

    /**
     * Download barcode PDF
     */
    public function downloadBarcode(Request $request)
    {
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'sku' => 'required|string',
            'name' => 'required|string',
            'quantity' => 'required|integer|min:1|max:100'
        ]);

        $sku = $request->sku;
        $name = $request->name;
        $quantity = (int) $request->quantity;

        try {
            // Generate barcode using Picqer library
            $generator = new BarcodeGeneratorPNG();
            $barcodeData = $generator->getBarcode($sku, $generator::TYPE_CODE_128, 3, 60);
            $barcodeBase64 = base64_encode($barcodeData);

            // Prepare data for PDF
            $data = [
                'sku' => $sku,
                'name' => $name,
                'quantity' => $quantity,
                'barcode' => $barcodeBase64,
                'generated_at' => now()->format('Y-m-d H:i:s')
            ];

            // Generate PDF
            $pdf = Pdf::loadView('admin.products.barcode-pdf', $data);
            $pdf->setPaper('A4', 'portrait');

            $filename = 'barcode_' . Str::slug($sku) . '_' . $quantity . '_' . date('Y-m-d_H-i-s') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Barcode generation error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate barcode PDF: ' . $e->getMessage()
                ], 500);
            }

            notyf()->error('Failed to generate barcode PDF!');
            return redirect()->back();
        }
    }
}
