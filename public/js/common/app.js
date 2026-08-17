/**
 * Common App JS - CSRF setup, AJAX defaults, utilities
 */
(function(){
    'use strict';

    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // Setup AJAX defaults for jQuery
    if(window.jQuery){
        $.ajaxSetup({
            headers: {'X-CSRF-Token': csrfToken},
            error: function(xhr){
                if(xhr.status === 419) {
                    Swal.fire('Sesión Expirada','Por favor recargue la página.','warning').then(()=>location.reload());
                } else if(xhr.status === 401){
                    location.href = BASE_URL + '/login';
                }
            }
        });
    }

    // Delete confirmation with SweetAlert2
    document.addEventListener('click', function(e){
        const btn = e.target.closest('.btn-delete, [data-confirm]');
        if(!btn) return;
        e.preventDefault();
        const msg = btn.dataset.confirm || '¿Está seguro que desea eliminar este elemento?';
        const form = btn.closest('form') || btn.querySelector('form');
        const href = btn.getAttribute('href');

        Swal.fire({
            title: 'Confirmar',
            text: msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then(function(result){
            if(result.isConfirmed){
                if(form) form.submit();
                else if(href) location.href = href;
            }
        });
    });

    // Sidebar toggle (admin)
    const sidebarToggle = document.getElementById('sidebarToggle');
    if(sidebarToggle){
        sidebarToggle.addEventListener('click', function(){
            document.querySelector('.admin-sidebar')?.classList.toggle('show');
        });
    }

    // Auto-dismiss alerts after 5 seconds
    document.querySelectorAll('.alert-dismissible').forEach(function(alert){
        setTimeout(function(){
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if(bsAlert) bsAlert.close();
        }, 5000);
    });

    // Toast notification helper
    window.showToast = function(message, type){
        type = type || 'success';
        let container = document.querySelector('.toast-container');
        if(!container){
            container = document.createElement('div');
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1080';
            container.style.marginTop = '70px';
            document.body.appendChild(container);
        }
        const id = 'toast-' + Date.now();
        const colors = {success:'text-bg-success',error:'text-bg-danger',warning:'text-bg-warning',info:'text-bg-info'};
        const html = '<div id="'+id+'" class="toast '+colors[type]+'" role="alert"><div class="d-flex"><div class="toast-body">'+message+'</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>';
        container.insertAdjacentHTML('beforeend', html);
        const toastEl = document.getElementById(id);
        const toast = new bootstrap.Toast(toastEl, {delay:3000});
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', function(){ toastEl.remove(); });
    };

    // Format money helper (client-side)
    window.formatMoney = function(amount){
        return 'RD$ ' + parseFloat(amount).toLocaleString('es-DO',{minimumFractionDigits:2,maximumFractionDigits:2});
    };
})();
