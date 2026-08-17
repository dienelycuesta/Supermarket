<?php $layout = 'client/layouts/main'; ?>
<div class="container py-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Inicio</a></li><li class="breadcrumb-item"><a href="<?= url('catalog') ?>">Cat&aacute;logo</a></li><?php if($category): ?><li class="breadcrumb-item"><a href="<?= url('catalog?category=' . $category['id']) ?>"><?= e($category['name']) ?></a></li><?php endif; ?><li class="breadcrumb-item active"><?= e($product['name']) ?></li></ol></nav>
    <div class="row">
        <div class="col-lg-6 mb-4">
            <?php if($product['image']): ?><img src="<?= e(UPLOAD_URL . $product['image']) ?>" class="img-fluid rounded shadow" style="width:100%;max-height:500px;object-fit:cover"><?php else: ?><div class="bg-light rounded d-flex align-items-center justify-content-center shadow" style="height:400px"><i class="fas fa-box fa-5x text-muted"></i></div><?php endif; ?>
        </div>
        <div class="col-lg-6">
            <?php if($category): ?><span class="badge bg-success mb-2"><?= e($category['name']) ?></span><?php endif; ?>
            <h2><?= e($product['name']) ?></h2>
            <p class="text-muted">SKU: <?= e($product['sku']) ?> <?= $product['brand'] ? '| Marca: ' . e($product['brand']) : '' ?></p>
            <div class="mb-3">
                <?php if($product['compare_price'] && $product['compare_price'] > $product['sale_price']): ?>
                <span class="text-decoration-line-through text-muted fs-5"><?= formatMoney($product['compare_price']) ?></span>
                <?php endif; ?>
                <span class="fs-2 fw-bold text-success"><?= formatMoney($product['sale_price']) ?></span>
            </div>
            <p class="small text-muted">ITBIS (<?= e($product['tax_rate']) ?>%) incluido</p>

            <?php if($product['stock'] > 0): ?>
            <p class="text-success"><i class="fas fa-check-circle me-1"></i>En Stock (<?= $product['stock'] ?> disponibles)</p>
            <div class="d-flex align-items-center gap-2 gap-sm-3 mb-4 flex-wrap">
                <div class="input-group" style="width:130px;min-width:100px;max-width:150px">
                    <button class="btn btn-outline-secondary" type="button" onclick="let i=document.getElementById('qty');i.value=Math.max(1,parseInt(i.value)-1)">-</button>
                    <input type="number" class="form-control text-center" id="qty" value="1" min="1" max="<?= $product['stock'] ?>">
                    <button class="btn btn-outline-secondary" type="button" onclick="let i=document.getElementById('qty');i.value=Math.min(<?= $product['stock'] ?>,parseInt(i.value)+1)">+</button>
                </div>
                <button class="btn btn-success btn-lg add-to-cart" data-id="<?= $product['id'] ?>" data-qty-input="qty"><i class="fas fa-cart-plus me-2"></i>Agregar al Carrito</button>
            </div>
            <?php else: ?>
            <p class="text-danger"><i class="fas fa-times-circle me-1"></i>Agotado</p>
            <?php endif; ?>

            <?php if($product['description']): ?>
            <hr>
            <h5>Descripci&oacute;n</h5>
            <p><?= nl2br(e($product['description'])) ?></p>
            <?php endif; ?>

            <div class="small text-muted mt-3">
                <?php if($product['weight']): ?>Peso: <?= e($product['weight']) ?> <?= e($product['unit']) ?><br><?php endif; ?>
                Unidad: <?= e($product['unit']) ?>
            </div>
        </div>
    </div>

    <?php if(!empty($relatedProducts)): ?>
    <hr class="my-5">
    <h4 class="mb-4">Tambi&eacute;n Te Puede Gustar</h4>
    <div class="row">
        <?php foreach(array_slice($relatedProducts, 0, 4) as $rp): ?>
        <?php if($rp['id'] == $product['id']) continue; ?>
        <div class="col-lg-3 col-md-4 col-6 mb-4">
            <div class="card product-card h-100 border-0 shadow-sm">
                <a href="<?= url('product/' . $rp['slug']) ?>">
                    <?php if($rp['image']): ?><img src="<?= e(UPLOAD_URL . $rp['image']) ?>" class="card-img-top" style="height:180px;object-fit:cover"><?php else: ?><div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:180px"><i class="fas fa-box fa-3x text-muted"></i></div><?php endif; ?>
                </a>
                <div class="card-body">
                    <h6><a href="<?= url('product/' . $rp['slug']) ?>" class="text-decoration-none text-dark"><?= e($rp['name']) ?></a></h6>
                    <span class="fw-bold text-success"><?= formatMoney($rp['sale_price']) ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
