<?php $layout = 'admin/layouts/main'; ?>
<div class="d-flex justify-content-between mb-4"><h4>Registrar Pago - Pedido #<?= e($order['order_number']) ?></h4><a href="<?= url('admin/orders/' . $order['id']) ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a></div>
<div class="row"><div class="col-lg-6">
<div class="card mb-3"><div class="card-header">Resumen del Pedido</div><ul class="list-group list-group-flush">
    <li class="list-group-item d-flex justify-content-between"><span>Total del Pedido</span><strong><?= formatMoney($order['total_amount']) ?></strong></li>
    <li class="list-group-item d-flex justify-content-between"><span>Pagado</span><span class="text-success"><?= formatMoney($paidAmount) ?></span></li>
    <li class="list-group-item d-flex justify-content-between"><span>Saldo Pendiente</span><strong class="text-danger"><?= formatMoney($balance) ?></strong></li>
</ul></div>
<div class="card"><div class="card-body">
<form method="POST" action="<?= url('admin/payments/' . $order['id'] . '/store') ?>">
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">M&eacute;todo de Pago</label><select name="payment_method" class="form-select" required><option value="cash">Efectivo</option><option value="card_pos">Tarjeta (POS)</option><option value="bank_transfer">Transferencia</option></select></div>
    <div class="mb-3"><label class="form-label">Monto</label><input type="number" name="amount" class="form-control" step="0.01" value="<?= number_format($balance, 2, '.', '') ?>" required></div>
    <div class="mb-3"><label class="form-label">Referencia</label><input type="text" name="reference" class="form-control" placeholder="Recibo #, ref. transferencia, etc."></div>
    <div class="mb-3"><label class="form-label">Notas</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
    <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i>Registrar Pago</button>
</form></div></div>
</div></div>
