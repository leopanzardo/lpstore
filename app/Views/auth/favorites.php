<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="<?= base_url('perfil') ?>" class="list-group-item list-group-item-action">Mi Perfil</a>
            <a href="<?= base_url('mis-direcciones') ?>" class="list-group-item list-group-item-action">Mis Direcciones</a>
            <a href="<?= base_url('mis-pedidos') ?>" class="list-group-item list-group-item-action">Mis Pedidos</a>
            <a href="<?= base_url('mis-favoritos') ?>" class="list-group-item list-group-item-action active">Mis Favoritos</a>
            <a href="<?= base_url('logout') ?>" class="list-group-item list-group-item-action text-danger">Cerrar Sesión</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Mis Favoritos</h4>
            </div>
            <div class="card-body">
                <?php if (empty($favorites)): ?>
                    <p class="text-muted">No tienes productos favoritos aún.</p>
                    <a href="<?= base_url() ?>" class="btn btn-primary">Ir a la tienda</a>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($favorites as $favorite): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= esc($favorite->name) ?></h5>
                                        <p class="card-text h5 text-primary">$<?= number_format($favorite->base_price, 2) ?></p>
                                        <a href="<?= base_url('producto/' . $favorite->slug) ?>" class="btn btn-primary btn-sm">Ver producto</a>
                                        <button class="btn btn-danger btn-sm remove-favorite" data-product-id="<?= $favorite->product_id ?>">
                                            <i class="fas fa-heart-broken"></i> Quitar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>