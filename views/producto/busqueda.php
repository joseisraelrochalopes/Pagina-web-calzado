<?php if(isset($busqueda)): ?>
    <h1 class="text-center mb-5">Resultados de: "<?= htmlspecialchars($busqueda) ?>"</h1>
<?php else: ?>
    <h1 class="text-center mb-5">Resultados de búsqueda</h1>
<?php endif; ?>

<?php if($productos->num_rows == 0): ?>
    <div class="alert alert-warning text-center">
        No se han encontrado productos que coincidan con tu búsqueda.
    </div>
<?php else: ?>

    <div class="row">
        <?php while($pro = $productos->fetch_object()): ?>
            
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm product-card <?=$pro->stock == 0 ? 'opacity-75' : ''?>">
                    
                    <?php if($pro->imagen != null): ?>
                        <img src="<?=base_url?>assets/img/<?=$pro->imagen?>" class="card-img-top p-3" alt="<?=$pro->nombre?>">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/300x200?text=Sin+Imagen" class="card-img-top" alt="Sin imagen">
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?=$pro->nombre?></h5>
                        <p class="card-text text-muted"><?=number_format($pro->precio, 2)?> USD</p>
                        
                        <?php if($pro->stock > 0): ?>
                            <p class="text-success" style="font-size: 0.9rem;">
                                <i class="bi bi-box-seam"></i> Stock: <?=$pro->stock?>
                            </p>
                            <a href="<?=base_url?>carrito/add?id=<?=$pro->id?>" class="btn btn-primary mt-auto w-100">
                                <i class="bi bi-cart-plus"></i> Comprar
                            </a>
                        <?php else: ?>
                            <button class="btn btn-danger mt-auto w-100" disabled>
                                <i class="bi bi-x-circle"></i> AGOTADO
                            </button>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>

        <?php endwhile; ?>
    </div>

<?php endif; ?>