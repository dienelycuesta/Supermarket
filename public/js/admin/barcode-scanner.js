/**
 * Barcode Scanner - Quagga2 (camera) + USB scanner detection
 */
(function(){
    'use strict';

    let scanning = false;

    // USB/Keyboard Scanner Detection
    // USB scanners act as keyboard input, sending characters rapidly followed by Enter
    let barcodeBuffer = '';
    let barcodeTimer = null;

    document.addEventListener('keydown', function(e){
        // Skip if user is typing in an input (except barcode-specific inputs)
        const active = document.activeElement;
        if(active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA') && !active.classList.contains('barcode-input')) return;

        if(e.key === 'Enter' && barcodeBuffer.length >= 8){
            e.preventDefault();
            handleBarcodeScan(barcodeBuffer);
            barcodeBuffer = '';
            return;
        }

        if(e.key.length === 1){ // printable character
            barcodeBuffer += e.key;
            clearTimeout(barcodeTimer);
            barcodeTimer = setTimeout(function(){ barcodeBuffer = ''; }, 100); // USB scanners send chars within ~50ms
        }
    });

    // Camera Scanner with Quagga2
    window.startBarcodeScanner = function(callback){
        if(scanning) return;
        if(typeof Quagga === 'undefined'){
            showToast('Librería de escáner no cargada','error');
            return;
        }

        const container = document.getElementById('barcodeScannerView');
        if(!container) return;
        container.style.display = 'block';
        scanning = true;

        Quagga.init({
            inputStream:{
                name: 'Live',
                type: 'LiveStream',
                target: container,
                constraints:{
                    facingMode: 'environment',
                    width: {ideal: 640},
                    height: {ideal: 480}
                }
            },
            decoder:{
                readers: ['ean_reader','ean_8_reader','code_128_reader','upc_reader','upc_e_reader']
            },
            locate: true,
            frequency: 10
        }, function(err){
            if(err){
                showToast('Acceso a cámara denegado o no disponible','error');
                scanning = false;
                container.style.display = 'none';
                return;
            }
            Quagga.start();
        });

        Quagga.onDetected(function(result){
            const code = result.codeResult.code;
            if(code){
                Quagga.stop();
                scanning = false;
                container.style.display = 'none';
                if(callback) callback(code);
                else handleBarcodeScan(code);
            }
        });

        // Stop button
        const stopBtn = document.getElementById('stopScanner');
        if(stopBtn){
            stopBtn.onclick = function(){
                Quagga.stop();
                scanning = false;
                container.style.display = 'none';
            };
        }
    };

    window.stopBarcodeScanner = function(){
        if(scanning && typeof Quagga !== 'undefined'){
            Quagga.stop();
            scanning = false;
            const container = document.getElementById('barcodeScannerView');
            if(container) container.style.display = 'none';
        }
    };

    // Handle scanned barcode - lookup product
    function handleBarcodeScan(barcode){
        barcode = barcode.trim();
        if(!barcode) return;

        // Fill barcode input if exists
        const barcodeInput = document.querySelector('.barcode-input, #productBarcode, #barcodeSearch');
        if(barcodeInput) barcodeInput.value = barcode;

        // Lookup product via API
        $.getJSON(BASE_URL + '/api/v1/barcode-lookup.php', {barcode: barcode}, function(res){
            if(res.success && res.product){
                showToast('Producto encontrado: ' + res.product.name, 'success');
                // Trigger custom event
                document.dispatchEvent(new CustomEvent('barcodeProductFound', {detail: res.product}));
                // If product_id input exists, fill it
                const pidInput = document.getElementById('productId');
                if(pidInput) pidInput.value = res.product.id;
                const pnameInput = document.getElementById('productSearch');
                if(pnameInput) pnameInput.value = res.product.name + ' (' + res.product.sku + ')';
            } else {
                showToast('Producto no encontrado para código: ' + barcode, 'warning');
            }
        }).fail(function(){
            showToast('Error al buscar código de barras', 'error');
        });
    }

    // Expose globally
    window.handleBarcodeScan = handleBarcodeScan;
})();
