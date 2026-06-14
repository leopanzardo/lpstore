<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<h1>Nuestras sucursales</h1>

<?php if (empty($branches)): ?>
	<div class="alert alert-info">
		<i class="fas fa-info-circle me-2"></i> 
		No hay sucursales registradas. Visítanos en nuestra tienda online.
	</div>
<?php else: ?>
	<div class="row">
	<?php foreach ($branches as $branch): ?>
		<div class="col-md-6 mb-4">
			<div class="card h-100 shadow">
				<div class="card-header">
					<h3 class="h5 card-title">
						<?php if (isset($branch['isMain']) && $branch['isMain']): ?>
							<span class="badge bg-primary ms-2 float-end">Principal</span>
						<?php endif; ?>
						<i class="fas fa-store me-2 text-primary"></i>
						<?= $branch['addressLocality'] ?>
					</h3>
				</div>
				<div class="card-body">
					<!-- Dirección formateada -->
					<p class="card-text">
						<i class="fas fa-map-marker-alt me-2 text-danger"></i>
						<?= $branch['streetAddress'] ?><br>
						<?= $branch['addressLocality'] .', '. $branch['addressRegion'] ?><br>
						<?= $branch['addressCountry'] .' - CP '. $branch['postalCode'] ?>
					</p>
					
					<!-- Teléfono -->
					<?php if (!empty($branch['phone'])): ?>
						<p class="card-text">
							<i class="fas fa-phone me-2 text-success"></i>
							<a href="tel:<?= $branch['phone'] ?>"><?= $branch['phone'] ?></a>
						</p>
					<?php endif; ?>
					
					<!-- Email -->
					<?php if (!empty($branch['email'])): ?>
						<p class="card-text">
							<i class="fas fa-envelope me-2 text-primary"></i>
							<a href="mailto:<?= $branch['email'] ?>"><?= $branch['email'] ?></a>
						</p>
					<?php endif; ?>
					
					<!-- Horarios -->
					<?php if (!empty($branch['schedule'])): ?>
						<p class="card-text">
							<i class="fas fa-clock me-2 text-warning"></i>
							<strong>Horarios:</strong><br>
							<?php foreach ($branch['schedule'] as $schedule): ?>
								<?php
								$days = is_array($schedule['days']) ? implode(', ', $schedule['days']) : $schedule['days'];
								?>
								<?= $days ?>: <?= $schedule['opens'] ?> - <?= $schedule['closes'] ?><br>
							<?php endforeach; ?>
						</p>
					<?php endif; ?>
                                        
                    <!-- Google Maps (si hay coordenadas y API key) -->
                    <?php if (!empty($lpConfig->googleMapsApiKey)): ?>
                        <div class="mt-3">
                            <iframe
                                width="100%"
                                height="200"
                                frameborder="0"
                                style="border:0; border-radius: 8px;"
                                referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps/embed/v1/place?key=<?= $lpConfig->googleMapsApiKey ?>&q=<?= urlencode($branch['streetAddress'] . ', ' . $branch['addressLocality'] . ', ' . $branch['addressCountry']) ?>"
                                allowfullscreen>
                            </iframe>
                        </div>
                    <?php endif; ?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
<?php endif; ?>

<?= $this->endSection() ?>