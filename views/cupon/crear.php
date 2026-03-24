<h1 class="mb-4">Crear nuevo cupón</h1>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow p-4">
            <form action="<?=base_url?>cupon/save" method="POST">
                
                <div class="mb-3">
                    <label class="form-label">Código del cupón</label>
                    <input type="text" name="codigo" class="form-control" placeholder="Ej: VERANO2025" style="text-transform: uppercase;" required>
                    <div class="form-text">El código debe ser único. Se guardará en mayúsculas.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Porcentaje de Descuento (%)</label>
                    <div class="input-group">
                        <input type="number" name="porcentaje" class="form-control" placeholder="Ej: 10" min="1" max="100" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?=base_url?>cupon/gestion" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Cupón</button>
                </div>
            </form>
        </div>
    </div>
</div>