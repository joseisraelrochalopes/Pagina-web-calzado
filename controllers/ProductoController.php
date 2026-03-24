<?php
require_once 'models/Producto.php';
require_once 'models/Valoracion.php';
require_once 'models/Configuracion.php'; // NUEVO: Importar configuración

class ProductoController {
    
    public function index(){
        $producto = new Producto();
        $ofertas = $producto->getOfertas(3);
        $min_price = isset($_GET['min_price']) && $_GET['min_price'] != '' ? (float)$_GET['min_price'] : null;
        $max_price = isset($_GET['max_price']) && $_GET['max_price'] != '' ? (float)$_GET['max_price'] : null;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'new'; 
        $limit = 6; 
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        $total_products = $producto->getCount($min_price, $max_price);
        $total_pages = ceil($total_products / $limit);
        $productos = $producto->getPaginated($limit, $offset, $min_price, $max_price, $sort);
        require_once 'views/producto/destacados.php';
    }

    public function ver(){
        if(isset($_GET['id'])){
            $id = (int)$_GET['id'];
            $producto = new Producto();
            $producto->setId($id);
            $pro = $producto->getOne();
            
            if(is_object($pro)){
                $producto->setCategoria_id($pro->categoria_id);
                $relacionados = $producto->getAllCategory();
                
                $valoracion = new Valoracion();
                $valoracion->setProducto_id($id);
                $opiniones = $valoracion->getByProducto();
                $stats_valoracion = $valoracion->getMedia();

                $stocks_tallas = $producto->getStocksTallas();
                $galeria = $producto->getImages();

                if(!isset($_SESSION['historial'])) $_SESSION['historial'] = [];
                $key = array_search($id, $_SESSION['historial']);
                if($key !== false) unset($_SESSION['historial'][$key]);
                array_unshift($_SESSION['historial'], $id);
                $_SESSION['historial'] = array_slice($_SESSION['historial'], 0, 5);
                $historial_ids = array_diff($_SESSION['historial'], [$id]);
                $productos_historial = !empty($historial_ids) ? $producto->getByIds($historial_ids) : null;
                
                require_once 'views/producto/ver.php';
            } else {
                header('Location:'.base_url);    
            }
        }else{
            header('Location:'.base_url);
        }
    }

    public function gestion(){
        Utils::isAdmin();
        $producto = new Producto();
        $search = isset($_POST['search_admin']) ? $_POST['search_admin'] : null;
        $cat_id = isset($_POST['cat_admin']) && $_POST['cat_admin'] != '' ? $_POST['cat_admin'] : null;
        if($search || $cat_id){
            $productos = $producto->getAdminFiltered($search, $cat_id);
        } else {
            $productos = $producto->getAll();
        }
        require_once 'views/producto/gestion.php';
    }

    public function crear(){
        Utils::isAdmin();
        require_once 'views/producto/crear.php';
    }

    public function editar(){
        Utils::isAdmin();
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $edit = true;
            $producto = new Producto();
            $producto->setId($id);
            $pro = $producto->getOne();
            $stocks_tallas = $producto->getStocksTallas();
            $galeria_imagenes = $producto->getImages();
            require_once 'views/producto/crear.php';
        }else{
            header('Location:'.base_url.'producto/gestion');
        }
    }

    public function save(){
        Utils::isAdmin();
        if(isset($_POST)){
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : false;
            $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : false;
            $precio = isset($_POST['precio']) ? $_POST['precio'] : 0;
            $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : false;
            $oferta = isset($_POST['oferta']) ? $_POST['oferta'] : 'NO';
            $tipo_variante = isset($_POST['tipo_variante']) ? $_POST['tipo_variante'] : 'unico';
            
            if($nombre && $categoria){
                $producto = new Producto();
                $producto->setNombre($nombre);
                $producto->setDescripcion($descripcion);
                $producto->setPrecio($precio);
                $producto->setCategoria_id($categoria);
                $producto->setOferta($oferta);
                
                if(isset($_FILES['imagen'])){
                    $file = $_FILES['imagen'];
                    $filename = $file['name'];
                    $mimetype = $file['type'];
                    if($mimetype == "image/jpg" || $mimetype == 'image/jpeg' || $mimetype == 'image/png' || $mimetype == 'image/gif'){
                        if(!is_dir('assets/img')){ mkdir('assets/img', 0777, true); }
                        move_uploaded_file($file['tmp_name'], 'assets/img/'.$filename);
                        $producto->setImagen($filename);
                    }
                }
                
                if(isset($_GET['id'])){
                    $id = $_GET['id'];
                    $producto->setId($id);
                    $save = $producto->edit();
                    $producto_id = $id;
                }else{
                    $save = $producto->save();
                    $producto_id = $save;
                }
                
                if($save && $producto_id){
                    $producto->setId($producto_id);
                    
                    $tallas_cant = [];
                    $tallas_precio = [];

                    if($tipo_variante == 'unico'){
                        $tallas_cant['Única'] = isset($_POST['stock_unico']) ? (int)$_POST['stock_unico'] : 0;
                        $tallas_precio['Única'] = $precio;
                    } elseif($tipo_variante == 'ropa_adulto'){
                        $tallas_cant = $_POST['stock_ropa_adulto'] ?? [];
                        $tallas_precio = $_POST['precio_ropa_adulto'] ?? [];
                    } elseif($tipo_variante == 'ropa_nino'){
                        $tallas_cant = $_POST['stock_ropa_nino'] ?? [];
                        $tallas_precio = $_POST['precio_ropa_nino'] ?? [];
                    } elseif($tipo_variante == 'calzado_adulto'){
                        $tallas_cant = $_POST['stock_calzado_adulto'] ?? [];
                        $tallas_precio = $_POST['precio_calzado_adulto'] ?? [];
                    } elseif($tipo_variante == 'calzado_nino'){
                        $tallas_cant = $_POST['stock_calzado_nino'] ?? [];
                        $tallas_precio = $_POST['precio_calzado_nino'] ?? [];
                    }
                    
                    foreach($tallas_cant as $talla => $cantidad){
                        $cantidad = (int)$cantidad;
                        $precio_t = isset($tallas_precio[$talla]) && $tallas_precio[$talla] > 0 ? $tallas_precio[$talla] : $precio;
                        if($cantidad >= 0) {
                            $producto->saveStockTalla($talla, $cantidad, $precio_t);
                        }
                    }
                    $producto->updateStockTotal();

                    if(isset($_FILES['galeria'])){
                        $count = count($_FILES['galeria']['name']);
                        for($i=0; $i<$count; $i++){
                            $filename = $_FILES['galeria']['name'][$i];
                            $mimetype = $_FILES['galeria']['type'][$i];
                            $tmp_name = $_FILES['galeria']['tmp_name'][$i];
                            if(!empty($filename) && ($mimetype == "image/jpg" || $mimetype == 'image/jpeg' || $mimetype == 'image/png' || $mimetype == 'image/gif')){
                                if(!is_dir('assets/img/gallery')){ mkdir('assets/img/gallery', 0777, true); }
                                $new_filename = time()."_".$i."_".$filename;
                                move_uploaded_file($tmp_name, 'assets/img/gallery/'.$new_filename);
                                $producto->saveImage($new_filename);
                            }
                        }
                    }
                    $_SESSION['producto'] = "complete";
                }else{
                    $_SESSION['producto'] = "failed";
                }
            }else{
                $_SESSION['producto'] = "failed";
            }
        }else{
            $_SESSION['producto'] = "failed";
        }
        header("Location:".base_url."producto/gestion");
    }

    public function eliminarImagen(){
        Utils::isAdmin();
        if(isset($_GET['id']) && isset($_GET['pro_id'])){
            $id_imagen = $_GET['id'];
            $id_producto = $_GET['pro_id'];
            $producto = new Producto();
            $producto->deleteImageById($id_imagen);
            header("Location:".base_url."producto/editar?id=".$id_producto);
        } else {
            header("Location:".base_url."producto/gestion");
        }
    }

    public function activarDesactivar(){
        Utils::isAdmin();
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $producto = new Producto();
            $producto->setId($id);
            $update = $producto->toggleStatus();
            if($update){ $_SESSION['producto'] = 'status_changed'; }
            else{ $_SESSION['producto'] = 'status_failed'; }
        }else{
            $_SESSION['producto'] = 'status_failed';
        }
        header("Location:".base_url."producto/gestion");
    }

    public function buscar(){
        if(isset($_POST['busqueda'])){
            $busqueda = $_POST['busqueda'];
            $producto = new Producto();
            $productos = $producto->busqueda($busqueda);
            require_once 'views/producto/busqueda.php';
        }else{
            header('Location:'.base_url);
        }
    }
    
    // --- REPORTE ACTUALIZADO CON DATOS DE EMPRESA ---
    public function reporte(){
        Utils::isAdmin();
        
        // 1. Productos
        $producto = new Producto();
        $productos = $producto->getAll();
        
        // 2. Datos de Empresa
        $confModel = new Configuracion();
        $empresa = $confModel->getAll();

        require_once 'views/producto/reporte.php';
    }
}