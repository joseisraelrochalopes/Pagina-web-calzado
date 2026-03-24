<h1 class="text-center mb-4">Carrito de la compra</h1>

<div class="container-fluid">
    <?php if(isset($_SESSION['carrito_error'])): ?>
        <div class="alert alert-warning text-center alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i> <?=$_SESSION['carrito_error']?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php Utils::deleteSession('carrito_error'); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['cupon_error'])): ?>
        <div class="alert alert-danger text-center alert-dismissible fade show">
            <i class="bi bi-x-circle"></i> <?=$_SESSION['cupon_error']?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php Utils::deleteSession('cupon_error'); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['cupon_success'])): ?>
        <div class="alert alert-success text-center alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> <?=$_SESSION['cupon_success']?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php Utils::deleteSession('cupon_success'); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['carrito']) && count($_SESSION['carrito']) >= 1): ?>
        
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center text-nowrap">
                    <tr>
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Talla</th> 
                        <th>Precio</th>
                        <th>Unidades</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach($carrito as $indice => $elemento): 
                        $producto = $elemento['producto'];
                        $p_model = new Producto();
                        $p_model->setId($producto->id);
                        $prod_db = $p_model->getOne();
                    ?>
                    <tr>
                        <td class="text-center" style="min-width: 80px;">
                            <?php if($producto->imagen != null): ?>
                                <img src="<?=base_url?>assets/img/<?=$producto->imagen?>" class="img-fluid rounded" style="max-width: 60px;">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/60" class="img-fluid rounded">
                            <?php endif; ?>
                        </td>
                        <td style="min-width: 150px;">
                            <a href="<?=base_url?>producto/ver?id=<?=$producto->id?>" class="text-decoration-none text-dark fw-bold">
                                <?=$producto->nombre?>
                            </a>
                            <div class="text-muted small">
                                <strong>Stock:</strong> <?=$prod_db ? $prod_db->stock : 'No disponible'?>
                            </div>
                        </td>
                        
                        <td class="text-center fw-bold text-uppercase">
                            <?=$elemento['talla']?>
                        </td>
                        
                        <td class="text-center text-nowrap">$<?=number_format($producto->precio, 2)?> MXN</td>
                        
                        <td class="text-center">
                            <div class="btn-group shadow-sm" role="group">
                                <a href="<?=base_url?>carrito/down?index=<?=$indice?>" class="btn btn-sm btn-outline-secondary">-</a>
                                <button type="button" class="btn btn-sm btn-light fw-bold" disabled><?=$elemento['unidades']?></button>
                                <a href="<?=base_url?>carrito/up?index=<?=$indice?>" class="btn btn-sm btn-outline-secondary">+</a>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="<?=base_url?>carrito/delete?index=<?=$indice?>" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="row mt-4 g-4">
            <div class="col-12 col-lg-6">
                <a href="<?=base_url?>carrito/delete_all" class="btn btn-outline-danger w-100 mb-3" onclick="return confirm('¿Estás seguro?');">
                    <i class="bi bi-x-lg"></i> Vaciar Carrito
                </a>

                <div class="card shadow-sm border-info h-100">
                    <div class="card-body">
                        <h6 class="card-title text-info font-weight-bold"><i class="bi bi-ticket-perforated-fill"></i> ¿Tienes un cupón?</h6>
                        <?php if(!isset($_SESSION['cupon'])): ?>
                            <form action="<?=base_url?>carrito/aplicarCupon" method="POST" class="input-group">
                                <input type="text" name="codigo" class="form-control" placeholder="Código" required>
                                <button class="btn btn-info text-white" type="submit">Aplicar</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-success mb-0 d-flex justify-content-between align-items-center">
                                <span>
                                    <strong><?=$_SESSION['cupon']['codigo']?></strong> (<?=$_SESSION['cupon']['porcentaje']?>% Dto.)
                                </span>
                                <a href="<?=base_url?>carrito/borrarCupon" class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card bg-light shadow-sm border-0 h-100">
                    <div class="card-body">
                        <?php $stats = Utils::statsCarrito(); ?>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span class="fw-bold">$<?=number_format($stats['total'], 2)?> MXN</span>
                        </div>

                        <?php if(isset($_SESSION['cupon'])): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Descuento:</span>
                                <span>- $<?=number_format($stats['descuento'], 2)?> MXN</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3 align-items-center">
                                <h4 class="fw-bold mb-0">Total:</h4>
                                <h4 class="fw-bold text-primary mb-0">$<?=number_format($stats['total_con_descuento'], 2)?> MXN</h4>
                            </div>
                        <?php else: ?>
                            <hr>
                            <div class="d-flex justify-content-between mb-3 align-items-center">
                                <h4 class="fw-bold mb-0">Total:</h4>
                                <h4 class="fw-bold text-primary mb-0">$<?=number_format($stats['total'], 2)?> MXN</h4>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid mt-3">
                            <a href="<?=base_url?>pedido/hacer" class="btn btn-success btn-lg py-3 fw-bold shadow">
                                CONFIRMAR PEDIDO <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="alert alert-info text-center p-5 shadow-sm rounded">
            <i class="bi bi-cart-x display-1"></i>
            <h4 class="mt-3 fw-bold">El carrito está vacío</h4>
            <p>Añade algún calzado para comenzar</p>
            <a href="<?=base_url?>" class="btn btn-primary px-4">Ver productos</a>
        </div>
    <?php endif; ?>
</div>