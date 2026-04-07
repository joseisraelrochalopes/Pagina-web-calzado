<h1 class="mb-4">Mis Datos</h1>

<?php if(isset($_SESSION['user_update']) && $_SESSION['user_update'] == 'complete'): ?>
    <div class="alert alert-success">Tus datos se han actualizado correctamente.</div>
<?php elseif(isset($_SESSION['user_update']) && $_SESSION['user_update'] == 'failed'): ?>
    <div class="alert alert-danger">Error al actualizar tus datos.</div>
<?php endif; ?>
<?php Utils::deleteSession('user_update'); ?>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-body p-4">
                <form action="<?=base_url?>usuario/save_changes" method="POST" enctype="multipart/form-data">
                    
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="<?=$_SESSION['identity']->nombre?>" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" value="<?=$_SESSION['identity']->apellidos?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?=$_SESSION['identity']->email?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="<?=isset($_SESSION['identity']->telefono) ? $_SESSION['identity']->telefono : ''?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cambiar Contraseña <span class="text-muted small">(Déjalo vacío si no quieres cambiarla)</span></label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Avatar / Foto de perfil</label>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <?php if(isset($_SESSION['identity']->imagen) && !empty($_SESSION['identity']->imagen)): ?>
                                    <?php if(strpos($_SESSION['identity']->imagen, 'http') === 0): ?>
                                        <img src="<?=$_SESSION['identity']->imagen?>" class="rounded-circle border" width="60" height="60" style="object-fit: cover;">
                                    <?php else: ?>
                                        <img src="<?=base_url?>assets/img/users/<?=$_SESSION['identity']->imagen?>" class="rounded-circle border" width="60" height="60" style="object-fit: cover;">
                                    <?php endif; ?>
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/60" class="rounded-circle border">
                                <?php endif; ?>
                            </div>
                            <input type="file" name="imagen" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>