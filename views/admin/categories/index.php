<?php $layout = 'admin/layouts/main'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Categorías</h4>
    <a href="<?= url('admin/categories/create') ?>" class="btn btn-success"><i class="fas fa-plus me-1"></i>Agregar Categoría</a>
</div>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>ID</th><th>Nombre</th><th>Slug</th><th>Productos</th><th>Orden</th><th>Estado</th><th>Acciones</th></tr></thead>
                <tbody>
                <?php foreach ($categories as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><strong><?= e($c['name']) ?></strong></td>
                    <td><code><?= e($c['slug']) ?></code></td>
                    <td><span class="badge bg-info"><?= $c['product_count'] ?></span></td>
                    <td><?= $c['sort_order'] ?></td>
                    <td><span class="badge bg-<?= $c['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($c['status']) ?></span></td>
                    <td>
                        <a href="<?= url('admin/categories/' . $c['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="<?= url('admin/categories/' . $c['id'] . '/delete') ?>" class="d-inline delete-form"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
