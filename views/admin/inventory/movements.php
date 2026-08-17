<?php $layout = 'admin/layouts/main'; ?>
<div class="d-flex justify-content-between mb-4">
    <h4>Movimientos de Inventario <?= $product ? '- ' . e($product['name']) : '' ?></h4>
    <a href="<?= url('admin/inventory') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a>
</div>
<div class="card mb-3"><div class="card-body">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label">Fecha Inicio</label><input type="date" name="start_date" class="form-control" value="<?= e($startDate) ?>"></div>
        <div class="col-md-3"><label class="form-label">Fecha Fin</label><input type="date" name="end_date" class="form-control" value="<?= e($endDate) ?>"></div>
        <div class="col-md-3"><label class="form-label">Tipo</label><select name="type" class="form-select"><option value="">Todos</option><?php foreach(['entry'=>'Entrada','exit'=>'Salida','adjustment'=>'Ajuste','sale'=>'Venta','return'=>'Devolución'] as $t => $label): ?><option value="<?= $t ?>" <?= $type===$t?'selected':'' ?>><?= $label ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><button type="submit" class="btn btn-outline-success w-100">Filtrar</button></div>
    </form>
</div></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive">
<table class="table table-hover mb-0"><thead><tr><th>Fecha</th><th>Producto</th><th>Tipo</th><th>Cant</th><th>Anterior</th><th>Nuevo</th><th>Notas</th><th>Por</th></tr></thead><tbody>
<?php foreach ($movements as $m): ?>
<tr>
    <td class="small"><?= e(date('M j, H:i', strtotime($m['created_at']))) ?></td>
    <td><?= e($m['product_name'] ?? '') ?></td>
    <td><span class="badge bg-<?= badgeColor($m['type'], ['entry'=>'success','exit'=>'danger','sale'=>'warning text-dark','return'=>'info']) ?>"><?= ['entry'=>'entrada','exit'=>'salida','adjustment'=>'ajuste','sale'=>'venta','return'=>'devolución'][$m['type']] ?? e($m['type']) ?></span></td>
    <td><?= $m['quantity'] > 0 ? '+' : '' ?><?= $m['quantity'] ?></td>
    <td><?= $m['previous_stock'] ?></td><td><?= $m['new_stock'] ?></td>
    <td class="small"><?= e($m['notes'] ?? '') ?></td>
    <td class="small"><?= e(($m['first_name']??'') . ' ' . ($m['last_name']??'')) ?></td>
</tr>
<?php endforeach; ?>
<?php if(empty($movements)): ?><tr><td colspan="8" class="text-center text-muted py-3">Sin movimientos encontrados</td></tr><?php endif; ?>
</tbody></table></div></div></div>
<?php if(isset($paginator) && $paginator): ?><div class="mt-3"><?= $paginator->render() ?></div><?php endif; ?>
