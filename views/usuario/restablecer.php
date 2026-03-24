<h1 class="text-center mb-4">Restablecer Contraseña</h1>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow p-4 border-success">
            <div class="text-center mb-3">
                <i class="bi bi-shield-lock display-4 text-success"></i>
            </div>
            <h5 class="text-center mb-4">Crea una nueva contraseña</h5>
            
            <form action="<?=base_url?>usuario/save_new_password" method="POST">
                <input type="hidden" name="token" value="<?=$token?>">
                
                <div class="mb-3">
                    <label class="form-label">Nueva Contraseña</label>
                    <input type="password" name="password" class="form-control" required placeholder="Mínimo 6 caracteres">
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Guardar Contraseña</button>
                </div>
            </form>
        </div>
    </div>
</div>