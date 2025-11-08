"use strict";

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Get dashboard data from window object
    const dashboardData = window.dashboardData || {
        monthlySales: [],
        monthlyRevenue: [],
        activeSales: 0,
        returnedSales: 0,
        cancelledSales: 0,
        currency: '$'
    };

    console.log('Dashboard Data:', dashboardData);

    // Process monthly sales and revenue data
    const monthNames = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
    const salesData = [];
    const revenueData = [];
    const categories = [];

    // Create array for last 12 months
    const now = new Date();
    for (let i = 11; i >= 0; i--) {
        const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
        const monthIndex = date.getMonth();
        const year = date.getFullYear();

        categories.push(monthNames[monthIndex]);

        // Find matching sales data
        const salesRecord = dashboardData.monthlySales.find(
            s => s.month === (monthIndex + 1) && s.year === year
        );
        salesData.push(salesRecord ? parseFloat(salesRecord.total) : 0);

        // Find matching revenue data
        const revenueRecord = dashboardData.monthlyRevenue.find(
            s => s.month === (monthIndex + 1) && s.year === year
        );
        revenueData.push(revenueRecord ? parseFloat(revenueRecord.total_revenue) : 0);
    }

    console.log('Sales Data:', salesData);
    console.log('Revenue Data:', revenueData);
    console.log('Categories:', categories);

    // Check if Chart.js is available
    if (typeof Chart !== 'undefined') {
        // ================= Combined Bar & Line Chart using Chart.js =================
        const barCtx = document.getElementById('reviewChart');
        if (barCtx) {
            const barChart = new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: categories,
                    datasets: [
                        {
                            label: 'Sales',
                            data: salesData,
                            backgroundColor: 'rgba(46, 162, 250, 0.8)',
                            borderColor: 'rgba(46, 162, 250, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Revenue (Profit)',
                            data: revenueData,
                            type: 'line',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            yAxisID: 'y'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += dashboardData.currency + context.parsed.y.toFixed(2);
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
                                    return dashboardData.currency + value.toFixed(0);
                                }
                            },
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
            console.log('Bar chart created successfully');
        } else {
            console.error('Bar chart canvas element not found');
        }
    } else {
        console.error('Chart.js is not loaded');
    }

    // ================= Donut Chart for Sales by Status using ApexCharts =================
    const totalSales = dashboardData.activeSales + dashboardData.returnedSales + dashboardData.cancelledSales;

    console.log('Total Sales for Donut:', totalSales);
    console.log('Active Sales:', dashboardData.activeSales);
    console.log('Returned Sales:', dashboardData.returnedSales);
    console.log('Cancelled Sales:', dashboardData.cancelledSales);

    if (typeof ApexCharts !== 'undefined') {
        var pieOptions = {
            chart: {
                height: 300,
                type: "donut",
            },
            series: [
                parseFloat(dashboardData.activeSales) || 0,
                parseFloat(dashboardData.returnedSales) || 0,
                parseFloat(dashboardData.cancelledSales) || 0
            ],
            labels: ["Active", "Returned", "Cancelled"],
            colors: ["#28a745", "#ffc107", "#dc3545"],
            legend: {
                show: true,
                position: "bottom",
                formatter: function(seriesName, opts) {
                    const value = opts.w.globals.series[opts.seriesIndex];
                    const percentage = totalSales > 0 ? ((value / totalSales) * 100).toFixed(1) : 0;
                    return seriesName + ": " + dashboardData.currency + value.toFixed(2) + " (" + percentage + "%)";
                }
            },
            stroke: { width: 1 },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    return val > 0 ? val.toFixed(1) + "%" : "";
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return dashboardData.currency + value.toFixed(2);
                    }
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Sales',
                                formatter: function(w) {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return dashboardData.currency + total.toFixed(2);
                                }
                            }
                        }
                    }
                }
            },
            noData: {
                text: 'No sales data available',
                align: 'center',
                verticalAlign: 'middle'
            }
        };

        const chartRadialElement = document.querySelector("#chartRadial");
        if (chartRadialElement) {
            var pieChart = new ApexCharts(chartRadialElement, pieOptions);
            pieChart.render();
            console.log('Donut chart created successfully');
        } else {
            console.error('Donut chart element not found');
        }
    } else {
        console.error('ApexCharts is not loaded');
    }
});
