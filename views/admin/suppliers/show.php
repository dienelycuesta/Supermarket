<?php $layout = 'admin/layouts/main'; ?>
<div class="d-flex justify-content-between mb-4"><h4><?= e($supplier['name']) ?></h4><a href="<?= url('admin/suppliers') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a></div>
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card"><div class="card-header">Info del Proveedor</div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Contacto:</strong> <?= e($supplier['contact_person'] ?? 'N/A') ?></li>
            <li class="list-group-item"><strong>Correo:</strong> <?= e($supplier['email'] ?? 'N/A') ?></li>
            <li class="list-group-item"><strong>Teléfono:</strong> <?= e($supplier['phone'] ?? 'N/A') ?></li>
            <li class="list-group-item"><strong>RNC:</strong> <?= e($supplier['rnc'] ?? 'N/A') ?></li>
            <li class="list-group-item"><strong>Ciudad:</strong> <?= e($supplier['city'] ?? 'N/A') ?></li>
            <li class="list-group-item"><strong>Dirección:</strong> <?= e($supplier['address'] ?? 'N/A') ?></li>
        </ul></div>
    </div>
    <div class="col-md-8">
        <div class="card"><div class="card-header">Productos de este proveedor</div><div class="card-body p-0"><div class="table-responsive">
        <table class="table table-sm mb-0"><thead><tr><th>Nombre</th><th>SKU</th><th>Precio</th><th>Stock</th></tr></thead><tbody>
        <?php foreach ($products as $p): ?><tr><td><?= e($p['name']) ?></td><td><code><?= e($p['sku']) ?></code></td><td><?= formatMoney($p['sale_price']) ?></td><td><?= $p['stock'] ?></td></tr><?php endforeach; ?>
        <?php if (empty($products)): ?><tr><td colspan="4" class="text-center text-muted py-3">Sin productos</td></tr><?php endif; ?>
        </tbody></table></div></div></div>
    </div>
</div>
