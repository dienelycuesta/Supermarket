<?php $layout = 'client/layouts/main'; ?>
<div class="container py-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Inicio</a></li><li class="breadcrumb-item active">Mis Pedidos</li></ol></nav>
    <h4 class="mb-4">Mis Pedidos</h4>
    <?php if(empty($data)): ?>
    <div class="text-center py-5"><i class="fas fa-receipt fa-4x text-muted mb-3"></i><h5 class="text-muted">Sin pedidos a&uacute;n</h5><a href="<?= url('catalog') ?>" class="btn btn-success mt-3">Empezar a Comprar</a></div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Pedido #</th><th>Fecha</th><th class="d-none d-sm-table-cell">Art&iacute;culos</th><th>Total</th><th>Estado</th><th class="d-none d-md-table-cell">Pago</th><th></th></tr></thead>
            <tbody>
            <?php foreach($data as $o): ?>
            <tr>
                <td><strong><?= e($o['order_number']) ?></strong></td>
                <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                <td class="d-none d-sm-table-cell"><?= $o['item_count'] ?? '-' ?></td>
                <td class="fw-bold"><?= formatMoney($o['total_amount']) ?></td>
                <td>
                    <?php
                    $statusColors = ['pending'=>'warning','confirmed'=>'info','processing'=>'primary','ready'=>'secondary','delivered'=>'success','completed'=>'success','cancelled'=>'danger'];
                    $color = $statusColors[$o['status']] ?? 'secondary';
                    ?>
                    <span class="badge bg-<?= $color ?>"><?= ucfirst($o['status']) ?></span>
                </td>
                <td class="d-none d-md-table-cell">
                    <?php
                    $payColors = ['paid'=>'success','partial'=>'warning','pending'=>'danger','refunded'=>'secondary'];
                    $pc = $payColors[$o['payment_status']] ?? 'secondary';
                    ?>
                    <span class="badge bg-<?= $pc ?>"><?= ucfirst($o['payment_status']) ?></span>
                </td>
                <td><a href="<?= url('orders/' . $o['id']) ?>" class="btn btn-sm btn-outline-success">Ver</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if(isset($paginator) && $paginator): ?><?= $paginator->render() ?><?php endif; ?>
    <?php endif; ?>
</div>
