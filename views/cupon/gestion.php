<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Gestión de Cupones</h1>
    <a href="<?=base_url?>cupon/crear" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Crear Cupón
    </a>
</div>

<?php if(isset($_SESSION['cupon_status']) && $_SESSION['cupon_status'] == 'complete'): ?>
    <div class="alert alert-success">Cupón creado correctamente</div>
<?php elseif(isset($_SESSION['cupon_status']) && $_SESSION['cupon_status'] == 'failed'): ?>
    <div class="alert alert-danger">Error al crear el cupón (Quizás el código ya existe)</div>
<?php endif; ?>
<?php Utils::deleteSession('cupon_status'); ?>

<?php if(isset($_SESSION['delete']) && $_SESSION['delete'] == 'complete'): ?>
    <div class="alert alert-success">Cupón eliminado correctamente</div>
<?php endif; ?>
<?php Utils::deleteSession('delete'); ?>

<div class="card shadow">
    <div class="card-body">
        <table class="table table-hover table-bordered text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Descuento</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($c = $cupones->fetch_object()): ?>
                    <tr>
                        <td><?=$c->id;?></td>
                        <td class="fw-bold text-primary"><?=$c->codigo;?></td>
                        <td><?=$c->porcentaje;?>%</td>
                        <td>
                            <span class="badge bg-success"><?=$c->estado;?></span>
                        </td>
                        <td>
                            <a href="<?=base_url?>cupon/borrar?id=<?=$c->id?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este cupón?');">
                                <i class="bi bi-trash"></i> Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>