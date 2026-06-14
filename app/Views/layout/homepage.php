<?= $this->include('layout/header'); ?>

<div class="bg-info bg-gradient text-white text-center py-5 mb-5 shadow" style="margin-top: 80px">
    <h1 class="display-4">Bienvenido a <?= $lpConfig->storeName ?></h1>
    <p class="lead">Los mejores productos al mejor precio</p>
    <a href="<?= base_url('categoria/todas') ?>" class="btn btn-light btn-lg">Ver productos</a>
</div>

<div class="container">

<?= $this->renderSection('content'); ?>

</div>

<?= $this->include('layout/footer'); ?>