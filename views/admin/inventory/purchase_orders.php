<?php $layout = 'admin/layouts/main'; ?>
<div class="d-flex justify-content-between mb-4"><h4>Órdenes de Compra</h4><a href="<?= url('admin/inventory/purchase-orders/create') ?>" class="btn btn-success"><i class="fas fa-plus me-1"></i>Nueva OC</a></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive">
<table class="table table-hover mb-0"><thead><tr><th>OC #</th><th>Proveedor</th><th>Estado</th><th>Artículos</th><th>Total</th><th>Esperado</th><th>Acciones</th></tr></thead><tbody>
<?php foreach ($data as $po): ?>
<tr>
    <td><a href="<?= url('admin/inventory/purchase-orders/' . $po['id']) ?>"><?= e($po['order_number']) ?></a></td>
    <td><?= e($po['supplier_name']) ?></td>
    <td><span class="badge bg-<?= badgeColor($po['status'], ['draft'=>'secondary','ordered'=>'primary','received'=>'success','cancelled'=>'danger'], 'info') ?>"><?= ['draft'=>'borrador','ordered'=>'ordenado','received'=>'recibido','cancelled'=>'cancelado','partial'=>'parcial'][$po['status']] ?? e($po['status']) ?></span></td>
    <td><?= $po['items_count'] ?></td>
    <td><?= formatMoney($po['total']) ?></td>
    <td><?= $po['expected_date'] ? e(date('M j, Y', strtotime($po['expected_date']))) : '-' ?></td>
    <td><a href="<?= url('admin/inventory/purchase-orders/' . $po['id']) ?>" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
</tr>
<?php endforeach; ?>
<?php if(empty($data)): ?><tr><td colspan="7" class="text-center text-muted py-3">Sin órdenes de compra</td></tr><?php endif; ?>
</tbody></table></div></div></div>
<?php if(isset($paginator)): ?><div class="mt-3"><?= $paginator->render() ?></div><?php endif; ?>
