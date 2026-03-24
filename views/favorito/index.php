<h1 class="mb-4 text-danger"><i class="bi bi-heart-fill"></i> Mis Favoritos</h1>

<?php if(isset($favoritos) && $favoritos->num_rows > 0): ?>
    <div class="row">
        <?php while($pro = $favoritos->fetch_object()): ?>
            
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm border-danger">
                    <div class="position-relative">
                        <a href="<?=base_url?>producto/ver?id=<?=$pro->id?>">
                            <img src="<?=Utils::showImage($pro->imagen)?>" class="card-img-top p-3" alt="<?=$pro->nombre?>" style="height: 200px; object-fit: contain;">
                        </a>
                        <a href="<?=base_url?>favorito/add?id=<?=$pro->id?>" class="position-absolute top-0 end-0 btn btn-sm btn-danger m-2 rounded-circle" title="Quitar">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                    
                    <div class="card-body d-flex flex-column text-center">
                        <h6 class="card-title text-truncate"><?=$pro->nombre?></h6>
                        
                        <p class="card-text text-primary fw-bold mb-2"><?=Utils::formatPrice($pro->precio)?></p>
                        
                        <a href="<?=base_url?>producto/ver?id=<?=$pro->id?>" class="btn btn-sm btn-primary mt-auto">
                            <i class="bi bi-cart-plus"></i> Ver Opciones
                        </a>
                    </div>
                </div>
            </div>

        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="alert alert-secondary text-center py-5">
        <h3><i class="bi bi-heart-break display-4"></i></h3>
        <p class="lead">No tienes productos en tu lista de deseos.</p>
        <a href="<?=base_url?>" class="btn btn-outline-dark">Explorar Productos</a>
    </div>
<?php endif; ?>