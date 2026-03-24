<h1 class="mb-4">Configuración del Sistema</h1>

<?php if(isset($_SESSION['config_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?=$_SESSION['config_success']?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php Utils::deleteSession('config_success'); ?>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">Datos de la Empresa (Facturación)</div>
            <div class="card-body p-4">
                
                <form action="<?=base_url?>configuracion/save" method="POST" enctype="multipart/form-data">
                    
                    <h5 class="text-primary mb-3">Ajustes Generales</h5>
                    <div class="mb-4 row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Símbolo de Moneda</label>
                            <select name="moneda" class="form-select">
                                <option value="$" <?= $config->moneda == '$' ? 'selected' : '' ?>>Dólar Americano ($)</option>
                                <option value="MXN" <?= $config->moneda == 'MXN' ? 'selected' : '' ?>>Peso Mexicano (MXN)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Logo de la Empresa</label>
                            <input type="file" name="logo" class="form-control">
                            <?php if(!empty($config->logo)): ?>
                                <div class="mt-2">
                                    <img src="<?=base_url?>assets/img/logo/<?=$config->logo?>" height="50" class="border rounded p-1">
                                    <small class="text-muted d-block">Logo actual</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <h5 class="text-primary mb-3">Información Legal y Contacto</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Razón Social / Nombre de la Tienda</label>
                        <input type="text" name="nombre_empresa" class="form-control" value="<?=$config->nombre_empresa?>" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">RUC / NIT / CIF</label>
                            <input type="text" name="ruc" class="form-control" value="<?=$config->ruc?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="<?=$config->telefono?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico de Contacto</label>
                        <input type="email" name="email" class="form-control" value="<?=$config->email?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Dirección Fiscal</label>
                        <textarea name="direccion" class="form-control" rows="2"><?=$config->direccion?></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save"></i> Guardar Configuración
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-light border-info">
            <div class="card-body">
                <h5 class="card-title text-info"><i class="bi bi-info-circle"></i> Información</h5>
                <p class="card-text small">
                    Estos datos aparecerán automáticamente en:
                </p>
                <ul class="small text-muted">
                    <li>Encabezado de las Facturas PDF.</li>
                    <li>Pie de página del sitio web.</li>
                    <li>Correos electrónicos enviados (futuro).</li>
                </ul>
                <hr>
                <p class="small">El logo debe ser formato PNG o JPG, preferiblemente de fondo transparente.</p>
            </div>
        </div>
    </div>
</div>