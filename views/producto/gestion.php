<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Gestión de Productos</h1>
    <div>
        <a href="<?=base_url?>producto/reporte" target="_blank" class="btn btn-outline-dark me-2">
            <i class="bi bi-file-earmark-pdf-fill"></i> Descargar Informe
        </a>
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
    <div class="alert alert-success">Operación realizada correctamente</div>
<?php elseif(isset($_SESSION['producto']) && $_SESSION['producto'] == 'status_changed'): ?>
    <div class="alert alert-info">Estado actualizado</div>
<?php endif; ?>
<?php Utils::deleteSession('producto'); ?>

<div class="card shadow">
    <div class="card-body">
        <?php if($productos->num_rows == 0): ?>
            <p class="text-center text-muted my-3">No se encontraron productos con estos filtros.</p>
        <?php else: ?>
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Precio Base</th> <th>Stock Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($pro = $productos->fetch_object()): ?>
                        <tr class="<?= ($pro->activo == 0) ? 'table-secondary text-muted' : '' ?>">
                            
                            <td class="text-center"><?=$pro->id;?></td>
                            
                            <td class="text-center" style="width: 80px;">
                                <img src="<?=Utils::showImage($pro->imagen)?>" class="img-fluid rounded border" style="max-height: 50px;">
                            </td>
                            
                            <td>
                                <?=$pro->nombre;?>
                                <?php if($pro->oferta == 'SI'): ?>
                                    <span class="badge bg-danger ms-1" style="font-size: 0.6rem;">OFERTA</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-center fw-bold text-primary">
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
                                <div class="btn-group">
                                    <a href="<?=base_url?>producto/editar?id=<?=$pro->id?>" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?=base_url?>producto/activarDesactivar?id=<?=$pro->id?>" class="btn btn-<?= ($pro->activo==1)?'danger':'success' ?> btn-sm" title="Cambiar Estado">
                                        <i class="bi bi-<?= ($pro->activo==1)?'power':'check-lg' ?>"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>