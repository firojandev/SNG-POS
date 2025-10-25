<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\PaymentToSupplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class PurchaseService
{
    /**
     * Create a new purchase with items
     */
    public function createPurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            // Create the purchase
            $purchase = Purchase::create([
                'supplier_id' => $data['supplier_id'],
                'date' => $data['date'],
                'total_amount' => $data['total_amount'],
                'paid_amount' => $data['paid_amount'],
                'due_amount' => $data['due_amount'],
                'note' => $data['note'] ?? null,
            ]);

            // Create purchase items and update product stock
            foreach ($data['items'] as $item) {
                $this->createPurchaseItem($purchase, $item);
                $this->updateProductStock($item['product_id'], $item['quantity']);
            }

            // Update supplier balance with due amount
            $this->updateSupplierBalance($data['supplier_id'], $data['due_amount']);

            // Create payment record if there's a paid amount
            if ($data['paid_amount'] > 0) {
                $this->createPaymentRecord(
                    $data['supplier_id'],
                    $purchase->id,
                    $data['paid_amount'],
                    $purchase->created_at->format('Y-m-d')
                );
            }

            return $purchase->load(['supplier', 'items.product']);
        });
    }

    /**
     * Create a purchase item
     */
    private function createPurchaseItem(Purchase $purchase, array $item): PurchaseItem
    {
        // Calculate totals if not provided
        $unitPrice = $item['unit_price'];
        $quantity = $item['quantity'];
        $taxId = $item['tax_id'] ?? null;

        // Calculate unit total with tax if not already calculated
        if (!isset($item['tax_amount']) || !isset($item['unit_total'])) {
            $calculation = $this->calculateUnitTotal($unitPrice, $quantity, $taxId);
            $taxAmount = $calculation['tax_amount'];
            $unitTotal = $calculation['unit_total'];
        } else {
            $taxAmount = $item['tax_amount'];
            $unitTotal = $item['unit_total'];
        }

        return PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => $item['product_id'],
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'tax_amount' => $taxAmount,
            'unit_total' => $unitTotal,
        ]);
    }

    /**
     * Update product stock quantity
     */
    private function updateProductStock(int $productId, int $quantity): void
    {
        $product = Product::findOrFail($productId);
        $product->increment('stock_quantity', $quantity);
    }

    /**
     * Calculate unit total with tax
     */
    public function calculateUnitTotal(float $unitPrice, int $quantity, ?int $taxId = null): array
    {
        $subtotal = $unitPrice * $quantity;
        $taxAmount = 0;
        $taxPercentage = 0;

        if ($taxId) {
            $tax = Tax::find($taxId);
            if ($tax) {
                $taxPercentage = $tax->value;
                $taxAmount = ($subtotal * $taxPercentage) / 100;
            }
        }

        $unitTotal = $subtotal + $taxAmount;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'unit_total' => $unitTotal,
            'tax_percentage' => $taxPercentage
        ];
    }

    /**
     * Get products for purchase with search and category filter
     */
    public function getProductsForPurchase(?string $search = '', ?int $categoryId = null, int $page = 1, int $perPage = 12): array
    {
        $query = Product::with(['category', 'tax', 'unit'])
            ->where('is_active', true);

        // Ensure search is a string
        $search = $search ?? '';

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);

        // Add formatted attributes to each product
        $formattedProducts = collect($products->items())->map(function ($product) {
            $product->formatted_purchase_price = $product->formatted_purchase_price;
            $product->formatted_sell_price = $product->formatted_sell_price;
            return $product;
        });

        return [
            'data' => $formattedProducts,
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
            'has_more' => $products->hasMorePages()
        ];
    }

    /**
     * Get suppliers for purchase
     */
    public function getSuppliersForPurchase(): \Illuminate\Database\Eloquent\Collection
    {
        return Supplier::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get categories for product filter
     */
    public function getCategoriesForPurchase(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::orderBy('name')
            ->get();
    }

    /**
     * Update supplier balance by adding the due amount
     */
    private function updateSupplierBalance(int $supplierId, float $dueAmount): void
    {
        if ($dueAmount > 0) {
            $supplier = Supplier::findOrFail($supplierId);
            $supplier->increment('balance', $dueAmount);
        }
    }

    /**
     * Create a payment record to supplier
     */
    private function createPaymentRecord(int $supplierId, int $purchaseId, float $amount, string $paymentDate): PaymentToSupplier
    {
        return PaymentToSupplier::create([
            'supplier_id' => $supplierId,
            'purchase_id' => $purchaseId,
            'amount' => $amount,
            'payment_date' => $paymentDate,
            'note' => 'Payment from purchase',
        ]);
    }
}
