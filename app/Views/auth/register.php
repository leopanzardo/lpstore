<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<h1>Crear Cuenta</h1>

<p>Completa el siguiente formulario con tus datos para registrarte como cliente de nuestra tienda online y goza de los beneficios que eso conlleva:</p>

<ul>
    <li>Mis pedidos - Lleva un registro de todas las compras que has hecho, pudiendo controlar en qué estado se encuentra y cuánto has gastado en cada una de ellas.</li>
    <li>Mis direcciones - Puedes tener tantas direcciones de entrega de tus pedidos como necesites y seleccionar la que desees utilizar al momento de hacer tu compra y allí te enviaremos tu compra.</li>
    <li>Favoritos - Te gusta un artículo y no puedes comprarlo ahora? No importa, guárdalo en tus favoritos para comprarlo en otro momento. Pero apúrate, porque esto no garantiza que no se venda!</li>
</ul>

<p>Y recuerda, para utilizar nuestro sitio no necesitarás una contraseña, te enviaremos un código por email cada vez que desees iniciar sesión, así te evitas tener que estar recordando contraseñas innecesarias!</p>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
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
                
                <form method="POST" action="<?= base_url('registro') ?>">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?= old('first_name') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?= old('last_name') ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?= old('phone') ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                </form>
                
                <hr>
                
                <div class="text-center">
                    <p>¿Ya tienes cuenta? <a href="<?= base_url('login') ?>">Inicia sesión aquí</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>