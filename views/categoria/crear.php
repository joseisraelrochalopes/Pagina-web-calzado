<?php if(isset($edit) && isset($cat) && is_object($cat)): ?>
    <h1 class="mb-4">Editar categoría: <?=$cat->nombre?></h1>
    <?php $url_action = base_url."categoria/save?id=".$cat->id; ?>
<?php else: ?>
    <h1 class="mb-4">Crear nueva categoría</h1>
    <?php $url_action = base_url."categoria/save"; ?>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow p-4">
            <form action="<?=$url_action?>" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre de la categoría</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Tenis, Accesorios..." required value="<?= isset($cat) && is_object($cat) ? $cat->nombre : '' ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Imagen de la categoría</label>
                    
                    <?php if(isset($cat) && is_object($cat) && !empty($cat->imagen)): ?>
                        <div class="mb-3">
                            <img src="<?=base_url?>uploads/<?=$cat->imagen?>" class="img-thumbnail shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                    <?php endif; ?>

                    <input type="file" name="imagen" class="form-control">
                    
                    <?php if(isset($edit)): ?>
                        <small class="text-muted mt-1 d-block"><i class="bi bi-info-circle"></i> Si no seleccionas un archivo nuevo, se conservará la imagen actual.</small>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?=base_url?>categoria/index" class="btn btn-secondary px-4 rounded-pill shadow-sm">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>