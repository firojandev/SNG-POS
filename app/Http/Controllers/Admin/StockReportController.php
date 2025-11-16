<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockReportController extends Controller
{
    protected $reportService;

    public function __construct(SalesReportService $reportService)
    {
        $this->reportService = $reportService;
        $this->middleware('permission:view_stock_reports');
    }

    /**
     * Display stock report
     */
    public function index(Request $request)
    {
        $menu = 'stock-report';
        $title = 'Stock Report';

        // Get filter parameters
        $categoryId = $request->input('category_id');
        $stockStatus = $request->input('stock_status'); // 'low', 'out', 'in', 'all'
        $searchTerm = $request->input('search');
        $perPage = $request->input('per_page', 50); // Default 50 items per page

        // Get all categories for filter dropdown
        $categories = Category::orderBy('name')->get();

        // Build query with store filter
        $query = Product::with('category', 'unit')
            ->select('products.*');

        // Apply category filter
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Apply search filter
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('sku', 'like', '%' . $searchTerm . '%');
            });
        }

        // Apply stock status filter
        if ($stockStatus) {
            switch ($stockStatus) {
                case 'out':
                    $query->where('stock_quantity', '<=', 0);
                    break;
                case 'low':
                    $query->where('stock_quantity', '>', 0)
                          ->where('stock_quantity', '<=', 10); // Low stock threshold
                    break;
                case 'in':
                    $query->where('stock_quantity', '>', 10); // In stock threshold
                    break;
            }
        }

        // Get products with pagination
        $products = $query->orderBy('name')->paginate($perPage)->appends($request->except('page'));

        // Calculate summary statistics
        $summary = [
            'total_products' => Product::count(),
            'total_stock_value' => Product::sum(DB::raw('stock_quantity * purchase_price')),
            'out_of_stock' => Product::where('stock_quantity', '<=', 0)->count(),
            'low_stock' => Product::where('stock_quantity', '>', 0)
                                  ->where('stock_quantity', '<=', 10)
                                  ->count(),
            'in_stock' => Product::where('stock_quantity', '>', 10)->count(),
            'total_stock_quantity' => Product::sum('stock_quantity'),
        ];

        return view('admin.Reports.stock-report', compact(
            'menu',
            'title',
            'products',
            'categories',
            'summary',
            'categoryId',
            'stockStatus',
            'searchTerm'
        ));
    }

    /**
     * Export stock report to CSV
     */
    public function exportCsv(Request $request)
    {
        // Get filter parameters
        $categoryId = $request->input('category_id');
        $stockStatus = $request->input('stock_status');
        $searchTerm = $request->input('search');

        // Build query
        $query = Product::with('category');

        // Apply category filter
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Apply search filter
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('sku', 'like', '%' . $searchTerm . '%');
            });
        }

        // Apply stock status filter
        if ($stockStatus) {
            switch ($stockStatus) {
                case 'out':
                    $query->where('stock_quantity', '<=', 0);
                    break;
                case 'low':
                    $query->where('stock_quantity', '>', 0)
                          ->where('stock_quantity', '<=', 10); // Low stock threshold
                    break;
                case 'in':
                    $query->where('stock_quantity', '>', 10); // In stock threshold
                    break;
            }
        }

        $products = $query->orderBy('name')->get();

        $filename = 'stock_report_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Product Name',
                'SKU',
                'Category',
                'Stock Quantity',
                'Stock Status',
                'Purchase Price',
                'Sell Price',
                'Stock Value',
                'Unit'
            ]);

            // Data rows
            foreach ($products as $product) {
                $stockValue = $product->stock_quantity * $product->purchase_price;

                // Determine stock status (Low stock threshold: 10)
                if ($product->stock_quantity <= 0) {
                    $stockStatus = 'Out of Stock';
                } elseif ($product->stock_quantity <= 10) {
                    $stockStatus = 'Low Stock';
                } else {
                    $stockStatus = 'In Stock';
                }

                fputcsv($file, [
                    $product->name ?? 'N/A',
                    $product->sku ?? 'N/A',
                    $product->category->name ?? 'Uncategorized',
                    number_format($product->stock_quantity ?? 0, 0),
                    $stockStatus,
                    number_format($product->purchase_price ?? 0, 2),
                    number_format($product->sell_price ?? 0, 2),
                    number_format($stockValue, 2),
                    $product->unit->name ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
