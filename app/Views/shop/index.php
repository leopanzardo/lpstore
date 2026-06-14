<?= $this->extend('layout/homepage') ?>

<?= $this->section('content') ?>

	<!-- Productos destacados -->
	<?php if (!empty($featuredProducts)): ?>
		<h2 class="text-center mb-4">Productos Destacados</h2>
		<div class="row">
			<?php foreach ($featuredProducts as $product): ?>
				<div class="col-md-4 mb-4">
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
							<div class="row">
								<div class="col-6 card-text h5 text-primary">
									$<?= number_format($product->base_price, 2) ?>
								</div>
								<div class="col-6">
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
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<!-- Últimos productos -->
	<?php if (!empty($latestProducts)): ?>
		<h2 class="text-center mb-4 mt-5">Últimos Productos</h2>
		<div class="row">
			<?php foreach ($latestProducts as $product): ?>
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
		</div>
	<?php endif; ?>

<?= $this->endSection() ?>