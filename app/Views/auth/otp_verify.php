<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Verifica tu código</h4>
            </div>
            <div class="card-body">
                <p>Ingresa el código de 6 dígitos que enviamos a tu email.</p>
                
                <form method="POST" action="<?= base_url('auth/verify-otp') ?>">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="code" class="form-label">Código de verificación</label>
                        <input type="text" class="form-control text-center" id="code" name="code" 
                               maxlength="6" pattern="[0-9]{6}" required
                               style="font-size: 2rem; letter-spacing: 5px;">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-check"></i> Verificar y entrar
                    </button>
                </form>
                
                <hr>
                
                <div class="text-center">
                    <a href="<?= base_url('login') ?>">¿No recibiste el código? Intentar de nuevo</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('code').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
});
</script>

<?= $this->endSection() ?>