/**
 * Product Management JavaScript
 */

class ProductManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeComponents();
    }

    bindEvents() {
        // Image preview functionality
        const imageInput = document.getElementById('image');
        if (imageInput) {
            imageInput.addEventListener('change', (e) => this.previewImage(e.target));
        }

        // Auto-generate SKU
        const nameInput = document.getElementById('name');
        if (nameInput) {
            nameInput.addEventListener('input', (e) => this.generateSKU(e.target.value));
        }

        // Search form submission with loading
        const searchForm = document.getElementById('searchForm');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                this.showLoading();
            });
        }

        // File upload preview
        const csvFileInput = document.getElementById('csv_file');
        if (csvFileInput) {
            csvFileInput.addEventListener('change', (e) => this.previewFile(e.target));
        }

        // Delete confirmation with SweetAlert
        document.addEventListener('click', (e) => {
            if (e.target.closest('.delete-product')) {
                e.preventDefault();
                const form = e.target.closest('form');
                const productName = form.dataset.productName || 'this product';
                const productId = form.action.split('/').pop();
                this.openDeleteModal(productId, productName, form);
            }
            
            // Add loading for CSV export and import links
            if (e.target.closest('a[href*="export"]') || e.target.closest('a[href*="import"]')) {
                this.showLoading();
            }
            
            // Add loading for Add Product button
            if (e.target.closest('a[href*="create"]')) {
                this.showLoading();
            }
        });

        // Barcode generation
        document.addEventListener('click', (e) => {
            if (e.target.closest('.generate-barcode')) {
                e.preventDefault();
                const button = e.target.closest('.generate-barcode');
                const productId = button.dataset.productId;
                const sku = button.dataset.sku;
                const name = button.dataset.name;
                this.openBarcodeModal(productId, sku, name);
            }
        });

        // Barcode download button
        document.addEventListener('click', (e) => {
            if (e.target.closest('#downloadBarcodeBtn')) {
                e.preventDefault();
                this.downloadBarcodePDF();
            }
        });
    }

    initializeComponents() {
        // Initialize tooltips
        this.initTooltips();
        
        // Initialize animations
        this.initAnimations();
        
        // Initialize lazy loading for images
        this.initLazyLoading();
        
        // Ensure Select2 is initialized for product page elements
        this.initializeProductSelect2();
    }

    previewImage(input) {
        const preview = document.getElementById('imagePreview');
        if (!preview) return;

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="preview-image" alt="Preview">`;
                preview.classList.add('slide-up');
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.innerHTML = `
                <div class="upload-placeholder">
                    <i class="fa fa-image fa-3x text-muted mb-2"></i>
                    <p class="text-muted">Click to upload image</p>
                </div>
            `;
        }
    }

    generateSKU(productName) {
        const skuInput = document.getElementById('sku');
        if (!skuInput || skuInput.value) return; // Don't override existing SKU

        if (productName.length >= 3) {
            const prefix = productName.substring(0, 3).toUpperCase().replace(/[^A-Z]/g, '');
            const timestamp = Date.now().toString().slice(-6);
            const sku = `${prefix}${timestamp}`;
            skuInput.value = sku;
        }
    }

    performSearch() {
        const searchForm = document.querySelector('form');
        if (searchForm) {
            // Add loading state
            this.showLoading();
            searchForm.submit();
        }
    }

    previewFile(input) {
        const preview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            fileName.textContent = file.name;
            fileSize.textContent = this.formatFileSize(file.size);
            preview.style.display = 'block';
            preview.classList.add('fade-in');
        } else {
            preview.style.display = 'none';
        }
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    /**
     * Open delete confirmation with SweetAlert
     */
    openDeleteModal(productId, productName, form) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete "${productName}" product? This action will move it to trash.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.handleDelete(productId, productName, form);
                }
            });
        } else {
            // Fallback to regular confirm
            if (confirm(`Are you sure you want to delete "${productName}"? This action will move it to trash.`)) {
                this.handleDelete(productId, productName, form);
            }
        }
    }

    /**
     * Handle delete operation with AJAX
     */
    handleDelete(productId, productName, form) {
        if (!productId) {
            this.showNotification('Invalid product ID', 'error');
            return;
        }

        // Show loading with SweetAlert
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the product.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        } else {
            this.showLoading();
        }

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     form.querySelector('input[name="_token"]')?.value;

        // Make AJAX request
        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                _method: 'DELETE'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Deleted!',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Reload page to refresh the product list
                        window.location.reload();
                    });
                } else {
                    this.showNotification(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1500);
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Delete operation failed',
                        icon: 'error'
                    });
                } else {
                    this.showNotification(data.message || 'Delete operation failed', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to delete product',
                    icon: 'error'
                });
            } else {
                this.showNotification('Failed to delete product', 'error');
            }
        })
        .finally(() => {
            // Hide any loading states
            this.hideLoading();
            
            // Close any SweetAlert loading dialogs
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
        });
    }

    openBarcodeModal(productId, sku, name) {
        const modal = document.getElementById('barcodeQuantityModal');
        const modalProductName = document.getElementById('modalProductName');
        const modalProductSku = document.getElementById('modalProductSku');
        const productIdInput = document.getElementById('productId');
        const productSkuInput = document.getElementById('productSku');
        const productNameInput = document.getElementById('productName');
        const quantityInput = document.getElementById('barcodeQuantity');
        
        if (!modal) return;

        // Update modal content
        if (modalProductName) modalProductName.textContent = name;
        if (modalProductSku) modalProductSku.textContent = sku;
        if (productIdInput) productIdInput.value = productId;
        if (productSkuInput) productSkuInput.value = sku;
        if (productNameInput) productNameInput.value = name;
        
        // Clear quantity input
        if (quantityInput) quantityInput.value = '';
        
        // Show modal using Bootstrap
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }

    downloadBarcodePDF() {
        const form = document.getElementById('barcodeForm');
        const quantityInput = document.getElementById('barcodeQuantity');
        const productId = document.getElementById('productId').value;
        const sku = document.getElementById('productSku').value;
        const name = document.getElementById('productName').value;
        const quantity = quantityInput.value;

        // Validate quantity
        if (!quantity || quantity < 1 || quantity > 100) {
            this.showNotification('Please enter a valid quantity between 1 and 100', 'error');
            quantityInput.focus();
            return;
        }

        // Show loading
        const downloadBtn = document.getElementById('downloadBarcodeBtn');
        const originalText = downloadBtn.innerHTML;
        downloadBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Generating...';
        downloadBtn.disabled = true;

        // Create download URL
        const baseUrl = window.location.origin;
        const downloadUrl = `${baseUrl}/admin/products/barcode/download?product_id=${productId}&sku=${encodeURIComponent(sku)}&name=${encodeURIComponent(name)}&quantity=${quantity}`;
        
        // Create temporary link and trigger download
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.download = `barcode_${sku}_${quantity}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Reset button after a delay
        setTimeout(() => {
            downloadBtn.innerHTML = originalText;
            downloadBtn.disabled = false;
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('barcodeQuantityModal'));
            if (modal) {
                modal.hide();
            }
        }, 1500);
    }

    downloadSampleCSV() {
        const csvContent = [
            ['Name', 'SKU', 'Purchase Price', 'Sell Price', 'Stock Quantity', 'Category', 'Unit', 'Tax', 'Description'],
            ['Sample Product 1', 'P000001', '100', '150', '50', 'Electronics', 'Piece', 'GST 18%', 'Sample product description'],
            ['Sample Product 2', 'P000002', '200', '300', '25', 'Clothing', 'Piece', 'GST 12%', 'Another sample product'],
            ['Sample Product 3', 'P000003', '50', '75', '100', 'Food', 'Kg', 'GST 5%', 'Food item sample']
        ];

        let csvString = csvContent.map(row => row.join(',')).join('\n');
        
        const blob = new Blob([csvString], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'products_sample_template.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    showLoading() {
        // Remove existing loading overlay if any
        this.hideLoading();
        
        // Add loading overlay
        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.innerHTML = `
            <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-3 text-white fw-bold">Loading...</div>
            </div>
        `;
        document.body.appendChild(loadingOverlay);
        
        // Auto-hide after 10 seconds as fallback
        setTimeout(() => {
            this.hideLoading();
        }, 10000);
    }

    hideLoading() {
        const loadingOverlay = document.querySelector('.loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
    }

    initTooltips() {
        // Initialize Bootstrap tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    initAnimations() {
        // Add fade-in animation to product cards
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('fade-in');
        });
    }

    initLazyLoading() {
        // Lazy load product images
        const images = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    }

    initializeProductSelect2() {
        // Simple retry mechanism for Select2 initialization
        setTimeout(() => {
            if (typeof window.initializeSelect2 === 'function') {
                console.log('Reinitializing Select2 for product page...');
                window.initializeSelect2();
            }
        }, 500);
    }

    // Utility methods
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

    showNotification(message, type = 'success') {
        // Using notyf for notifications (will be available after flasher installation)
        if (typeof notyf !== 'undefined') {
            if (type === 'success') {
                notyf.success(message);
            } else {
                notyf.error(message);
            }
        } else {
            // Fallback to alert
            alert(message);
        }
    }
}

// Global functions for inline event handlers
function previewImage(input) {
    productManager.previewImage(input);
}

function openBarcodeModal(productId, sku, name) {
    productManager.openBarcodeModal(productId, sku, name);
}

function downloadBarcodePDF() {
    productManager.downloadBarcodePDF();
}

function downloadSampleCSV() {
    productManager.downloadSampleCSV();
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.productManager = new ProductManager();
});

