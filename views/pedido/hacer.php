<?php if(isset($_SESSION['identity'])): ?>
    <h1 class="mb-4 text-center">Finalizar Compra</h1>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <i class="bi bi-truck"></i> Datos de Envío y Pago
                </div>
                <div class="card-body p-4">
                    
                    <?php $stats = Utils::statsCarrito(); ?>
                    
                    <div class="alert alert-secondary mb-4 d-flex justify-content-between align-items-center">
                        <div>
                            Total a pagar: 
                            <strong class="fs-4 text-success ms-2">$<?=number_format($stats['total_con_descuento'], 2)?></strong>
                            
                            <?php if($stats['descuento'] > 0): ?>
                                <small class="text-muted ms-2 text-decoration-line-through">$<?=number_format($stats['total'], 2)?></small>
                                <span class="badge bg-info text-dark ms-1">Cupón aplicado</span>
                            <?php endif; ?>
                        </div>
                        <a href="<?=base_url?>carrito/index" class="text-decoration-none small">Volver al carrito</a>
                    </div>

                    <form action="<?=base_url?>pedido/add" method="POST">
                        
                        <h5 class="mb-3 border-bottom pb-2">Dirección de Entrega</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Provincia / Estado</label>
                                <input type="text" name="provincia" class="form-control" placeholder="Ej: Lima" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ciudad / Distrito</label>
                                <input type="text" name="localidad" class="form-control" placeholder="Ej: Miraflores" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Dirección Completa</label>
                            <input type="text" name="direccion" class="form-control" placeholder="Av. Larco 123, Dpto 401" required>
                        </div>

                        <h5 class="mb-3 border-bottom pb-2">Método de Pago</h5>
                        
                        <div class="payment-options mb-4">
                            <div class="form-check p-3 border rounded mb-2 bg-light">
                                <input class="form-check-input" type="radio" name="metodo_pago" id="pago_tarjeta" value="Tarjeta de Crédito/Débito" checked>
                                <label class="form-check-label w-100 fw-bold" for="pago_tarjeta">
                                    <i class="bi bi-credit-card-2-front-fill text-primary"></i> Tarjeta de Crédito / Débito
                                    <small class="d-block text-muted fw-normal">Visa, Mastercard, Amex</small>
                                </label>
                            </div>

                            <div class="form-check p-3 border rounded mb-2 bg-light">
                                <input class="form-check-input" type="radio" name="metodo_pago" id="pago_paypal" value="PayPal">
                                <label class="form-check-label w-100 fw-bold" for="pago_paypal">
                                    <i class="bi bi-paypal text-primary"></i> PayPal
                                    <small class="d-block text-muted fw-normal">Paga seguro con tu cuenta PayPal</small>
                                </label>
                            </div>

                            <div class="form-check p-3 border rounded bg-light">
                                <input class="form-check-input" type="radio" name="metodo_pago" id="pago_transferencia" value="Transferencia Bancaria">
                                <label class="form-check-label w-100 fw-bold" for="pago_transferencia">
                                    <i class="bi bi-bank text-primary"></i> Transferencia Bancaria
                                    <small class="d-block text-muted fw-normal">BCP, Interbank, BBVA</small>
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <input type="submit" value="Confirmar y Pagar" class="btn btn-success btn-lg">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="text-center mt-5">
        <h1 class="mb-4">Necesitas identificarte</h1>
        <p class="lead">Para tramitar el pedido debes iniciar sesión.</p>
        <a href="<?=base_url?>usuario/login" class="btn btn-primary">Ir al Login</a>
    </div>
<?php endif; ?>