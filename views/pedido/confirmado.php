<?php if (isset($_SESSION['pedido']) && $_SESSION['pedido'] == 'complete'): ?>

    <div class="text-center mb-5">
        <h1 class="display-4 text-success"><i class="bi bi-check-circle-fill"></i> ¡Pedido Registrado!</h1>
        <p class="lead">Tu pedido ha sido guardado con éxito. A continuación, realiza el pago según el método elegido.</p>
    </div>

    <?php if (isset($pedido)): ?>
        <div class="row">
            <div class="col-md-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <i class="bi bi-cart-check"></i> Resumen de Compra
                    </div>
                    <div class="card-body">
                        <h3>Orden: <span class="text-primary">#<?= $pedido->id ?></span></h3>
                        <h3>Total: <span class="text-success">$<?= number_format($pedido->coste, 2) ?></span></h3>
                        <hr>
                        <h4 class="mt-4 mb-3 small fw-bold text-uppercase text-muted">Productos:</h4>
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Precio</th>
                                    <th>Cant.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($producto = $productos->fetch_object()): ?>
                                    <tr>
                                        <td>
                                            <?php if ($producto->imagen != null): ?>
                                                <img src="<?= base_url ?>assets/img/<?= $producto->imagen ?>" class="img-thumbnail" width="50">
                                            <?php else: ?>
                                                <img src="https://via.placeholder.com/50" class="img-thumbnail">
                                            <?php endif; ?>
                                        </td>
                                        <td class="small"><?= $producto->nombre ?></td>
                                        <td class="small">$<?= $producto->precio ?></td>
                                        <td class="small"><?= $producto->unidades ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card shadow border-primary">
                    <div class="card-header bg-primary text-white fw-bold text-center">
                        INSTRUCCIONES DE PAGO
                    </div>
                    <div class="card-body text-center p-4">
                        
                        <?php if ($pedido->metodo_pago == 'PayPal'): ?>
                            <i class="bi bi-paypal text-primary display-4 mb-3"></i>
                            <h5>Paga con PayPal o Tarjeta</h5>
                            <p class="small text-muted">Haz clic en el botón de abajo para completar el pago de forma segura.</p>
                            
                            <div id="paypal-button-container"></div>

                            <script src="https://www.paypal.com/sdk/js?client-id=test&currency=MXN"></script>
                            <script>
                                paypal.Buttons({
                                    createOrder: function(data, actions) {
                                        return actions.order.create({
                                            purchase_units: [{
                                                amount: { value: '<?= $pedido->coste ?>' }
                                            }]
                                        });
                                    },
                                    onApprove: function(data, actions) {
                                        return actions.order.capture().then(function(details) {
                                            alert('¡Pago exitoso, ' + details.payer.name.given_name + '!');
                                            window.location.href = "<?= base_url ?>carrito/delete_all";
                                        });
                                    }
                                }).render('#paypal-button-container');
                            </script>

                        <?php elseif ($pedido->metodo_pago == 'Transferencia Bancaria'): ?>
                            <i class="bi bi-bank text-info display-4 mb-3"></i>
                            <h5>Transferencia Electrónica</h5>
                            <div class="bg-light p-3 rounded border text-start mb-3">
                                <p class="mb-1 small"><strong>Banco:</strong> BBVA / Banco Azteca</p>
                                <p class="mb-1 small"><strong>CLABE:</strong> 0123 4567 8901 2345 67</p>
                                <p class="mb-1 small"><strong>Concepto:</strong> <span class="badge bg-dark">PEDIDO-<?= $pedido->id ?></span></p>
                            </div>
                            <p class="small text-muted italic">Envía tu comprobante a pagos@tutienda.com</p>

                        <?php else: ?>
                            <i class="bi bi-shop text-warning display-4 mb-3"></i>
                            <h5>Pago en OXXO / Efectivo</h5>
                            <p class="small">Dicta esta referencia al cajero:</p>
                            <h3 class="fw-bold bg-warning d-inline-block px-3 py-1 rounded">
                                9000-<?= str_pad($pedido->id, 4, "0", STR_PAD_LEFT) ?>-<?= date('s') ?>
                            </h3>
                            <p class="mt-3 small text-muted text-start">
                                <i class="bi bi-info-circle"></i> El pago puede tardar hasta 24 horas en acreditarse. Guarda tu ticket.
                            </p>
                        <?php endif; ?>

                        <?php if ($pedido->metodo_pago != 'PayPal'): ?>
                            <a href="https://wa.me/525512345678?text=Hola,%20acabo%20de%20realizar%20el%20pedido%20%23<?= $pedido->id ?>.%20Aquí%20está%20mi%20comprobante%20de%20pago." target="_blank" class="btn w-100 mb-3 fw-bold text-white shadow-sm py-2" style="background-color: #25D366; border-color: #25D366;">
                                <i class="bi bi-whatsapp fs-5 me-2 align-middle"></i> Enviar comprobante
                            </a>
                        <?php endif; ?>
                        <hr>
                        <a href="<?= base_url ?>pedido/mis_pedidos" class="btn btn-dark w-100 mb-2">Ver Mis Pedidos</a>
                        <a href="<?= base_url ?>" class="btn btn-outline-secondary w-100">Seguir Comprando</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php elseif (isset($_SESSION['pedido']) && $_SESSION['pedido'] != 'complete'): ?>
    <div class="alert alert-danger text-center p-5">
        <i class="bi bi-x-octagon display-1"></i>
        <h1 class="mt-3">Tu pedido NO ha podido procesarse</h1>
        <p>Hubo un error interno al guardar la orden. Por favor intenta de nuevo.</p>
        <a href="<?= base_url ?>carrito/index" class="btn btn-danger">Volver al carrito</a>
    </div>
<?php endif; ?>