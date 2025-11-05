"use strict";

/**
 * Sales Summary Report Manager
 *
 * @package    SNG-POS
 * @subpackage Sales Report Module
 * @version    1.0.0
 */

class SalesReportManager {
    constructor() {
        // Configuration
        this.routes = window.salesReportRoutes || {};
        this.config = window.salesReportConfig || {};
        this.currency = this.config.currency || '$';

        // Chart instances
        this.dailySalesChart = null;
        this.salesByStatusChart = null;

        // Initialize
        this.init();
    }

    /**
     * Initialize the page
     */
    init() {
        console.log('SalesReportManager: Initializing...');

        // Initialize jQuery UI datepickers
        this.initializeDatePickers();

        // Initialize charts
        this.initializeCharts();

        // Initialize quick date filters
        this.initQuickDateFilters();

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
     * Initialize charts
     */
    initializeCharts() {
        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded!');
            return;
        }

        // Get chart data from window
        const dailySalesData = window.salesReportData?.dailySales || [];
        const salesByStatusData = window.salesReportData?.salesByStatus || [];

        // Initialize Daily Sales Chart
        this.initDailySalesChart(dailySalesData);

        // Initialize Sales by Status Chart
        this.initSalesByStatusChart(salesByStatusData);
    }

    /**
     * Initialize Daily Sales Line Chart
     */
    initDailySalesChart(data) {
        const ctx = document.getElementById('dailySalesChart');
        if (!ctx) return;

        this.dailySalesChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: data.map(item => item.sale_date),
                datasets: [{
                    label: 'Total Sales',
                    data: data.map(item => parseFloat(item.total_amount)),
                    borderColor: 'rgb(0, 123, 255)',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Paid Amount',
                    data: data.map(item => parseFloat(item.paid_amount)),
                    borderColor: 'rgb(40, 167, 69)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: (context) => {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += this.currency + context.parsed.y.toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => {
                                return this.currency + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * Initialize Sales by Status Doughnut Chart
     */
    initSalesByStatusChart(data) {
        const ctx = document.getElementById('salesByStatusChart');
        if (!ctx) return;

        const statusColors = {
            'active': 'rgb(40, 167, 69)',
            'returned': 'rgb(255, 193, 7)',
            'cancelled': 'rgb(220, 53, 69)'
        };

        this.salesByStatusChart = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: data.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
                datasets: [{
                    data: data.map(item => item.count),
                    backgroundColor: data.map(item => statusColors[item.status] || 'rgb(108, 117, 125)'),
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
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * Initialize quick date filter buttons
     */
    initQuickDateFilters() {
        const filterForm = document.querySelector('form[action*="sales-report"]');
        if (!filterForm) return;

        const quickFiltersContainer = document.createElement('div');
        quickFiltersContainer.className = 'col-12 mb-3';
        quickFiltersContainer.innerHTML = `
            <label class="form-label">Quick Filters:</label>
            <div class="btn-group flex-wrap" role="group">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="today">Today</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="yesterday">Yesterday</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="this-week">This Week</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="last-week">Last Week</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="this-month">This Month</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="last-month">Last Month</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="this-year">This Year</button>
            </div>
        `;

        // Insert quick filters before the date inputs
        const firstRow = filterForm.querySelector('.row');
        if (firstRow) {
            firstRow.insertBefore(quickFiltersContainer, firstRow.firstChild);
        }

        // Add event listeners to quick filter buttons
        const quickFilterButtons = quickFiltersContainer.querySelectorAll('[data-filter]');
        quickFilterButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const filter = button.dataset.filter;
                const dates = this.getDateRange(filter);

                if (dates) {
                    $('#start_date').datepicker('setDate', dates.start);
                    $('#end_date').datepicker('setDate', dates.end);

                    // Highlight active button
                    quickFilterButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                }
            });
        });
    }

    /**
     * Get date range for quick filters
     */
    getDateRange(filter) {
        const today = new Date();
        let start = new Date();
        let end = new Date();

        switch(filter) {
            case 'today':
                start = today;
                end = today;
                break;

            case 'yesterday':
                start.setDate(today.getDate() - 1);
                end.setDate(today.getDate() - 1);
                break;

            case 'this-week':
                const firstDayOfWeek = today.getDate() - today.getDay();
                start.setDate(firstDayOfWeek);
                end = today;
                break;

            case 'last-week':
                const lastWeekStart = today.getDate() - today.getDay() - 7;
                const lastWeekEnd = lastWeekStart + 6;
                start.setDate(lastWeekStart);
                end.setDate(lastWeekEnd);
                break;

            case 'this-month':
                start.setDate(1);
                end = today;
                break;

            case 'last-month':
                start.setMonth(today.getMonth() - 1);
                start.setDate(1);
                end = new Date(today.getFullYear(), today.getMonth(), 0);
                break;

            case 'this-year':
                start.setMonth(0);
                start.setDate(1);
                end = today;
                break;

            default:
                return null;
        }

        return {
            start: start,
            end: end
        };
    }

    /**
     * Bind events
     */
    bindEvents() {
        // Export CSV button
        const exportButton = document.querySelector('a[href*="export-csv"]');
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

    /**
     * Print report
     */
    printReport() {
        window.print();
    }
}

/**
 * Initialize on page load
 */
$(document).ready(function() {
    // Initialize Sales Report Manager
    if (typeof window.salesReportData !== 'undefined') {
        window.salesReportManagerInstance = new SalesReportManager();
    }
});

/**
 * Export print function to global scope
 */
window.printReport = function() {
    if (window.salesReportManagerInstance) {
        window.salesReportManagerInstance.printReport();
    } else {
        window.print();
    }
};
