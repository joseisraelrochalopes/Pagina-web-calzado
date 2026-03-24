<?php 
    $titulo = "Crear nuevo producto";
    $url_action = base_url."producto/save";
    $nombre_val = "";
    $desc_val = "";
    $precio_val = "";
    $cat_val = "";
    $oferta_val = "NO";
    
    $stocks_tallas = isset($stocks_tallas) ? $stocks_tallas : [];
    
    function getVal($data, $talla, $field){
        return isset($data[$talla][$field]) ? $data[$talla][$field] : 0;
    }
    
    $tipo_seleccionado = 'unico'; 

    if(isset($edit) && isset($pro) && is_object($pro)){
        $titulo = "Editar producto: " . $pro->nombre;
        $url_action = base_url."producto/save?id=".$pro->id;
        $nombre_val = $pro->nombre;
        $desc_val = $pro->descripcion;
        $precio_val = $pro->precio;
        $cat_val = $pro->categoria_id;
        $oferta_val = $pro->oferta;
        
        $keys = array_keys($stocks_tallas);
        // Lógica limpia: Solo busca Talla Única o números de Calzado
        if(in_array('Única', $keys)) $tipo_seleccionado = 'unico';
        elseif(in_array('40', $keys)) $tipo_seleccionado = 'calzado_adulto';
        elseif(in_array('30', $keys)) $tipo_seleccionado = 'calzado_nino';
    }
?>

<h1 class="mb-4"><?=$titulo?></h1>

<div class="card shadow p-4 mb-5">
    <form action="<?=$url_action?>" method="POST" enctype="multipart/form-data">
        
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?=$nombre_val?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3"><?=$desc_val?></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Precio Base (General)</label>
                <div class="input-group">
                    <span class="input-group-text"><?=Utils::getMonedaSymbol()?></span>
                    <input type="number" name="precio" step="0.01" class="form-control" value="<?=$precio_val?>" required>
                </div>
                <div class="form-text">Este precio se usará si no especificas uno por talla.</div>
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label">¿En Oferta?</label>
                <select name="oferta" class="form-select">
                    <option value="NO" <?= $oferta_val == 'NO' ? 'selected' : '' ?>>No</option>
                    <option value="SI" <?= $oferta_val == 'SI' ? 'selected' : '' ?>>Sí</option>
                </select>
            </div>
        </div>

        <div class="card bg-light mb-3 border-secondary">
            <div class="card-header fw-bold bg-secondary text-white">Stock y Precios por Variante</div>
            <div class="card-body">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Tipo de Producto:</label>
                    <select name="tipo_variante" id="tipo_variante" class="form-select" onchange="mostrarStock()">
                        <option value="unico" <?= $tipo_seleccionado == 'unico' ? 'selected' : '' ?>>Accesorio / Talla Única</option>
                        <option value="calzado_adulto" <?= $tipo_seleccionado == 'calzado_adulto' ? 'selected' : '' ?>>Calzado Adultos (35 - 45)</option>
                        <option value="calzado_nino" <?= $tipo_seleccionado == 'calzado_nino' ? 'selected' : '' ?>>Calzado Niños (18 - 34)</option>
                    </select>
                </div>

                <div id="block_calzado_adulto" class="stock-block" style="display:none;">
                    <div class="row text-center">
                        <?php for($i=35; $i<=45; $i++): 
                             $stock = getVal($stocks_tallas, $i, 'stock'); $price = getVal($stocks_tallas, $i, 'precio'); ?>
                        <div class="col-3 col-md-2 mb-3 border p-2 bg-white">
                            <label class="fw-bold"><?=$i?></label>
                            <input type="number" name="stock_calzado_adulto[<?=$i?>]" class="form-control text-center mb-1 form-control-sm" placeholder="Stock" value="<?=$stock?>" min="0">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><?=Utils::getMonedaSymbol()?></span>
                                <input type="number" name="precio_calzado_adulto[<?=$i?>]" class="form-control text-center" placeholder="Precio" value="<?=$price?>" step="0.01">
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div id="block_calzado_nino" class="stock-block" style="display:none;">
                    <div class="row text-center">
                        <?php for($i=18; $i<=34; $i++): 
                             $stock = getVal($stocks_tallas, $i, 'stock'); $price = getVal($stocks_tallas, $i, 'precio'); ?>
                        <div class="col-3 col-md-2 mb-3 border p-2 bg-white">
                            <label class="fw-bold"><?=$i?></label>
                            <input type="number" name="stock_calzado_nino[<?=$i?>]" class="form-control text-center mb-1 form-control-sm" placeholder="Stock" value="<?=$stock?>" min="0">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><?=Utils::getMonedaSymbol()?></span>
                                <input type="number" name="precio_calzado_nino[<?=$i?>]" class="form-control text-center" placeholder="Precio" value="<?=$price?>" step="0.01">
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div id="block_unico" class="stock-block" style="display:none;">
                    <label class="form-label">Stock General:</label>
                    <input type="number" name="stock_unico" class="form-control" value="<?=(isset($stocks_tallas['Única']['stock']) ? $stocks_tallas['Única']['stock'] : 0)?>" min="0">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Categoría</label>
            <select name="categoria" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php 
                    $categorias = Utils::showCategorias();
                    while($cat = $categorias->fetch_object()): 
                        $selected = ($cat->id == $cat_val) ? 'selected' : '';
                ?>
                    <option value="<?=$cat->id?>" <?=$selected?>><?=$cat->nombre?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Imagen Principal</label>
                <?php if(isset($pro) && is_object($pro) && !empty($pro->imagen)): ?>
                    <div class="mb-2"><img src="<?=Utils::showImage($pro->imagen)?>" width="100"></div>
                <?php endif; ?>
                <input type="file" name="imagen" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-primary"><i class="bi bi-images"></i> Galería (Opcional)</label>
                <input type="file" name="galeria[]" class="form-control" multiple accept="image/*">
            </div>
        </div>
        
        <?php if(isset($galeria_imagenes) && $galeria_imagenes->num_rows > 0): ?>
            <div class="card mb-4 border-light bg-white">
                <div class="card-header bg-white fw-bold">Imágenes de la Galería</div>
                <div class="card-body">
                    <div class="d-flex gap-3 overflow-auto">
                        <?php while($img = $galeria_imagenes->fetch_object()): ?>
                            <div class="position-relative">
                                <img src="<?=base_url?>assets/img/gallery/<?=$img->imagen?>" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                <a href="<?=base_url?>producto/eliminarImagen?id=<?=$img->id?>&pro_id=<?=$pro->id?>" class="position-absolute top-0 end-0 badge rounded-pill bg-danger text-white text-decoration-none m-1" onclick="return confirm('¿Borrar esta imagen?')"><i class="bi bi-x-lg"></i></a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="<?=base_url?>producto/gestion" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Producto</button>
        </div>
    </form>
</div>

<script>
    function mostrarStock(){
        var tipo = document.getElementById('tipo_variante').value;
        var bloques = document.getElementsByClassName('stock-block');
        for(var i=0; i<bloques.length; i++){ bloques[i].style.display = 'none'; }
        document.getElementById('block_' + tipo).style.display = 'block';
    }
    window.onload = function() { mostrarStock(); };
</script>