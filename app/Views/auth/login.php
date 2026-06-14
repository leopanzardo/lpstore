<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<h1>Iniciar Sesión</h1>

<?= 'Session: '. session('otp_email') ?>

<p>Si eres un usuario regristrado puedes iniciar sesión ingresando tu email, haciendo clic en el botón Enviar código y te enviaremos un código de acceso que podrás utilizar para ingresar a la tienda. No necesitas utilizar, ni estar recordando contraseñas &#128521;</p>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                
                <form method="POST" action="<?= base_url('auth/send-otp') ?>">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-envelope"></i> Enviar código
                    </button>
                </form>
                
                <hr>
                
                <div class="text-center">
                    <p>¿No tienes cuenta? <a href="<?= base_url('registro') ?>">Regístrate aquí</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>