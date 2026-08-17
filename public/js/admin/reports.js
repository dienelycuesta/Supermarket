/**
 * Admin Reports - Charts and data loading
 */
(function(){
    'use strict';

    // Sales Chart
    const salesCtx = document.getElementById('reportSalesChart');
    if(salesCtx){
        const form = document.getElementById('reportFilterForm');
        const fromDate = document.getElementById('dateFrom')?.value || '';
        const toDate = document.getElementById('dateTo')?.value || '';

        $.getJSON(BASE_URL + '/api/v1/reports.php', {
            action: 'sales_chart',
            from: fromDate,
            to: toDate,
            days: 30
        }, function(res){
            if(!res.success) return;
            new Chart(salesCtx, {
                type: 'bar',
                data: {
                    labels: res.labels,
                    datasets: [{
                        label: 'Ventas (RD$)',
                        data: res.data,
                        backgroundColor: 'rgba(40,167,69,0.7)',
                        borderColor: '#28a745',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {legend:{display:false}},
                    scales: {y:{beginAtZero:true,ticks:{callback:function(v){return 'RD$'+v.toLocaleString();}}}}
                }
            });
        });
    }

    // Payment Methods Doughnut
    const payCtx = document.getElementById('reportPaymentChart');
    if(payCtx){
        $.getJSON(BASE_URL + '/api/v1/reports.php', {action:'payment_methods'}, function(res){
            if(!res.success) return;
            new Chart(payCtx, {
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

    // CSV Export buttons
    document.querySelectorAll('.btn-export-csv').forEach(function(btn){
        btn.addEventListener('click', function(){
            const type = this.dataset.type || 'sales';
            const from = document.getElementById('dateFrom')?.value || '';
            const to = document.getElementById('dateTo')?.value || '';
            window.location.href = BASE_URL + '/admin/reports/export?type=' + type + '&from=' + from + '&to=' + to;
        });
    });
})();
