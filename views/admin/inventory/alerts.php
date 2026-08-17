<?php $layout = 'admin/layouts/main'; ?>
<h4 class="mb-4">Alertas de Stock</h4>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th>Producto</th><th>Tipo de Alerta</th><th>Stock Actual</th><th>Stock Mínimo</th><th>Creado</th><th>Acción</th></tr></thead><tbody>
<?php foreach($alerts as $a): ?>
<tr><td><?= e($a['product_name']) ?></td><td><span class="badge bg-<?= $a['alert_type']==='out_of_stock'?'danger':'warning text-dark' ?>"><?= $a['alert_type']==='out_of_stock'?'agotado':'stock bajo' ?></span></td><td><?= $a['current_stock_now'] ?></td><td><?= $a['min_stock'] ?></td><td><?= timeAgo($a['created_at']) ?></td>
<td><form method="POST" action="<?= url('admin/inventory/alerts/' . $a['id'] . '/resolve') ?>"><?= csrf_field() ?><button class="btn btn-sm btn-success">Resolver</button></form></td></tr>
<?php endforeach; ?>
<?php if(empty($alerts)): ?><tr><td colspan="6" class="text-center text-muted py-3">Sin alertas activas</td></tr><?php endif; ?>
</tbody></table></div></div></div>
