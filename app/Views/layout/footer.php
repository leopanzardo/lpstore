    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><?= $lpConfig->storeName ?></h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt"></i> <a href="<?= base_url('ubicacion') ?>" class="text-white-50"><?= $mainBranch['streetAddress'] ?>, <?= $mainBranch['addressLocality'] ?></a></li>
                        <?php if ($mainBranch['phone']): ?>
                            <li><i class="fas fa-phone"></i> <a href="tel:<?= $mainBranch['phone'] ?>" class="text-white-50"><?= $mainBranch['phone'] ?></a></li>
                        <?php endif; ?>
                        <?php if ($mainBranch['whatsapp']): ?>
                            <li><i class="fab fa-whatsapp"></i> <a href="https://wa.me/<?= $mainBranch['whatsapp'] ?>" class="text-white-50" target="_blank">WhatsApp</a></li>
                        <?php endif; ?>
                        <?php if ($mainBranch['email']): ?>
                            <li><i class="fas fa-envelope"></i> <a href="mailto:<?= $mainBranch['email'] ?>" class="text-white-50"><?= $mainBranch['email'] ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Nosotros</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('acerca') ?>" class="text-white-50">Acerca</a></li>
                        <li><a href="<?= base_url('contacto') ?>" class="text-white-50">Contacto</a></li>
                        <li><a href="<?= base_url('sucursales') ?>" class="text-white-50">Sucursales</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Mi Cuenta</h5>
                    <ul class="list-unstyled">
                        <?php if (session('isLoggedIn')): ?>
                            <li><a href="<?= base_url('perfil') ?>" class="text-white-50">Mi Perfil</a></li>
                            <li><a href="<?= base_url('mis-direcciones') ?>" class="text-white-50">Direcciones</a></li>
                            <li><a href="<?= base_url('mis-pedidos') ?>" class="text-white-50">Pedidos</a></li>
                            <li><a href="<?= base_url('mis-favoritos') ?>" class="text-white-50">Favoritos</a></li>
                            <li><a href="<?= base_url('logout') ?>" class="text-white-50 text-danger">Cerrar Sesión</a></li>
                        <?php else: ?>
                            <li><a href="<?= base_url('login') ?>" class="text-white-50">Iniciar Sesión</a></li>
                            <li><a href="<?= base_url('registro') ?>" class="text-white-50">Crear Cuenta</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <?php if ($lpConfig->facebookUrl || $lpConfig->instagramUrl || $lpConfig->twitterUrl): ?>
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <?php if ($lpConfig->facebookUrl): ?>
                            <a href="<?= $lpConfig->facebookUrl ?>" target="_blank" class="text-white me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <?php endif; ?>
                        <?php if ($lpConfig->instagramUrl): ?>
                            <a href="<?= $lpConfig->instagramUrl ?>" target="_blank" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <?php endif; ?>
                        <?php if ($lpConfig->twitterUrl): ?>
                            <a href="<?= $lpConfig->twitterUrl ?>" target="_blank" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <hr>
            <div class="text-center">
                <small>&copy; <?= date('Y') ?> <?= $lpConfig->storeName ?>. Todos los derechos reservados.</small>
            </div>
        </div>
    </footer>

    <script>
        // Variables globales para JavaScript
        const LP_STORE = {
            baseUrl: '<?= base_url() ?>',
            csrfToken: '<?= csrf_hash() ?>'
        };
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js" integrity="sha512-HvOjJrdwNpDbkGJIG2ZNqDlVqMo77qbs4Me4cah0HoDrfhrbA+8SBlZn1KrvAQw7cILLPFJvdwIgphzQmMm+Pw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.23.0/sweetalert2.min.js" integrity="sha512-pnPZhx5S+z5FSVwy62gcyG2Mun8h6R+PG01MidzU+NGF06/ytcm2r6+AaWMBXAnDHsdHWtsxS0dH8FBKA84FlQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="<?= base_url('assets/js/script.js') . '?v=' . filemtime('assets/js/script.js') ?>"></script>

</body>
</html>