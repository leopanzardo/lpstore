/**
 * LP Store - JavaScript Principal
 * Versión: 1.0
 * Descripción: Manejo de carrito, favoritos, toasts, SweetAlert2, etc.
 */

// ==================
// FUNCIONES GLOBALES
// ==================

/**
 * Muestra un toast de Sweetalert2
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo: 'success', 'error', 'warning'
 */
function showToast(message, type = 'success') {
	Swal.fire({
		toast: true,
		position: "bottom-end",
		icon: type,
		title: message,
		showConfirmButton: false,
		theme: 'bootstrap-5',
		timer: 3000
	});
}

/**
 * Actualiza el contador del carrito en el navbar
 * @param {number} count - Nueva cantidad
 */
function updateCartCount(count) {
    const badge = document.getElementById('cartCountBadge');
    if (badge) {
        badge.textContent = count;
        if (count > 0) {
            badge.classList.remove('invisible');
        } else {
            badge.classList.add('invisible');
        }
    }
}

/**
 * Formatea un número como moneda uruguaya usando Intl.NumberFormat
 * @param {number} amount - Monto en pesos (entero)
 * @returns {string} Monto formateado (ej: "$ 1.199")
 */
function formatMoney(amount) {
    return new Intl.NumberFormat('es-UY', {
        style: 'currency',
        currency: 'UYU',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

// =============================================
// AGREGAR AL CARRITO (DESDE LISTADOS Y DETALLE)
// =============================================

// Manejar formularios de agregar al carrito (listados)
document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const productId = this.dataset.productId;
        const quantity = this.querySelector('input[name="quantity"]').value;
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        
        const csrfToken = document.querySelector('input[name="csrf_test_name"]');
        if (csrfToken) {
            formData.append('csrf_test_name', csrfToken.value);
        }
        
        try {
            const response = await fetch(LP_STORE.baseUrl + 'carrito/agregar', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                updateCartCount(data.cartCount);
                showToast(data.message, 'success');
                
                // Feedback visual en el botón
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    const originalHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i> Agregado';
                    btn.classList.add('btn-success');
                    btn.classList.remove('btn-primary');
                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-primary');
                    }, 2000);
                }
            } else {
                showToast(data.message || 'Error al agregar', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de conexión', 'error');
        }
    });
});

// Formulario de detalle de producto (puede tener variaciones)
const addToCartDetailForm = document.getElementById('addToCartForm');
if (addToCartDetailForm) {
    addToCartDetailForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch(LP_STORE.baseUrl + 'carrito/agregar', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                updateCartCount(data.cartCount);
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'Error al agregar', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de conexión', 'error');
        }
    });
}

// =========================================
// CARRITO: ACTUALIZAR CANTIDADES Y ELIMINAR
// =========================================

// Actualizar cantidad de producto en el carrito
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('change', async function() {
        const row = this.closest('tr');
        const itemId = row?.dataset.itemId;
        const unitPrice = parseInt(row?.dataset.itemPrice);
        let quantity = parseInt(this.value);
        
        if (!itemId || isNaN(unitPrice)) return;
        
        if (isNaN(quantity) || quantity < 1) {
            quantity = 1;
            this.value = 1;
        }
        
        const formData = new FormData();
        formData.append('item_id', itemId);
        formData.append('quantity', quantity);
        
        const csrfToken = document.querySelector('input[name="csrf_test_name"]');
        if (csrfToken) {
            formData.append('csrf_test_name', csrfToken.value);
        }
        
        try {
            const response = await fetch(LP_STORE.baseUrl + '/carrito/actualizar', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                updateCartCount(data.cartCount);
                
                // Calcular subtotal
                const subtotal = unitPrice * quantity;
                const subtotalCell = row.querySelector('.item-subtotal');
                if (subtotalCell) {
                    subtotalCell.textContent = formatMoney(subtotal);
                }
                
                // Actualizar total general
                const totalElement = document.getElementById('cartTotal');
                if (totalElement) {
                    totalElement.textContent = formatMoney(data.cartTotal);
                }
                
                showToast('Cantidad actualizada', 'success');
            } else {
                showToast(data.message || 'Error al actualizar', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de conexión', 'error');
        }
    });
});

// Eliminar producto del carrito (con SweetAlert2)
function setupRemoveItemButtons() {
    document.querySelectorAll('.remove-item').forEach(btn => {
        // Evitar duplicar event listeners
        if (btn.hasAttribute('data-swal-listener')) return;
        btn.setAttribute('data-swal-listener', 'true');
        
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const row = this.closest('tr');
            const itemId = row?.dataset.itemId || this.dataset.itemId;
            const productName = this.dataset.productName || row?.querySelector('.product-name')?.innerText || 'este producto';
            
            const result = await Swal.fire({
                title: '¿Eliminar producto?',
                text: `¿Estás seguro de que quieres eliminar "${productName}" del carrito?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                theme: 'bootstrap-5'
            });
            
            if (result.isConfirmed) {
                try {
                    // Crear FormData
                    const formData = new FormData();
                    formData.append('item_id', itemId);
                    
                    // Agregar CSRF token si existe
                    const csrfToken = document.querySelector('input[name="csrf_test_name"]');
                    if (csrfToken) {
                        formData.append('csrf_test_name', csrfToken.value);
                    }
                    
                    const response = await fetch(LP_STORE.baseUrl + 'carrito/eliminar', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showToast('Producto eliminado del carrito');
                        if (row) row.remove();
                        updateCartCount(data.cartCount);
                        const totalElement = document.getElementById('cartTotal');
                        if (totalElement) {
                            totalElement.textContent = formatMoney(data.cartTotal);
                        }
                        if (document.querySelectorAll('#cartTable tbody tr').length === 0) {
                            location.reload();
                        }
                    } else {
                        showToast(data.message || 'No se pudo eliminar', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('Error de conexión', 'error');
                }
            }
        });
    });
}

// Vaciar carrito (con SweetAlert2)
const clearCartBtn = document.getElementById('clearCartBtn');
if (clearCartBtn) {
    clearCartBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        
        const result = await Swal.fire({
            title: '¿Vaciar carrito completo?',
            text: 'Esta acción eliminará todos los productos de tu carrito. ¿Estás seguro?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, vaciar carrito',
            cancelButtonText: 'Cancelar',
            theme: 'bootstrap-5'
        });
        
        if (result.isConfirmed) {
            window.location.href = LP_STORE.baseUrl + 'carrito/vaciar';
        }
    });
}

// =========
// FAVORITOS
// =========

// Agregar a favoritos (desde detalle de producto)
const favoriteBtn = document.getElementById('favoriteBtn');
if (favoriteBtn) {
    favoriteBtn.addEventListener('click', async function() {
        const productId = this.dataset.productId;
        
        try {
            // Crear FormData
            const formData = new FormData();
            formData.append('product_id', productId);
            
            // Agregar CSRF token si existe
            const csrfToken = document.querySelector('input[name="csrf_test_name"]');
            if (csrfToken) {
                formData.append('csrf_test_name', csrfToken.value);
            }
            
            const response = await fetch(LP_STORE.baseUrl + '/favoritos/agregar', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast(data.message, 'success');
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                }
            } else {
                showToast(data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de conexión', 'error');
        }
    });
}

// Eliminar favorito (desde página de favoritos, con SweetAlert2)
function setupRemoveFavoriteButtons() {
    document.querySelectorAll('.remove-favorite').forEach(btn => {
        if (btn.hasAttribute('data-swal-listener')) return;
        btn.setAttribute('data-swal-listener', 'true');
        
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const productName = this.dataset.productName || 'este producto';
            
            const result = await Swal.fire({
                title: '¿Quitar de favoritos?',
                text: `¿Deseas eliminar "${productName}" de tu lista de favoritos?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, quitar',
                cancelButtonText: 'Cancelar',
                theme: 'bootstrap-5'
            });
            
            if (result.isConfirmed) {
                try {
                    // Crear FormData
                    const formData = new FormData();
                    formData.append('product_id', productId);
                    
                    // Agregar CSRF token si existe
                    const csrfToken = document.querySelector('input[name="csrf_test_name"]');
                    if (csrfToken) {
                        formData.append('csrf_test_name', csrfToken.value);
                    }
                    
                    const response = await fetch(LP_STORE.baseUrl + '/favoritos/remover', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showToast('Producto eliminado de favoritos');
                        const card = this.closest('.col-md-4, .col-md-3');
                        if (card) card.remove();
                    } else {
                        showToast(data.message || 'No se pudo eliminar', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('Error de conexión', 'error');
                }
            }
        });
    });
}

// =================================================
// PÁGINA DE PRODUCTO: VARIACIONES Y PRECIO DINÁMICO
// =================================================

const variationSelects = document.querySelectorAll('#addToCartForm select');
const priceElement = document.getElementById('productPrice');
const basePrice = priceElement ? parseFloat(priceElement.dataset.basePrice) : 0;

if (variationSelects.length > 0 && priceElement) {
    variationSelects.forEach(select => {
        select.addEventListener('change', updateProductPrice);
    });
}

function updateProductPrice() {
    let additionalPrice = 0;
    variationSelects.forEach(select => {
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.dataset.price) {
            additionalPrice += parseFloat(selectedOption.dataset.price);
        }
    });
    const totalPrice = basePrice + additionalPrice;
    priceElement.textContent = formatMoney(totalPrice);
}

// =================================================
// CHECKOUT: MOSTRAR/OCULTAR FORMULARIO DE DIRECCIÓN
// =================================================

const addressSelect = document.getElementById('addressSelect');
const addressForm = document.getElementById('addressForm');

if (addressSelect && addressForm) {
    addressSelect.addEventListener('change', function() {
        if (this.value === 'new') {
            addressForm.style.display = 'block';
            addressForm.querySelectorAll('input').forEach(input => {
                input.disabled = false;
                if (input.hasAttribute('required')) input.required = true;
            });
        } else {
            addressForm.style.display = 'none';
            addressForm.querySelectorAll('input').forEach(input => {
                input.disabled = true;
                input.required = false;
            });
        }
    });
    
    // Trigger inicial
    if (addressSelect.value !== 'new') {
        addressForm.style.display = 'none';
        addressForm.querySelectorAll('input').forEach(input => {
            input.disabled = true;
            input.required = false;
        });
    }
}

// ==============================================
// GESTIÓN DE DIRECCIONES
// ==============================================

// Establecer dirección como principal
document.querySelectorAll('.set-default-address').forEach(btn => {
    btn.addEventListener('click', async function() {
        const addressId = this.dataset.id;
        const result = await Swal.fire({
            title: '¿Establecer como principal?',
            text: 'Esta será tu dirección de envío por defecto.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Sí, establecer',
            cancelButtonText: 'Cancelar'
        });
        
        if (result.isConfirmed) {
            window.location.href = LP_STORE.baseUrl + '/direccion/default/' + addressId;
        }
    });
});

// Eliminar dirección
document.querySelectorAll('.delete-address').forEach(btn => {
    btn.addEventListener('click', async function() {
        const addressId = this.dataset.id;
        const result = await Swal.fire({
            title: '¿Eliminar dirección?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });
        
        if (result.isConfirmed) {
            window.location.href = LP_STORE.baseUrl + '/direccion/eliminar/' + addressId;
        }
    });
});

// Editar dirección - cargar modal
document.querySelectorAll('.edit-address').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const line1 = this.dataset.line1;
        const line2 = this.dataset.line2 || '';
        const city = this.dataset.city;
        const state = this.dataset.state || '';
        const postal = this.dataset.postal || '';
        const country = this.dataset.country;
        
        document.getElementById('edit_line1').value = line1;
        document.getElementById('edit_line2').value = line2;
        document.getElementById('edit_city').value = city;
        document.getElementById('edit_state').value = state;
        document.getElementById('edit_postal').value = postal;
        document.getElementById('edit_country').value = country;
        
        document.getElementById('editAddressForm').action = LP_STORE.baseUrl + '/direccion/actualizar/' + id;
        
        const editModal = new bootstrap.Modal(document.getElementById('editAddressModal'));
        editModal.show();
    });
});

// =============================
// INICIALIZACIÓN DE COMPONENTES
// =============================

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
    
    // Configurar botones de eliminar en carrito
    setupRemoveItemButtons();
    
    // Configurar botones de eliminar en favoritos
    setupRemoveFavoriteButtons();
    
    console.log('LP Store: JavaScript inicializado correctamente');
});