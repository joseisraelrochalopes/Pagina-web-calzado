<style>
    /* ✨ EFECTOS VISUALES ANIMADOS ✨ */
    .card-efecto {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
        border: none !important;
    }
    .card-efecto:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.2) !important;
    }
    .img-wrapper { overflow: hidden; }
    .img-efecto { transition: transform 0.5s ease; }
    .card-efecto:hover .img-efecto { transform: scale(1.08); }
    
    @media (max-width: 768px) {
        .carousel-img-box { min-height: 250px !important; }
        .carousel-text-box { padding: 2rem 1.5rem !important; text-align: center; }
        .carousel-text-box h2 { font-size: 1.8rem; }
    }

    /* 🔥 PAGINACIÓN LIMPIA Y RESPONSIVA 🔥 */
    .paginacion-scroll {
        overflow-x: auto;
        white-space: nowrap;
        padding: 15px 5px;
        -webkit-overflow-scrolling: touch;
    }
    
    .paginacion-scroll::-webkit-scrollbar { height: 4px; }
    .paginacion-scroll::-webkit-scrollbar-thumb { background: #eee; border-radius: 10px; }

    .pagination .page-item {
        margin: 0 3px;
    }

    .pagination .page-link {
        border-radius: 8px !important;
        border: 1px solid #dee2e6 !important;
        color: #333;
        padding: 8px 14px;
        font-weight: bold;
        transition: all 0.2s;
    }

    .pagination .page-item.active .page-link {
        background-color: #000 !important;
        border-color: #000 !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
</style>

<?php if(isset($ofertas) && $ofertas->num_rows > 0 && !isset($_GET['page']) && !isset($_GET['sort'])): ?>
    <div id="carouselOfertas" class="carousel slide mb-5 shadow-lg rounded overflow-hidden" data-bs-ride="carousel" style="border-radius: 20px;">
        <div class="carousel-inner">
            <?php 
                $active = true;
                while($oferta = $ofertas->fetch_object()): 
            ?>
                <div class="carousel-item <?= $active ? 'active' : '' ?>" data-bs-interval="4000">
                    <div class="row g-0 bg-dark text-white align-items-stretch" style="min-height: 400px;">
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
                    <div class="img-wrapper">
                        <a href="<?=base_url?>producto/ver?id=<?=$pro->id?>">
                            <img src="<?=Utils::showImage($pro->imagen)?>" class="card-img-top p-4 img-efecto" alt="<?=$pro->nombre?>" style="height: 220px; object-fit: contain; background-color: #f8f9fa;">
                        </a>
                    </div>
                    <div class="card-body d-flex flex-column text-center">
                        <h5 class="card-title mb-1 fw-bold"><?=$pro->nombre?></h5>
                        <p class="card-text text-success fw-bold fs-5 mt-2 mb-3"><?=Utils::formatPrice($pro->precio)?></p>
                        <div class="mt-auto">
                            <a href="<?=base_url?>producto/ver?id=<?=$pro->id?>" class="btn btn-dark w-100 rounded-pill shadow-sm">Ver Detalles</a>
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

    // LÓGICA DE RANGO INTELIGENTE PARA QUE NO SE VEA FEO
    $range = 2; // Mostrará 2 números antes y 2 después de la página actual
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