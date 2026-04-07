
<?php 
    // 🔥 MAGIA: OBTENER LOS FAVORITOS DEL USUARIO ACTUAL PARA PINTAR LOS CORAZONES 🔥
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

<?php if(isset($ofertas) && $ofertas->num_rows > 0 && !isset($_GET['page']) && !isset($_GET['sort'])): ?>
    <div id="carouselOfertas" class="carousel slide mb-5 shadow-lg rounded overflow-hidden" data-bs-ride="carousel" style="border-radius: 20px;">
        <div class="carousel-inner">
            <?php 
                $active = true;
                while($oferta = $ofertas->fetch_object()): 
            ?>
                <div class="carousel-item <?= $active ? 'active' : '' ?>" data-bs-interval="4000">
                    <div class="row g-0 carousel-ofertas-bg text-white align-items-stretch" style="min-height: 400px;">
                        <div class="col-md-6 p-0 bg-white d-flex align-items-center justify-content-center carousel-img-box">
                            <img src="<?=Utils::showImage($oferta->imagen)?>" class="img-fluid p-4" style="width: 100%; height: 400px; object-fit: contain;" alt="<?=$oferta->nombre?>">
                        </div>
                        <div class="col-md-6 p-5 carousel-text-box d-flex flex-column justify-content-center align-items-md-start align-items-center text-md-start text-center">
                            <div>
                                <span class="badge bg-danger mb-3 px-3 py-2 rounded-pill shadow-sm">🔥 ¡SÚPER OFERTA!</span>
                                <h2 class="display-5 fw-bold"><?=$oferta->nombre?></h2>
                                <p class="lead text-light opacity-75"><?=substr($oferta->descripcion, 0, 80)?>...</p>
                                <h3 class="text-warning mb-4 fw-bold" style="font-size: 2.5rem;"><?=Utils::formatPrice($oferta->precio)?></h3>
                                <a href="<?=base_url?>producto/ver?id=<?=$oferta->id?>" class="btn btn-light btn-lg px-5 rounded-pill shadow-sm fw-bold text-dark">
                                    Ver Oferta <i class="bi bi-arrow-right-circle ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                $active = false; 
                endwhile; 
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselOfertas" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselOfertas" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
        </button>
    </div>
<?php endif; ?>

<h1 class="text-center mb-5 fw-bold" style="color: #2c3e50;">Catálogo de Productos</h1>

<div class="card mb-5 shadow-sm border-0" style="border-radius: 15px;">
    <div class="card-body p-4">
        <form action="<?=base_url?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-muted fw-bold small">Precio Mínimo</label>
                <div class="input-group shadow-sm rounded">
                    <span class="input-group-text bg-white border-end-0"><?=Utils::getMonedaSymbol()?></span>
                    <input type="number" name="min_price" class="form-control border-start-0" placeholder="0" value="<?= isset($_GET['min_price']) ? $_GET['min_price'] : '' ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted fw-bold small">Precio Máximo</label>
                <div class="input-group shadow-sm rounded">
                    <span class="input-group-text bg-white border-end-0"><?=Utils::getMonedaSymbol()?></span>
                    <input type="number" name="max_price" class="form-control border-start-0" placeholder="Max" value="<?= isset($_GET['max_price']) ? $_GET['max_price'] : '' ?>">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted fw-bold small">Ordenar por</label>
                <select name="sort" class="form-select shadow-sm">
                    <option value="new" <?= (isset($_GET['sort']) && $_GET['sort'] == 'new') ? 'selected' : '' ?>>🌟 Más Nuevos</option>
                    <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>📉 Precio: Menor a Mayor</option>
                    <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>📈 Precio: Mayor a Menor</option>
                    <option value="name_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : '' ?>>🔤 Nombre (A-Z)</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 shadow-sm" style="height: 38px;"><i class="bi bi-funnel"></i> Filtrar</button>
            </div>
        </form>
        <?php if(isset($_GET['min_price']) || isset($_GET['sort'])): ?>
            <div class="mt-3 text-end">
                <a href="<?=base_url?>" class="text-danger text-decoration-none small fw-bold"><i class="bi bi-x-circle"></i> Limpiar todos los filtros</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <?php if($productos->num_rows == 0): ?>
        <div class="col-12 text-center text-muted py-5">
            <i class="bi bi-search" style="font-size: 4rem; color: #ccc;"></i>
            <h3 class="mt-3">No se encontraron productos.</h3>
        </div>
    <?php else: ?>
        <?php while($pro = $productos->fetch_object()): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card h-100 shadow-sm card-efecto <?=$pro->stock == 0 ? 'opacity-75' : ''?>">
                    
                    <?php if($pro->oferta == 'SI'): ?>
                        <span class="position-absolute top-0 start-0 badge bg-danger m-3 px-2 py-1 shadow">OFERTA</span>
                    <?php endif; ?>

                    <div class="img-wrapper">
                        <a href="<?=base_url?>producto/ver?id=<?=$pro->id?>">
                            <img src="<?=Utils::showImage($pro->imagen)?>" class="card-img-top p-4 img-efecto" alt="<?=$pro->nombre?>" style="height: 220px; object-fit: contain; background-color: #f8f9fa;">
                        </a>
                    </div>
                    <div class="card-body d-flex flex-column text-center">
                        <h5 class="card-title mb-1 fw-bold">
                            <a href="<?=base_url?>producto/ver?id=<?=$pro->id?>" class="text-decoration-none text-dark"><?=$pro->nombre?></a>
                        </h5>
                        <p class="card-text text-success fw-bold fs-5 mt-2 mb-3"><?=Utils::formatPrice($pro->precio)?></p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto gap-2">
                            <?php if($pro->stock > 0): ?>
                                <a href="<?=base_url?>producto/ver?id=<?=$pro->id?>" class="btn btn-dark flex-grow-1 rounded-pill shadow-sm" style="font-size: 0.9rem;">
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
    <?php endif; ?>
</div>

<?php 
    $url_params = "";
    if(isset($_GET['min_price'])) $url_params .= "&min_price=".$_GET['min_price'];
    if(isset($_GET['max_price'])) $url_params .= "&max_price=".$_GET['max_price'];
    if(isset($_GET['sort'])) $url_params .= "&sort=".$_GET['sort'];
    $range = 2; 
?>

<?php if(isset($total_pages) && $total_pages > 1): ?>
    <nav aria-label="Navegación" class="mt-5 mb-4 paginacion-scroll text-center">
        <ul class="pagination justify-content-center flex-nowrap d-inline-flex" style="margin-bottom: 0;">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link shadow-sm" href="<?=base_url?>producto/index?page=<?=($page-1)?><?=$url_params?>">«</a>
            </li>
            <?php if($page > ($range + 1)): ?>
                <li class="page-item"><a class="page-link shadow-sm" href="<?=base_url?>producto/index?page=1<?=$url_params?>">1</a></li>
                <?php if($page > ($range + 2)): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
            <?php endif; ?>
            <?php for($i = max(1, $page - $range); $i <= min($total_pages, $page + $range); $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link shadow-sm" href="<?=base_url?>producto/index?page=<?=$i?><?=$url_params?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <?php if($page < ($total_pages - $range)): ?>
                <?php if($page < ($total_pages - $range - 1)): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                <li class="page-item"><a class="page-link shadow-sm" href="<?=base_url?>producto/index?page=<?=$total_pages?><?=$url_params?>"><?=$total_pages?></a></li>
            <?php endif; ?>
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link shadow-sm" href="<?=base_url?>producto/index?page=<?=($page+1)?><?=$url_params?>">»</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>