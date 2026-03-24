<h1 class="mb-4">Crear nueva categoría</h1>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow p-4">
            <form action="<?=base_url?>categoria/save" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nombre de la categoría</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Pantalones, Accesorios..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Imagen de la categoría</label>
                    <input type="file" name="imagen" class="form-control">
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?=base_url?>categoria/index" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>