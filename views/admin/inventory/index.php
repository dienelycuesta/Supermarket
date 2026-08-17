<?php $layout = 'admin/layouts/main'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Inventario</h4>
    <div>
        <a href="<?= url('admin/inventory/adjust') ?>" class="btn btn-success"><i class="fas fa-sliders-h me-1"></i>Ajuste de Stock</a>
        <a href="<?= url('admin/inventory/purchase-orders') ?>" class="btn btn-outline-primary"><i class="fas fa-file-invoice me-1"></i>Órdenes de Compra</a>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-3 mb-3"><div class="card stat-card border-start border-info border-4"><div class="card-body"><div class="text-muted small">Total de Productos</div><div class="h4 mb-0"><?= $totalProducts ?></div></div></div></div>
    <div class="col-md-3 mb-3"><div class="card stat-card border-start border-warning border-4"><div class="card-body"><div class="text-muted small">Stock Bajo</div><div class="h4 mb-0"><?= $lowStockCount ?></div></div></div></div>
    <div class="col-md-3 mb-3"><div class="card stat-card border-start border-danger border-4"><div class="card-body"><div class="text-muted small">Agotado</div><div class="h4 mb-0"><?= $outOfStockCount ?></div></div></div></div>
    <div class="col-md-3 mb-3"><div class="card stat-card border-start border-success border-4"><div class="card-body"><div class="text-muted small">Valor del Stock</div><div class="h4 mb-0"><?= formatMoney($totalValue) ?></div></div></div></div>
</div>
<?php if (!empty($alerts)): ?>
<div class="card mb-4"><div class="card-header bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i>Alertas Activas (<?= count($alerts) ?>)</div><div class="card-body p-0"><div class="table-responsive">
<table class="table table-sm mb-0"><thead><tr><th>Producto</th><th>Tipo</th><th>Stock Actual</th><th>Stock Mínimo</th><th>Acción</th></tr></thead><tbody>
<?php foreach (array_slice($alerts, 0, 5) as $a): ?>
<tr><td><?= e($a['product_name']) ?></td><td><span class="badge bg-<?= $a['alert_type']==='out_of_stock'?'danger':'warning text-dark' ?>"><?= $a['alert_type']==='out_of_stock'?'agotado':'stock bajo' ?></span></td><td><?= $a['current_stock_now'] ?></td><td><?= $a['min_stock'] ?></td><td><form method="POST" action="<?= url('admin/inventory/alerts/' . $a['id'] . '/resolve') ?>" class="d-inline"><?= csrf_field() ?><button class="btn btn-sm btn-outline-success">Resolver</button></form></td></tr>
<?php endforeach; ?></tbody></table></div></div></div>
<?php endif; ?>
<div class="card"><div class="card-header">Niveles de Stock</div><div class="card-body p-0"><div class="table-responsive">
<table class="table table-hover mb-0" id="stockTable"><thead><tr><th>Producto</th><th>SKU</th><th>Categoría</th><th>Stock</th><th>Mín</th><th>Máx</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
<?php foreach ($products as $p): ?>
<tr>
    <td><?= e($p['name']) ?></td><td><code><?= e($p['sku']) ?></code></td><td><?= e($p['category_name'] ?? '-') ?></td>
    <td><strong><?= $p['stock'] ?></strong></td><td><?= $p['min_stock'] ?></td><td><?= $p['max_stock'] ?></td>
    <td><span class="badge bg-<?= $p['stock']==0?'danger':($p['stock']<=$p['min_stock']?'warning text-dark':'success') ?>"><?= $p['stock']==0?'Agotado':($p['stock']<=$p['min_stock']?'Bajo':'En Stock') ?></span></td>
    <td><a href="<?= url('admin/inventory/movements?product_id=' . $p['id']) ?>" class="btn btn-sm btn-outline-info" title="Historial"><i class="fas fa-history"></i></a></td>
</tr>
<?php endforeach; ?></tbody></table></div></div></div>
<script>$(function(){$('#stockTable').DataTable({pageLength:25,order:[[3,'asc']]});});</script>
