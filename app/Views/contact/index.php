<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<h1>Contáctanos</h1>

<div class="row">
	<div class="col-md-6">
		<p>Si deseas ponerse en contacto con <?= $lpConfig->storeName ?> puedes utilizar el siguiente formulario. Complétalo con tus datos y el mensaje que quieras enviarnos, haz clic en el botón Enviar mensaje y te responderemos a la mayor brevedad posible.<br>De lo contrario puedes contactarnos por cualquiera de los medios que se encuentran en esta página que con gusto atenderemos tu consulta.</p>
		<div class="card">
			<div class="card-body">
				<?php if (session()->getFlashdata('errors')): ?>
					<div class="alert alert-danger">
						<ul>
							<?php foreach (session()->getFlashdata('errors') as $error): ?>
								<li><?= $error ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
				
				<form method="POST" action="<?= base_url('contacto/enviar') ?>">
					<div class="mb-3">
						<label for="name" class="form-label">Nombre completo *</label>
						<input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
					</div>
					
					<div class="mb-3">
						<label for="email" class="form-label">Email *</label>
						<input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
					</div>
					
					<div class="mb-3">
						<label for="subject" class="form-label">Asunto *</label>
						<input type="text" class="form-control" id="subject" name="subject" value="<?= old('subject') ?>" required>
					</div>
					
					<div class="mb-3">
						<label for="message" class="form-label">Mensaje *</label>
						<textarea class="form-control" id="message" name="message" rows="5" required><?= old('message') ?></textarea>
					</div>
					
					<?= csrf_field() ?>
					<button type="submit" class="btn btn-primary float-end">
						<i class="fas fa-paper-plane"></i> Enviar mensaje
					</button>
				</form>
			</div>
		</div>
	</div>
	
	<div class="col-md-6">
		<h4><?= $lpConfig->storeName ?></h4>
		<p><i class="fas fa-map-marker-alt text-primary"></i> <?= $mainBranch['streetAddress'] ?></p>
		<p><i class="fas fa-phone text-primary"></i> <?= $mainBranch['phone'] ?></p>
		<p><i class="fas fa-envelope text-primary"></i> <?= $mainBranch['email'] ?></p>

		<!-- Horarios -->
		<?php if (!empty($branch['schedule'])): ?>
			<p><i class="fas fa-clock text-primary"></i> Horario de atención:<br>
			<?php foreach ($branch['schedule'] as $schedule): ?>
				<?php
				$days = is_array($schedule['days']) ? implode(', ', $schedule['days']) : $schedule['days'];
				?>
				<?= $days ?>: <?= $schedule['opens'] ?> - <?= $schedule['closes'] ?><br>
			<?php endforeach; ?>
			</p>
		<?php endif; ?>
		
		<!-- Mapa (opcional) -->
		<h4 class="mt-5">Ubicación</h4>
		<iframe width="100%" height="370" frameborder="0" style="border:0" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed/v1/place?key=<?= $lpConfig->googleMapsApiKey ?>&q=<?= urlencode($mainBranch['streetAddress'].','.$mainBranch['addressLocality'].','.$mainBranch['addressRegion'].','.$mainBranch['addressCountry']) ?>" allowfullscreen>
		</iframe>
	</div>
</div>

<?= $this->endSection() ?>