<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="<?= base_url('perfil') ?>" class="list-group-item list-group-item-action">Mi Perfil</a>
            <a href="<?= base_url('mis-direcciones') ?>" class="list-group-item list-group-item-action">Mis Direcciones</a>
            <a href="<?= base_url('mis-pedidos') ?>" class="list-group-item list-group-item-action active">Mis Pedidos</a>
            <a href="<?= base_url('mis-favoritos') ?>" class="list-group-item list-group-item-action">Mis Favoritos</a>
            <a href="<?= base_url('logout') ?>" class="list-group-item list-group-item-action text-danger">Cerrar Sesión</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Mis Pedidos</h4>
            </div>
            <div class="card-body">
                <?php if (empty($orders)): ?>
                    <p class="text-muted">No has realizado ningún pedido aún.</p>
                    <a href="<?= base_url() ?>" class="btn btn-primary">Ir a la tienda</a>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>N° Pedido</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?= $order->order_number ?></td>
                                        <td><?= date('d/m/Y', strtotime($order->created_at)) ?></td>
                                        <td>$<?= number_format($order->total_amount, 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $order->status == 'Entregada' ? 'success' : ($order->status == 'Cancelada' ? 'danger' : 'warning') ?>">
                                                <?= $order->status ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('pedido/' . $order->id) ?>" class="btn btn-sm btn-info">
                                                Ver Detalle
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>