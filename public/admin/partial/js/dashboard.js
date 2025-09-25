"use strict";

// ================= Bar / Column Chart =================
var barOptions = {
    chart: {
        height: "100%",
        type: "bar",
        toolbar: { show: false },
    },
    series: [
        {
            name: "Income",
            data: [40, 50, 41, 71, 27, 41, 20, 52, 75, 32, 57, 16]
        },
        {
            name: "Total Sales Item",
            data: [23, 42, 35, 27, 43, 22, 17, 31, 22, 22, 12, 16]
        }
    ],
    plotOptions: {
        bar: { columnWidth: "50%" }
    },
    stroke: { width: [0, 4], curve: "smooth" },
    xaxis: { categories: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"] },
    yaxis: [
        { title: { text: "Total Income" } },
        { opposite: true, title: { text: "Expense" } }
    ],
    theme: { mode: "light", palette: "palette1" },
};
var barChart = new ApexCharts(document.querySelector("#reviewChart"), barOptions);
barChart.render();


// ================= Pie Chart =================
var pieOptions = {
    chart: {
        height: 400,
        type: "pie",
    },
    series: [44, 55, 41, 17, 15],
    labels: ["Direct","Affiliate","Sponsored","Wholesale","Over Phone"],
    colors: ["#2EA2FA", "#44ABF9", "#5DB8FD", "#70BEFA", "#8ACCFD"],
    legend: { show: true, position: "bottom" },
    stroke: { width: 1 }
};
var pieChart = new ApexCharts(document.querySelector("#chartRadial"), pieOptions);
pieChart.render();


// ================= Dark / Light Mode Toggle =================
$.fn.modeSelectChart = function() {
    if ($(this).is(":checked")) {
        // Dark Mode
        barChart.updateOptions({
            theme: { mode: "dark" }
        });
        pieChart.updateOptions({
            colors: ["#0573c7", "#0781df", "#0390fc", "#229af7", "#36a6fc"],
            legend: {
                labels: { colors: "#f0f0f0", useSeriesColors: false }
            }
        });
    } else {
        // Light Mode
        barChart.updateOptions({
            theme: { mode: "light", palette: "palette1" },
            legend: {
                labels: { colors: "#333333", useSeriesColors: false }
            }
        });
        pieChart.updateOptions({
            colors: ["#2EA2FA", "#44ABF9", "#5DB8FD", "#70BEFA", "#8ACCFD"],
            legend: {
                labels: { colors: "#333333", useSeriesColors: false }
            }
        });
    }
};

// Initialize
$(document).ready(function () {
    $("#darkMode").modeSelectChart();
});
$(document).on("change", "#darkMode", function () {
    $(this).modeSelectChart();
});
