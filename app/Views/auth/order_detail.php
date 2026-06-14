<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Detalle del Pedido #<?= $order->order_number ?></h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Información del Pedido</h5>
                        <p>
                            <strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($order->created_at)) ?><br>
                            <strong>Estado:</strong> 
                            <span class="badge bg-<?= $order->status == 'Entregada' ? 'success' : ($order->status == 'Cancelada' ? 'danger' : 'warning') ?>">
                                <?= $order->status ?>
                            </span><br>
                            <strong>Método de pago:</strong> <?= $order->payment_method ?? 'Pendiente' ?><br>
                            <strong>Estado del pago:</strong> <?= ucfirst($order->payment_status) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5>Dirección de Envío</h5>
                        <p><?= nl2br(esc($order->shipping_address)) ?></p>
                    </div>
                </div>
                
                <h5>Productos</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Variación</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order->items as $item): ?>
                                <tr>
                                    <td><?= esc($item->product_name_snapshot) ?></td>
                                    <td><?= $item->variation_snapshot ? esc($item->variation_snapshot) : '-' ?></td>
                                    <td><?= $item->quantity ?></td>
                                    <td>$<?= number_format($item->unit_price, 2) ?></td>
                                    <td>$<?= number_format($item->subtotal, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td><strong>$<?= number_format($order->total_amount, 2) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <a href="<?= base_url('mis-pedidos') ?>" class="btn btn-secondary">Volver a mis pedidos</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>