<?php $layout = 'admin/layouts/main'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Productos</h4>
    <a href="<?= url('admin/products/create') ?>" class="btn btn-success"><i class="fas fa-plus me-1"></i>Agregar Producto</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Buscar nombre, SKU, código de barras..." value="<?= e($search) ?>"></div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">Todas las Categorías</option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $categoryId == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2"><button type="submit" class="btn btn-outline-success w-100">Filtrar</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="productsTable">
                <thead><tr><th width="60">Imagen</th><th>SKU</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Estado</th><th width="120">Acciones</th></tr></thead>
                <tbody>
                <?php foreach ($data as $p): ?>
                <tr>
                    <td><?php if ($p['image']): ?><img src="<?= e(UPLOAD_URL . $p['image']) ?>" width="45" height="45" class="rounded" style="object-fit:cover"><?php else: ?><div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:45px;height:45px"><i class="fas fa-box text-muted"></i></div><?php endif; ?></td>
                    <td><code><?= e($p['sku']) ?></code></td>
                    <td><a href="<?= url('admin/products/' . $p['id']) ?>"><?= e($p['name']) ?></a></td>
                    <td><?= e($p['category_name'] ?? '-') ?></td>
                    <td><?= formatMoney($p['sale_price']) ?></td>
                    <td><span class="badge bg-<?= $p['stock'] == 0 ? 'danger' : ($p['stock'] <= $p['min_stock'] ? 'warning text-dark' : 'success') ?>"><?= $p['stock'] ?></span></td>
                    <td><span class="badge bg-<?= $p['is_active'] ? 'success' : 'secondary' ?>"><?= $p['is_active'] ? 'Activo' : 'Inactivo' ?></span></td>
                    <td>
                        <a href="<?= url('admin/products/' . $p['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="<?= url('admin/products/' . $p['id'] . '/delete') ?>" class="d-inline delete-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if (isset($paginator)): ?><div class="mt-3"><?= $paginator->render() ?></div><?php endif; ?>
