/**
 * Admin Dashboard - Charts with Chart.js
 */
(function(){
    'use strict';

    // Sales Chart (Line)
    const salesCtx = document.getElementById('salesChart');
    if(salesCtx){
        $.getJSON(BASE_URL + '/api/v1/reports.php?action=sales_chart&days=30', function(res){
            if(!res.success) return;
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: res.labels,
                    datasets: [{
                        label: 'Daily Sales (RD$)',
                        data: res.data,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40,167,69,0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 3,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {legend:{display:false}},
                    scales: {
                        y: {beginAtZero:true, ticks:{callback:function(v){return 'RD$'+v.toLocaleString();}}}
                    }
                }
            });
        });
    }

    // Payment Methods Chart (Doughnut)
    const paymentCtx = document.getElementById('paymentChart');
    if(paymentCtx){
        $.getJSON(BASE_URL + '/api/v1/reports.php?action=payment_methods', function(res){
            if(!res.success) return;
            new Chart(paymentCtx, {
                type: 'doughnut',
                data: {
                    labels: res.labels,
                    datasets: [{
                        data: res.data,
                        backgroundColor: ['#28a745','#007bff','#6f42c1','#ffc107','#dc3545']
                    }]
                },
                options: {responsive:true, plugins:{legend:{position:'bottom'}}}
            });
        });
    }
})();
