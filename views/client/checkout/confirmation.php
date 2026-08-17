<?php $layout = 'client/layouts/main';
$paymentLabels = ['cash' => 'Efectivo / Pago en tienda', 'bank_transfer' => 'Transferencia Bancaria', 'stripe' => 'Tarjeta'];
$paymentLabel = $paymentLabels[$order['payment_method']] ?? ucfirst(str_replace('_', ' ', $order['payment_method'] ?? ''));
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-4">
                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width:80px;height:80px"><i class="fas fa-check fa-3x"></i></div>
                <h2 class="mt-3">&iexcl;Pedido Confirmado!</h2>
                <p class="text-muted">Gracias por tu compra. Tu n&uacute;mero de pedido es:</p>
                <h3 class="text-success fw-bold"><?= e($order['order_number']) ?></h3>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white"><strong>Detalles del Pedido</strong></div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6 mb-2"><span class="text-muted small">Fecha</span><br><strong><?= date('d/m/Y h:i A', strtotime($order['created_at'])) ?></strong></div>
                        <div class="col-sm-6 mb-2"><span class="text-muted small">Estado</span><br><span class="badge bg-warning text-dark">Pendiente</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-6 mb-2"><span class="text-muted small">M&eacute;todo de Pago</span><br><strong><?= e($paymentLabel) ?></strong></div>
                        <div class="col-sm-6 mb-2"><span class="text-muted small">Total</span><br><strong class="text-success fs-5"><?= formatMoney($order['total_amount']) ?></strong></div>
                    </div>
                    <?php if ($order['shipping_address']): ?>
                    <div class="mb-2">
                        <span class="text-muted small">Direcci&oacute;n de Entrega</span><br>
                        <strong><i class="fas fa-map-marker-alt text-success me-1"></i><?= e($order['shipping_address']) ?></strong>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($order['payment_method'] === 'bank_transfer'): ?>
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white"><i class="fas fa-university me-2"></i>Instrucciones de Transferencia</div>
                <div class="card-body">
                    <p>Por favor transfiera <strong class="text-success fs-5"><?= formatMoney($order['total_amount']) ?></strong> a la siguiente cuenta:</p>
                    <div class="bg-light rounded p-3 mb-3">
                        <div class="row small">
                            <div class="col-sm-6 mb-2"><span class="text-muted">Banco</span><br><strong>Banco Popular Dominicano</strong></div>
                            <div class="col-sm-6 mb-2"><span class="text-muted">Tipo de Cuenta</span><br><strong>Cuenta Corriente</strong></div>
                            <div class="col-sm-6 mb-2"><span class="text-muted">No. de Cuenta</span><br><strong class="text-primary user-select-all fs-6">823-456789-0</strong></div>
                            <div class="col-sm-6 mb-2"><span class="text-muted">Titular</span><br><strong><?= APP_NAME ?></strong></div>
                            <div class="col-sm-6 mb-2"><span class="text-muted">RNC</span><br><strong class="user-select-all">130-12345-6</strong></div>
                            <div class="col-sm-6 mb-2"><span class="text-muted">Referencia</span><br><strong class="text-primary"><?= e($order['order_number']) ?></strong></div>
                        </div>
                    </div>
                    <div class="alert alert-warning mb-0 small"><i class="fas fa-exclamation-triangle me-1"></i>Incluya el n&uacute;mero de pedido <strong><?= e($order['order_number']) ?></strong> como concepto/referencia. Su pedido ser&aacute; procesado una vez se confirme la transferencia.</div>
                </div>
            </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header bg-white"><strong>Art&iacute;culos (<?= count($orderItems) ?>)</strong></div>
                <div class="card-body p-0">
                    <?php foreach ($orderItems as $item): ?>
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <div class="flex-grow-1">
                            <strong><?= e($item['product_name']) ?></strong>
                            <div class="small text-muted"><?= $item['quantity'] ?> x <?= formatMoney($item['unit_price']) ?></div>
                        </div>
                        <div class="fw-bold"><?= formatMoney($item['subtotal']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between"><span>Subtotal</span><span><?= formatMoney($order['subtotal']) ?></span></li>
                    <li class="list-group-item d-flex justify-content-between"><span>ITBIS (18%)</span><span><?= formatMoney($order['tax_amount']) ?></span></li>
                    <li class="list-group-item d-flex justify-content-between bg-light"><strong>Total</strong><strong class="text-success"><?= formatMoney($order['total_amount']) ?></strong></li>
                </ul>
            </div>

            <div class="text-center">
                <?php $wsUrl = buildWhatsAppOrderUrl($order, $orderItems); ?>
                <a href="<?= $wsUrl ?>" target="_blank" class="btn btn-success me-2"><i class="fab fa-whatsapp me-2"></i>Enviar por WhatsApp</a>
                <a href="<?= url('my-orders') ?>" class="btn btn-outline-success me-2"><i class="fas fa-list me-1"></i>Ver Mis Pedidos</a>
                <a href="<?= url('catalog') ?>" class="btn btn-outline-secondary"><i class="fas fa-shopping-bag me-1"></i>Seguir Comprando</a>
            </div>
        </div>
    </div>
</div>
