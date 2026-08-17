<?php $layout = 'client/layouts/main'; ?>

<!-- Categories -->
<?php if (!empty($categories)): ?>
<div class="categories-bar">
    <div class="container">
        <div class="categories-scroll">
            <a href="<?= url('catalog') ?>" class="cat-chip active">
                <i class="fas fa-border-all"></i> Todos
            </a>
            <?php
            $icons = ['fas fa-apple-whole','fas fa-drumstick-bite','fas fa-fish','fas fa-cheese','fas fa-bread-slice','fas fa-wine-bottle','fas fa-pump-soap','fas fa-cookie-bite','fas fa-ice-cream','fas fa-pepper-hot','fas fa-egg','fas fa-mug-hot','fas fa-jar','fas fa-bottle-water'];
            $i = 0;
            foreach($categories as $cat):
                $icon = $icons[$i % count($icons)];
                $i++;
            ?>
            <a href="<?= url('catalog?category=' . $cat['id']) ?>" class="cat-chip">
                <i class="<?= $icon ?>"></i> <?= e($cat['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Ofertas -->
<?php if (!empty($onSaleProducts)): ?>
<section class="section-block">
    <div class="container">
        <div class="section-head">
            <h5 class="section-title"><span class="title-accent sale"></span>Ofertas del D&iacute;a</h5>
            <a href="<?= url('catalog?sort=price_asc') ?>" class="see-all">Ver todas <i class="fas fa-chevron-right ms-1"></i></a>
        </div>
        <div class="row g-3">
            <?php foreach(array_slice($onSaleProducts, 0, 5) as $p):
                $discount = round((($p['compare_price'] - $p['sale_price']) / $p['compare_price']) * 100);
            ?>
            <div class="col-6 col-md-4 col-lg">
                <div class="card prod-card h-100">
                    <div class="prod-img-wrap">
                        <span class="discount-badge">-<?= $discount ?>%</span>
                        <a href="<?= url('product/' . $p['slug']) ?>">
                            <?php if($p['image']): ?>
                            <img src="<?= e(UPLOAD_URL . $p['image']) ?>" class="prod-img" alt="<?= e($p['name']) ?>">
                            <?php else: ?>
                            <div class="prod-img prod-img-placeholder"><i class="fas fa-box fa-2x text-muted"></i></div>
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="prod-info">
                        <?php if($p['category_name']): ?><span class="prod-cat"><?= e($p['category_name']) ?></span><?php endif; ?>
                        <a href="<?= url('product/' . $p['slug']) ?>" class="prod-name"><?= e($p['name']) ?></a>
                        <div class="prod-prices">
                            <span class="prod-price-now sale"><?= formatMoney($p['sale_price']) ?></span>
                            <span class="prod-price-old"><?= formatMoney($p['compare_price']) ?></span>
                        </div>
                        <button class="btn btn-sm btn-add w-100 add-to-cart" data-id="<?= $p['id'] ?>"><i class="fas fa-plus me-1"></i>Agregar</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Destacados -->
<?php if (!empty($featuredProducts)): ?>
<section class="section-block alt">
    <div class="container">
        <div class="section-head">
            <h5 class="section-title"><span class="title-accent featured"></span>Destacados</h5>
            <a href="<?= url('catalog') ?>" class="see-all">Ver todos <i class="fas fa-chevron-right ms-1"></i></a>
        </div>
        <div class="row g-3">
            <?php foreach($featuredProducts as $p): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card prod-card h-100">
                    <div class="prod-img-wrap">
                        <?php if($p['compare_price'] && $p['compare_price'] > $p['sale_price']):
                            $d = round((($p['compare_price'] - $p['sale_price']) / $p['compare_price']) * 100);
                        ?>
                        <span class="discount-badge">-<?= $d ?>%</span>
                        <?php endif; ?>
                        <a href="<?= url('product/' . $p['slug']) ?>">
                            <?php if($p['image']): ?>
                            <img src="<?= e(UPLOAD_URL . $p['image']) ?>" class="prod-img" alt="<?= e($p['name']) ?>">
                            <?php else: ?>
                            <div class="prod-img prod-img-placeholder"><i class="fas fa-box fa-2x text-muted"></i></div>
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="prod-info">
                        <?php if($p['category_name']): ?><span class="prod-cat"><?= e($p['category_name']) ?></span><?php endif; ?>
                        <a href="<?= url('product/' . $p['slug']) ?>" class="prod-name"><?= e($p['name']) ?></a>
                        <div class="prod-prices">
                            <span class="prod-price-now"><?= formatMoney($p['sale_price']) ?></span>
                            <?php if($p['compare_price'] && $p['compare_price'] > $p['sale_price']): ?>
                            <span class="prod-price-old"><?= formatMoney($p['compare_price']) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if($p['stock'] > 0): ?>
                        <button class="btn btn-sm btn-add w-100 add-to-cart" data-id="<?= $p['id'] ?>"><i class="fas fa-cart-plus me-1"></i>Agregar</button>
                        <?php else: ?>
                        <button class="btn btn-sm btn-secondary w-100" disabled>Agotado</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?= url('catalog') ?>" class="btn btn-outline-success rounded-pill px-4">Ver todo el cat&aacute;logo <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>
