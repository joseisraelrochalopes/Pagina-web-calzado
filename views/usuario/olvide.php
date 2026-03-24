<h1 class="text-center mb-4">Recuperar Contraseña</h1>

<div class="row justify-content-center">
    <div class="col-md-6">
        
        <?php if(isset($_SESSION['reset_status']) && $_SESSION['reset_status'] == 'sent'): ?>
            <div class="alert alert-success">
                <strong>¡Enlace generado!</strong> 
                <br>
                En un entorno real, recibirías un correo. Para probar, haz clic aquí:<br>
                <a href="<?=$_SESSION['reset_link_simulation']?>" class="fw-bold text-success text-decoration-underline">REESTABLECER MI CONTRASEÑA</a>
            </div>
            <?php Utils::deleteSession('reset_link_simulation'); ?>
        <?php elseif(isset($_SESSION['reset_status']) && $_SESSION['reset_status'] == 'failed'): ?>
            <div class="alert alert-danger">No se encontró una cuenta con ese email.</div>
        <?php endif; ?>
        <?php Utils::deleteSession('reset_status'); ?>

        <div class="card shadow p-4">
            <p class="text-muted">Introduce tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>
            
            <form action="<?=base_url?>usuario/send_reset" method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Enviar enlace</button>
                    <a href="<?=base_url?>usuario/login" class="btn btn-outline-secondary">Volver al Login</a>
                </div>
            </form>
        </div>
    </div>
</div>