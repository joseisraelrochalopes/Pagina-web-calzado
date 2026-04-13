<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <h1>Gestión de Productos</h1>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?=base_url?>producto/reporte" target="_blank" class="btn btn-outline-dark">
            <i class="bi bi-file-earmark-pdf-fill"></i> Descargar Informe
        </a>
        
        <button type="submit" form="formEliminarMasivo" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar TODOS los productos seleccionados? Esta acción no se puede deshacer.');">
            <i class="bi bi-trash"></i> Eliminar Seleccionados
        </button>

        <a href="<?=base_url?>producto/crear" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Crear Producto
        </a>
    </div>
</div>

<div class="card mb-4 bg-light border-0">
    <div class="card-body">
        <form action="<?=base_url?>producto/gestion" method="POST" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label text-muted small">Buscar por nombre:</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search_admin" class="form-control" placeholder="Ej: Camiseta..." value="<?=isset($_POST['search_admin']) ? $_POST['search_admin'] : ''?>">
                </div>
            </div>
            
            <div class="col-md-4">
                <label class="form-label text-muted small">Filtrar por Categoría:</label>
                <select name="cat_admin" class="form-select">
                    <option value="">-- Ver Todas --</option>
                    <?php 
                        $cats = Utils::showCategorias();
                        while($cat = $cats->fetch_object()): 
                    ?>
                        <option value="<?=$cat->id?>" <?= (isset($_POST['cat_admin']) && $_POST['cat_admin'] == $cat->id) ? 'selected' : '' ?>>
                            <?=$cat->nombre?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark w-100">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<?php if(isset($_SESSION['producto']) && $_SESSION['producto'] == 'complete'): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm"><i class="bi bi-check-circle me-2"></i> Operación realizada correctamente <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php elseif(isset($_SESSION['producto']) && $_SESSION['producto'] == 'status_changed'): ?>
    <div class="alert alert-info alert-dismissible fade show shadow-sm"><i class="bi bi-info-circle me-2"></i> Estado actualizado <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php elseif(isset($_SESSION['producto']) && $_SESSION['producto'] == 'deleted'): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm"><i class="bi bi-trash me-2"></i> Producto(s) eliminado(s) correctamente <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php elseif(isset($_SESSION['producto']) && $_SESSION['producto'] == 'delete_failed'): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm"><i class="bi bi-exclamation-triangle me-2"></i> Error al eliminar. Es posible que el producto esté asociado a un pedido. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php Utils::deleteSession('producto'); ?>

<div class="card shadow border-0" style="border-radius: 10px;">
    <div class="card-body">
        <?php if($productos->num_rows == 0): ?>
            <p class="text-center text-muted my-3">No se encontraron productos con estos filtros.</p>
        <?php else: ?>
            
            <form id="formEliminarMasivo" action="<?=base_url?>producto/eliminarMasivo" method="POST">
                
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th style="width: 40px;">
                                    <input class="form-check-input" type="checkbox" id="selectAll" title="Seleccionar todos">
                                </th>
                                <th>ID</th>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Precio Base</th> 
                                <th>Stock Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($pro = $productos->fetch_object()): ?>
                                <tr class="<?= ($pro->activo == 0) ? 'table-secondary text-muted' : '' ?>">
                                    
                                    <td class="text-center">
                                        <input class="form-check-input product-checkbox" type="checkbox" name="ids[]" value="<?=$pro->id?>">
                                    </td>

                                    <td class="text-center"><?=$pro->id;?></td>
                                    
                                    <td class="text-center" style="width: 80px;">
                                        <img src="<?=Utils::showImage($pro->imagen)?>" class="img-fluid rounded border" style="max-height: 50px; object-fit: contain; background: #fff;">
                                    </td>
                                    
                                    <td>
                                        <span class="fw-bold"><?=$pro->nombre;?></span>
                                        <?php if($pro->oferta == 'SI'): ?>
                                            <span class="badge bg-danger ms-1" style="font-size: 0.6rem;">OFERTA</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="text-center fw-bold text-success">
                                        <?=Utils::formatPrice($pro->precio);?>
                                    </td>
                                    
                                    <td class="text-center <?= ($pro->stock < 5) ? 'text-danger fw-bold' : '' ?>">
                                        <?=$pro->stock;?>
                                    </td>
                                    
                                    <td class="text-center">
                                        <?php if($pro->activo == 1): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm">
                                            <a href="<?=base_url?>producto/editar?id=<?=$pro->id?>" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?=base_url?>producto/activarDesactivar?id=<?=$pro->id?>" class="btn btn-<?= ($pro->activo==1)?'secondary':'success' ?> btn-sm" title="Cambiar Estado">
                                                <i class="bi bi-<?= ($pro->activo==1)?'power':'check-lg' ?>"></i>
                                            </a>
                                            <a href="<?=base_url?>producto/eliminar?id=<?=$pro->id?>" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto definitivamente?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                </form>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selectAll = document.getElementById("selectAll");
        if(selectAll) {
            selectAll.addEventListener("change", function() {
                const checkboxes = document.querySelectorAll(".product-checkbox");
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = selectAll.checked;
                });
            });
        }
    });
</script>