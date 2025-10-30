<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Vat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

class InvoiceService
{
    /**
     * Create a new invoice with items
     */
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            // Create the invoice
            $invoice = Invoice::create([
                'customer_id' => $data['customer_id'],
                'date' => $data['date'],
                'unit_total' => $data['unit_total'],
                'total_vat' => $data['total_vat'] ?? 0,
                'total_amount' => $data['total_amount'],
                'discount_type' => $data['discount_type'] ?? 'flat',
                'discount_value' => $data['discount_value'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'payable_amount' => $data['payable_amount'],
                'paid_amount' => $data['paid_amount'],
                'due_amount' => $data['due_amount'],
                'note' => $data['note'] ?? null,
                'status' => 'active'
            ]);

            // Create invoice items and decrease product stock
            foreach ($data['items'] as $item) {
                $this->createInvoiceItem($invoice, $item);
                $this->decreaseProductStock($item['product_id'], $item['quantity']);
            }

            // Update customer balance with due amount if exists
            if (isset($data['due_amount']) && $data['due_amount'] > 0) {
                $this->updateCustomerBalance($data['customer_id'], $data['due_amount']);
            }

            return $invoice->load(['customer', 'items.product']);
        });
    }

    /**
     * Create an invoice item
     */
    private function createInvoiceItem(Invoice $invoice, array $item): InvoiceItem
    {
        $unitPrice = $item['unit_price'];
        $quantity = $item['quantity'];
        $vatId = $item['vat_id'] ?? null;

        // Item discount fields
        $itemDiscountType = $item['item_discount_type'] ?? null;
        $itemDiscountValue = $item['item_discount_value'] ?? 0;
        $itemDiscountAmount = $item['item_discount_amount'] ?? 0;

        // VAT amount and unit total should be provided by frontend
        $vatAmount = $item['vat_amount'] ?? 0;
        $unitTotal = $item['unit_total'] ?? ($unitPrice * $quantity);

        return InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $item['product_id'],
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'vat_amount' => $vatAmount,
            'unit_total' => $unitTotal,
            'item_discount_type' => $itemDiscountType,
            'item_discount_value' => $itemDiscountValue,
            'item_discount_amount' => $itemDiscountAmount,
        ]);
    }

    /**
     * Decrease product stock quantity
     */
    private function decreaseProductStock(int $productId, int $quantity): void
    {
        $product = Product::findOrFail($productId);

        // Check if sufficient stock is available
        if ($product->stock_quantity < $quantity) {
            throw new \Exception("Insufficient stock for product: {$product->name}. Available: {$product->stock_quantity}, Requested: {$quantity}");
        }

        $product->decrement('stock_quantity', $quantity);
    }

    /**
     * Calculate unit total and vat amount separately
     * Unit Total = Price × Quantity (WITHOUT VAT)
     * VAT Amount = (Price × Quantity) × VAT%
     */
    public function calculateUnitTotal(float $unitPrice, int $quantity, ?int $vatId = null): array
    {
        // Unit total is just price × quantity WITHOUT VAT
        $unitTotal = $unitPrice * $quantity;
        $vatAmount = 0;
        $vatPercentage = 0;

        // Calculate VAT separately
        if ($vatId) {
            $vat = Vat::find($vatId);
            if ($vat) {
                $vatPercentage = $vat->value;
                $vatAmount = ($unitTotal * $vatPercentage) / 100;
            }
        }

        return [
            'unit_total' => $unitTotal,  // WITHOUT VAT
            'vat_amount' => $vatAmount,  // Separate VAT amount
            'vat_percentage' => $vatPercentage
        ];
    }

    /**
     * Get products for invoice with search and category filter
     */
    public function getProductsForInvoice(?string $search = '', ?int $categoryId = null, int $page = 1, int $perPage = 12): array
    {
        $query = Product::with(['category', 'vat', 'unit'])
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
     * Get customers for invoice
     */
    public function getCustomersForInvoice(): \Illuminate\Database\Eloquent\Collection
    {
        return Customer::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get categories for product filter
     */
    public function getCategoriesForInvoice(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::orderBy('name')
            ->get();
    }

    /**
     * Update customer balance by adding the due amount
     */
    private function updateCustomerBalance(int $customerId, float $dueAmount): void
    {
        if ($dueAmount > 0) {
            $customer = Customer::findOrFail($customerId);
            // Assuming customer has a balance field
            if (Schema::hasColumn('customers', 'balance')) {
                $customer->increment('balance', $dueAmount);
            }
        }
    }

    /**
     * Return an invoice and restore stock
     */
    public function returnInvoice(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            // Check if invoice is already returned or cancelled
            if ($invoice->status === 'returned') {
                throw new \Exception('Invoice has already been returned.');
            }

            if ($invoice->status === 'cancelled') {
                throw new \Exception('Cannot return a cancelled invoice.');
            }

            // Restore stock for each item
            foreach ($invoice->items as $item) {
                $this->increaseProductStock($item->product_id, $item->quantity);
            }

            // Update invoice status
            $invoice->update(['status' => 'returned']);

            // Update customer balance (decrease by due amount)
            if ($invoice->due_amount > 0) {
                $this->decreaseCustomerBalance($invoice->customer_id, $invoice->due_amount);
            }

            return $invoice->fresh();
        });
    }

    /**
     * Cancel an invoice
     */
    public function cancelInvoice(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            // Check if invoice is already cancelled or returned
            if ($invoice->status === 'cancelled') {
                throw new \Exception('Invoice has already been cancelled.');
            }

            if ($invoice->status === 'returned') {
                throw new \Exception('Cannot cancel a returned invoice.');
            }

            // Restore stock for each item
            foreach ($invoice->items as $item) {
                $this->increaseProductStock($item->product_id, $item->quantity);
            }

            // Update invoice status
            $invoice->update(['status' => 'cancelled']);

            // Update customer balance (decrease by due amount)
            if ($invoice->due_amount > 0) {
                $this->decreaseCustomerBalance($invoice->customer_id, $invoice->due_amount);
            }

            return $invoice->fresh();
        });
    }

    /**
     * Increase product stock quantity (for returns/cancellations)
     */
    private function increaseProductStock(int $productId, int $quantity): void
    {
        $product = Product::findOrFail($productId);
        $product->increment('stock_quantity', $quantity);
    }

    /**
     * Decrease customer balance
     */
    private function decreaseCustomerBalance(int $customerId, float $amount): void
    {
        if ($amount > 0) {
            $customer = Customer::findOrFail($customerId);
            // Assuming customer has a balance field
            if (Schema::hasColumn('customers', 'balance')) {
                $customer->decrement('balance', $amount);
            }
        }
    }
}
