<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-6">
        <!-- Imagen principal -->
        <?php if (!empty($images)): ?>
            <img src="<?= base_url($images[0]->image_path) ?>" 
                 class="img-fluid rounded" 
                 alt="<?= esc($product->name) ?>"
                 onerror="this.src='<?= base_url('assets/images/no-image.jpg') ?>'"
                 id="mainImage">
            
            <!-- Miniaturas -->
            <?php if (count($images) > 1): ?>
                <div class="row mt-3">
                    <?php foreach ($images as $img): ?>
                        <div class="col-3">
                            <img src="<?= base_url($img->image_path) ?>" 
                                 class="img-thumbnail" 
                                 alt="Miniatura"
                                 onerror="this.src='<?= base_url('assets/images/no-image.jpg') ?>'"
                                 style="cursor: pointer;"
                                 onclick="document.getElementById('mainImage').src='<?= base_url($img->image_path) ?>'">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <img src="<?= base_url('assets/images/no-image.jpg') ?>" 
                 class="img-fluid rounded" 
                 alt="Sin imagen">
        <?php endif; ?>
    </div>
    
    <div class="col-md-6">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('categoria/' . $product->category_slug) ?>"><?= esc($product->category_name) ?></a></li>
                <li class="breadcrumb-item active"><?= esc($product->name) ?></li>
            </ol>
        </nav>
        
        <h1><?= esc($product->name) ?></h1>
        
        <!-- Precio del producto (con data-base-price para JavaScript) -->
        <div class="my-3">
            <span class="h2 text-primary" id="productPrice" data-base-price="<?= $product->base_price ?>">
                $<?= number_format($product->base_price, 2) ?>
            </span>
        </div>
        
        <?php if ($product->description): ?>
            <div class="my-3">
                <h5>Descripción</h5>
                <p><?= nl2br(esc($product->description)) ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Variaciones del producto -->
        <?php if (!empty($variations)): ?>
            <form id="addToCartForm">
                <?php foreach ($variations as $attribute => $options): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold"><?= ucfirst($attribute) ?>:</label>
                        <select name="variation_<?= $attribute ?>" class="form-select" required>
                            <option value="">Seleccione <?= $attribute ?></option>
                            <?php foreach ($options as $option): ?>
                                <option value="<?= $option->id ?>" data-price="<?= $option->price ?>">
                                    <?= esc($option->value) ?> 
                                    <?php if ($option->price > 0): ?>
                                        (+$<?= number_format($option->price, 2) ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Cantidad:</label>
                    <input type="number" name="quantity" class="form-control" style="width: 100px;" value="1" min="1" required>
                </div>
                
                <input type="hidden" name="product_id" value="<?= $product->id ?>">
                <?= csrf_field() ?>
                
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-cart"></i> Agregar al carrito
                </button>
            </form>
        <?php else: ?>
            <form id="addToCartForm">
                <div class="mb-3">
                    <label class="form-label fw-bold">Cantidad:</label>
                    <input type="number" name="quantity" class="form-control" style="width: 100px;" value="1" min="1" required>
                </div>
                
                <input type="hidden" name="product_id" value="<?= $product->id ?>">
                <?= csrf_field() ?>
                
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-cart"></i> Agregar al carrito
                </button>
            </form>
        <?php endif; ?>
        
        <!-- Botón de favoritos (con data-product-id) -->
        <?php if (session('isLoggedIn')): ?>
            <button class="btn btn-outline-danger mt-2" id="favoriteBtn" data-product-id="<?= $product->id ?>">
                <i class="far fa-heart"></i> Agregar a favoritos
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Productos relacionados -->
<?php if (!empty($relatedProducts)): ?>
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="text-center mb-4">Productos relacionados</h3>
        </div>
        <?php foreach ($relatedProducts as $relProduct): ?>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <!-- Imagen del producto relacionado -->
                    <a href="<?= base_url('producto/' . $relProduct->slug) ?>" class="text-decoration-none">
                        <?php
                        // Obtener la imagen principal del producto relacionado
                        $imageModel = new \App\Models\ProductImageModel();
                        $primaryImage = $imageModel->getPrimaryImage($relProduct->id);
                        $imagePath = $primaryImage ? $primaryImage->image_path : 'assets/images/no-image.jpg';
                        ?>
                        <img src="<?= base_url($imagePath) ?>" 
                             class="card-img-top" 
                             alt="<?= esc($relProduct->name) ?>"
                             style="height: 180px; object-fit: cover;"
                             onerror="this.src='<?= base_url('assets/images/no-image.jpg') ?>'">
                    </a>
                    <div class="card-body text-center">
                        <h6 class="card-title">
                            <a href="<?= base_url('producto/' . $relProduct->slug) ?>" class="text-decoration-none text-dark">
                                <?= esc($relProduct->name) ?>
                            </a>
                        </h6>
                        <p class="card-text h5 text-primary">$<?= number_format($relProduct->base_price, 2) ?></p>
                        <button class="btn btn-sm btn-outline-primary add-to-cart-related" data-product-id="<?= $relProduct->id ?>">
                            <i class="fas fa-cart-plus"></i> Comprar
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>