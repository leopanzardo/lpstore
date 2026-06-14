<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $title ?? 'LP Store' ?> - <?= $lpConfig->storeName ?? 'LP Store' ?></title>
	<meta name="description" content="<?= $lpConfig->metaDescription ?? 'Tu tienda online de confianza' ?>">
	<meta name="keywords" content="<?= $lpConfig->metaKeywords ?? 'tienda online, productos, compras' ?>">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css" integrity="sha512-2bBQCjcnw658Lho4nlXJcc6WkV/UxpE/sAokbXPxQNGqmNdQrWqtw26Ns9kFF/yG792pKR1Sx8/Y1Lf1XN4GKA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.23.0/sweetalert2.min.css" integrity="sha512-Ivy7sPrd6LPp20adiK3al16GBelPtqswhJnyXuha3kGtmQ1G2qWpjuipfVDaZUwH26b3RDe8x707asEpvxl7iA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime('assets/css/style.css') ?>">
</head>
<body>

	<nav class="navbar navbar-expand-md bg-primary bg-gradient shadow fixed-top">
		<div class="container-fluid">
			<a class="navbar-brand" href="<?= base_url() ?>">
				<?php if ($lpConfig->storeLogo ?? false): ?>
					<img src="<?= base_url('uploads/' . $lpConfig->storeLogo) ?>" alt="<?= $lpConfig->storeName ?>" height="40">
				<?php else: ?>
					<?= $lpConfig->storeName ?? 'LP Store' ?>
				<?php endif; ?>
			</a>

			<div class="collapse navbar-collapse" id="collapsibleNavbar">

				<ul class="navbar-nav me-4">
					<li class="nav-item">
						<a class="nav-link" href="<?= base_url('acerca') ?>">Acerca</a>
					</li>
					<?php if (!empty($categories)): ?>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"> Categorías</a>
						<ul class="dropdown-menu">
							<?php foreach ($categories as $category): ?>
								<li><a href="<?= base_url('categoria/' . $category->slug) ?>" class="dropdown-item"><?= esc($category->name) ?></a></li>
							<?php endforeach; ?>
						</ul>
					</li>
					<?php endif; ?>
					<li class="nav-item">
						<a class="nav-link" href="/contacto"><i class="fa-solid fa-envelope fa-lg"></i> Contacto</a>
					</li>
				</ul>

				<form action="/buscar" method="get" class="d-flex mx-auto w-50">
					<div class="input-group">
						<input class="form-control" name="q" type="text" placeholder="Buscar">
						<button class="btn btn-dark" type="submit"><i class="fa-solid fa-magnifying-glass fa-lg"></i></button>
					</div>
				</form>

				<ul class="navbar-nav ms-4">
					<li class="nav-item">
						<a class="nav-link" href="/carrito">
							<i class="fa-solid fa-cart-shopping fa-2x"></i>
							<? if (session('cartCount')) { ?>
							<span class="badge bg-danger rounded-pill" id="cartCountBadge"><?= session('cartCount') ?></span>
							<? } else { ?>
							<span class="badge bg-danger rounded-pill hidden" id="cartCountBadge"></span>
							<? } ?>
						</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
							<i class="fa-solid fa-circle-user fa-2x"></i>
						</a>
						<ul class="dropdown-menu dropdown-menu-end">
						<?php if (session('isLoggedIn')): ?>
							<li><a class="dropdown-item" href="<?= base_url('perfil') ?>"><i class="fas fa-id-card fa-lg me-2"></i>Mi Perfil</a></li>
							<li><a class="dropdown-item" href="<?= base_url('mis-direcciones') ?>"><i class="fas fa-map-marker-alt fa-lg me-2"></i>Direcciones</a></li>
							<li><a class="dropdown-item" href="<?= base_url('mis-pedidos') ?>"><i class="fas fa-truck fa-lg me-2"></i>Pedidos</a></li>
							<li><a class="dropdown-item" href="<?= base_url('mis-favoritos') ?>"><i class="fas fa-heart fa-lg me-2"></i>Favoritos</a></li>
							<li><hr class="dropdown-divider"></li>
							<li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt fa-lg me-2"></i>Cerrar Sesión</a></li>
						<?php else: ?>
							<li><a href="<?= base_url('login') ?>" class="dropdown-item"><i class="fas fa-sign-in-alt fa-lg"></i> Ingresar</a></li>
							<li><a href="<?= base_url('registro') ?>" class="dropdown-item"><i class="fas fa-user-plus fa-lg"></i> Registrarse</a>
						<?php endif; ?>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</nav>