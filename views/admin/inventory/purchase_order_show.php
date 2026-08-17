<?php $layout = 'admin/layouts/main'; ?>
<div class="d-flex justify-content-between mb-4"><h4>OC #<?= e($po['order_number']) ?></h4><a href="<?= url('admin/inventory/purchase-orders') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a></div>
<div class="row">
    <div class="col-md-4 mb-4"><div class="card"><div class="card-header">Detalles de la Orden</div><ul class="list-group list-group-flush">
        <li class="list-group-item d-flex justify-content-between"><span>Estado</span><span class="badge bg-<?= badgeColor($po['status'], ['draft'=>'secondary','ordered'=>'primary','received'=>'success','cancelled'=>'danger'], 'info') ?>"><?= ['draft'=>'borrador','ordered'=>'ordenado','received'=>'recibido','cancelled'=>'cancelado','partial'=>'parcial'][$po['status']] ?? e($po['status']) ?></span></li>
        <li class="list-group-item d-flex justify-content-between"><span>Proveedor</span><span><?= e($po['supplier_name']) ?></span></li>
        <li class="list-group-item d-flex justify-content-between"><span>Esperado</span><span><?= $po['expected_date'] ? e(date('M j, Y', strtotime($po['expected_date']))) : 'N/A' ?></span></li>
        <li class="list-group-item d-flex justify-content-between"><span>Total</span><strong><?= formatMoney($po['total']) ?></strong></li>
    </ul></div></div>
    <div class="col-md-8">
        <div class="card mb-3"><div class="card-header">Artículos</div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>Producto</th><th>Ordenado</th><th>Recibido</th><th>Costo Unitario</th><th>Total</th></tr></thead><tbody>
        <?php foreach($po['items'] as $item): ?>
        <tr><td><?= e($item['product_name']) ?> <small class="text-muted">(<?= e($item['sku']) ?>)</small></td><td><?= $item['quantity_ordered'] ?></td><td><?= $item['quantity_received'] ?></td><td><?= formatMoney($item['unit_cost']) ?></td><td><?= formatMoney($item['total_cost']) ?></td></tr>
        <?php endforeach; ?></tbody></table></div></div></div>

        <?php if (in_array($po['status'], ['draft','ordered','partial'])): ?>
        <div class="card"><div class="card-header">Recibir Artículos</div><div class="card-body">
        <form method="POST" action="<?= url('admin/inventory/purchase-orders/' . $po['id'] . '/receive') ?>">
            <?= csrf_field() ?>
            <?php foreach($po['items'] as $item): ?>
            <div class="row mb-2 align-items-center">
                <div class="col-6"><strong><?= e($item['product_name']) ?></strong> <small>(Ordenado: <?= $item['quantity_ordered'] ?>, Recibido: <?= $item['quantity_received'] ?>)</small></div>
                <div class="col-3"><input type="number" name="quantity_received[<?= $item['id'] ?>]" class="form-control" value="<?= $item['quantity_ordered'] - $item['quantity_received'] ?>" min="0"></div>
            </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-success mt-2"><i class="fas fa-check me-1"></i>Recibir Artículos</button>
        </form></div></div>
        <?php endif; ?>
    </div>
</div>
