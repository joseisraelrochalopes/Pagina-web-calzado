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

<?php 
    // 🔥 MAGIA: OBTENER LOS FAVORITOS DEL USUARIO ACTUAL 🔥
    $mis_favoritos_ids = array();
    if(isset($_SESSION['identity'])){
        require_once 'models/Favorito.php';
        $fav_model_view = new Favorito();
        $fav_model_view->setUsuario_id($_SESSION['identity']->id);
        $mis_favs_view = $fav_model_view->getAllByUser();
        if($mis_favs_view){
            while($fav_item = $mis_favs_view->fetch_object()){
                $id_prod = isset($fav_item->producto_id) ? $fav_item->producto_id : $fav_item->id;
                $mis_favoritos_ids[] = $id_prod; 
            }
        }
    }
?>

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
                        
                        <?php if($pro->oferta == 'SI'): ?>
                            <span class="position-absolute top-0 start-0 badge bg-danger m-3 px-2 py-1 shadow" style="z-index: 2;">OFERTA</span>
                        <?php endif; ?>

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
                                <a href="<?=base_url?>producto/ver?id=<?=$pro->id?>" class="text-decoration-none" style="color: #0a192f;">
                                    <?=$pro->nombre?>
                                </a>
                            </h5>
                            
                            <p class="card-text fw-bold fs-5 mt-2 mb-3" style="color: #b89324;">
                                <?=Utils::formatPrice($pro->precio)?>
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto gap-2">
                                <?php if($pro->stock > 0): ?>
                                    <a href="<?=base_url?>producto/ver?id=<?=$pro->id?>" class="btn btn-dark flex-grow-1 rounded-pill shadow-sm" style="font-size: 0.9rem; background-color: #0a192f;">
                                        <i class="bi bi-eye"></i> Ver Tallas
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-danger flex-grow-1 rounded-pill shadow-sm" style="font-size: 0.9rem;" disabled>AGOTADO</button>
                                <?php endif; ?>

                                <?php 
                                    // 🔥 REVISAR SI EL PRODUCTO YA ES FAVORITO 🔥
                                    $es_favorito = in_array($pro->id, $mis_favoritos_ids);
                                ?>

                                <a href="<?=base_url?>favorito/<?= $es_favorito ? 'eliminar' : 'add' ?>?id=<?=$pro->id?>" 
                                   class="btn <?= $es_favorito ? 'btn-danger' : 'btn-outline-danger' ?> rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                                   style="width: 38px; height: 38px; min-width: 38px;" 
                                   title="<?= $es_favorito ? 'Quitar de favoritos' : 'Añadir a favoritos' ?>">
                                    <i class="bi <?= $es_favorito ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                                </a>
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