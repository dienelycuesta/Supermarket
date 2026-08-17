<?php $layout = 'client/layouts/main'; ?>
<div class="container py-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Inicio</a></li><li class="breadcrumb-item active"><?= e($pageTitle) ?></li></ol></nav>
    <h4 class="mb-3"><?= e($pageTitle) ?> <small class="text-muted">(<?= $totalProducts ?> productos)</small></h4>
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card"><div class="card-header"><strong>Categor&iacute;as</strong></div>
            <div class="list-group list-group-flush">
                <a href="<?= url('catalog') ?>" class="list-group-item list-group-item-action <?= !$categoryId ? 'active' : '' ?>">Todas las Categor&iacute;as</a>
                <?php foreach($categories as $c): ?>
                <a href="<?= url('catalog?category=' . $c['id']) ?>" class="list-group-item list-group-item-action d-flex justify-content-between <?= $categoryId == $c['id'] ? 'active' : '' ?>"><?= e($c['name']) ?> <span class="badge bg-secondary rounded-pill"><?= $c['product_count'] ?? 0 ?></span></a>
                <?php endforeach; ?>
            </div></div>
            <div class="card mt-3"><div class="card-header"><strong>Ordenar por</strong></div><div class="card-body">
            <select class="form-select" onchange="location.href=this.value">
                <?php foreach(['name_asc'=>'Nombre A-Z','name_desc'=>'Nombre Z-A','price_asc'=>'Precio Menor-Mayor','price_desc'=>'Precio Mayor-Menor','newest'=>'M&aacute;s Recientes'] as $k=>$v): ?>
                <option value="<?= url('catalog?category=' . $categoryId . '&search=' . urlencode($search) . '&sort=' . $k) ?>" <?= $sort===$k?'selected':'' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select></div></div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <?php if($search): ?><div class="alert alert-info">Mostrando resultados para "<strong><?= e($search) ?></strong>" <a href="<?= url('catalog' . ($categoryId ? '?category=' . $categoryId : '')) ?>" class="ms-2">Limpiar</a></div><?php endif; ?>
            <?php if(empty($products)): ?>
            <div class="text-center py-5"><i class="fas fa-search fa-3x text-muted mb-3"></i><h5 class="text-muted">No se encontraron productos</h5><a href="<?= url('catalog') ?>" class="btn btn-outline-success">Ver Todos</a></div>
            <?php else: ?>
            <div class="row">
                <?php foreach($products as $p): ?>
                <div class="col-md-4 col-6 mb-4">
                    <div class="card product-card h-100 border-0 shadow-sm">
                        <a href="<?= url('product/' . $p['slug']) ?>">
                            <?php if($p['image']): ?><img src="<?= e(UPLOAD_URL . $p['image']) ?>" class="card-img-top" style="height:180px;object-fit:cover"><?php else: ?><div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:180px"><i class="fas fa-box fa-3x text-muted"></i></div><?php endif; ?>
                        </a>
                        <div class="card-body d-flex flex-column">
                            <small class="text-muted"><?= e($p['category_name'] ?? '') ?></small>
                            <h6 class="card-title mt-1"><a href="<?= url('product/' . $p['slug']) ?>" class="text-decoration-none text-dark"><?= e($p['name']) ?></a></h6>
                            <div class="mt-auto">
                                <div class="mb-2">
                                    <?php if($p['compare_price'] && $p['compare_price'] > $p['sale_price']): ?><span class="text-decoration-line-through text-muted small"><?= formatMoney($p['compare_price']) ?></span><?php endif; ?>
                                    <span class="fw-bold text-success"><?= formatMoney($p['sale_price']) ?></span>
                                </div>
                                <?php if($p['stock'] > 0): ?><button class="btn btn-success btn-sm w-100 add-to-cart" data-id="<?= $p['id'] ?>"><i class="fas fa-cart-plus me-1"></i>Agregar al Carrito</button>
                                <?php else: ?><button class="btn btn-secondary btn-sm w-100" disabled>Agotado</button><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php if($paginator): ?><?= $paginator->render() ?><?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
