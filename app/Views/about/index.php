<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<h1>Acerca de <?= esc($lpConfig->storeName) ?></h1>



<div class="row">
    <div class="col-md-8">

        <?php if ($lpConfig->storeDescription): ?>
            <div class="mb-4">
                <?= $lpConfig->storeDescription ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> 
                El comercio aún no ha completado esta sección. Pronto habrá más información.
            </div>
        <?php endif; ?>

    </div>
    <div class="col-md-4">
                
        <!-- Información de contacto -->
        <h3 class="h5 mb-3"><i class="fas fa-address-card me-2"></i>Contacto</h3>
        <ul class="list-unstyled">
            <?php if ($mainBranch['email']): ?>
                <li class="mb-2"><i class="fas fa-envelope me-2 text-primary"></i> 
                    <a href="mailto:<?= $mainBranch['email'] ?>"><?= $mainBranch['email'] ?></a>
                </li>
            <?php endif; ?>
            <?php if ($mainBranch['phone']): ?>
                <li class="mb-2"><i class="fas fa-phone me-2 text-primary"></i> 
                    <a href="tel:<?= $mainBranch['phone'] ?>"><?= $mainBranch['phone'] ?></a>
                </li>
            <?php endif; ?>
            <?php if ($mainBranch['whatsapp']): ?>
                <li class="mb-2"><i class="fab fa-whatsapp me-2 text-success"></i> 
                    <a href="https://wa.me/<?= $mainBranch['whatsapp'] ?>" target="_blank">WhatsApp</a>
                </li>
            <?php endif; ?>
            <?php if ($mainBranch['streetAddress']): ?>
                <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-danger"></i> 
                    <?= $mainBranch['streetAddress'] ?>
                </li>
            <?php endif; ?>
        </ul>
                
        <!-- Redes sociales -->
        <?php if ($lpConfig->facebookUrl || $lpConfig->instagramUrl || $lpConfig->twitterUrl): ?>
            <hr class="my-4">
            <h3 class="h5 mb-3"><i class="fas fa-share-alt me-2"></i>Síguenos</h3>
            <div>
                <?php if ($lpConfig->facebookUrl): ?>
                    <a href="<?= $lpConfig->facebookUrl ?>" target="_blank" class="btn btn-outline-primary me-2">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                <?php endif; ?>
                <?php if ($lpConfig->instagramUrl): ?>
                    <a href="<?= $lpConfig->instagramUrl ?>" target="_blank" class="btn btn-outline-danger me-2">
                        <i class="fab fa-instagram"></i> Instagram
                    </a>
                <?php endif; ?>
                <?php if ($lpConfig->twitterUrl): ?>
                    <a href="<?= $lpConfig->twitterUrl ?>" target="_blank" class="btn btn-outline-info me-2">
                        <i class="fab fa-twitter"></i> Twitter
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    </div>
</div>

<?= $this->endSection() ?>