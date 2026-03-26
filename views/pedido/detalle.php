<style>
    /* ESTILOS PARA LA LÍNEA DE TIEMPO (TRACKING) */
    .tracking-track {
        position: relative;
        background-color: #ddd;
        height: 5px;
        display: flex;
        margin-bottom: 50px;
        margin-top: 25px;
    }
    /* 🔥 SE MODIFICÓ AL 20% PARA QUE QUEPAN LOS 5 PASOS 🔥 */
    .tracking-step {
        flex-grow: 1;
        width: 20%; 
        margin-top: -12px;
        text-align: center;
        position: relative;
    }
    .tracking-step .icon {
        display: inline-block;
        width: 30px;
        height: 30px;
        line-height: 30px;
        position: relative;
        border-radius: 100%;
        background: #ddd;
        color: #fff;
    }
    .tracking-step .text {
        display: block;
        margin-top: 7px;
        font-size: 12px;
        color: #999;
    }
    /* Estado Activo */
    .tracking-step.active .icon {
        background: #0d6efd; /* Azul Bootstrap */
    }
    .tracking-step.active .text {
        font-weight: bold;
        color: #000;
    }
    /* Barra de progreso */
    .tracking-bar {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        background-color: #0d6efd;
        width: 0%;
        transition: width 0.5s;
    }
    /* Efecto para el botón de detalles */
    .btn-detalle {
        transition: all 0.3s ease;
        border-radius: 8px;
    }
    .btn-detalle:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3) !important;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Detalle del pedido #<?= $pedido->id ?></h1>
    <div>
        <a href="<?=base_url?>pedido/factura?id=<?=$pedido->id?>" target="_blank" class="btn btn-outline-dark me-2">
            <i class="bi bi-printer"></i> Factura
        </a>
        
        <?php if($pedido->estado == 'confirm'): ?>
            <a href="<?=base_url?>pedido/cancelar?id=<?=$pedido->id?>" class="btn btn-outline-danger" onclick="return confirm('¿Seguro que quieres cancelar? El stock se devolverá.');">
                <i class="bi bi-x-circle"></i> Cancelar Pedido
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if(isset($_SESSION['status_pedido']) && $_SESSION['status_pedido'] == 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <strong><i class="bi bi-check-circle-fill fs-5 me-2"></i>¡Excelente!</strong> El estado del pedido se ha actualizado y se ha enviado la notificación por correo al cliente.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['status_pedido']); // Borramos el mensaje para que no salga siempre ?>
<?php endif; ?>
<?php if (isset($pedido)): ?>
    
    <?php if($pedido->estado != 'cancelled'): ?>
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <div class="tracking-track">
                    <?php 
                        $width = '0%';
                        if($pedido->estado == 'confirm') $width = '20%';
                        elseif($pedido->estado == 'preparation') $width = '40%';
                        elseif($pedido->estado == 'ready') $width = '60%';
                        elseif($pedido->estado == 'sended') $width = '80%';
                        elseif($pedido->estado == 'delivered') $width = '100%';
                    ?>
                    <div class="tracking-bar" style="width: <?=$width?>;"></div>
                    
                    <div class="tracking-step <?=($pedido->estado == 'confirm' || $pedido->estado == 'preparation' || $pedido->estado == 'ready' || $pedido->estado == 'sended' || $pedido->estado == 'delivered') ? 'active' : ''?>">
                        <span class="icon"><i class="bi bi-check"></i></span>
                        <span class="text">Confirmado</span>
                    </div>
                    <div class="tracking-step <?=($pedido->estado == 'preparation' || $pedido->estado == 'ready' || $pedido->estado == 'sended' || $pedido->estado == 'delivered') ? 'active' : ''?>">
                        <span class="icon"><i class="bi bi-box-seam"></i></span>
                        <span class="text">Preparando</span>
                    </div>
                    <div class="tracking-step <?=($pedido->estado == 'ready' || $pedido->estado == 'sended' || $pedido->estado == 'delivered') ? 'active' : ''?>">
                        <span class="icon"><i class="bi bi-box-arrow-right"></i></span>
                        <span class="text">Listo envío</span>
                    </div>
                    <div class="tracking-step <?=($pedido->estado == 'sended' || $pedido->estado == 'delivered') ? 'active' : ''?>">
                        <span class="icon"><i class="bi bi-truck"></i></span>
                        <span class="text">Enviado</span>
                    </div>
                    <div class="tracking-step <?=($pedido->estado == 'delivered') ? 'active' : ''?>">
                        <span class="icon"><i class="bi bi-house-door"></i></span>
                        <span class="text">Entregado</span>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger text-center mb-4">
            <h4><i class="bi bi-x-octagon-fill"></i> ESTE PEDIDO HA SIDO CANCELADO</h4>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['admin'])): ?>
        <div class="card mb-4 border-primary shadow-sm">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="bi bi-gear-fill"></i> Gestión Administrativa
            </div>
            <div class="card-body">
                <form action="<?=base_url?>pedido/estado" method="POST" class="row g-3 align-items-center mb-3">
                    <input type="hidden" name="pedido_id" value="<?=$pedido->id?>">
                    <div class="col-md-8">
                        <select name="estado" class="form-select fw-bold border-primary">
                            <option value="confirm" <?=$pedido->estado == "confirm" ? 'selected' : '';?>>Pendiente</option>
                            <option value="preparation" <?=$pedido->estado == "preparation" ? 'selected' : '';?>>En preparación</option>
                            <option value="ready" <?=$pedido->estado == "ready" ? 'selected' : '';?>>Preparado para enviar</option>
                            <option value="sended" <?=$pedido->estado == "sended" ? 'selected' : '';?>>Enviado</option>
                            <option value="delivered" <?=$pedido->estado == "delivered" ? 'selected' : '';?>>Entregado / Finalizado</option>
                            <option value="cancelled" <?=$pedido->estado == "cancelled" ? 'selected' : '';?>>Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Actualizar Estado</button>
                    </div>
                </form>
                
                <hr>
                
                <?php 
                    $estado_texto = "en proceso";
                    if($pedido->estado == 'confirm') $estado_texto = "CONFIRMADO y en espera de preparación";
                    elseif($pedido->estado == 'preparation') $estado_texto = "EN PREPARACIÓN en nuestro almacén";
                    elseif($pedido->estado == 'ready') $estado_texto = "LISTO PARA ENVIARSE";
                    elseif($pedido->estado == 'sended') $estado_texto = "ENVIADO y va en camino a tu domicilio";
                    elseif($pedido->estado == 'delivered') $estado_texto = "ENTREGADO FINALIZADO. ¡Gracias por tu compra!";
                    elseif($pedido->estado == 'cancelled') $estado_texto = "CANCELADO";

                    $mensaje_wa = "¡Hola! Te contactamos para informarte que tu pedido #" . $pedido->id . " de zapatos ha sido actualizado y ahora se encuentra: *" . $estado_texto . "*. ¡Cualquier duda estamos a tus órdenes!";
                ?>
                <a href="https://wa.me/?text=<?=urlencode($mensaje_wa)?>" target="_blank" class="btn btn-success w-100 fw-bold shadow-sm" style="background-color: #25D366; border-color: #25D366;">
                    <i class="bi bi-whatsapp"></i> Notificar este cambio al cliente por WhatsApp
                </a>

            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <i class="bi bi-truck"></i> Datos de envío
                </div>
                <div class="card-body">
                    <p><strong>Estado Actual:</strong> 
                        <?php
                            if($pedido->estado == 'confirm') echo "PENDIENTE";
                            elseif($pedido->estado == 'preparation') echo "EN PREPARACIÓN";
                            elseif($pedido->estado == 'ready') echo "LISTO PARA ENVIAR";
                            elseif($pedido->estado == 'sended') echo "ENVIADO";
                            elseif($pedido->estado == 'delivered') echo "ENTREGADO";
                            elseif($pedido->estado == 'cancelled') echo "CANCELADO";
                            else echo strtoupper($pedido->estado);
                        ?>
                    </p>
                    
                    <p><strong>Método de Pago:</strong> <?=strtoupper($pedido->metodo_pago ?? 'No especificado')?></p>
                    
                    <hr>
                    <p><strong>Provincia:</strong> <?= $pedido->provincia ?></p>
                    <p><strong>Ciudad:</strong> <?= $pedido->localidad ?></p>
                    <p><strong>Dirección:</strong> <?= $pedido->direccion ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <i class="bi bi-basket"></i> Resumen
                </div>
                <div class="card-body">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Unidades</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($producto = $productos->fetch_object()): ?>
                                <tr>
                                    <td class="text-center" style="width: 80px;">
                                        <img src="<?=Utils::showImage($producto->imagen)?>" class="img-fluid rounded" width="50">
                                    </td>
                                    <td>
                                        <a href="<?= base_url ?>producto/ver?id=<?= $producto->id ?>" class="text-decoration-none text-dark">
                                            <?= $producto->nombre ?>
                                        </a>
                                        <?php if(isset($producto->talla) && $producto->talla != 'Única'): ?>
                                            <br><span class="badge bg-secondary text-light mt-1">Talla: <?=$producto->talla?></span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td><?=Utils::formatPrice($producto->precio)?></td>
                                    
                                    <td><?= $producto->unidades ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    
                    <div class="text-end mt-3">
                        <h3 class="text-success">Total: <?=Utils::formatPrice($pedido->coste)?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>