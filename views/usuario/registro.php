<h2 class="text-center mb-4">Registrarse</h2>

<?php if(isset($_SESSION['register']) && $_SESSION['register'] == 'complete'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Registro completado correctamente. Ya puedes iniciar sesión.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php elseif(isset($_SESSION['register']) && $_SESSION['register'] == 'failed'): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        Registro fallido, introduce bien los datos o el correo ya existe.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php Utils::deleteSession('register'); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow border-0">
            <div class="card-body p-4">
                <form action="<?=base_url?>usuario/save" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Tu nombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Apellidos</label>
                        <input type="text" name="apellidos" class="form-control" placeholder="Tus apellidos" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Teléfono / Celular</label>
                        <input type="tel" name="telefono" class="form-control" placeholder="Ej: 5512345678" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="********" required>
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">Crear cuenta</button>
                    </div>
                </form>

                <div class="text-center text-muted mb-3 d-flex align-items-center justify-content-center">
                    <hr class="flex-grow-1">
                    <small class="mx-2">O TAMBIÉN PUEDES</small>
                    <hr class="flex-grow-1">
                </div>

                <div class="d-grid gap-2">
                    <?php
                    // CONFIGURACIÓN DE GOOGLE (Reemplaza con tus datos de la imagen)
                    $client_id = "520144432766-h1tl34gmborl48sqahct3h42ls4tptdg.apps.googleusercontent.com"; 
                    $redirect_uri = base_url . "usuario/google_callback";
                    $google_url = "https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id=" . $client_id . "&redirect_uri=" . $redirect_uri . "&scope=email%20profile&access_type=offline";
                    ?>
                    <a href="<?= $google_url ?>" class="btn btn-outline-danger btn-lg shadow-sm d-flex align-items-center justify-content-center">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" width="22" class="me-2">
                        Registrarse con Google
                    </a>
                </div>
            </div>
            <div class="card-footer text-center bg-light">
                <small>¿Ya tienes cuenta? <a href="<?=base_url?>usuario/login" class="text-decoration-none">Inicia sesión</a></small>
            </div>
        </div>
    </div>
</div>