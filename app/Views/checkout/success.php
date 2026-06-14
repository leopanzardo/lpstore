<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card text-center">
            <div class="card-body py-5">
                <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                <h2 class="mt-3">¡Gracias por tu compra!</h2>
                <p class="lead">Tu pedido #<?= $order->order_number ?> ha sido confirmado.</p>
                <p>Te hemos enviado un email con los detalles de tu compra.</p>
                <hr>
                <a href="<?= base_url() ?>" class="btn btn-primary">Seguir comprando</a>
                <a href="<?= base_url('mis-pedidos') ?>" class="btn btn-outline-secondary">Ver mis pedidos</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>