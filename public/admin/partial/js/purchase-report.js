"use strict";

/**
 * Purchase Report Chart Initialization
 * This script handles the daily purchase chart rendering
 */

// Initialize when DOM is ready
$(document).ready(function() {
    // Initialize datepicker
    initializeDatepicker();

    // Initialize purchase chart
    initializePurchaseChart();
});

/**
 * Initialize datepicker with proper format
 */
function initializeDatepicker() {
    const dateFormat = window.purchaseReportConfig.dateFormatPhp;
    let jsDateFormat = 'yy-mm-dd'; // default

    // Convert PHP date format to jQuery UI format
    const formatMap = {
        'Y-m-d': 'yy-mm-dd',
        'd-m-Y': 'dd-mm-yy',
        'm/d/Y': 'mm/dd/yy',
        'd/m/Y': 'dd/mm/yy'
    };
    jsDateFormat = formatMap[dateFormat] || 'yy-mm-dd';

    $('#start_date, #end_date').datepicker({
        dateFormat: jsDateFormat,
        changeMonth: true,
        changeYear: true,
        maxDate: 0
    });
}

/**
 * Initialize Daily Purchase Chart
 */
function initializePurchaseChart() {
    const dailyPurchaseData = window.purchaseReportData.dailyPurchases;

    if (!dailyPurchaseData || dailyPurchaseData.length === 0) {
        return;
    }

    const ctx = document.getElementById('dailyPurchaseChart');
    if (!ctx) {
        return;
    }

    const currency = window.purchaseReportConfig.currency;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyPurchaseData.map(item => item.purchase_date),
            datasets: [
                {
                    label: 'Total Amount',
                    data: dailyPurchaseData.map(item => parseFloat(item.total_amount)),
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Paid Amount',
                    data: dailyPurchaseData.map(item => parseFloat(item.paid_amount)),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Due Amount',
                    data: dailyPurchaseData.map(item => parseFloat(item.due_amount)),
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
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
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += currency + context.parsed.y.toFixed(2);
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return currency + value.toFixed(2);
                        }
                    }
                }
            }
        }
    });
}
