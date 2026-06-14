<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($category->name) ?></li>
            </ol>
        </nav>
        
        <h1><?= esc($category->name) ?></h1>
        <?php if ($category->description): ?>
            <p class="lead"><?= esc($category->description) ?></p>
        <?php endif; ?>
    </div>
</div>

<div class="row mt-4">
    <?php if (empty($products)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                No hay productos en esta categoría aún.
            </div>
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
                        <?php if ($product->description): ?>
                            <p class="card-text small text-muted"><?= esc(substr($product->description, 0, 60)) ?>...</p>
                        <?php endif; ?>
                        <p class="card-text h5 text-primary">$<?= number_format($product->base_price, 2) ?></p>
                        
                        <!-- Formulario para agregar al carrito directamente -->
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