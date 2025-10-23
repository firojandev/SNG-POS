"use strict";

/**
 * Purchase Create/Edit Page JavaScript
 * Handles all purchase-related functionality
 *
 * @package    SNG-POS
 * @subpackage Purchase Module
 * @author     Your Name
 * @version    1.0.0
 * @since      1.0.0
 *
 * Features:
 * - Product search and selection with pagination
 * - Dynamic cart management with real-time updates
 * - Automatic tax calculations
 * - Supplier management with inline creation
 * - Form validation and submission
 * - Optimized rendering for better performance
 */

class PurchaseManager {
    constructor() {
        this.cart = [];
        this.selectedSupplier = null;
        this.currentCategory = null;
        this.searchTerm = '';
        this.currentPage = 1;
        this.loading = false;
        this.hasMoreProducts = true;

        // Store routes and URLs
        this.routes = {
            products: window.purchaseRoutes.products,
            calculateUnitTotal: window.purchaseRoutes.calculateUnitTotal,
            store: window.purchaseRoutes.store,
            suppliersStore: window.purchaseRoutes.suppliersStore,
            suppliersGetData: window.purchaseRoutes.suppliersGetData
        };
        this.defaultImage = window.purchaseConfig.defaultImage;
        this.currency = window.purchaseConfig.currency;

        this.init();
    }

    init() {
        console.log('PurchaseManager: Initializing...');
        this.initializeDatePicker();
        this.bindEvents();
        // Load products immediately when page loads
        console.log('PurchaseManager: Loading initial products...');
        this.loadProducts(true);
        this.updateCartDisplay();
    }

    initializeDatePicker() {
        // Initialize jQuery UI datepicker
        if (typeof $.fn.datepicker !== 'undefined') {
            const phpFmt = (window.purchaseConfig && window.purchaseConfig.dateFormatPhp) ? window.purchaseConfig.dateFormatPhp : 'Y-m-d';
            const jqFmt = this.phpDateFormatToJqueryUI(phpFmt);
            $('#purchaseDate').datepicker({
                dateFormat: jqFmt
            });
            // Set today's date as default
            $('#purchaseDate').datepicker('setDate', new Date());
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

        // Form submission
        $('#purchaseForm').on('submit', (e) => {
            e.preventDefault();
            this.submitPurchase();
        });

        // Supplier modal events
        $('#supplierModal').on('hidden.bs.modal', () => {
            this.resetSupplierForm();
        });

        $('#supplierForm').on('submit', (e) => {
            e.preventDefault();
            this.saveSupplier();
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

        console.log(`PurchaseManager: Loading products (reset: ${reset}, page: ${this.currentPage})`);
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
            console.log(`PurchaseManager: Fetching from URL: ${url}`);

            const response = await fetch(url);

            // Check if response is ok
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('PurchaseManager: Non-JSON response received:', text.substring(0, 200));
                throw new Error('Server returned non-JSON response. Please check the server logs.');
            }

            const result = await response.json();



            console.log('PurchaseManager: API Response:', result);

            if (result.success) {
                console.log(`PurchaseManager: Loaded ${result.data.length} products`);
                console.log('PurchaseManager: First product sample:', result.data[0]);
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
                <button class="btn btn-sm btn-primary" onclick="purchaseManager.loadProducts(true)">
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
        const formattedPrice = product.formatted_purchase_price || this.formatCurrency(product.purchase_price);

        // Use actual product image or fallback
        const productImage = product.image ? `/storage/${product.image}` : this.defaultImage;

        return $(`
            <div class="col-sm-6">
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
                                ${product.tax ? `<tr><td><strong>Tax</strong></td><td>:</td><td>${product.tax.value}%</td></tr>` : ''}
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
                unit_price: parseFloat(product.purchase_price),
                quantity: 1,
                tax_id: product.tax_id,
                tax_percentage: product.tax ? product.tax.value : 0
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
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fa fa-shopping-cart fa-2x mb-2"></i>
                        <p class="mb-0">Cart is empty. Select products from the right panel to add them.</p>
                    </td>
                </tr>
            `);
            $('#totalAmount').text(this.formatCurrency(0));
            $('#dueAmount').text(this.formatCurrency(0));
            $('#paidAmount').val(0);
            return;
        }

        // Remove empty message if exists
        $('#emptyCartMessage').remove();

        let totalAmount = 0;

        // Calculate totals for all items
        const itemCalculations = {};
        for (const item of this.cart) {
            const calculation = await this.calculateItemTotal(item);
            itemCalculations[item.product_id] = calculation;
            totalAmount += calculation.unit_total;
        }

        // If we're updating a specific product, only update that row
        if (updatedProductId !== null) {
            const item = this.cart.find(i => i.product_id === updatedProductId);
            if (item) {
                const calculation = itemCalculations[updatedProductId];
                const existingRow = $(`tr[data-product-id="${item.product_id}"]`);

                if (existingRow.length) {
                    // Update existing row
                    existingRow.find('td:eq(2) .qty-count').val(item.quantity);
                    existingRow.find('td:eq(3)').text(this.formatCurrency(calculation.tax_amount));
                    existingRow.find('td:eq(4)').text(this.formatCurrency(calculation.unit_total));
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

        // Update totals
        $('#totalAmount').text(this.formatCurrency(totalAmount));
        $('#dueAmount').text(this.formatCurrency(totalAmount));
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
                <td>${this.formatCurrency(calculation.tax_amount)}</td>
                <td>${this.formatCurrency(calculation.unit_total)}</td>
                <td><a href="#" class="btn btn-sm btn-danger text-12 py-0 px-1 remove-item"><i class="fa fa-minus"></i></a></td>
            </tr>
        `);
    }

    async calculateItemTotal(item) {
        try {
            const response = await fetch(this.routes.calculateUnitTotal, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({
                    unit_price: item.unit_price,
                    quantity: item.quantity,
                    tax_id: item.tax_id
                })
            });

            const result = await response.json();
            return result.success ? result.data : { unit_total: item.unit_price * item.quantity, tax_amount: 0 };
        } catch (error) {
            console.error('Error calculating total:', error);
            return { unit_total: item.unit_price * item.quantity, tax_amount: 0 };
        }
    }

    calculateDueAmount() {
        const totalAmount = this.parseCurrency($('#totalAmount').text());
        let paidAmount = parseFloat($('#paidAmount').val()) || 0;

        // Prevent paid amount from exceeding total amount
        if (paidAmount > totalAmount) {
            paidAmount = totalAmount;
            $('#paidAmount').val(paidAmount.toFixed(2));
            this.showToast('Paid amount cannot exceed total amount', 'info');
        }

        const dueAmount = Math.max(0, totalAmount - paidAmount);
        $('#dueAmount').text(this.formatCurrency(dueAmount));
    }

    updateProductSelection() {
        $('.pos-product-item').each(function() {
            const productId = $(this).data('product-id');
            const isInCart = purchaseManager.cart.some(item => item.product_id === productId);
            $(this).toggleClass('selected', isInCart);
        });
    }

    async submitPurchase() {
        if (this.cart.length === 0) {
            this.showAlert('Please add at least one item to the purchase.', 'warning');
            return;
        }

        if (!$('#supplierSelect').val()) {
            this.showAlert('Please select a supplier.', 'warning');
            return;
        }

        if (!$('#purchaseDate').val()) {
            this.showAlert('Please select a purchase date.', 'warning');
            return;
        }

        // Disable submit button and show loading
        const submitBtn = $('#purchaseForm button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        // Calculate item totals with tax
        const items = await Promise.all(this.cart.map(async (item) => {
            const calculation = await this.calculateItemTotal(item);
            return {
                product_id: item.product_id,
                unit_price: item.unit_price,
                quantity: item.quantity,
                tax_id: item.tax_id || null,
                tax_amount: calculation.tax_amount,
                unit_total: calculation.unit_total
            };
        }));

        const formData = {
            supplier_id: $('#supplierSelect').val(),
            date: $('#purchaseDate').val(),
            items: items,
            total_amount: this.parseCurrency($('#totalAmount').text()),
            paid_amount: parseFloat($('#paidAmount').val()) || 0,
            due_amount: this.parseCurrency($('#dueAmount').text()),
            note: $('#note').val()
        };

        try {
            const response = await fetch(this.routes.store, {
                method: 'POST',
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
                // Redirect to purchase show page
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 500);
            } else {
                submitBtn.prop('disabled', false).html(originalText);
                this.showAlert(result.message || 'Failed to create purchase', 'danger');

                // Display validation errors if present
                if (result.errors) {
                    this.displayValidationErrors(result.errors);
                }
            }
        } catch (error) {
            console.error('Error submitting purchase:', error);
            submitBtn.prop('disabled', false).html(originalText);
            this.showAlert('Error: Failed to create purchase. Please try again.', 'danger');
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

    async saveSupplier() {
        const formData = new FormData($('#supplierForm')[0]);

        try {
            const response = await fetch(this.routes.suppliersStore, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: formData
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    $('#supplierModal').modal('hide');
                    this.loadSuppliers();
                    $('#supplierSelect').val(result.data.id).trigger('change');
                    alert('Supplier added successfully!');
                } else {
                    this.displaySupplierErrors(result.errors);
                }
            }
        } catch (error) {
            console.error('Error saving supplier:', error);
            alert('Error: Failed to save supplier');
        }
    }

    async loadSuppliers() {
        try {
            const response = await fetch(this.routes.suppliersGetData);
            const result = await response.json();

            if (result.success) {
                const select = $('#supplierSelect');
                select.empty().append('<option value="">Select Supplier</option>');

                result.data.forEach(supplier => {
                    select.append(`<option value="${supplier.id}">${supplier.name}</option>`);
                });
            }
        } catch (error) {
            console.error('Error loading suppliers:', error);
        }
    }

    displaySupplierErrors(errors) {
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

    resetSupplierForm() {
        $('#supplierForm')[0].reset();
        $('.invalid-feedback').text('').hide();
        $('.form-control').removeClass('is-invalid');
    }

    formatCurrency(amount) {
        return `${this.currency}${parseFloat(amount).toFixed(2)}`;
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
let purchaseManager;
$(document).ready(function() {
    // Check if routes are available
    if (typeof window.purchaseRoutes === 'undefined') {
        console.error('PurchaseManager: Routes not available. Make sure the Blade template includes the route configuration.');
        return;
    }

    console.log('PurchaseManager: Routes available:', window.purchaseRoutes);

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

    // Initialize Purchase Manager
    purchaseManager = new PurchaseManager();
});
