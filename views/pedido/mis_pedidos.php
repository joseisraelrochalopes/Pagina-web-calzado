<style>
    /* Efecto para el botón de detalles */
    .btn-detalle {
        transition: all 0.3s ease;
        border-radius: 8px;
    }
    .btn-detalle:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3) !important;
    }
    
    /* Efecto hover suave para las filas de la tabla */
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <?php if(isset($gestion)): ?>
        <h1><i class="bi bi-gear-fill text-dark"></i> Gestionar Pedidos Activos</h1>
        <a href="<?=base_url?>pedido/exportar" class="btn btn-success fw-bold shadow-sm">
            <i class="bi bi-file-earmark-spreadsheet"></i> Exportar a Excel
        </a>
    <?php else: ?>
        <h1><i class="bi bi-bag-check-fill text-dark"></i> Mis Pedidos</h1>
    <?php endif; ?>
</div>

<?php if(isset($gestion)): ?>
<div class="card mb-4 bg-light border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="<?=base_url?>pedido/gestion" method="POST" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small text-muted fw-bold">Buscar por ID:</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0">#</span>
                    <input type="number" name="busquedaId" class="form-control border-start-0" placeholder="Ej: 5" value="<?=isset($_POST['busquedaId']) ? $_POST['busquedaId'] : ''?>">
                    <button class="btn btn-primary px-3" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted fw-bold">Filtrar por Estado:</label>
                <select name="filtroEstado" class="form-select shadow-sm" onchange="this.form.submit()">
                    <option value="">-- Ver Activos --</option>
                    <option value="confirm" <?= (isset($_POST['filtroEstado']) && $_POST['filtroEstado'] == 'confirm') ? 'selected' : '' ?>>Pendientes (Esperando Pago)</option>
                    <option value="preparation" <?= (isset($_POST['filtroEstado']) && $_POST['filtroEstado'] == 'preparation') ? 'selected' : '' ?>>En Preparación</option>
                    <option value="ready" <?= (isset($_POST['filtroEstado']) && $_POST['filtroEstado'] == 'ready') ? 'selected' : '' ?>>Listos para enviar</option>
                    <option value="sended" <?= (isset($_POST['filtroEstado']) && $_POST['filtroEstado'] == 'sended') ? 'selected' : '' ?>>Enviados</option>
                    
                    <option value="delivered" <?= (isset($_POST['filtroEstado']) && $_POST['filtroEstado'] == 'delivered') ? 'selected' : '' ?>>Entregados / Finalizados</option>
                    
                    <option value="cancelled" <?= (isset($_POST['filtroEstado']) && $_POST['filtroEstado'] == 'cancelled') ? 'selected' : '' ?>>Cancelados</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <a href="<?=base_url?>pedido/gestion" class="btn btn-outline-secondary w-100 shadow-sm fw-bold">
                    <i class="bi bi-arrow-counterclockwise"></i> Limpiar Filtros
                </a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <?php if($pedidos->num_rows == 0): ?>
            <div class="alert alert-warning text-center m-4 fw-bold p-4 rounded-3">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i> No hay pedidos en esta sección.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover m-0 text-center align-middle border-top">
                    <thead class="table-dark">
                        <tr>
                            <th class="py-3">Nº Pedido</th>
                            <th class="py-3">Fecha</th>
                            <th class="py-3">Método de Pago</th>
                            <th class="py-3">Total</th>
                            <th class="py-3">Estado</th>
                            <th class="py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($ped = $pedidos->fetch_object()): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark fs-5">#<?=$ped->id?></span>
                                </td>
                                
                                <td class="small text-muted fw-semibold"><?=$ped->fecha?></td>

                                <td>
                                    <?php if($ped->metodo_pago == 'PayPal'): ?>
                                        <span class="badge rounded-pill bg-light text-primary border border-primary px-3 py-2">
                                            <i class="bi bi-paypal me-1"></i> PayPal
                                        </span>
                                    <?php elseif($ped->metodo_pago == 'Transferencia Bancaria'): ?>
                                        <span class="badge rounded-pill bg-light text-info border border-info px-3 py-2">
                                            <i class="bi bi-bank me-1"></i> Transf.
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-light text-warning border border-warning text-dark px-3 py-2">
                                            <i class="bi bi-shop me-1"></i> OXXO / Efectivo
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="fw-bold text-success fs-6">$<?=number_format($ped->coste, 2)?> MXN</td>
                                
                                <td>
                                    <?php if($ped->estado == 'confirm'): ?>
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm">Pendiente</span>
                                    <?php elseif($ped->estado == 'preparation'): ?>
                                        <span class="badge bg-info text-dark px-3 py-2 rounded-pill shadow-sm">En preparación</span>
                                    <?php elseif($ped->estado == 'ready'): ?>
                                        <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm">Preparado</span>
                                    <?php elseif($ped->estado == 'sended'): ?>
                                        <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm">Enviado</span>
                                    
                                    <?php elseif($ped->estado == 'delivered'): ?>
                                        <span class="badge bg-dark text-white px-3 py-2 rounded-pill shadow-sm">Entregado</span>
                                        
                                    <?php elseif($ped->estado == 'cancelled'): ?>
                                        <span class="badge bg-danger px-3 py-2 rounded-pill shadow-sm">Cancelado</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary px-3 py-2 rounded-pill shadow-sm"><?=$ped->estado?></span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a href="<?=base_url?>pedido/detalle?id=<?=$ped->id?>" class="btn btn-primary btn-sm btn-detalle fw-bold px-3 shadow-sm">
                                        <i class="bi bi-eye-fill me-1"></i> Ver Detalles
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>