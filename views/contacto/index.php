<h1 class="text-center mb-4">Contáctanos</h1>

<div class="row justify-content-center">
    <div class="col-md-8">
        
        <?php if(isset($_SESSION['contacto']) && $_SESSION['contacto'] == 'complete'): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <strong>¡Mensaje enviado!</strong> Nos pondremos en contacto contigo pronto.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif(isset($_SESSION['contacto']) && $_SESSION['contacto'] == 'failed'): ?>
            <div class="alert alert-danger">Error al enviar el mensaje.</div>
        <?php endif; ?>
        <?php Utils::deleteSession('contacto'); ?>

        <div class="card shadow">
            <div class="card-body p-5">
                <div class="row mb-4">
                    <div class="col-md-6 border-end">
                        <h4><i class="bi bi-geo-alt-fill text-primary"></i> Ubicación</h4>
                        <p class="text-muted">5 de Mayo 210, Cedro, 92930 Tihuatlán, Ver.</p>
                        
                        <h4><i class="bi bi-envelope-fill text-primary"></i> Email</h4>
                        <p class="text-muted">soporte@tiendamaster.com</p>
                        
                        <h4><i class="bi bi-telephone-fill text-primary"></i> Teléfono</h4>
                        <p class="text-muted">(01) 555-0123</p>
                    </div>
                    
                    <div class="col-md-6">
                        <form action="<?=base_url?>contacto/enviar" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" required 
                                       value="<?= isset($_SESSION['identity']) ? $_SESSION['identity']->nombre . ' ' . $_SESSION['identity']->apellidos : '' ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required
                                       value="<?= isset($_SESSION['identity']) ? $_SESSION['identity']->email : '' ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Asunto</label>
                                <select name="asunto" class="form-select">
                                    <option>Consulta General</option>
                                    <option>Problema con mi pedido</option>
                                    <option>Sugerencia</option>
                                    <option>Reclamo</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mensaje</label>
                                <textarea name="mensaje" class="form-control" rows="4" required></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>