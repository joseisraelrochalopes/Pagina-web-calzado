<?php
require_once 'models/Producto.php';
require_once 'models/Cupon.php';

class CarritoController {

    public function index(){
        if(isset($_SESSION['carrito']) && count($_SESSION['carrito']) >= 1){
            $carrito = $_SESSION['carrito'];
        }else{
            $carrito = array();
        }
        require_once 'views/carrito/index.php';
    }

    public function add(){
        // Recogemos ID, TALLA y ahora también la CANTIDAD
        if(isset($_POST['producto_id'])){
            $producto_id = $_POST['producto_id'];
            $talla = isset($_POST['talla']) ? $_POST['talla'] : 'Única';
            // Atrapamos la cantidad (si no mandan nada, por defecto es 1)
            $cantidad_solicitada = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
            
            // Seguridad extra por si intentan mandar números negativos desde el navegador
            if($cantidad_solicitada < 1) {
                $cantidad_solicitada = 1;
            }
        } elseif(isset($_GET['id'])){
            // Soporte para añadir desde el catálogo rápido (1 unidad por defecto)
            $producto_id = $_GET['id'];
            $talla = 'Única'; 
            $cantidad_solicitada = 1;
        } else {
            header('Location:'.base_url);
            return;
        }

        $productoModel = new Producto();
        $productoModel->setId($producto_id);
        
        // --- OBTENER DATOS DE LA TALLA (PRECIO Y STOCK) ---
        $infoTalla = $productoModel->getInfoByTalla($talla);
        
        // Si no encuentra la talla específica, usa el general
        if(!$infoTalla) { 
            $pro = $productoModel->getOne();
            $precio_real = $pro->precio;
            $stock_real = $pro->stock;
        } else {
            $pro = $productoModel->getOne(); 
            $precio_real = $infoTalla->precio;
            $stock_real = $infoTalla->stock;
        }

        $counter = 0;
        if(isset($_SESSION['carrito'])){
            foreach($_SESSION['carrito'] as $indice => $elemento){
                // Si el producto y la talla ya están en el carrito
                if($elemento['id_producto'] == $producto_id && $elemento['talla'] == $talla){
                    $counter++;
                    
                    // Verificamos si lo que ya tiene en el carrito + lo que quiere añadir no supera el stock
                    if($elemento['unidades'] + $cantidad_solicitada > $stock_real){
                        $_SESSION['carrito_error'] = "No hay suficiente stock de la talla $talla para añadir $cantidad_solicitada pares más.";
                    } else {
                        // Sumamos la cantidad exacta que pidió
                        $_SESSION['carrito'][$indice]['unidades'] += $cantidad_solicitada;
                    }
                }
            }
        }
        
        // Si el producto/talla no estaba en el carrito, lo metemos como nuevo
        if($counter == 0){
            if(is_object($pro)){
                if($stock_real >= $cantidad_solicitada){
                    $_SESSION['carrito'][] = array(
                        "id_producto" => $pro->id,
                        "precio" => $precio_real, 
                        "unidades" => $cantidad_solicitada, // GUARDAMOS LA CANTIDAD QUE ELIGIÓ
                        "producto" => $pro,
                        "talla" => $talla 
                    );
                } else {
                    $_SESSION['carrito_error'] = "Solo nos quedan $stock_real unidades de la talla $talla.";
                }
            }
        }
        header("Location:".base_url."carrito/index");
    }

    public function delete(){
        if(isset($_GET['index'])){
            $index = $_GET['index'];
            unset($_SESSION['carrito'][$index]);
            // Reordenar array para evitar huecos en índices
            $_SESSION['carrito'] = array_values($_SESSION['carrito']);
        }
        header("Location:".base_url."carrito/index");
    }

    public function delete_all(){
        unset($_SESSION['carrito']);
        unset($_SESSION['cupon']);
        header("Location:".base_url."carrito/index");
    }
    
    public function up(){
        if(isset($_GET['index'])){
            $index = $_GET['index'];
            $producto_id = $_SESSION['carrito'][$index]['id_producto'];
            $talla_actual = $_SESSION['carrito'][$index]['talla'];
            
            $productoModel = new Producto();
            $productoModel->setId($producto_id);
            
            $stock_real_talla = $productoModel->getStockByTalla($talla_actual);
            
            if($_SESSION['carrito'][$index]['unidades'] + 1 <= $stock_real_talla){
                $_SESSION['carrito'][$index]['unidades']++;
            } else {
                $_SESSION['carrito_error'] = "Has alcanzado el límite de stock para la talla $talla_actual.";
            }
        }
        header("Location:".base_url."carrito/index");
    }
    
    public function down(){
        if(isset($_GET['index'])){
            $index = $_GET['index'];
            $_SESSION['carrito'][$index]['unidades']--;
            
            if($_SESSION['carrito'][$index]['unidades'] == 0){
                unset($_SESSION['carrito'][$index]);
                $_SESSION['carrito'] = array_values($_SESSION['carrito']);
            }
        }
        header("Location:".base_url."carrito/index");
    }

    public function aplicarCupon(){
        if(isset($_POST['codigo'])){
            $codigo = $_POST['codigo'];
            $cuponModel = new Cupon();
            $cuponModel->setCodigo($codigo);
            $cupon = $cuponModel->getByName();
            
            if($cupon){
                $_SESSION['cupon'] = [
                    'codigo' => $cupon->codigo,
                    'porcentaje' => $cupon->porcentaje
                ];
                $_SESSION['cupon_success'] = "¡Cupón aplicado con éxito!";
            }else{
                $_SESSION['cupon_error'] = "El cupón no existe o no es válido.";
            }
        }
        header("Location:".base_url."carrito/index");
    }

    public function borrarCupon(){
        if(isset($_SESSION['cupon'])){
            unset($_SESSION['cupon']);
        }
        header("Location:".base_url."carrito/index");
    }
}