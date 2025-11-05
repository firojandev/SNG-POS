"use strict";

/**
 * Product Wise Sales Report Manager
 *
 * @package    SNG-POS
 * @subpackage Product Wise Sales Report Module
 * @version    1.0.0
 */

class ProductWiseSalesManager {
    constructor() {
        // Configuration
        this.config = window.productWiseSalesConfig || {};
        this.currency = this.config.currency || '$';
        this.categoryData = window.categoryData || [];

        // Chart instance
        this.categoryChart = null;

        // DataTable instance
        this.dataTable = null;

        // Initialize
        this.init();
    }

    /**
     * Initialize the page
     */
    init() {
        console.log('ProductWiseSalesManager: Initializing...');

        // Initialize jQuery UI datepickers
        this.initializeDatePickers();

        // Initialize DataTable
        this.initDataTable();

        // Initialize category chart
        this.initCategoryChart();

        // Bind events
        this.bindEvents();
    }

    /**
     * Initialize jQuery UI datepickers
     */
    initializeDatePickers() {
        if (typeof $.fn.datepicker !== 'undefined') {
            const phpFmt = this.config.dateFormatPhp || 'Y-m-d';
            const jqFmt = this.phpDateFormatToJqueryUI(phpFmt);

            $('#start_date, #end_date').datepicker({
                dateFormat: jqFmt,
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                maxDate: 0 // Cannot select future dates
            });

            // Date range validation
            $('#start_date').on('change', () => {
                const startDate = $('#start_date').datepicker('getDate');
                const endDate = $('#end_date').datepicker('getDate');

                if (startDate && endDate && startDate > endDate) {
                    alert('Start date cannot be greater than end date');
                    $('#start_date').val('');
                }
            });

            $('#end_date').on('change', () => {
                const startDate = $('#start_date').datepicker('getDate');
                const endDate = $('#end_date').datepicker('getDate');

                if (startDate && endDate && endDate < startDate) {
                    alert('End date cannot be less than start date');
                    $('#end_date').val('');
                }
            });
        }
    }

    /**
     * Map PHP date format to jQuery UI datepicker format
     */
    phpDateFormatToJqueryUI(phpFormat) {
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

    /**
     * Initialize DataTable
     */
    initDataTable() {
        if (typeof $.fn.DataTable !== 'undefined') {
            this.dataTable = $('#productSalesTable').DataTable({
                pageLength: 25,
                order: [[5, 'desc']], // Order by sales column
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    search: "Search products:",
                    lengthMenu: "Show _MENU_ products",
                    info: "Showing _START_ to _END_ of _TOTAL_ products",
                    infoEmpty: "No products to show",
                    infoFiltered: "(filtered from _MAX_ total products)",
                    zeroRecords: "No matching products found"
                }
            });
        }
    }

    /**
     * Initialize category chart
     */
    initCategoryChart() {
        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded!');
            return;
        }

        const ctx = document.getElementById('categoryChart');
        if (!ctx || this.categoryData.length === 0) return;

        // Generate random colors for categories
        const colors = this.generateColors(this.categoryData.length);

        this.categoryChart = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: this.categoryData.map(item => item.category),
                datasets: [{
                    data: this.categoryData.map(item => item.total_sales),
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);

                                const formattedValue = this.currency + value.toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });

                                return label + ': ' + formattedValue + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * Generate random colors
     */
    generateColors(count) {
        const baseColors = [
            'rgb(0, 123, 255)',      // Blue
            'rgb(40, 167, 69)',      // Green
            'rgb(255, 193, 7)',      // Yellow
            'rgb(220, 53, 69)',      // Red
            'rgb(23, 162, 184)',     // Cyan
            'rgb(108, 117, 125)',    // Gray
            'rgb(111, 66, 193)',     // Purple
            'rgb(253, 126, 20)',     // Orange
            'rgb(232, 62, 140)',     // Pink
            'rgb(13, 110, 253)'      // Indigo
        ];

        const colors = [];
        for (let i = 0; i < count; i++) {
            colors.push(baseColors[i % baseColors.length]);
        }
        return colors;
    }

    /**
     * Bind events
     */
    bindEvents() {
        // Export CSV button
        const exportButton = document.querySelector('a[href*="export-product-wise-csv"]');
        if (exportButton) {
            exportButton.addEventListener('click', (e) => {
                // Show loading indicator
                const originalText = exportButton.innerHTML;
                exportButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Exporting...';
                exportButton.classList.add('disabled');

                // Re-enable after 3 seconds
                setTimeout(() => {
                    exportButton.innerHTML = originalText;
                    exportButton.classList.remove('disabled');
                }, 3000);
            });
        }
    }
}

/**
 * Initialize on page load
 */
$(document).ready(function() {
    // Initialize Product Wise Sales Manager
    if (typeof window.categoryData !== 'undefined') {
        window.productWiseSalesManagerInstance = new ProductWiseSalesManager();
    }
});
