<?php $layout = 'admin/layouts/main'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><?= e($product['name']) ?></h4>
    <div>
        <a href="<?= url('admin/products/' . $product['id'] . '/edit') ?>" class="btn btn-outline-primary"><i class="fas fa-edit me-1"></i>Editar</a>
        <a href="<?= url('admin/products') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a>
    </div>
</div>
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <?php if ($product['image']): ?><img src="<?= e(UPLOAD_URL . $product['image']) ?>" class="img-fluid rounded mb-3" style="max-height:250px"><?php else: ?><div class="bg-light rounded p-5 mb-3"><i class="fas fa-box fa-4x text-muted"></i></div><?php endif; ?>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header">Detalles</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between"><span>SKU</span><code><?= e($product['sku']) ?></code></li>
                <li class="list-group-item d-flex justify-content-between"><span>Código de Barras</span><span><?= e($product['barcode'] ?? 'N/A') ?></span></li>
                <li class="list-group-item d-flex justify-content-between"><span>Categoría</span><span><?= e($product['category_name'] ?? 'N/A') ?></span></li>
                <li class="list-group-item d-flex justify-content-between"><span>Proveedor</span><span><?= e($product['supplier_name'] ?? 'N/A') ?></span></li>
                <li class="list-group-item d-flex justify-content-between"><span>Marca</span><span><?= e($product['brand'] ?? 'N/A') ?></span></li>
            </ul>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Precios</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"><span>Costo</span><span><?= formatMoney($product['cost_price']) ?></span></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Precio de Venta</span><strong class="text-success"><?= formatMoney($product['sale_price']) ?></strong></li>
                        <?php if ($product['compare_price']): ?><li class="list-group-item d-flex justify-content-between"><span>Comparación</span><span class="text-decoration-line-through"><?= formatMoney($product['compare_price']) ?></span></li><?php endif; ?>
                        <li class="list-group-item d-flex justify-content-between"><span>Impuesto</span><span><?= e($product['tax_rate']) ?>%</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Stock</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"><span>Actual</span><span class="badge bg-<?= $product['stock'] <= $product['min_stock'] ? ($product['stock'] == 0 ? 'danger' : 'warning text-dark') : 'success' ?> fs-6"><?= $product['stock'] ?></span></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Mín</span><span><?= $product['min_stock'] ?></span></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Máx</span><span><?= $product['max_stock'] ?></span></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Unidad</span><span><?= e($product['unit']) ?></span></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Movimientos de Inventario -->
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h6 class="mb-0">Movimientos de Inventario Recientes</h6>
                <a href="<?= url('admin/inventory/movements?product_id=' . $product['id']) ?>" class="btn btn-sm btn-outline-success">Ver Todo</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Fecha</th><th>Tipo</th><th>Cant</th><th>Stock</th><th>Notas</th><th>Por</th></tr></thead>
                        <tbody>
                        <?php foreach (array_slice($movements, 0, 10) as $m): ?>
                        <tr>
                            <td class="small"><?= e(date('M j, H:i', strtotime($m['created_at']))) ?></td>
                            <td><span class="badge bg-<?= badgeColor($m['type'], ['entry'=>'success','exit'=>'danger','sale'=>'warning text-dark','return'=>'info']) ?>"><?= e($m['type']) ?></span></td>
                            <td><?= $m['quantity'] > 0 ? '+' : '' ?><?= $m['quantity'] ?></td>
                            <td><?= $m['previous_stock'] ?> &rarr; <?= $m['new_stock'] ?></td>
                            <td class="small"><?= e($m['notes'] ?? '') ?></td>
                            <td class="small"><?= e(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? '')) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($movements)): ?><tr><td colspan="6" class="text-center text-muted py-3">Sin movimientos aún</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
