<h1 class="mb-4">Gestionar Categorías</h1>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="<?=base_url?>categoria/crear" class="btn btn-success shadow-sm">
        <i class="bi bi-plus-circle"></i> Crear Categoría
    </a>
</div>

<?php if(isset($_SESSION['delete']) && $_SESSION['delete'] == 'complete'): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle me-2"></i> La categoría se ha eliminado correctamente.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php elseif(isset($_SESSION['delete']) && $_SESSION['delete'] == 'failed_fk'): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> <strong>¡No se puede eliminar!</strong> Esta categoría aún tiene productos asociados. Debes eliminar o cambiar de categoría esos productos primero.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php unset($_SESSION['delete']); // Limpiamos la sesión para que no se quede el mensaje pegado ?>


<div class="card shadow border-0" style="border-radius: 15px;">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover table-bordered text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>IMAGEN</th> 
                        <th>NOMBRE</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($cat = $categorias->fetch_object()): ?>
                        <tr>
                            <td><?=$cat->id;?></td>
                            <td>
                                <?php if($cat->imagen != null): ?>
                                    <img src="<?=base_url?>uploads/<?=$cat->imagen?>" class="img-thumbnail shadow-sm" style="width: 60px; height: 60px; object-fit: cover; border-radius: 10px;">
                                <?php else: ?>
                                    <span class="badge bg-secondary">Sin foto</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold text-dark"><?=$cat->nombre;?></td>
                            
                            <td>
                                <a href="<?=base_url?>categoria/editar?id=<?=$cat->id?>" class="btn btn-warning btn-sm shadow-sm text-dark fw-bold" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="<?=base_url?>categoria/eliminar?id=<?=$cat->id?>" class="btn btn-danger btn-sm shadow-sm fw-bold ms-1" title="Eliminar" onclick="return confirm('¿Estás totalmente seguro de que deseas eliminar la categoría <?=$cat->nombre?>?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                            
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>