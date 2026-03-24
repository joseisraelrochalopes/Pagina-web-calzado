<?php if(isset($pro)): ?>
    
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=base_url?>" class="text-decoration-none" style="color: #0a192f;">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?=$pro->nombre?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="text-center position-relative">
                <?php if($pro->oferta == 'SI'): ?>
                    <span class="position-absolute top-0 start-0 badge bg-danger rounded-pill m-3 p-2 fs-6 shadow">¡OFERTA!</span>
                <?php endif; ?>
                
                <img id="mainImage" src="<?=Utils::showImage($pro->imagen)?>" class="img-fluid rounded shadow-sm border" style="max-height: 450px; width: 100%; object-fit: contain;">
            </div>

            <div class="d-flex justify-content-center mt-3 gap-2 overflow-auto">
                <img src="<?=Utils::showImage($pro->imagen)?>" class="img-thumbnail thumb-active" style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;" onclick="changeImage(this)">
                
                <?php if(isset($galeria)): ?>
                    <?php while($img = $galeria->fetch_object()): ?>
                        <img src="<?=base_url?>assets/img/gallery/<?=$img->imagen?>" class="img-thumbnail" style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;" onclick="changeImage(this)">
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-6">
            <h1 class="fw-bold mb-1" style="color: #0a192f;"><?=$pro->nombre?></h1>
            
            <div class="mb-3 text-warning">
                <?php 
                    $media = round($stats_valoracion->media ?? 0);
                    for($i=1; $i<=5; $i++){ echo ($i <= $media) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>'; }
                ?>
                <span class="text-muted small ms-2">(<?= $stats_valoracion->total ?> opiniones)</span>
            </div>

            <h2 class="my-3 fw-bold" id="productPrice" style="color: #b89324;"><?=Utils::formatPrice($pro->precio)?></h2>
            
            <p class="lead text-muted"><?=$pro->descripcion?></p>
            
            <div class="mt-4">
                <?php if($pro->stock > 0): ?>
                    <form action="<?=base_url?>carrito/add" method="POST" class="mt-4 border-top pt-4">
                        <input type="hidden" name="producto_id" value="<?=$pro->id?>">
                        
                        <?php 
                            $mostrar_select = false;
                            if(isset($stocks_tallas) && is_array($stocks_tallas) && count($stocks_tallas) > 0){
                                if(count($stocks_tallas) > 1 || !isset($stocks_tallas['Única'])){
                                    $mostrar_select = true;
                                }
                            }
                        ?>
                        <?php if($mostrar_select): ?>
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-secondary mb-3">Selecciona tu talla (MX):</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach($stocks_tallas as $talla => $data): ?>
                                        <?php if($data['stock'] > 0): ?>
                                            <input type="radio" class="btn-check" name="talla" id="talla_<?=$talla?>" value="<?=$talla?>" data-price="<?=Utils::formatPrice($data['precio'])?>" data-stock="<?=$data['stock']?>" onchange="updatePrice()" required>
                                            <label class="btn btn-outline-navy fw-bold px-4 py-2 rounded-3 btn-talla-custom" for="talla_<?=$talla?>">
                                                <?=$talla?>
                                            </label>
                                        <?php else: ?>
                                            <input type="radio" class="btn-check" name="talla" id="talla_<?=$talla?>" value="<?=$talla?>" disabled>
                                            <label class="btn btn-outline-secondary fw-bold px-4 py-2 rounded-3 opacity-50 text-decoration-line-through" for="talla_<?=$talla?>" title="Agotado">
                                                <?=$talla?>
                                            </label>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="talla" value="Única">
                        <?php endif; ?>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary mb-2">Cantidad:</label>
                            <div class="input-group" style="width: 150px;">
                                <button class="btn btn-outline-navy fw-bold px-3" type="button" onclick="restarCantidad()">-</button>
                                <input type="number" name="cantidad" id="inputCantidad" class="form-control text-center fw-bold bg-white" value="1" min="1" readonly style="border-color: #0a192f;">
                                <button class="btn btn-outline-navy fw-bold px-3" type="button" onclick="sumarCantidad()">+</button>
                            </div>
                            <small id="stockAviso" class="text-muted mt-1 d-block"></small>
                        </div>

                        <button type="submit" class="btn btn-navy-premium btn-lg py-3 shadow-sm fw-bold w-100 mt-2">
                            <i class="bi bi-cart-plus me-2 fs-5"></i> AÑADIR AL CARRITO
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-danger fw-bold fs-5"><i class="bi bi-x-circle-fill"></i> Producto Agotado</p>
                    <button class="btn btn-secondary btn-lg px-5 w-100 py-3 fw-bold" disabled>Sin Stock</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold text-dark" id="home-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">Opiniones y Reseñas</button>
                </li>
            </ul>
            <div class="tab-content p-4 border border-top-0 bg-white rounded-bottom" id="myTabContent">
                <?php if(isset($_SESSION['identity'])): ?>
                    <div class="card mb-4 border-0 bg-light">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Deja tu opinión</h5>
                            <form action="<?=base_url?>valoracion/save" method="POST">
                                <input type="hidden" name="producto_id" value="<?=$pro->id?>">
                                <div class="mb-2">
                                    <select name="nota" class="form-select w-auto border-0 shadow-sm"><option value="5">⭐⭐⭐⭐⭐ Excelente</option><option value="4">⭐⭐⭐⭐ Muy bueno</option><option value="3">⭐⭐⭐ Bueno</option><option value="2">⭐⭐ Regular</option><option value="1">⭐ Malo</option></select>
                                </div>
                                <div class="mb-2"><textarea name="comentario" class="form-control border-0 shadow-sm" placeholder="Cuéntanos qué te pareció este calzado..." rows="3" required></textarea></div>
                                <button type="submit" class="btn btn-dark btn-sm px-4 fw-bold shadow-sm" style="background-color: #0a192f;">Publicar Opinión</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if($opiniones->num_rows > 0): ?>
                    <?php while($op = $opiniones->fetch_object()): ?>
                        <div class="d-flex mb-3 border-bottom pb-3">
                            <div class="flex-shrink-0">
                                <img src="<?= $op->imagen ? base_url.'assets/img/users/'.$op->imagen : 'https://ui-avatars.com/api/?name='.urlencode($op->nombre).'&background=0a192f&color=fff' ?>" class="rounded-circle shadow-sm" width="50" height="50" style="object-fit: cover;">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 fw-bold"><?=$op->nombre?> <small class="text-muted ms-2 fw-normal"><?=$op->fecha?></small></h6>
                                <div class="text-warning small mb-1"><?php for($i=1; $i<=5; $i++){ echo ($i <= $op->nota) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>'; } ?></div>
                                <p class="mb-0 text-secondary"><?=$op->comentario?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-4"><i class="bi bi-chat-square-text fs-1 d-block mb-2 text-light"></i>Aún no hay opiniones. ¡Sé el primero en calificarlo!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="mt-5 pt-5 border-top">
        <h3 class="mb-4 fw-bold">También te podría interesar</h3>
        <div class="row">
            <?php 
            $counter = 0;
            while($rel = $relacionados->fetch_object()): 
                if($rel->id == $pro->id) continue;
                if($counter >= 3) break;
                $counter++;
            ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm border-0 bg-light transition-hover">
                        <div class="row g-0 align-items-center">
                            <div class="col-4 p-2 text-center bg-white rounded-start h-100 d-flex align-items-center justify-content-center">
                                <img src="<?=Utils::showImage($rel->imagen)?>" class="img-fluid rounded" style="max-height: 80px;">
                            </div>
                            <div class="col-8">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-1 small text-truncate fw-bold"><?=$rel->nombre?></h6>
                                    <p class="card-text fw-bold mb-2" style="color: #b89324;"><?=Utils::formatPrice($rel->precio)?></p>
                                    <a href="<?=base_url?>producto/ver?id=<?=$rel->id?>" class="btn btn-sm btn-outline-navy px-3 rounded-pill stretched-link fw-bold">Ver Detalles</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php if(isset($productos_historial) && $productos_historial && $productos_historial->num_rows > 0): ?>
    <div class="mt-5 pt-4 pb-4 px-4 bg-white rounded-4 shadow-sm border" style="border-color: #e6f1ff !important;">
        <h5 class="mb-3 text-secondary fw-bold"><i class="bi bi-clock-history me-2"></i> Visto Recientemente</h5>
        <div class="row flex-nowrap overflow-auto pb-2 custom-scrollbar">
            <?php while($hist = $productos_historial->fetch_object()): ?>
                <div class="col-6 col-md-2">
                    <div class="card h-100 border-0 shadow-sm transition-hover">
                        <a href="<?=base_url?>producto/ver?id=<?=$hist->id?>" class="p-2 d-flex justify-content-center align-items-center bg-light rounded-top" style="height: 120px;">
                            <img src="<?=Utils::showImage($hist->imagen)?>" class="img-fluid" style="max-height: 100px; object-fit: contain;">
                        </a>
                        <div class="card-body p-2 text-center">
                            <p class="card-title small text-truncate mb-1">
                                <a href="<?=base_url?>producto/ver?id=<?=$hist->id?>" class="text-decoration-none text-dark fw-bold"><?=$hist->nombre?></a>
                            </p>
                            <p class="small fw-bold m-0" style="color: #b89324;"><?=Utils::formatPrice($hist->precio)?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

<?php else: ?>
    <div class="text-center py-5">
        <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
        <h2 class="fw-bold">El producto no existe o fue eliminado</h2>
        <a href="<?=base_url?>" class="btn btn-navy-premium mt-3 px-4 py-2 fw-bold">Volver a la tienda</a>
    </div>
<?php endif; ?>

<script>
    let stockMaximoActual = 1;
    function changeImage(element) { document.getElementById('mainImage').src = element.src; }
    function updatePrice() {
        var radios = document.getElementsByName('talla');
        var inputCantidad = document.getElementById('inputCantidad');
        var stockAviso = document.getElementById('stockAviso');
        for (var i = 0; i < radios.length; i++) {
            if (radios[i].checked) {
                var newPrice = radios[i].getAttribute('data-price');
                if(newPrice) { document.getElementById('productPrice').innerText = newPrice; }
                var stockDisponible = radios[i].getAttribute('data-stock');
                if(stockDisponible) {
                    stockMaximoActual = parseInt(stockDisponible);
                    stockAviso.innerText = "Disponibles: " + stockMaximoActual + " pares";
                } else {
                    stockMaximoActual = <?= isset($pro->stock) ? $pro->stock : 1 ?>;
                    stockAviso.innerText = "";
                }
                inputCantidad.value = 1;
                break;
            }
        }
    }
    function sumarCantidad() {
        var radios = document.getElementsByName('talla');
        var tallaSeleccionada = false;
        if(radios.length > 0) {
            for (var i = 0; i < radios.length; i++) { if(radios[i].checked) tallaSeleccionada = true; }
            if(!tallaSeleccionada) { alert("Por favor, selecciona una talla primero."); return; }
        }
        var input = document.getElementById('inputCantidad');
        var valorActual = parseInt(input.value);
        if(valorActual < stockMaximoActual) { input.value = valorActual + 1; }
    }
    function restarCantidad() {
        var input = document.getElementById('inputCantidad');
        var valorActual = parseInt(input.value);
        if(valorActual > 1) { input.value = valorActual - 1; }
    }
    window.onload = function() { updatePrice(); };
</script>

<style>
    /* ✨ ESTILOS PREMIUM UNIFICADOS ✨ */
    .btn-navy-premium {
        background-color: #0a192f;
        border: 2px solid #0a192f;
        color: white;
        transition: all 0.3s ease;
    }
    .btn-navy-premium:hover {
        background-color: #b89324;
        border-color: #b89324;
        color: white;
        transform: translateY(-2px);
    }
    .btn-outline-navy {
        border: 2px solid #0a192f;
        color: #0a192f;
        transition: all 0.3s;
    }
    .btn-outline-navy:hover {
        background-color: #0a192f;
        color: white;
    }
    .btn-talla-custom:hover, .btn-check:checked + .btn-talla-custom {
        background-color: #0a192f !important;
        color: white !important;
        border-color: #0a192f !important;
    }
    .transition-hover {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .transition-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #b89324; }
</style>