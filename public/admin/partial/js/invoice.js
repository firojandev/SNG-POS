"use strict";

/**
 * Invoice Create/Edit Page JavaScript
 * Handles all invoice-related functionality
 *
 * @package    SNG-POS
 * @subpackage Invoice Module
 * @author     Your Name
 * @version    1.0.0
 * @since      1.0.0
 *
 * Features:
 * - Product search and selection with pagination
 * - Dynamic cart management with real-time updates
 * - Automatic vat calculations
 * - Customer management with inline creation
 * - Form validation and submission
 * - Optimized rendering for better performance
 */

class InvoiceManager {
    constructor() {
        this.cart = [];
        this.selectedCustomer = null;
        this.currentCategory = null;
        this.searchTerm = '';
        this.currentPage = 1;
        this.loading = false;
        this.hasMoreProducts = true;

        // Store routes and URLs
        this.routes = {
            products: window.invoiceRoutes.products,
            calculateUnitTotal: window.invoiceRoutes.calculateUnitTotal,
            store: window.invoiceRoutes.store,
            customersStore: window.invoiceRoutes.customersStore,
            customersGetData: window.invoiceRoutes.customersGetData
        };
        this.defaultImage = window.invoiceConfig.defaultImage;
        this.currency = window.invoiceConfig.currency;

        this.init();
    }

    init() {
        console.log('InvoiceManager: Initializing...');
        this.initializeDatePicker();
        this.bindEvents();

        // Check if in edit mode and load existing invoice data
        if (window.invoiceConfig.isEditMode && window.invoiceConfig.invoiceData) {
            console.log('InvoiceManager: Loading existing invoice data...');
            this.loadExistingInvoice();
        }

        // Load products immediately when page loads
        console.log('InvoiceManager: Loading initial products...');
        this.loadProducts(true);
        this.updateCartDisplay();
    }

    initializeDatePicker() {
        // Initialize jQuery UI datepicker
        if (typeof $.fn.datepicker !== 'undefined') {
            const phpFmt = (window.invoiceConfig && window.invoiceConfig.dateFormatPhp) ? window.invoiceConfig.dateFormatPhp : 'Y-m-d';
            const jqFmt = this.phpDateFormatToJqueryUI(phpFmt);
            $('#invoiceDate').datepicker({
                dateFormat: jqFmt
            });
            // Set today's date as default
            $('#invoiceDate').datepicker('setDate', new Date());
        }
    }

    // Map PHP date format to jQuery UI datepicker format
    phpDateFormatToJqueryUI(phpFormat) {
        // Basic mapping for common tokens used in settings
        const map = {
            'Y': 'yy',
            'y': 'y',
            'm': 'mm',
            'n': 'm',
            'd': 'dd',
            'j': 'd',
            '/': '/',
            '-': '-',
            ' ': ' '
        };
        let result = '';
        for (let i = 0; i < phpFormat.length; i++) {
            const ch = phpFormat[i];
            result += (map[ch] !== undefined) ? map[ch] : ch;
        }
        return result;
    }

    loadExistingInvoice() {
        const invoiceData = window.invoiceConfig.invoiceData;

        // Set customer
        $('#customerSelect').val(invoiceData.customer_id).trigger('change');
        this.selectedCustomer = invoiceData.customer_id;

        // Set note
        if (invoiceData.note) {
            $('#note').val(invoiceData.note);
        }

        // Set discount
        if (invoiceData.discount_type) {
            $('#discountType').val(invoiceData.discount_type);
            $('#discountValue').val(invoiceData.discount_value);
        }

        // Set paid amount
        if (invoiceData.paid_amount) {
            $('#paidAmount').val(invoiceData.paid_amount);
        }

        // Load items into cart
        if (invoiceData.items && invoiceData.items.length > 0) {
            this.cart = [];
            invoiceData.items.forEach(item => {
                this.cart.push({
                    product_id: item.product.id,
                    product_name: item.product.name,
                    sku: item.product.sku,
                    unit_price: parseFloat(item.unit_price),
                    formatted_unit_price: this.formatCurrency(item.unit_price),
                    quantity: item.quantity,
                    vat_id: item.product.vat_id,
                    vat_percentage: item.product.vat ? item.product.vat.value : 0,
                    item_discount_type: item.item_discount_type || null,
                    item_discount_value: parseFloat(item.item_discount_value || 0),
                    item_discount_amount: parseFloat(item.item_discount_amount || 0)
                });
            });
        }
    }

    bindEvents() {
        // Product search
        $('#productSearch').on('input', this.debounce(() => {
            this.searchTerm = $('#productSearch').val();
            this.resetPagination();
            this.loadProducts(true);
        }, 300));

        // Category filter
        $('#categoryFilter').on('change', (e) => {
            this.currentCategory = e.target.value || null;
            this.resetPagination();
            this.loadProducts(true);
        });

        // Load more products
        $(document).on('click', '#loadMoreProducts', () => {
            this.loadMoreProducts();
        });

        // Quantity controls
        $(document).on('click', '.qty-increment', (e) => {
            e.preventDefault();
            this.updateQuantity($(e.target).closest('tr'), 1);
        });

        $(document).on('click', '.qty-decrement', (e) => {
            e.preventDefault();
            this.updateQuantity($(e.target).closest('tr'), -1);
        });

        $(document).on('input', '.qty-count', (e) => {
            const row = $(e.target).closest('tr');
            const quantity = parseInt($(e.target).val()) || 1;
            this.setQuantity(row, quantity);
        });

        // Remove item
        $(document).on('click', '.remove-item', (e) => {
            e.preventDefault();
            const productId = $(e.target).closest('tr').data('product-id');
            this.removeFromCart(productId);
        });

        // Paid amount change
        $('#paidAmount').on('input', () => {
            this.calculateDueAmount();
        });

        // Invoice-level discount changes
        $('#discountType, #discountValue').on('change input', () => {
            this.calculateTotalsWithDiscount();
        });

        // Item-level discount changes
        $(document).on('change', '.item-discount-type', (e) => {
            const row = $(e.target).closest('tr');
            const productId = row.data('product-id');
            this.updateItemDiscount(productId);
        });

        $(document).on('input', '.item-discount-value', (e) => {
            const row = $(e.target).closest('tr');
            const productId = row.data('product-id');
            this.updateItemDiscount(productId);
        });

        // Form submission
        $('#invoiceForm').on('submit', (e) => {
            e.preventDefault();
            this.submitInvoice();
        });

        // Customer modal events
        $('#customerModal').on('hidden.bs.modal', () => {
            this.resetCustomerForm();
        });

        $('#customerForm').on('submit', (e) => {
            e.preventDefault();
            this.saveCustomer();
        });
    }

    resetPagination() {
        this.currentPage = 1;
        this.hasMoreProducts = true;
        $('#productsContainer').empty();
        $('#loadMoreBtn').remove();
    }

    async loadProducts(reset = false) {
        if (this.loading) return;

        console.log(`InvoiceManager: Loading products (reset: ${reset}, page: ${this.currentPage})`);
        this.loading = true;

        if (reset) {
            this.currentPage = 1;
            this.hasMoreProducts = true;
            $('#productsContainer').empty();
            $('#loadMoreBtn').remove();

            // Show loading indicator for initial load
            $('#productsContainer').html(`
                <div class="col-12 text-center py-4" id="loadingIndicator">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading products...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading products...</p>
                </div>
            `);
        }

        try {
            const url = `${this.routes.products}?search=${this.searchTerm}&category_id=${this.currentCategory || ''}&page=${this.currentPage}`;
            console.log(`InvoiceManager: Fetching from URL: ${url}`);

            const response = await fetch(url);

            // Check if response is ok
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('InvoiceManager: Non-JSON response received:', text.substring(0, 200));
                throw new Error('Server returned non-JSON response. Please check the server logs.');
            }

            const result = await response.json();



            console.log('InvoiceManager: API Response:', result);

            if (result.success) {
                console.log(`InvoiceManager: Loaded ${result.data.length} products`);
                console.log('InvoiceManager: First product sample:', result.data[0]);
                this.renderProducts(result.data);
                this.updateLoadMoreButton(result.has_more);
                this.currentPage++;
            } else {
                console.error('Failed to load products:', result.message);
                this.showError('Failed to load products. Please try again.');
            }
        } catch (error) {
            console.error('Error loading products:', error);
            this.showError(`Error: ${error.message}`);
        } finally {
            this.loading = false;
        }
    }

    renderProducts(products) {
        const container = $('#productsContainer');

        if (products.length === 0 && this.currentPage === 1) {
            // Show empty state for first page
            container.html(`
                <div class="col-12 text-center py-4">
                    <div class="alert alert-info" role="alert">
                        <i class="fa fa-info-circle"></i>
                        No products found. Try adjusting your search or category filter.
                    </div>
                </div>
            `);
            return;
        }

        // Clear loading indicator if it exists
        $('#loadingIndicator').remove();

        products.forEach(product => {
            const productCard = this.createProductCard(product);
            container.append(productCard);
        });
    }

    updateLoadMoreButton(hasMore) {
        this.hasMoreProducts = hasMore;

        // Remove existing load more button
        $('#loadMoreBtn').remove();

        if (hasMore) {
            const loadMoreBtn = $(`
                <div class="text-center py-3" id="loadMoreBtn">
                    <button class="btn btn-sm btn-brand-secondary" id="loadMoreProducts">
                        <span class="spinner-border spinner-border-sm d-none" id="loadMoreSpinner" role="status" aria-hidden="true"></span>
                        <span id="loadMoreText">Load More Products</span>
                    </button>
                </div>
            `);
            $('#productsContainer').after(loadMoreBtn);
        }
    }

    showError(message) {
        $('#productsContainer').html(`
            <div class="col-12 text-center py-4">
                <div class="alert alert-warning" role="alert">
                    <i class="fa fa-exclamation-triangle"></i>
                    ${message}
                </div>
                <button class="btn btn-sm btn-primary" onclick="invoiceManager.loadProducts(true)">
                    <i class="fa fa-refresh"></i> Try Again
                </button>
            </div>
        `);
    }

    async loadMoreProducts() {
        if (this.loading || !this.hasMoreProducts) return;

        // Show loading state on button
        $('#loadMoreSpinner').removeClass('d-none');
        $('#loadMoreText').text('Loading...');
        $('#loadMoreProducts').prop('disabled', true);

        try {
            await this.loadProducts(false);
        } finally {
            // Reset button state
            $('#loadMoreSpinner').addClass('d-none');
            $('#loadMoreText').text('Load More Products');
            $('#loadMoreProducts').prop('disabled', false);
        }
    }

    createProductCard(product) {
        const isSelected = this.cart.some(item => item.product_id === product.id);
        const selectedClass = isSelected ? 'selected' : '';

        // Use formatted price from API or format it ourselves
        const formattedPrice = product.formatted_sell_price || this.formatCurrency(product.sell_price);

        // Use actual product image or fallback
        const productImage = product.image ? `/storage/${product.image}` : this.defaultImage;

        return $(`
            <div class="col-12">
                <div class="wiz-card pos-product-item ${selectedClass}" data-product-id="${product.id}">
                    <div class="me-1">
                        <div class="pos-product-fig">
                            <img src="${productImage}" alt="${product.name}" onerror="this.src='${this.defaultImage}'">
                        </div>
                    </div>
                    <div class="pos-product-content">
                        <table class="pos-product-table">
                            <tbody>
                                <tr>
                                    <td colspan="3">${product.name}</td>
                                </tr>
                                <tr>
                                    <td><strong>Price</strong></td>
                                    <td>:</td>
                                    <td>${formattedPrice}</td>
                                </tr>
                                <tr>
                                    <td><strong>SKU</strong></td>
                                    <td>:</td>
                                    <td>${product.sku}</td>
                                </tr>
                                ${product.vat ? `<tr><td><strong>Vat</strong></td><td>:</td><td>${product.vat.value}%</td></tr>` : ''}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `).on('click', () => {
            this.addToCart(product);
        });
    }

    addToCart(product) {
        const existingItem = this.cart.find(item => item.product_id === product.id);

        if (existingItem) {
            existingItem.quantity += 1;
            // Visual feedback for quantity update
            this.showToast(`${product.name} quantity updated to ${existingItem.quantity}`, 'info');
            // Only update this specific row
            this.updateCartDisplay(product.id);
        } else {
            this.cart.push({
                product_id: product.id,
                product_name: product.name,
                sku: product.sku,
                unit_price: parseFloat(product.sell_price),
                formatted_unit_price: product.formatted_sell_price,
                quantity: 1,
                vat_id: product.vat_id,
                vat_percentage: product.vat ? product.vat.value : 0,
                item_discount_type: null,
                item_discount_value: 0,
                item_discount_amount: 0
            });
            // Visual feedback for new item
            this.showToast(`${product.name} added to cart`, 'success');
            // Full rebuild needed for new item
            this.updateCartDisplay();
        }

        this.updateProductSelection();
    }

    showToast(message, type = 'info') {
        // Simple toast notification
        const toastHtml = `
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                <div class="toast show align-items-center text-white bg-${type === 'success' ? 'success' : 'info'} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        `;

        const $toast = $(toastHtml);
        $('body').append($toast);

        // Auto remove after 2 seconds
        setTimeout(() => {
            $toast.fadeOut('slow', function() {
                $(this).remove();
            });
        }, 2000);
    }

    removeFromCart(productId) {
        this.cart = this.cart.filter(item => item.product_id !== productId);
        this.updateCartDisplay();
        this.updateProductSelection();
    }

    updateQuantity(row, change) {
        const productId = row.data('product-id');
        const item = this.cart.find(item => item.product_id === productId);

        if (item) {
            item.quantity = Math.max(1, item.quantity + change);
            // Only update this specific row
            this.updateCartDisplay(productId);
        }
    }

    setQuantity(row, quantity) {
        const productId = row.data('product-id');
        const item = this.cart.find(item => item.product_id === productId);

        if (item) {
            item.quantity = Math.max(1, quantity);
            // Only update this specific row
            this.updateCartDisplay(productId);
        }
    }

    async updateCartDisplay(updatedProductId = null) {
        const tbody = $('#cartTableBody');

        if (this.cart.length === 0) {
            // Show empty cart message
            tbody.html(`
                <tr id="emptyCartMessage">
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="fa fa-shopping-cart fa-2x mb-2"></i>
                        <p class="mb-0">Cart is empty. Select products from the right panel to add them.</p>
                    </td>
                </tr>
            `);
            $('#unitTotalAmount').text(this.formatCurrency(0));
            $('#totalVatAmount').text(this.formatCurrency(0));
            $('#totalAmount').text(this.formatCurrency(0));
            $('#payableAmount').text(this.formatCurrency(0));
            $('#dueAmount').text(this.formatCurrency(0));
            $('#paidAmount').val(0);
            return;
        }

        // Remove empty message if exists
        $('#emptyCartMessage').remove();

        let subtotal = 0;
        let totalVat = 0;

        // Calculate totals for all items
        const itemCalculations = {};
        for (const item of this.cart) {
            const calculation = await this.calculateItemTotal(item);
            itemCalculations[item.product_id] = calculation;
            subtotal += calculation.unit_total;
            totalVat += calculation.vat_amount;
        }

        // If we're updating a specific product, only update that row
        if (updatedProductId !== null) {
            const item = this.cart.find(i => i.product_id === updatedProductId);
            if (item) {
                const calculation = itemCalculations[updatedProductId];
                const existingRow = $(`tr[data-product-id="${item.product_id}"]`);

                if (existingRow.length) {
                    // Update existing row - column indexes: Name=0, U/P=1, QTY=2, Disc=3, V/U=4, U/T=5, Remove=6
                    existingRow.find('td:eq(2) .qty-count').val(item.quantity);
                    existingRow.find('td:eq(4)').text(this.formatCurrency(calculation.vat_amount));
                    existingRow.find('td:eq(5)').text(this.formatCurrency(calculation.unit_total));
                } else {
                    // Row doesn't exist, add it
                    tbody.append(this.createCartRow(item, calculation));
                }
            }
        } else {
            // Full rebuild of cart table
            tbody.empty();

            for (const item of this.cart) {
                const calculation = itemCalculations[item.product_id];
                tbody.append(this.createCartRow(item, calculation));
            }
        }

        // Update totals with discount
        $('#unitTotalAmount').text(this.formatCurrency(subtotal));
        $('#totalVatAmount').text(this.formatCurrency(totalVat));
        this.calculateTotalsWithDiscount();
    }

    calculateTotalsWithDiscount() {
        const unitTotal = this.parseCurrency($('#unitTotalAmount').text());
        const totalVat = this.parseCurrency($('#totalVatAmount').text());

        // Calculate Total Amount = Unit Total + Total VAT
        const totalAmount = unitTotal + totalVat;
        $('#totalAmount').text(this.formatCurrency(totalAmount));

        // Get invoice-level discount type and value
        const discountType = $('#discountType').val();
        const discountValue = parseFloat($('#discountValue').val()) || 0;

        let discountAmount = 0;

        if (discountValue > 0) {
            if (discountType === 'percentage') {
                // Calculate percentage discount
                discountAmount = (totalAmount * discountValue) / 100;

                // Prevent percentage from exceeding 100
                if (discountValue > 100) {
                    $('#discountValue').val(100);
                    discountAmount = totalAmount;
                }
            } else {
                // Flat discount
                discountAmount = discountValue;

                // Prevent flat discount from exceeding total amount
                if (discountAmount > totalAmount) {
                    discountAmount = totalAmount;
                    $('#discountValue').val(totalAmount.toFixed(2));
                }
            }
        }

        // Display calculated discount amount
        $('#discountAmountDisplay').text(`-${this.formatCurrency(discountAmount)}`);

        // Calculate Payable Amount = Total Amount - Discount
        const payableAmount = Math.max(0, totalAmount - discountAmount);
        $('#payableAmount').text(this.formatCurrency(payableAmount));

        this.calculateDueAmount();
    }

    createCartRow(item, calculation) {
        return $(`
            <tr data-product-id="${item.product_id}">
                <td>${item.product_name}</td>
                <td>${item.formatted_unit_price || this.formatCurrency(item.unit_price)}</td>
                <td>
                    <div class="qty-counter-group qty-group-slim">
                        <a href="#" class="btn btn-sm no-focus qty-decrement">-</a>
                        <input type="number" min="1" value="${item.quantity}" class="form-control form-control-sm no-focus qty-count">
                        <a href="#" class="btn btn-sm no-focus qty-increment">+</a>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column gap-1" style="min-width: 100px;">
                        <select class="form-control form-control-sm item-discount-type">
                            <option value="">None</option>
                            <option value="flat" ${item.item_discount_type === 'flat' ? 'selected' : ''}>Flat</option>
                            <option value="percentage" ${item.item_discount_type === 'percentage' ? 'selected' : ''}>%</option>
                        </select>
                        <input type="number" class="form-control form-control-sm item-discount-value" placeholder="0" value="${item.item_discount_value || 0}" step="0.01" min="0" ${!item.item_discount_type ? 'disabled' : ''}>
                        <small class="text-muted item-discount-display">${item.item_discount_amount > 0 ? '-' + this.formatCurrency(item.item_discount_amount) : '-' + this.currency + '0.00'}</small>
                    </div>
                </td>
                <td class="vat-amount">${this.formatCurrency(calculation.vat_amount)}</td>
                <td class="unit-total">${this.formatCurrency(calculation.unit_total)}</td>
                <td><a href="#" class="btn btn-sm btn-danger text-12 py-0 px-1 remove-item"><i class="fa fa-minus"></i></a></td>
            </tr>
        `);
    }

    async calculateItemTotal(item) {
        try {
            // Calculate base subtotal (price × quantity) - BEFORE discount
            const baseSubtotal = item.unit_price * item.quantity;

            // Call backend API to get VAT calculation on ORIGINAL price (Option B)
            const response = await fetch(this.routes.calculateUnitTotal, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({
                    unit_price: item.unit_price,
                    quantity: item.quantity,
                    vat_id: item.vat_id
                })
            });

            const result = await response.json();

            if (result.success) {
                // VAT is calculated on original price (from backend)
                const vatAmount = result.data.vat_amount;

                // Apply item-level discount to get final unit_total
                const discountAmount = item.item_discount_amount || 0;
                const unitTotalAfterDiscount = baseSubtotal - discountAmount;

                return {
                    unit_total: unitTotalAfterDiscount,  // After item discount
                    vat_amount: vatAmount,               // VAT on original price (Option B)
                    vat_percentage: result.data.vat_percentage
                };
            }

            // Fallback calculation
            return { unit_total: baseSubtotal - (item.item_discount_amount || 0), vat_amount: 0 };
        } catch (error) {
            console.error('Error calculating total:', error);
            const baseSubtotal = item.unit_price * item.quantity;
            return { unit_total: baseSubtotal - (item.item_discount_amount || 0), vat_amount: 0 };
        }
    }

    calculateDueAmount() {
        const payableAmount = this.parseCurrency($('#payableAmount').text());
        let paidAmount = parseFloat($('#paidAmount').val()) || 0;

        // Prevent paid amount from exceeding payable amount
        if (paidAmount > payableAmount) {
            paidAmount = payableAmount;
            $('#paidAmount').val(paidAmount.toFixed(2));
            this.showToast('Paid amount cannot exceed payable amount', 'info');
        }

        const dueAmount = Math.max(0, payableAmount - paidAmount);
        $('#dueAmount').text(this.formatCurrency(dueAmount));
    }

    updateItemDiscount(productId) {
        const item = this.cart.find(i => i.product_id === productId);
        if (!item) return;

        const row = $(`tr[data-product-id="${productId}"]`);
        const discountType = row.find('.item-discount-type').val();
        const discountValue = parseFloat(row.find('.item-discount-value').val()) || 0;

        // Enable/disable discount value input based on type
        row.find('.item-discount-value').prop('disabled', !discountType);

        // Update item discount fields
        item.item_discount_type = discountType || null;
        item.item_discount_value = discountType ? discountValue : 0;

        // Calculate discount amount based on type
        const subtotal = item.unit_price * item.quantity;

        if (discountType === 'percentage') {
            // Percentage discount
            let percentage = discountValue;
            if (percentage > 100) {
                percentage = 100;
                row.find('.item-discount-value').val(100);
            }
            item.item_discount_amount = (subtotal * percentage) / 100;
        } else if (discountType === 'flat') {
            // Flat discount
            let flatAmount = discountValue;
            if (flatAmount > subtotal) {
                flatAmount = subtotal;
                row.find('.item-discount-value').val(flatAmount.toFixed(2));
            }
            item.item_discount_amount = flatAmount;
        } else {
            item.item_discount_amount = 0;
        }

        // Update discount display
        row.find('.item-discount-display').text(
            item.item_discount_amount > 0
                ? `-${this.formatCurrency(item.item_discount_amount)}`
                : `-${this.currency}0.00`
        );

        // Recalculate totals
        this.updateCartDisplay(productId);
    }

    updateProductSelection() {
        $('.pos-product-item').each(function() {
            const productId = $(this).data('product-id');
            const isInCart = invoiceManager.cart.some(item => item.product_id === productId);
            $(this).toggleClass('selected', isInCart);
        });
    }

    async submitInvoice() {
        if (this.cart.length === 0) {
            this.showAlert('Please add at least one item to the invoice.', 'warning');
            return;
        }

        if (!$('#customerSelect').val()) {
            this.showAlert('Please select a customer.', 'warning');
            return;
        }

        if (!$('#invoiceDate').val()) {
            this.showAlert('Please select a invoice date.', 'warning');
            return;
        }

        // Disable submit button and show loading
        const submitBtn = $('#invoiceForm button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        // Calculate item totals with vat and discounts
        const items = await Promise.all(this.cart.map(async (item) => {
            const calculation = await this.calculateItemTotal(item);
            return {
                product_id: item.product_id,
                unit_price: item.unit_price,
                quantity: item.quantity,
                vat_id: item.vat_id || null,
                vat_amount: calculation.vat_amount,
                unit_total: calculation.unit_total,
                item_discount_type: item.item_discount_type || null,
                item_discount_value: item.item_discount_value || 0,
                item_discount_amount: item.item_discount_amount || 0
            };
        }));

        // Get invoice-level discount data
        const discountType = $('#discountType').val();
        const discountValue = parseFloat($('#discountValue').val()) || 0;

        // Calculate discount amount based on type
        const totalAmount = this.parseCurrency($('#totalAmount').text());
        let discountAmount = 0;

        if (discountValue > 0 && discountType) {
            if (discountType === 'percentage') {
                discountAmount = (totalAmount * Math.min(discountValue, 100)) / 100;
            } else {
                discountAmount = Math.min(discountValue, totalAmount);
            }
        }

        const formData = {
            customer_id: $('#customerSelect').val(),
            date: $('#invoiceDate').val(),
            items: items,
            unit_total: this.parseCurrency($('#unitTotalAmount').text()),
            total_vat: this.parseCurrency($('#totalVatAmount').text()),
            total_amount: this.parseCurrency($('#totalAmount').text()),
            discount_type: discountType,
            discount_value: discountValue,
            discount_amount: discountAmount,
            payable_amount: this.parseCurrency($('#payableAmount').text()),
            paid_amount: parseFloat($('#paidAmount').val()) || 0,
            due_amount: this.parseCurrency($('#dueAmount').text()),
            note: $('#note').val()
        };

        try {
            // Use PUT method for edit mode, POST for create mode
            const isEditMode = window.invoiceConfig.isEditMode || false;
            const method = isEditMode ? 'PUT' : 'POST';

            const response = await fetch(this.routes.store, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert(result.message, 'success');
                // Redirect to invoice show page
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 500);
            } else {
                submitBtn.prop('disabled', false).html(originalText);
                this.showAlert(result.message || (isEditMode ? 'Failed to update invoice' : 'Failed to create invoice'), 'danger');

                // Display validation errors if present
                if (result.errors) {
                    this.displayValidationErrors(result.errors);
                }
            }
        } catch (error) {
            console.error('Error submitting invoice:', error);
            submitBtn.prop('disabled', false).html(originalText);
            const isEditMode = window.invoiceConfig.isEditMode || false;
            this.showAlert('Error: Failed to ' + (isEditMode ? 'update' : 'create') + ' invoice. Please try again.', 'danger');
        }
    }

    displayValidationErrors(errors) {
        let errorMessage = '<ul class="mb-0">';
        Object.values(errors).forEach(errorArray => {
            errorArray.forEach(error => {
                errorMessage += `<li>${error}</li>`;
            });
        });
        errorMessage += '</ul>';

        this.showAlert(errorMessage, 'danger');
    }

    showAlert(message, type = 'info') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        // Remove existing alerts
        $('.main-content .alert').remove();

        // Add new alert at the top
        $('.main-content').prepend(alertHtml);

        // Auto-dismiss after 5 seconds for success messages
        if (type === 'success') {
            setTimeout(() => {
                $('.alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);
        }

        // Scroll to top to show alert
        $('html, body').animate({ scrollTop: 0 }, 'fast');
    }

    async saveCustomer() {
        const formData = new FormData($('#customerForm')[0]);

        try {
            const response = await fetch(this.routes.customersStore, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: formData
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    $('#customerModal').modal('hide');
                    this.loadCustomers();
                    $('#customerSelect').val(result.data.id).trigger('change');
                    alert('Customer added successfully!');
                } else {
                    this.displayCustomerErrors(result.errors);
                }
            }
        } catch (error) {
            console.error('Error saving customer:', error);
            alert('Error: Failed to save customer');
        }
    }

    async loadCustomers() {
        try {
            const response = await fetch(this.routes.customersGetData);
            const result = await response.json();

            if (result.success) {
                const select = $('#customerSelect');
                select.empty().append('<option value="">Select Customer</option>');

                result.data.forEach(customer => {
                    select.append(`<option value="${customer.id}">${customer.name}</option>`);
                });
            }
        } catch (error) {
            console.error('Error loading customers:', error);
        }
    }

    displayCustomerErrors(errors) {
        // Clear previous errors
        $('.invalid-feedback').text('').hide();
        $('.form-control').removeClass('is-invalid');

        // Display new errors
        Object.keys(errors).forEach(field => {
            const errorElement = $(`#${field}Error`);
            const inputElement = $(`[name="${field}"]`);

            errorElement.text(errors[field][0]).show();
            inputElement.addClass('is-invalid');
        });
    }

    resetCustomerForm() {
        $('#customerForm')[0].reset();
        $('.invalid-feedback').text('').hide();
        $('.form-control').removeClass('is-invalid');
    }

    formatCurrency(amount) {
        const value = parseFloat(amount);
        if (isNaN(value)) {
            console.warn('formatCurrency received NaN, using 0.00 instead. Amount:', amount);
            return `${this.currency}0.00`;
        }
        return `${this.currency}${value.toFixed(2)}`;
    }

    parseCurrency(currencyString) {
        return parseFloat(currencyString.replace(/[^0-9.-]+/g, '')) || 0;
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Initialize when document is ready
let invoiceManager;
$(document).ready(function() {
    // Check if routes are available
    if (typeof window.invoiceRoutes === 'undefined') {
        console.error('InvoiceManager: Routes not available. Make sure the Blade template includes the route configuration.');
        return;
    }

    console.log('InvoiceManager: Routes available:', window.invoiceRoutes);

    // Initialize Select2
    $(".select2").select2({
        theme: "bootstrap-5",
        containerCssClass: "select2--small",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small",
    });

    // Adjust scrollable content padding
    $('.pos-card-body-content').each(function () {
        var initHeight = 0;
        $(this).children().each(function () {
            initHeight = parseFloat(initHeight + $(this).outerHeight());
        });

        if ($(this).outerHeight() < initHeight) {
            $(this).css({
                'padding-right': 4 + 'px'
            });
        } else {
            $(this).css({
                'padding-right': 0
            });
        }
    });

    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Initialize Invoice Manager
    invoiceManager = new InvoiceManager();
});
