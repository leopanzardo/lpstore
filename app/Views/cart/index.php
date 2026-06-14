<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Mi Carrito</h1>
        
        <?php if (empty($cartItems)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Tu carrito está vacío.
                <a href="<?= base_url() ?>" class="alert-link">Continúa comprando</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered" id="cartTable">
                    <thead>
                        <tr class="table-info">
                            <th>Producto</th>
                            <th>Variación</th>
                            <th width="120">Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr data-item-id="<?= $item['id'] ?>" data-item-price="<?= $item['price'] ?>">
                            <td>
                                <a href="<?= base_url('producto/' . $item['product_slug']) ?>">
                                    <?= esc($item['product_name']) ?>
                                </a>
                            </td>
                            <td><?= $item['variation_name'] ?? '-' ?></td>
                            <td>
                                <input type="number" 
                                       class="form-control form-control-sm quantity-input" 
                                       value="<?= $item['quantity'] ?>" 
                                       min="1" 
                                       style="width: 80px;">
                            </td>
                            <td class="item-price"><?= number_to_currency($item['price'], 'UYU', 'es_UY') ?></td>
                            <td class="item-subtotal"><?= number_to_currency($item['subtotal'], 'UYU', 'es_UY') ?></td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm remove-item">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td colspan="2">
                                <strong id="cartTotal">$<?= number_format($total, 2) ?></strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <a href="<?= base_url() ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Seguir comprando
                    </a>
                </div>
                <div class="col-md-6 text-end">
                    <a href="<?= base_url('carrito/vaciar') ?>" class="btn btn-danger me-2" id="clearCartBtn">
                        <i class="fas fa-trash-alt"></i> Vaciar carrito
                    </a>
                    <a href="<?= base_url('checkout') ?>" class="btn btn-success btn-lg">
                        <i class="fas fa-credit-card"></i> Proceder al pago
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>