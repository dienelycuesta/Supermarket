<?php $layout = 'client/layouts/main'; ?>
<div class="container py-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Inicio</a></li><li class="breadcrumb-item"><a href="<?= url('orders') ?>">Mis Pedidos</a></li><li class="breadcrumb-item active"><?= e($order['order_number']) ?></li></ol></nav>
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Pedido <?= e($order['order_number']) ?></strong>
                    <?php
                    $statusColors = ['pending'=>'warning','confirmed'=>'info','processing'=>'primary','ready'=>'secondary','delivered'=>'success','completed'=>'success','cancelled'=>'danger'];
                    $color = $statusColors[$order['status']] ?? 'secondary';
                    ?>
                    <span class="badge bg-<?= $color ?> fs-6"><?= ucfirst($order['status']) ?></span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3"><strong>Fecha:</strong><br><?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></div>
                        <div class="col-sm-6 mb-3"><strong>M&eacute;todo de Pago:</strong><br><?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?></div>
                        <div class="col-sm-6 mb-3"><strong>Estado del Pago:</strong><br>
                            <?php $payColors = ['paid'=>'success','partial'=>'warning','pending'=>'danger','refunded'=>'secondary']; ?>
                            <span class="badge bg-<?= $payColors[$order['payment_status']] ?? 'secondary' ?>"><?= ucfirst($order['payment_status']) ?></span>
                        </div>
                        <?php if($order['shipping_address']): ?>
                        <div class="col-sm-6 mb-3"><strong>Direcci&oacute;n de Entrega:</strong><br><?= e($order['shipping_address']) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if($order['notes']): ?><div class="mt-2"><strong>Notas:</strong><br><?= e($order['notes']) ?></div><?php endif; ?>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><strong>Art&iacute;culos</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>Producto</th><th>Precio</th><th>Cant</th><th>Subtotal</th></tr></thead>
                            <tbody>
                            <?php foreach($orderItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if(!empty($item['image'])): ?><img src="<?= e(UPLOAD_URL . $item['image']) ?>" class="rounded me-2 d-none d-sm-inline-block" style="width:40px;height:40px;object-fit:cover"><?php endif; ?>
                                        <span><?= e($item['product_name']) ?></span>
                                    </div>
                                </td>
                                <td><?= formatMoney($item['unit_price']) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td class="fw-bold"><?= formatMoney($item['subtotal']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card sticky-top" style="top:80px">
                <div class="card-header"><strong>Resumen del Pedido</strong></div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between"><span>Subtotal</span><span><?= formatMoney($order['subtotal']) ?></span></li>
                    <li class="list-group-item d-flex justify-content-between"><span>Impuesto</span><span><?= formatMoney($order['tax_amount']) ?></span></li>
                    <?php if($order['discount_amount'] > 0): ?>
                    <li class="list-group-item d-flex justify-content-between text-danger"><span>Descuento</span><span>-<?= formatMoney($order['discount_amount']) ?></span></li>
                    <?php endif; ?>
                    <li class="list-group-item d-flex justify-content-between"><strong class="fs-5">Total</strong><strong class="fs-5 text-success"><?= formatMoney($order['total_amount']) ?></strong></li>
                </ul>
                <div class="card-body">
                    <a href="<?= url('orders') ?>" class="btn btn-outline-secondary w-100"><i class="fas fa-arrow-left me-1"></i>Volver a Pedidos</a>
                </div>
            </div>
        </div>
    </div>
</div>
