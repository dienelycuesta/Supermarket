<?php $layout = 'admin/layouts/main'; ?>
<h4 class="mb-4">Pagos</h4>
<div class="row mb-4">
    <?php foreach($totals as $t): ?>
    <div class="col-md-3 mb-3"><div class="card"><div class="card-body text-center">
        <div class="text-muted small"><?= ($t['payment_method']==='cash'?'Efectivo':($t['payment_method']==='card_pos'?'Tarjeta POS':($t['payment_method']==='stripe'?'Stripe':($t['payment_method']==='bank_transfer'?'Transferencia':e(ucfirst(str_replace('_',' ',$t['payment_method']))))))) ?></div>
        <div class="h5 mb-0"><?= formatMoney($t['total']) ?></div>
        <small class="text-muted"><?= $t['count'] ?> pagos</small>
    </div></div></div>
    <?php endforeach; ?>
</div>
<div class="card mb-3"><div class="card-body">
<form method="GET" class="row g-2 align-items-end">
    <div class="col-md-3"><label class="form-label">Inicio</label><input type="date" name="start_date" class="form-control" value="<?= e($startDate) ?>"></div>
    <div class="col-md-3"><label class="form-label">Fin</label><input type="date" name="end_date" class="form-control" value="<?= e($endDate) ?>"></div>
    <div class="col-md-3"><label class="form-label">M&eacute;todo</label><select name="method" class="form-select"><option value="">Todos</option><?php foreach(['cash','card_pos','stripe','bank_transfer'] as $m): ?><option value="<?= $m ?>" <?= ($method??'')===$m?'selected':'' ?>><?= ($m==='cash'?'Efectivo':($m==='card_pos'?'Tarjeta POS':($m==='stripe'?'Stripe':($m==='bank_transfer'?'Transferencia':ucfirst(str_replace('_',' ',$m)))))) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-3"><button type="submit" class="btn btn-outline-success w-100">Filtrar</button></div>
</form></div></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th>ID</th><th>Pedido</th><th>M&eacute;todo</th><th>Monto</th><th>Estado</th><th>Referencia</th><th>Fecha</th></tr></thead><tbody>
<?php foreach($payments as $p): ?>
<tr><td><?= $p['id'] ?></td><td><a href="<?= url('admin/orders/' . $p['order_id']) ?>">#<?= $p['order_id'] ?></a></td>
<td><span class="badge bg-<?= badgeColor($p['payment_method'], ['cash'=>'success','card_pos'=>'primary','stripe'=>'info','bank_transfer'=>'secondary'], 'dark') ?>"><?= ($p['payment_method']==='cash'?'efectivo':($p['payment_method']==='card_pos'?'tarjeta POS':($p['payment_method']==='stripe'?'stripe':($p['payment_method']==='bank_transfer'?'transferencia':e(str_replace('_',' ',$p['payment_method'])))))) ?></span></td>
<td><?= formatMoney($p['amount']) ?></td><td><span class="badge bg-<?= $p['status']==='completed'?'success':($p['status']==='pending'?'warning text-dark':'danger') ?>"><?= ($p['status']==='completed'?'completado':($p['status']==='pending'?'pendiente':($p['status']==='paid'?'pagado':($p['status']==='partial'?'parcial':($p['status']==='refunded'?'reembolsado':e($p['status'])))))) ?></span></td>
<td><?= e($p['reference'] ?? '-') ?></td><td class="small"><?= e(date('M j, H:i', strtotime($p['created_at']))) ?></td></tr>
<?php endforeach; ?></tbody></table></div></div></div>
