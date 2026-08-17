<?php $layout = 'client/layouts/main';
$defaultAddrText = '';
if (!empty($defaultAddress)) {
    $parts = [$defaultAddress['address_line1']];
    if ($defaultAddress['address_line2']) $parts[] = $defaultAddress['address_line2'];
    if ($defaultAddress['city']) $parts[] = $defaultAddress['city'];
    if ($defaultAddress['state']) $parts[] = $defaultAddress['state'];
    $defaultAddrText = implode(', ', $parts);
}
?>
<div class="container py-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Inicio</a></li><li class="breadcrumb-item"><a href="<?= url('cart') ?>">Carrito</a></li><li class="breadcrumb-item active">Pagar</li></ol></nav>
    <form method="POST" action="<?= url('checkout') ?>" id="checkoutForm">
    <?= csrf_field() ?>
    <input type="hidden" name="customer_name" value="<?= e($user['first_name'] . ' ' . $user['last_name']) ?>">
    <input type="hidden" name="customer_email" value="<?= e($user['email']) ?>">
    <input type="hidden" name="customer_phone" value="<?= e($user['phone'] ?? '') ?>">
    <div class="row">
        <div class="col-lg-7 mb-4">
            <h4 class="mb-3"><i class="fas fa-lock me-2 text-success"></i>Finalizar Compra</h4>

            <!-- Delivery Address -->
            <div class="card mb-3">
                <div class="card-header bg-white"><i class="fas fa-map-marker-alt me-2 text-success"></i><strong>Direcci&oacute;n de Entrega</strong></div>
                <div class="card-body">
                    <?php if (!empty($addresses)): ?>
                    <?php foreach ($addresses as $a):
                        $addrText = $a['address_line1'];
                        if ($a['address_line2']) $addrText .= ', ' . $a['address_line2'];
                        if ($a['city']) $addrText .= ', ' . $a['city'];
                        if ($a['state']) $addrText .= ', ' . $a['state'];
                    ?>
                    <div class="form-check border rounded p-3 mb-2 address-option <?= $a['is_default'] ? 'border-success bg-success bg-opacity-10' : '' ?>">
                        <input class="form-check-input" type="radio" name="address_id" id="addr_<?= $a['id'] ?>" value="<?= e($addrText) ?>" <?= $a['is_default'] ? 'checked' : '' ?>>
                        <label class="form-check-label w-100" for="addr_<?= $a['id'] ?>">
                            <strong><?= e($a['label']) ?></strong>
                            <?php if ($a['is_default']): ?><span class="badge bg-success ms-1">Principal</span><?php endif; ?>
                            <br><span class="text-muted small"><?= e($addrText) ?></span>
                        </label>
                    </div>
                    <?php endforeach; ?>
                    <div class="form-check border rounded p-3 mb-2 address-option">
                        <input class="form-check-input" type="radio" name="address_id" id="addr_map" value="map">
                        <label class="form-check-label" for="addr_map"><i class="fas fa-map me-1 text-success"></i><strong>Seleccionar en el mapa</strong></label>
                    </div>
                    <?php else: ?>
                    <p class="text-muted small mb-2">Selecciona tu direcci&oacute;n en el mapa o escr&iacute;bela</p>
                    <?php endif; ?>

                    <!-- Map section -->
                    <div id="checkoutMapWrap" style="<?= !empty($addresses) ? 'display:none' : '' ?>">
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="ckMapSearch" class="form-control" placeholder="Buscar direcci&oacute;n...">
                            <button type="button" class="btn btn-success" id="ckMapSearchBtn">Buscar</button>
                        </div>
                        <div id="checkoutMap" style="height:220px;border-radius:8px;z-index:1"></div>
                        <div id="ckSelectedAddr" class="mt-2 small text-success fw-bold" style="display:none"><i class="fas fa-check-circle me-1"></i><span id="ckAddrText"></span></div>
                    </div>

                    <input type="hidden" name="address" id="finalAddress" value="<?= e($defaultAddrText) ?>">
                </div>
            </div>

            <!-- Payment Method -->
            <div class="card mb-3">
                <div class="card-header bg-white"><i class="fas fa-credit-card me-2 text-success"></i><strong>M&eacute;todo de Pago</strong></div>
                <div class="card-body p-2">
                    <div class="payment-option border rounded p-3 mb-2 border-success bg-success bg-opacity-10">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="pm_cash" value="cash" checked>
                            <label class="form-check-label" for="pm_cash"><i class="fas fa-money-bill-wave text-success me-2"></i><strong>Efectivo</strong> <span class="text-muted small">- Paga al recibir</span></label>
                        </div>
                    </div>
                    <div class="payment-option border rounded p-3 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="pm_bank" value="bank_transfer">
                            <label class="form-check-label" for="pm_bank"><i class="fas fa-university text-primary me-2"></i><strong>Transferencia</strong> <span class="text-muted small">- Banco Popular</span></label>
                        </div>
                        <div id="bankDetails" class="mt-2 ms-4 d-none small">
                            <div class="bg-light rounded p-2">
                                <strong>Cta:</strong> 823-456789-0 | <strong>Titular:</strong> <?= APP_NAME ?> | <strong>RNC:</strong> 130-12345-6
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-3">
                <input type="text" name="notes" class="form-control" placeholder="Notas del pedido (opcional)">
            </div>

            <button type="submit" class="btn btn-success btn-lg w-100" id="submitBtn"><i class="fas fa-check-circle me-2"></i>Confirmar Pedido &mdash; <?= formatMoney($total) ?></button>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-5">
            <div class="card sticky-top" style="top:80px">
                <div class="card-header bg-white"><strong>Resumen</strong> <span class="text-muted small">(<?= count($items) ?> art&iacute;culos)</span></div>
                <div class="card-body p-0" style="max-height:350px;overflow-y:auto">
                    <?php foreach ($items as $item): ?>
                    <div class="d-flex align-items-center p-2 border-bottom">
                        <?php if ($item['image']): ?><img src="<?= e(UPLOAD_URL . $item['image']) ?>" class="rounded me-2" style="width:40px;height:40px;object-fit:cover"><?php else: ?><div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px"><i class="fas fa-box text-muted small"></i></div><?php endif; ?>
                        <div class="flex-grow-1 small">
                            <div class="fw-bold"><?= e($item['name']) ?></div>
                            <span class="text-muted"><?= $item['quantity'] ?> x <?= formatMoney($item['sale_price']) ?></span>
                        </div>
                        <div class="fw-bold small"><?= formatMoney($item['line_total']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between small"><span>Subtotal</span><span><?= formatMoney($subtotal) ?></span></li>
                    <li class="list-group-item d-flex justify-content-between small"><span>ITBIS (18%)</span><span><?= formatMoney($tax) ?></span></li>
                    <li class="list-group-item d-flex justify-content-between bg-light"><strong>Total</strong><strong class="text-success"><?= formatMoney($total) ?></strong></li>
                </ul>
            </div>
        </div>
    </div>
    </form>
</div>
<script>
(function(){
    // Payment toggle
    var bankDetails = document.getElementById('bankDetails');
    document.querySelectorAll('.payment-option').forEach(function(el){
        el.addEventListener('click', function(){
            var radio = this.querySelector('input[type=radio]');
            if(radio) radio.checked = true;
            document.querySelectorAll('.payment-option').forEach(function(o){ o.classList.remove('border-success','bg-success','bg-opacity-10'); });
            this.classList.add('border-success','bg-success','bg-opacity-10');
            bankDetails.classList.toggle('d-none', radio.value !== 'bank_transfer');
        });
    });

    // Address selection
    var addressRadios = document.querySelectorAll('input[name="address_id"]');
    var mapWrap = document.getElementById('checkoutMapWrap');
    var finalAddr = document.getElementById('finalAddress');
    var mapInited = false;

    if (addressRadios.length > 0) {
        addressRadios.forEach(function(r){
            r.addEventListener('change', function(){
                document.querySelectorAll('.address-option').forEach(function(el){ el.classList.remove('border-success','bg-success','bg-opacity-10'); });
                this.closest('.address-option').classList.add('border-success','bg-success','bg-opacity-10');
                if (this.value === 'map') {
                    mapWrap.style.display = 'block';
                    initCheckoutMap();
                } else {
                    mapWrap.style.display = 'none';
                    finalAddr.value = this.value;
                }
            });
        });
    } else {
        // No saved addresses - init map right away
        initCheckoutMap();
    }

    var ckMap, ckMarker;
    function initCheckoutMap(){
        if(mapInited){ ckMap.invalidateSize(); return; }
        mapInited = true;
        var lat = 18.4861, lng = -69.9312;
        ckMap = L.map('checkoutMap').setView([lat, lng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(ckMap);
        ckMarker = L.marker([lat, lng], {draggable: true}).addTo(ckMap);
        ckMarker.on('dragend', function(){ ckReverseGeocode(ckMarker.getLatLng()); });
        ckMap.on('click', function(e){
            ckMarker.setLatLng(e.latlng);
            ckReverseGeocode(e.latlng);
        });
        if(navigator.geolocation){
            navigator.geolocation.getCurrentPosition(function(pos){
                var ll = L.latLng(pos.coords.latitude, pos.coords.longitude);
                ckMap.setView(ll, 16);
                ckMarker.setLatLng(ll);
                ckReverseGeocode(ll);
            });
        }
        // Search
        document.getElementById('ckMapSearchBtn').addEventListener('click', ckDoSearch);
        document.getElementById('ckMapSearch').addEventListener('keydown', function(e){
            if(e.key === 'Enter'){ e.preventDefault(); ckDoSearch(); }
        });
    }

    function ckDoSearch(){
        var q = document.getElementById('ckMapSearch').value.trim();
        if(!q) return;
        fetch('https://nominatim.openstreetmap.org/search?format=json&q='+encodeURIComponent(q)+',+Republica+Dominicana&limit=1')
        .then(function(r){ return r.json(); })
        .then(function(data){
            if(data.length > 0){
                var ll = L.latLng(parseFloat(data[0].lat), parseFloat(data[0].lon));
                ckMap.setView(ll, 17);
                ckMarker.setLatLng(ll);
                ckReverseGeocode(ll);
            } else {
                showToast('Direcci\u00f3n no encontrada', 'warning');
            }
        });
    }

    function ckReverseGeocode(latlng){
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat='+latlng.lat+'&lon='+latlng.lng+'&addressdetails=1')
        .then(function(r){ return r.json(); })
        .then(function(data){
            if(!data || !data.address) return;
            var a = data.address;
            var road = a.road || a.pedestrian || a.footway || '';
            var number = a.house_number ? ' #' + a.house_number : '';
            var suburb = a.suburb || a.neighbourhood || '';
            var city = a.city || a.town || a.village || a.municipality || '';
            var state = a.state || a.province || '';
            var parts = [];
            if(road) parts.push(road + number);
            if(suburb) parts.push(suburb);
            if(city) parts.push(city);
            if(state) parts.push(state);
            var fullAddr = parts.join(', ') || data.display_name;
            finalAddr.value = fullAddr;
            document.getElementById('ckAddrText').textContent = fullAddr;
            document.getElementById('ckSelectedAddr').style.display = 'block';
        });
    }

    // Prevent double submit
    document.getElementById('checkoutForm').addEventListener('submit', function(){
        var btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
    });
})();
</script>
