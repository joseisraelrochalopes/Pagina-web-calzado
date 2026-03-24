<h1 class="text-center mb-4">Iniciar Sesión</h1>

<?php if(isset($_SESSION['reset_complete'])): ?>
    <div class="alert alert-success text-center">
        ¡Contraseña restablecida con éxito! Ahora puedes iniciar sesión.
    </div>
    <?php Utils::deleteSession('reset_complete'); ?>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-5">
        
        <?php if(isset($_SESSION['error_login'])): ?>
            <div class="alert alert-danger text-center">
                <?=$_SESSION['error_login'];?>
            </div>
            <?php Utils::deleteSession('error_login'); ?>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body p-4">
                <form action="<?=base_url?>usuario/identificar" method="POST">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                        <div class="text-end mt-1">
                            <a href="<?=base_url?>usuario/olvide" class="text-decoration-none small text-muted">¿Olvidaste tu contraseña?</a>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                ¿No tienes cuenta? <a href="<?=base_url?>usuario/registro">Regístrate aquí</a>
            </div>
        </div>
    </div>
</div>