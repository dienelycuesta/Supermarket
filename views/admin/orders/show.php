<?php $layout = 'admin/layouts/main';
$statusLabels = ['completed'=>'Completado','pending'=>'Pendiente','confirmed'=>'Confirmado','processing'=>'En Proceso','cancelled'=>'Cancelado','delivered'=>'Entregado','ready'=>'Listo'];
$statusColors = ['completed'=>'success','pending'=>'warning text-dark','confirmed'=>'info','processing'=>'primary','cancelled'=>'danger','delivered'=>'success','ready'=>'info'];
$paymentLabels = ['cash'=>'Efectivo','bank_transfer'=>'Transferencia','stripe'=>'Tarjeta','card_pos'=>'Tarjeta POS'];
$sc = isset($statusColors[$order['status']]) ? $statusColors[$order['status']] : 'secondary';
$sl = isset($statusLabels[$order['status']]) ? $statusLabels[$order['status']] : ucfirst($order['status']);
$pl = isset($paymentLabels[$order['payment_method']]) ? $paymentLabels[$order['payment_method']] : ucfirst(str_replace('_',' ',$order['payment_method'] ?? '-'));
?>
<div class="d-flex justify-content-between mb-4">
    <h4>Pedido #<?= e($order['order_number']) ?> <span class="badge bg-<?= $sc ?>"><?= $sl ?></span></h4>
    <a href="<?= url('admin/orders') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a>
</div>
<div class="row">
    <div class="col-lg-8">
        <!-- Items -->
        <div class="card mb-3"><div class="card-header">Art&iacute;culos</div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>Producto</th><th>SKU</th><th>Cant</th><th>Precio Unit.</th><th>Subtotal</th></tr></thead><tbody>
        <?php foreach($order['items'] as $item): ?>
        <tr><td><?= e($item['product_name']) ?></td><td><code><?= e($item['product_sku']) ?></code></td><td><?= $item['quantity'] ?></td><td><?= formatMoney($item['unit_price']) ?></td><td><?= formatMoney($item['subtotal']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr><td colspan="4" class="text-end"><strong>Subtotal</strong></td><td><?= formatMoney($order['subtotal']) ?></td></tr>
            <tr><td colspan="4" class="text-end"><strong>ITBIS</strong></td><td><?= formatMoney($order['tax_amount']) ?></td></tr>
            <tr><td colspan="4" class="text-end"><strong class="fs-5">Total</strong></td><td><strong class="fs-5 text-success"><?= formatMoney($order['total_amount']) ?></strong></td></tr>
        </tfoot></table></div></div></div>

        <?php if (!empty($order['payments'])): ?>
        <div class="card mb-3"><div class="card-header">Pagos</div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>M&eacute;todo</th><th>Monto</th><th>Estado</th><th>Referencia</th><th>Fecha</th></tr></thead><tbody>
        <?php foreach($order['payments'] as $p):
            $pmLabel = isset($paymentLabels[$p['payment_method']]) ? $paymentLabels[$p['payment_method']] : ucfirst(str_replace('_',' ',$p['payment_method']));
        ?>
        <tr><td><?= e($pmLabel) ?></td><td><?= formatMoney($p['amount']) ?></td><td><span class="badge bg-<?= $p['status']==='completed'?'success':($p['status']==='pending'?'warning text-dark':'danger') ?>"><?= ($p['status']==='completed'?'completado':($p['status']==='pending'?'pendiente':e($p['status']))) ?></span></td><td><?= e($p['reference'] ?? '-') ?></td><td class="small"><?= $p['processed_at'] ? e(date('d/m/Y H:i', strtotime($p['processed_at']))) : '-' ?></td></tr>
        <?php endforeach; ?></tbody></table></div></div></div>
        <?php endif; ?>
    </div>
    <div class="col-lg-4">
        <!-- Customer Info -->
        <div class="card mb-3">
            <div class="card-header">Cliente</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><i class="fas fa-user text-muted me-2"></i><strong><?= e($order['customer_name'] ?? 'N/A') ?></strong></li>
                <?php if ($order['customer_email']): ?><li class="list-group-item"><i class="fas fa-envelope text-muted me-2"></i><?= e($order['customer_email']) ?></li><?php endif; ?>
                <?php if ($order['customer_phone']): ?><li class="list-group-item"><i class="fas fa-phone text-muted me-2"></i><?= e($order['customer_phone']) ?></li><?php endif; ?>
                <li class="list-group-item"><i class="fas fa-tag text-muted me-2"></i>Tipo: <span class="badge bg-<?= ($order['order_type']??'')==='online'?'info':'secondary' ?>"><?= ($order['order_type']??'')==='online'?'En l&iacute;nea':'En tienda' ?></span></li>
                <li class="list-group-item"><i class="fas fa-credit-card text-muted me-2"></i>Pago: <strong><?= e($pl) ?></strong></li>
                <li class="list-group-item"><i class="fas fa-calendar text-muted me-2"></i><?= date('d/m/Y h:i A', strtotime($order['created_at'])) ?></li>
            </ul>
        </div>

        <!-- Delivery Address -->
        <?php if ($order['shipping_address']): ?>
        <div class="card mb-3 border-success">
            <div class="card-header bg-success bg-opacity-10"><i class="fas fa-map-marker-alt text-success me-2"></i><strong>Direcci&oacute;n de Entrega</strong></div>
            <div class="card-body">
                <p class="mb-0"><?= e($order['shipping_address']) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($order['notes']): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-sticky-note me-2"></i>Notas</div>
            <div class="card-body"><p class="mb-0"><?= e($order['notes']) ?></p></div>
        </div>
        <?php endif; ?>

        <!-- WhatsApp -->
        <?php
        $wsPhone = !empty($order['customer_phone']) ? $order['customer_phone'] : WHATSAPP_PHONE;
        $wsUrl = buildWhatsAppOrderUrl($order, $order['items'], $wsPhone);
        ?>
        <a href="<?= $wsUrl ?>" target="_blank" class="btn btn-success w-100 mb-3"><i class="fab fa-whatsapp me-2"></i>Enviar Pedido por WhatsApp</a>

        <!-- Actions -->
        <div class="card mb-3"><div class="card-header">Acciones</div><div class="card-body">
            <?php if($order['status'] === 'pending'): ?>
            <form method="POST" action="<?= url('admin/orders/' . $order['id'] . '/status') ?>" class="mb-2"><?= csrf_field() ?><input type="hidden" name="status" value="processing"><button class="btn btn-info w-100"><i class="fas fa-cog me-1"></i>Marcar en Proceso</button></form>
            <form method="POST" action="<?= url('admin/orders/' . $order['id'] . '/status') ?>" class="mb-2"><?= csrf_field() ?><input type="hidden" name="status" value="completed"><button class="btn btn-success w-100"><i class="fas fa-check me-1"></i>Marcar Completado</button></form>
            <a href="<?= url('admin/payments/' . $order['id'] . '/record') ?>" class="btn btn-outline-primary w-100 mb-2"><i class="fas fa-money-bill me-1"></i>Registrar Pago</a>
            <?php elseif($order['status'] === 'processing'): ?>
            <form method="POST" action="<?= url('admin/orders/' . $order['id'] . '/status') ?>" class="mb-2"><?= csrf_field() ?><input type="hidden" name="status" value="completed"><button class="btn btn-success w-100"><i class="fas fa-check me-1"></i>Marcar Completado</button></form>
            <a href="<?= url('admin/payments/' . $order['id'] . '/record') ?>" class="btn btn-outline-primary w-100 mb-2"><i class="fas fa-money-bill me-1"></i>Registrar Pago</a>
            <?php endif; ?>
            <?php if(in_array($order['status'], ['pending','processing'])): ?>
            <form method="POST" action="<?= url('admin/orders/' . $order['id'] . '/status') ?>" class="delete-form mb-2"><?= csrf_field() ?><input type="hidden" name="status" value="cancelled"><button class="btn btn-outline-danger w-100"><i class="fas fa-times me-1"></i>Cancelar Pedido</button></form>
            <?php endif; ?>
            <hr>
            <form method="POST" action="<?= url('admin/orders/' . $order['id'] . '/delete') ?>" onsubmit="return confirm('&iquest;Eliminar este pedido permanentemente? Esta acci&oacute;n no se puede deshacer.')"><?= csrf_field() ?><button class="btn btn-danger w-100"><i class="fas fa-trash me-1"></i>Eliminar Pedido</button></form>
        </div></div>
    </div>
</div>
