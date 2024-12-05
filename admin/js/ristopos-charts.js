(function($) {
    'use strict';

    $(document).ready(function() {
        if ($('#bestSellingProductsChart').length) {
            renderBarChart();
        }
        if ($('#salesByDayChart').length) {
            renderLineChart();
        }
    });

    function renderBarChart() {
        var ctx = document.getElementById('bestSellingProductsChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: bestSellingProductsData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Prodotti Più Venduti'
                    }
                }
            }
        });
    }

    function renderLineChart() {
        var ctx = document.getElementById('salesByDayChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: salesByDayData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Vendite per Giorno'
                    }
                }
            }
        });
    }

})(jQuery);