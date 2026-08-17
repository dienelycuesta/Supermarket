<?php $layout = 'admin/layouts/main'; ?>
<h4 class="mb-4">Reportes</h4>
<div class="card mb-4"><div class="card-body">
<form method="GET" class="row g-2 align-items-end">
    <div class="col-md-3"><label class="form-label">Fecha Inicio</label><input type="date" name="start_date" class="form-control" value="<?= e($startDate) ?>"></div>
    <div class="col-md-3"><label class="form-label">Fecha Fin</label><input type="date" name="end_date" class="form-control" value="<?= e($endDate) ?>"></div>
    <div class="col-md-2"><button type="submit" class="btn btn-success w-100">Aplicar</button></div>
    <div class="col-md-4 text-end">
        <a href="<?= url('admin/reports/export/sales?start_date=' . $startDate . '&end_date=' . $endDate) ?>" class="btn btn-outline-success"><i class="fas fa-download me-1"></i>Ventas CSV</a>
        <a href="<?= url('admin/reports/export/inventory?start_date=' . $startDate . '&end_date=' . $endDate) ?>" class="btn btn-outline-primary"><i class="fas fa-download me-1"></i>Inventario CSV</a>
    </div>
</form></div></div>

<!-- Resumen de Ventas -->
<div class="row mb-4">
    <div class="col-md-4 mb-3"><div class="card stat-card border-start border-success border-4"><div class="card-body"><div class="text-muted small">Total de Ventas</div><div class="h4 mb-0"><?= formatMoney($salesStats['total_sales'] ?? 0) ?></div></div></div></div>
    <div class="col-md-4 mb-3"><div class="card stat-card border-start border-info border-4"><div class="card-body"><div class="text-muted small">Total de Pedidos</div><div class="h4 mb-0"><?= $salesStats['total_orders'] ?? 0 ?></div></div></div></div>
    <div class="col-md-4 mb-3"><div class="card stat-card border-start border-primary border-4"><div class="card-body"><div class="text-muted small">Pedido Promedio</div><div class="h4 mb-0"><?= formatMoney($salesStats['avg_order'] ?? 0) ?></div></div></div></div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card"><div class="card-header">Gr&aacute;fico de Ventas</div><div class="card-body"><canvas id="reportSalesChart" height="300"></canvas></div></div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card"><div class="card-header">Pagos por M&eacute;todo</div><div class="card-body"><canvas id="paymentChart" height="300"></canvas></div></div>
    </div>
</div>

<!-- Productos M&aacute;s Vendidos -->
<div class="card">
    <div class="card-header">Productos M&aacute;s Vendidos</div>
    <div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
    <thead><tr><th>#</th><th>Producto</th><th>SKU</th><th>Unidades Vendidas</th><th>Ingresos</th></tr></thead><tbody>
    <?php foreach($topProducts as $i => $p): ?>
    <tr><td><?= $i+1 ?></td><td><?= e($p['product_name']) ?></td><td><code><?= e($p['product_sku']) ?></code></td><td><?= $p['total_sold'] ?></td><td><?= formatMoney($p['total_revenue']) ?></td></tr>
    <?php endforeach; ?>
    <?php if(empty($topProducts)): ?><tr><td colspan="5" class="text-center text-muted py-3">Sin datos de ventas para este per&iacute;odo</td></tr><?php endif; ?>
    </tbody></table></div></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    fetch('<?= url('api/v1/reports.php') ?>?action=sales_chart&start_date=<?= e($startDate) ?>&end_date=<?= e($endDate) ?>')
    .then(r=>r.json()).then(data=>{
        new Chart(document.getElementById('reportSalesChart'),{type:'line',data:{labels:data.labels,datasets:[{label:'Ventas',data:data.values,borderColor:'#28a745',backgroundColor:'rgba(40,167,69,0.1)',fill:true,tension:0.3}]},options:{responsive:true,maintainAspectRatio:false}});
    }).catch(()=>{});
    const pmData = <?= json_encode($paymentTotals) ?>;
    if(pmData.length){
        new Chart(document.getElementById('paymentChart'),{type:'doughnut',data:{labels:pmData.map(p=>p.payment_method),datasets:[{data:pmData.map(p=>p.total),backgroundColor:['#28a745','#0d6efd','#6f42c1','#6c757d']}]},options:{responsive:true,maintainAspectRatio:false}});
    }
});
</script>
