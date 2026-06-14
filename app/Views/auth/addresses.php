<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="<?= base_url('perfil') ?>" class="list-group-item list-group-item-action">Mi Perfil</a>
            <a href="<?= base_url('mis-direcciones') ?>" class="list-group-item list-group-item-action active">Mis Direcciones</a>
            <a href="<?= base_url('mis-pedidos') ?>" class="list-group-item list-group-item-action">Mis Pedidos</a>
            <a href="<?= base_url('mis-favoritos') ?>" class="list-group-item list-group-item-action">Mis Favoritos</a>
            <a href="<?= base_url('logout') ?>" class="list-group-item list-group-item-action text-danger">Cerrar Sesión</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Mis Direcciones</h4>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addressModal">
                    <i class="fas fa-plus"></i> Agregar Dirección
                </button>
            </div>
            <div class="card-body">
                <?php if (empty($addresses)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No tienes direcciones guardadas.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addressModal">
                            Agregar mi primera dirección
                        </button>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($addresses as $address): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 <?= $defaultAddress && $defaultAddress->id == $address->id ? 'border-primary border-2' : '' ?>">
                                    <div class="card-body">
                                        <?php if ($defaultAddress && $defaultAddress->id == $address->id): ?>
                                            <span class="badge bg-primary mb-2 float-end">Principal</span>
                                        <?php endif; ?>
                                        <p class="mb-1"><strong><?= esc($address->address_line1) ?></strong></p>
                                        <?php if ($address->address_line2): ?>
                                            <p class="mb-1"><?= esc($address->address_line2) ?></p>
                                        <?php endif; ?>
                                        <p class="mb-1"><?= esc($address->city) ?></p>
                                        <?php if ($address->state): ?>
                                            <p class="mb-1"><?= esc($address->state) ?></p>
                                        <?php endif; ?>
                                        <p class="mb-1"><?= esc($address->postal_code) ?> - <?= esc($address->country) ?></p>
                                        <hr>
                                        <div class="btn-group w-100" role="group">
                                            <?php if (!$defaultAddress || $defaultAddress->id != $address->id): ?>
                                                <button class="btn btn-sm btn-outline-success set-default-address" 
                                                        data-id="<?= $address->id ?>">
                                                    <i class="fas fa-check-circle"></i> Principal
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-outline-primary edit-address" 
                                                    data-id="<?= $address->id ?>"
                                                    data-line1="<?= esc($address->address_line1) ?>"
                                                    data-line2="<?= esc($address->address_line2) ?>"
                                                    data-city="<?= esc($address->city) ?>"
                                                    data-state="<?= esc($address->state) ?>"
                                                    data-postal="<?= esc($address->postal_code) ?>"
                                                    data-country="<?= esc($address->country) ?>">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-address" 
                                                    data-id="<?= $address->id ?>">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar dirección -->
<div class="modal fade" id="addressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Dirección</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= base_url('direccion/agregar') ?>" id="addAddressForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Dirección Línea 1</label>
                        <input type="text" class="form-control" name="address_line1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección Línea 2</label>
                        <input type="text" class="form-control" name="address_line2">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ciudad</label>
                        <input type="text" class="form-control" name="city" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Departamento/Estado</label>
                        <input type="text" class="form-control" name="state">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Código Postal</label>
                        <input type="text" class="form-control" name="postal_code">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">País</label>
                        <input type="text" class="form-control" name="country" value="Uruguay" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_default" value="1" id="isDefault">
                        <label class="form-check-label" for="isDefault">Establecer como dirección principal</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Dirección</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar dirección -->
<div class="modal fade" id="editAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Dirección</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" id="editAddressForm">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Dirección Línea 1</label>
                        <input type="text" class="form-control" name="address_line1" id="edit_line1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección Línea 2</label>
                        <input type="text" class="form-control" name="address_line2" id="edit_line2">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ciudad</label>
                        <input type="text" class="form-control" name="city" id="edit_city" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Departamento/Estado</label>
                        <input type="text" class="form-control" name="state" id="edit_state">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Código Postal</label>
                        <input type="text" class="form-control" name="postal_code" id="edit_postal">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">País</label>
                        <input type="text" class="form-control" name="country" id="edit_country" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>