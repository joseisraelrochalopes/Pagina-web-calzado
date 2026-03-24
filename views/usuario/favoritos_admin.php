<?php if(isset($user_data)): ?>
<div class="container mt-5">
    <div class="card shadow border-0">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0400FF 0%, #00FBFF 100%); padding: 20px;">
            <h3 class="mb-0">
                <i class="bi bi-heart-fill"></i> Favoritos de: <?= $user_data->nombre ?> <?= $user_data->apellidos ?>
            </h3>
            <a href="<?=base_url?>usuario/gestion" class="btn btn-light btn-sm fw-bold shadow-sm">
                <i class="bi bi-arrow-left"></i> Volver a Gestión
            </a>
        </div>

        <div class="card-body p-4">
            <div class="row mb-4 align-items-center">
                <div class="col-md-1">
                    <?php if($user_data->imagen): ?>
                        <img src="<?=base_url?>assets/img/users/<?=$user_data->imagen?>" class="rounded-circle border" width="60" height="60" style="object-fit: cover;">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/60?text=U" class="rounded-circle border">
                    <?php endif; ?>
                </div>
                <div class="col-md-11">
                    <p class="mb-0 text-muted">Estás consultando la lista de deseos del cliente:</p>
                    <h5 class="fw-bold"><?= $user_data->email ?></h5>
                </div>
            </div>

            <?php if(isset($mis_favoritos) && $mis_favoritos->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle border">
                        <thead class="table-light">
                            <tr>
                                <th>Imagen</th>
                                <th>Producto</th>
                                <th>Precio Unitario</th>
                                <th>Estado / Stock</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($fav = $mis_favoritos->fetch_object()): ?>
                                <tr>
                                    <td>
                                        <?php if($fav->imagen): ?>
                                            <img src="<?=base_url?>assets/img/<?=$fav->imagen?>" width="70" class="rounded shadow-sm">
                                        <?php else: ?>
                                            <img src="<?=base_url?>assets/img/no-image.png" width="70" class="rounded shadow-sm">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold" style="font-size: 1.1rem;"><?=$fav->nombre?></div>
                                        <small class="text-muted">Categoría ID: <?=$fav->categoria_id?></small>
                                    </td>
                                    <td>
                                        <span class="text-primary fw-bold" style="font-size: 1.2rem;">S/. <?=number_format($fav->precio, 2)?></span>
                                    </td>
                                    <td>
                                        <?php if($fav->stock > 0): ?>
                                            <span class="badge bg-success" style="padding: 8px 12px;">
                                                <i class="bi bi-check-circle"></i> Stock: <?=$fav->stock?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger" style="padding: 8px 12px;">
                                                <i class="bi bi-x-circle"></i> AGOTADO
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?=base_url?>producto/ver&id=<?=$fav->id?>" class="btn btn-outline-dark btn-sm">
                                            <i class="bi bi-search"></i> Ver Ficha
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-heartbreak display-1 text-muted opacity-25"></i>
                    <h4 class="mt-3 text-muted">Este cliente aún no ha guardado favoritos.</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>