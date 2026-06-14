<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="<?= base_url('perfil') ?>" class="list-group-item list-group-item-action active">Mi Perfil</a>
            <a href="<?= base_url('mis-direcciones') ?>" class="list-group-item list-group-item-action">Mis Direcciones</a>
            <a href="<?= base_url('mis-pedidos') ?>" class="list-group-item list-group-item-action">Mis Pedidos</a>
            <a href="<?= base_url('mis-favoritos') ?>" class="list-group-item list-group-item-action">Mis Favoritos</a>
            <a href="<?= base_url('logout') ?>" class="list-group-item list-group-item-action text-danger">Cerrar Sesión</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Datos Personales</h4>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?= base_url('perfil/actualizar') ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?= old('first_name', $user->first_name) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?= old('last_name', $user->last_name) ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="<?= $user->email ?>" disabled>
                        <small class="text-muted">El email no puede ser modificado</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?= old('phone', $user->phone) ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Actualizar Datos</button>
                </form>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>