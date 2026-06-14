<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<h1>Resultados de búsqueda: <?= esc($searchTerm) ?></h1>

<div class="row">
    <?php if (empty($products)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                No se encontraron productos para "<?= esc($searchTerm) ?>".
            </div>
            <a href="<?= base_url() ?>" class="btn btn-primary">Volver al inicio</a>
        </div>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100 product-card">
                    <a href="<?= base_url('producto/' . $product->slug) ?>">
                        <img src="<?= base_url('uploads/products/thumb_' . ($product->id) . '.jpg') ?>" 
                             class="card-img-top" 
                             alt="<?= esc($product->name) ?>"
                             onerror="this.src='<?= base_url('assets/images/no-image.jpg') ?>'">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="<?= base_url('producto/' . $product->slug) ?>" class="text-decoration-none text-dark">
                                <?= esc($product->name) ?>
                            </a>
                        </h5>
                        <p class="card-text text-muted"><?= esc($product->category_name) ?></p>
                        <p class="card-text h5 text-primary">$<?= number_format($product->base_price, 2) ?></p>
                        
                        <form class="add-to-cart-form" data-product-id="<?= $product->id ?>">
                            <?= csrf_field() ?>
                            <div class="row g-2 align-items-center">
                                <div class="col-5">
                                    <input type="number" name="quantity" class="form-control form-control-sm" value="1" min="1" required>
                                </div>
                                <div class="col-7">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-cart-plus"></i> Agregar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>