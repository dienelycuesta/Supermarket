<?php $layout = 'client/layouts/main'; ?>
<div class="container py-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Inicio</a></li><li class="breadcrumb-item active">Mi Cuenta</li></ol></nav>
    <h4 class="mb-4">Mi Cuenta</h4>
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="list-group">
                <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="list">Perfil</a>
                <a href="#password" class="list-group-item list-group-item-action" data-bs-toggle="list">Cambiar Contrase&ntilde;a</a>
                <a href="#addresses" class="list-group-item list-group-item-action" data-bs-toggle="list">Direcciones</a>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="tab-content">
                <!-- Profile -->
                <div class="tab-pane fade show active" id="profile">
                    <div class="card">
                        <div class="card-header"><strong>Informaci&oacute;n del Perfil</strong></div>
                        <div class="card-body">
                            <form method="POST" action="<?= url('account/update') ?>">
                                <?= csrf_field() ?>
                                <div class="row">
                                    <div class="col-md-6 mb-3"><label class="form-label">Nombre</label><input type="text" name="first_name" class="form-control" value="<?= e($user['first_name']) ?>" required></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Apellido</label><input type="text" name="last_name" class="form-control" value="<?= e($user['last_name']) ?>" required></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Correo</label><input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Tel&eacute;fono</label><input type="tel" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>"></div>
                                </div>
                                <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Guardar Cambios</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Password -->
                <div class="tab-pane fade" id="password">
                    <div class="card">
                        <div class="card-header"><strong>Cambiar Contrase&ntilde;a</strong></div>
                        <div class="card-body">
                            <form method="POST" action="<?= url('account/password') ?>">
                                <?= csrf_field() ?>
                                <div class="mb-3"><label class="form-label">Contrase&ntilde;a Actual</label><input type="password" name="current_password" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label">Nueva Contrase&ntilde;a</label><input type="password" name="password" class="form-control" minlength="8" required></div>
                                <div class="mb-3"><label class="form-label">Confirmar Nueva Contrase&ntilde;a</label><input type="password" name="password_confirmation" class="form-control" minlength="8" required></div>
                                <button type="submit" class="btn btn-success"><i class="fas fa-key me-1"></i>Actualizar Contrase&ntilde;a</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Addresses -->
                <div class="tab-pane fade" id="addresses">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center"><strong>Direcciones Guardadas</strong><button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addAddressModal"><i class="fas fa-plus me-1"></i>Agregar</button></div>
                        <div class="card-body">
                            <?php if(empty($addresses)): ?>
                            <p class="text-muted text-center py-3">Sin direcciones guardadas</p>
                            <?php else: ?>
                            <?php foreach($addresses as $addr): ?>
                            <div class="border rounded p-3 mb-2 d-flex justify-content-between align-items-start">
                                <div>
                                    <strong><?= e($addr['label']) ?></strong> <?php if($addr['is_default']): ?><span class="badge bg-success">Predeterminada</span><?php endif; ?>
                                    <br><span class="text-muted"><?= e($addr['address_line1']) ?><?= $addr['address_line2'] ? ', ' . e($addr['address_line2']) : '' ?>, <?= e($addr['city']) ?><?= $addr['state'] ? ', ' . e($addr['state']) : '' ?></span>
                                </div>
                                <form method="POST" action="<?= url('account/address/' . $addr['id'] . '/delete') ?>" class="d-inline"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('&iquest;Eliminar esta direcci&oacute;n?')"><i class="fas fa-times"></i></button></form>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <form method="POST" action="<?= url('account/address') ?>">
        <?= csrf_field() ?>
        <div class="modal-header"><h5 class="modal-title"><i class="fas fa-map-marker-alt me-2 text-success"></i>Agregar Direcci&oacute;n</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <!-- Map -->
            <div class="mb-3">
                <div class="input-group mb-2">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="mapSearch" class="form-control" placeholder="Buscar direcci&oacute;n en el mapa...">
                    <button type="button" class="btn btn-success" id="mapSearchBtn">Buscar</button>
                </div>
                <div id="accountMap" style="height:250px;border-radius:8px;z-index:1"></div>
                <div class="small text-muted mt-1"><i class="fas fa-info-circle me-1"></i>Haz clic en el mapa o busca para seleccionar la ubicaci&oacute;n</div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Etiqueta</label><input type="text" name="label" class="form-control" placeholder="ej. Casa, Oficina" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Direcci&oacute;n</label><input type="text" name="address_line1" id="accAddr1" class="form-control" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Direcci&oacute;n L&iacute;nea 2</label><input type="text" name="address_line2" id="accAddr2" class="form-control"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Ciudad</label><input type="text" name="city" id="accCity" class="form-control" required></div>
                <div class="col-md-3 mb-3"><label class="form-label">Provincia</label><input type="text" name="state" id="accState" class="form-control"></div>
            </div>
            <input type="hidden" name="latitude" id="accLat">
            <input type="hidden" name="longitude" id="accLng">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="is_default" value="1" id="isDefault"><label class="form-check-label" for="isDefault">Establecer como predeterminada</label></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-success">Guardar Direcci&oacute;n</button></div>
    </form>
</div></div></div>
<script>
(function(){
    var map, marker;
    var defaultLat = 18.4861, defaultLng = -69.9312; // Santo Domingo

    // Init map when modal opens
    var modal = document.getElementById('addAddressModal');
    modal.addEventListener('shown.bs.modal', function(){
        if(map) { map.invalidateSize(); return; }
        map = L.map('accountMap').setView([defaultLat, defaultLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);
        marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);
        marker.on('dragend', function(){ reverseGeocode(marker.getLatLng()); });
        map.on('click', function(e){
            marker.setLatLng(e.latlng);
            reverseGeocode(e.latlng);
        });

        // Try user's location
        if(navigator.geolocation){
            navigator.geolocation.getCurrentPosition(function(pos){
                var ll = L.latLng(pos.coords.latitude, pos.coords.longitude);
                map.setView(ll, 16);
                marker.setLatLng(ll);
                reverseGeocode(ll);
            });
        }
    });

    // Search
    document.getElementById('mapSearchBtn').addEventListener('click', doSearch);
    document.getElementById('mapSearch').addEventListener('keydown', function(e){
        if(e.key === 'Enter'){ e.preventDefault(); doSearch(); }
    });

    function doSearch(){
        var q = document.getElementById('mapSearch').value.trim();
        if(!q) return;
        fetch('https://nominatim.openstreetmap.org/search?format=json&q='+encodeURIComponent(q)+',+Republica+Dominicana&limit=1')
        .then(function(r){ return r.json(); })
        .then(function(data){
            if(data.length > 0){
                var ll = L.latLng(parseFloat(data[0].lat), parseFloat(data[0].lon));
                map.setView(ll, 17);
                marker.setLatLng(ll);
                reverseGeocode(ll);
            } else {
                showToast('Direcci\u00f3n no encontrada', 'warning');
            }
        });
    }

    function reverseGeocode(latlng){
        document.getElementById('accLat').value = latlng.lat.toFixed(7);
        document.getElementById('accLng').value = latlng.lng.toFixed(7);
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat='+latlng.lat+'&lon='+latlng.lng+'&addressdetails=1')
        .then(function(r){ return r.json(); })
        .then(function(data){
            if(!data || !data.address) return;
            var a = data.address;
            var road = a.road || a.pedestrian || a.footway || '';
            var number = a.house_number || '';
            var addr1 = road + (number ? ' #' + number : '');
            var city = a.city || a.town || a.village || a.municipality || '';
            var state = a.state || a.province || '';
            var suburb = a.suburb || a.neighbourhood || '';

            document.getElementById('accAddr1').value = addr1 || data.display_name.split(',')[0];
            document.getElementById('accAddr2').value = suburb;
            document.getElementById('accCity').value = city;
            document.getElementById('accState').value = state;
        });
    }
})();
</script>
