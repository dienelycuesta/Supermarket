<?php $layout = 'admin/layouts/main';
$paymentLabels = ['cash'=>'Efectivo','bank_transfer'=>'Transferencia','stripe'=>'Tarjeta','card_pos'=>'Tarjeta POS'];
?>
<div class="d-flex justify-content-between mb-4">
    <h4>Pedidos</h4>
    <div>
        <?php if (!empty($data)): ?>
        <form method="POST" action="<?= url('admin/orders/clear-all') ?>" class="d-inline" onsubmit="return confirm('&iquest;Eliminar TODOS los pedidos? Esta acci&oacute;n no se puede deshacer.')"><?= csrf_field() ?><button class="btn btn-outline-danger me-2"><i class="fas fa-trash me-1"></i>Limpiar Todo</button></form>
        <?php endif; ?>
        <a href="<?= url('admin/orders/in-store') ?>" class="btn btn-success"><i class="fas fa-cash-register me-1"></i>Nueva Venta en Tienda</a>
    </div>
</div>
<div class="card mb-3"><div class="card-body">
<form method="GET" class="row g-2 align-items-end">
    <div class="col-md-3"><select name="status" class="form-select"><option value="">Todos los Estados</option><?php foreach(['pending'=>'Pendiente','processing'=>'En proceso','completed'=>'Completado','cancelled'=>'Cancelado'] as $k=>$v): ?><option value="<?= $k ?>" <?= ($statusFilter??'')===$k?'selected':'' ?>><?= $v ?></option><?php endforeach; ?></select></div>
    <div class="col-md-3"><select name="type" class="form-select"><option value="">Todos los Tipos</option><option value="online" <?= ($typeFilter??'')==='online'?'selected':'' ?>>En L&iacute;nea</option><option value="in_store" <?= ($typeFilter??'')==='in_store'?'selected':'' ?>>En Tienda</option></select></div>
    <div class="col-md-2"><button type="submit" class="btn btn-outline-success w-100">Filtrar</button></div>
</form></div></div>

<?php if (empty($data)): ?>
<div class="card"><div class="card-body text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3"></i><h5>Sin pedidos</h5><p>No se encontraron pedidos con los filtros seleccionados.</p></div></div>
<?php else: ?>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th>Pedido #</th><th>Cliente</th><th>Tipo</th><th>Total</th><th>Estado</th><th>Pago</th><th>Fecha</th><th>Acciones</th></tr></thead><tbody>
<?php foreach($data as $o):
    $sc = badgeColor($o['status'], ['completed'=>'success','pending'=>'warning text-dark','processing'=>'info','cancelled'=>'danger']);
    $sl = ['completed'=>'completado','pending'=>'pendiente','processing'=>'en proceso','cancelled'=>'cancelado'];
    $pm = isset($paymentLabels[$o['payment_method']]) ? $paymentLabels[$o['payment_method']] : ($o['payment_method'] ? ucfirst(str_replace('_',' ',$o['payment_method'])) : '-');
?>
<tr>
    <td><a href="<?= url('admin/orders/' . $o['id']) ?>" class="fw-bold text-decoration-none"><?= e($o['order_number']) ?></a></td>
    <td><?= e($o['customer_name'] ?? 'N/A') ?></td>
    <td><span class="badge bg-<?= ($o['order_type']??'')==='online'?'info':'secondary' ?>"><?= ($o['order_type']??'')==='online'?'en l&iacute;nea':'en tienda' ?></span></td>
    <td class="fw-bold"><?= formatMoney($o['total_amount']) ?></td>
    <td><span class="badge bg-<?= $sc ?>"><?= isset($sl[$o['status']]) ? $sl[$o['status']] : e($o['status']) ?></span></td>
    <td class="small"><?= e($pm) ?></td>
    <td class="small text-muted"><?= timeAgo($o['created_at']) ?></td>
    <td class="text-nowrap">
        <a href="<?= url('admin/orders/' . $o['id']) ?>" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-eye"></i></a>
        <form method="POST" action="<?= url('admin/orders/' . $o['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('&iquest;Eliminar este pedido?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button></form>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div></div>
<?php if(isset($paginator)): ?><div class="mt-3"><?= $paginator->render() ?></div><?php endif; ?>
<?php endif; ?>
