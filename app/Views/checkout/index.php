<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Datos de Envío</h4>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form id="shippingForm">
                    <?= csrf_field() ?>
                    
                    <?php if ($isLoggedIn): ?>
                        <div class="mb-3">
                            <label class="form-label">Seleccionar dirección</label>
                            <select class="form-select" name="address_id" id="addressSelect">
                                <option value="new">+ Agregar nueva dirección</option>
                                <?php if (!empty($addresses)): ?>
                                    <?php foreach ($addresses as $address): ?>
                                        <option value="<?= $address->id ?>" <?= ($defaultAddress && $defaultAddress->id == $address->id) ? 'selected' : '' ?>>
                                            <?= esc($address->address_line1) ?>, <?= esc($address->city) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    
                    <div id="addressForm">
                        <div class="row">
                            <?php if (!$isLoggedIn): ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" name="first_name" id="first_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" class="form-control" name="last_name" id="last_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" name="phone" id="phone">
                                </div>
                            <?php endif; ?>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label">Dirección Línea 1</label>
                                <input type="text" class="form-control" name="address_line1" id="address_line1" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Dirección Línea 2</label>
                                <input type="text" class="form-control" name="address_line2" id="address_line2">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ciudad</label>
                                <input type="text" class="form-control" name="city" id="city" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Departamento/Estado</label>
                                <input type="text" class="form-control" name="state" id="state">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Código Postal</label>
                                <input type="text" class="form-control" name="postal_code" id="postal_code">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">País</label>
                                <input type="text" class="form-control" name="country" id="country" value="Uruguay" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Brick de pago -->
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="mb-0">Método de pago</h4>
            </div>
            <div class="card-body">
                <div id="paymentBrick_container"></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Resumen del Pedido</h4>
            </div>
            <div class="card-body">
                <?php foreach ($cartItems as $item): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <?= esc($item['product_name']) ?>
                            <?php if ($item['variation_name']): ?>
                                <br><small class="text-muted"><?= esc($item['variation_name']) ?></small>
                            <?php endif; ?>
                            <br><small>Cant: <?= $item['quantity'] ?></small>
                        </div>
                        <div>$<?= number_format($item['subtotal'], 2) ?></div>
                    </div>
                <?php endforeach; ?>
                
                <hr>
                
                <div class="d-flex justify-content-between fw-bold">
                    <div>Total:</div>
                    <div id="cartTotal">$<?= number_format($total, 2) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://sdk.mercadopago.com/js/v2"></script>
<script>
// Manejo de dirección seleccionada
<?php if ($isLoggedIn): ?>
const addressSelect = document.getElementById('addressSelect');
const addressForm = document.getElementById('addressForm');

if (addressSelect) {
    addressSelect.addEventListener('change', function() {
        if (this.value === 'new') {
            addressForm.style.display = 'block';
            addressForm.querySelectorAll('input').forEach(input => input.disabled = false);
        } else {
            addressForm.style.display = 'none';
            addressForm.querySelectorAll('input').forEach(input => input.disabled = true);
        }
    });
    
    if (addressSelect.value !== 'new') {
        addressForm.style.display = 'none';
        addressForm.querySelectorAll('input').forEach(input => input.disabled = true);
    }
}
<?php endif; ?>

// Esperar a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    const total = <?= $total ?>;
    const userEmail = '<?= session()->get('userEmail') ?? session()->get('guestEmail') ?? '' ?>';
    
    const mp = new MercadoPago('<?= $lpConfig->mercadopagoPublicKey ?>', {
        locale: 'es-UY'
    });
    
    const bricksBuilder = mp.bricks();
    
    const renderPaymentBrick = async () => {
        const settings = {
            initialization: {
                amount: total,
                payer: {
                    email: userEmail,
                },
            },
            customization: {
                visual: {
                    style: {
                        theme: 'bootstrap',
                    },
                },
                paymentMethods: {
                    creditCard: 'all',
                    debitCard: 'all',
                    ticket: 'all',
                    bankTransfer: 'all',
                    wallet_purchase: 'all',
                    atm: 'all',
                },
            },
            callbacks: {
                onReady: () => {
                    console.log('Brick de pago listo');
                },
                onSubmit: async ({ selectedPaymentMethod, formData }) => {
                    console.log('OnSubmit', { selectedPaymentMethod, formData });
                    
                    // Primero, validar y guardar los datos de envío
                    const shippingForm = document.getElementById('shippingForm');
                    const formDataShipping = new FormData(shippingForm);
                    
                    // Validar dirección
                    let hasAddress = false;
                    <?php if ($isLoggedIn): ?>
                        const addressSelect = document.getElementById('addressSelect');
                        if (addressSelect && addressSelect.value !== 'new') {
                            hasAddress = true;
                        }
                    <?php endif; ?>
                    
                    if (!hasAddress) {
                        const addressLine1 = formDataShipping.get('address_line1');
                        const city = formDataShipping.get('city');
                        if (!addressLine1 || !city) {
                            Swal.fire('Error', 'Debes completar la dirección de envío', 'error');
                            return;
                        }
                    }
                    
                    // Guardar dirección en sesión (mediante fetch)
                    await fetch('<?= base_url('checkout/save-shipping') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            <?php if (!$isLoggedIn): ?>
                                email: formDataShipping.get('email'),
                                first_name: formDataShipping.get('first_name'),
                                last_name: formDataShipping.get('last_name'),
                                phone: formDataShipping.get('phone'),
                            <?php endif; ?>
                            address_line1: formDataShipping.get('address_line1'),
                            address_line2: formDataShipping.get('address_line2'),
                            city: formDataShipping.get('city'),
                            state: formDataShipping.get('state'),
                            postal_code: formDataShipping.get('postal_code'),
                            country: formDataShipping.get('country'),
                            address_id: addressSelect ? addressSelect.value : null
                        })
                    });
                    
                    // Procesar el pago
                    const response = await fetch('<?= base_url('checkout/process-payment') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(formData),
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        window.location.href = result.redirect;
                    } else {
                        Swal.fire('Error', result.message || 'Error al procesar el pago', 'error');
                    }
                },
                onError: (error) => {
                    console.error('Brick error:', error);
                    Swal.fire('Error', 'Hubo un problema con el método de pago', 'error');
                },
            },
        };
        
        window.paymentBrickController = await bricksBuilder.create('payment', 'paymentBrick_container', settings);
    };
    
    renderPaymentBrick();
});
</script>

<?= $this->endSection() ?>