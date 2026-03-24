<style>
    /* ✨ EFECTOS PREMIUM PARA CATEGORÍAS ✨ */
    .card-efecto {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
        border: none !important;
        background-color: #fff;
    }
    .card-efecto:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.2) !important;
    }
    .img-wrapper {
        overflow: hidden;
        background-color: #f8f9fa;
    }
    .img-efecto {
        transition: transform 0.5s ease;
    }
    .card-efecto:hover .img-efecto {
        transform: scale(1.08);
    }
    .titulo-categoria {
        color: #0a192f;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        position: relative;
        display: inline-block;
        padding-bottom: 10px;
    }
    .titulo-categoria::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 25%;
        width: 50%;
        height: 4px;
        background-color: #b89324;
        border-radius: 10px;
    }
</style>

<?php if(isset($categoria) && is_object($categoria)): ?>
    <div class="text-center mb-5">
        <h1 class="titulo-categoria">
            <i class="bi bi-star-fill text-warning me-2"></i> <?= $categoria->nombre ?>
        </h1>
    </div>
    
    <?php if(!isset($productos) || $productos == null || $productos->num_rows == 0): ?>
        <div class="alert shadow-sm text-center py-5" style="background-color: #fff; border-left: 5px solid #b89324; border-radius: 15px;">
            <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
            <h3 class="mt-3 text-dark">No hay productos para mostrar en esta categoría.</h3>
            <a href="<?=base_url?>" class="btn btn-dark mt-3 rounded-pill px-4" style="background-color: #0a192f;">Volver al inicio</a>
        </div>
    <?php else: ?>
        
        <div class="row">
            <?php while($pro = $productos->fetch_object()): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 shadow-sm card-efecto <?=$pro->stock == 0 ? 'opacity-75' : ''?>">
                        
                        <div class="img-wrapper text-center">
                            <a href="<?=base_url?>producto/ver?id=<?=$pro->id?>">
                                <?php if($pro->imagen != null): ?>
                                    <img src="<?=base_url?>assets/img/<?=$pro->imagen?>" class="card-img-top p-4 img-efecto" alt="<?=$pro->nombre?>" style="height: 220px; object-fit: contain;">
                                <?php else: ?>
                                    <img src="<?=base_url?>assets/img/no-image.png" class="card-img-top p-4 img-efecto" alt="Sin imagen" style="height: 220px; object-fit: contain;">
                                <?php endif; ?>
                            </a>
                        </div>
                        
                        <div class="card-body d-flex flex-column text-center">
                            <h5 class="card-title mb-1 fw-bold" style="font-size: 1rem; color: #0a192f;">
                                <?=$pro->nombre?>
                            </h5>
                            
                            <p class="card-text fw-bold fs-5 mt-2 mb-3" style="color: #b89324;">
                                <?=Utils::formatPrice($pro->precio)?>
                            </p>
                            
                            <div class="mt-auto">
                                <?php if($pro->stock > 0): ?>
                                    <a href="<?=base_url?>producto/ver?id=<?=$pro->id?>" class="btn btn-dark w-100 rounded-pill shadow-sm py-2" style="background-color: #0a192f; border: none; font-size: 0.9rem;">
                                        <i class="bi bi-cart-plus"></i> VER OPCIONES
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-danger w-100 rounded-pill shadow-sm py-2" style="font-size: 0.9rem;" disabled>
                                        <i class="bi bi-x-circle"></i> AGOTADO
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        
    <?php endif; ?>

<?php else: ?>
    <div class="alert alert-danger text-center shadow py-5" style="border-radius: 15px;">
        <i class="bi bi-x-octagon fs-1"></i>
        <h1 class="mt-3">La categoría no existe</h1>
        <a href="<?=base_url?>" class="btn btn-outline-danger mt-3 rounded-pill px-5">Volver al inicio</a>
    </div>
<?php endif; ?>