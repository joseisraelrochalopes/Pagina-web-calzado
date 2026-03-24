<h1 class="mb-4">Gestionar Categorías</h1>

<div class="mb-4">
    <a href="<?=base_url?>categoria/crear" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Crear Categoría
    </a>
</div>

<div class="card shadow">
    <div class="card-body">
        <table class="table table-hover table-bordered text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>IMAGEN</th> <th>NOMBRE</th>
                </tr>
            </thead>
            <tbody>
                <?php while($cat = $categorias->fetch_object()): ?>
                    <tr>
                        <td><?=$cat->id;?></td>
                        <td>
                            <?php if($cat->imagen != null): ?>
                                <img src="<?=base_url?>uploads/<?=$cat->imagen?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                            <?php else: ?>
                                <span class="text-muted">Sin foto</span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold"><?=$cat->nombre;?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>