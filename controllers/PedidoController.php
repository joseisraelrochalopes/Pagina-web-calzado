<?php
require_once 'models/Pedido.php';
require_once 'models/Configuracion.php';

class PedidoController {
    
    public function hacer(){
        if(!isset($_SESSION['identity'])){
            header("Location:".base_url."usuario/login");
        }else{
            require_once 'views/pedido/hacer.php';
        }
    }

    public function add(){
        if(isset($_SESSION['identity'])){
            $provincia = isset($_POST['provincia']) ? $_POST['provincia'] : false;
            $localidad = isset($_POST['localidad']) ? $_POST['localidad'] : false;
            $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : false;
            $metodo_pago = isset($_POST['metodo_pago']) ? $_POST['metodo_pago'] : 'No especificado';
            
            $stats = Utils::statsCarrito();
            $coste = $stats['total_con_descuento'];
            
            if($provincia && $localidad && $direccion){
                $pedido = new Pedido();
                $pedido->setUsuario_id($_SESSION['identity']->id);
                $pedido->setProvincia($provincia);
                $pedido->setLocalidad($localidad);
                $pedido->setDireccion($direccion);
                $pedido->setCoste($coste);
                $pedido->setCosteEnvio( ($coste >= 200) ? 0 : 10 );
                $pedido->setMetodoPago($metodo_pago);
                
                // Si es PayPal, el estado inicial es 'Pendiente de Pago'
                if($metodo_pago == 'PayPal'){
                    $pedido->setEstado('confirm'); 
                }

                $save = $pedido->save();
                $save_linea = $pedido->save_linea();
                
                if($save && $save_linea){
                    $_SESSION['pedido'] = "complete";
                    
                    if($metodo_pago != 'PayPal'){
                        unset($_SESSION['carrito']);
                        unset($_SESSION['cupon']);
                    }
                    
                    header("Location:".base_url."pedido/confirmado");
                }else{
                    $_SESSION['pedido'] = "failed";
                    header("Location:".base_url."pedido/hacer");
                }
            }else{
                $_SESSION['pedido'] = "failed";
                header("Location:".base_url."pedido/hacer");
            }
        }else{
            header("Location:".base_url);
        }
    }

    public function confirmado(){
        if(isset($_SESSION['identity'])){
            $identity = $_SESSION['identity'];
            $pedido_obj = new Pedido();
            $pedido_obj->setUsuario_id($identity->id);
            
            $pedido = $pedido_obj->getOneByUser();
            
            $productos_obj = new Pedido();
            $productos = $productos_obj->getProductosByPedido($pedido->id);
        }
        require_once 'views/pedido/confirmado.php';
    }

    public function mis_pedidos(){
        if(isset($_SESSION['identity'])){
            $usuario_id = $_SESSION['identity']->id;
            $pedido = new Pedido();
            $pedido->setUsuario_id($usuario_id);
            $pedidos = $pedido->getAllByUser();
            require_once 'views/pedido/mis_pedidos.php';
        }else{
            header("Location:".base_url."usuario/login");
        }
    }

    public function detalle(){
        if(isset($_SESSION['identity'])){
            if(isset($_GET['id'])){
                $id = $_GET['id'];
                $pedido = new Pedido();
                $pedido->setId($id);
                $pedido = $pedido->getOne();
                $pedido_productos = new Pedido();
                $productos = $pedido_productos->getProductosByPedido($id);
                require_once 'views/pedido/detalle.php';
            }else{
                header("Location:".base_url."pedido/mis_pedidos");
            }
        }else{
            header("Location:".base_url."usuario/login");
        }
    }

    public function factura(){
        Utils::isAdmin();
        if(isset($_SESSION['identity'])){
            if(isset($_GET['id'])){
                $id = $_GET['id'];
                $pedido = new Pedido();
                $pedido->setId($id);
                $pedido = $pedido->getOne();
                $pedido_productos = new Pedido();
                $productos = $pedido_productos->getProductosByPedido($id);
                $confModel = new Configuracion();
                $empresa = $confModel->getAll();
                require_once 'views/pedido/factura.php';
            }
        }else{
            header("Location:".base_url);
        }
    }

    public function gestion(){
        Utils::isAdmin();
        $gestion = true;
        $pedido = new Pedido();
        if(isset($_POST['busquedaId']) && !empty($_POST['busquedaId'])){
            $pedidos = $pedido->getById($_POST['busquedaId']);
        } elseif(isset($_POST['filtroEstado']) && $_POST['filtroEstado'] != ''){
            $pedidos = $pedido->getByStatus($_POST['filtroEstado']);
        } else {
            $pedidos = $pedido->getAll();
        }
        require_once 'views/pedido/mis_pedidos.php';
    }

    public function estado(){
        Utils::isAdmin();
        if(isset($_POST['pedido_id']) && isset($_POST['estado'])){
            $id = $_POST['pedido_id'];
            $estado = $_POST['estado'];
            
            $pedido = new Pedido();
            $pedido->setId($id);
            
            $pedido_info = $pedido->getOne(); 
            
            $pedido->setEstado($estado);
            $edit = $pedido->edit();
            
            if($edit && $pedido_info){
                
                $db = Database::connect();
                $usuario_id = $pedido_info->usuario_id;
                $sql = "SELECT nombre, email FROM usuarios WHERE id = {$usuario_id}";
                $resultado = $db->query($sql);
                
                if($resultado && $resultado->num_rows == 1){
                    $user_data = $resultado->fetch_object();
                    
                    if(!empty($user_data->email)){
                        
                        $estado_texto = "en proceso";
                        if($estado == 'confirm') $estado_texto = "CONFIRMADO y en espera de preparación";
                        elseif($estado == 'preparation') $estado_texto = "EN PREPARACIÓN en nuestro almacén";
                        elseif($estado == 'ready') $estado_texto = "LISTO PARA ENVIARSE";
                        elseif($estado == 'sended') $estado_texto = "ENVIADO y va en camino a tu domicilio";
                        elseif($estado == 'cancelled') $estado_texto = "CANCELADO";

                        $to = $user_data->email;
                        $subject = "Actualización de tu pedido #$id - Calsado Shop";
                        
                        $message = "Hola " . $user_data->nombre . ",\n\n";
                        $message .= "Te informamos que el estado de tu pedido #" . $id . " ha sido actualizado.\n\n";
                        $message .= "Nuevo estado: " . strtoupper($estado_texto) . "\n\n";
                        $message .= "Puedes revisar los detalles entrando a tu cuenta en nuestra tienda.\n\n";
                        $message .= "Gracias por tu compra.";

                        // ACTUALIZADO PARA ENVIAR COMO CALSADO.SHOP
                        $headers = "From: notificaciones@calsado.shop" . "\r\n" .
                                   "Reply-To: contacto@calsado.shop" . "\r\n" .
                                   "X-Mailer: PHP/" . phpversion();

                        @mail($to, $subject, $message, $headers); 
                    }
                }
                
                // 🔥 ESTA ES LA LÍNEA NUEVA QUE ACTIVA EL MENSAJE VERDE 🔥
                $_SESSION['status_pedido'] = "success";
            }
            
            header("Location:".base_url.'pedido/detalle?id='.$id);
        }else{
            header("Location:".base_url);
        }
    }

    public function cancelar(){
        if(isset($_SESSION['identity']) && isset($_GET['id'])){
            $id = $_GET['id'];
            $pedido = new Pedido();
            $pedido->setId($id);
            $ped = $pedido->getOne();
            if($ped->usuario_id == $_SESSION['identity']->id || isset($_SESSION['admin'])){
                if($ped->estado == 'confirm'){
                    $pedido->cancelar();
                }
            }
            header("Location:".base_url.'pedido/detalle?id='.$id);
        }else{
            header("Location:".base_url);
        }
    }

    public function exportar(){
        Utils::isAdmin();
        while (ob_get_level()) { ob_get_clean(); }
        $filename = "pedidos_" . date('Y-m-d') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fputs($output, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($output, array('ID Pedido', 'Usuario ID', 'Provincia', 'Ciudad', 'Direccion', 'Coste Total', 'Coste Envio', 'Metodo Pago', 'Estado', 'Fecha', 'Hora'));
        $pedido = new Pedido();
        $pedidos = $pedido->getAll();
        while($fila = $pedidos->fetch_assoc()){
            fputcsv($output, $fila);
        }
        fclose($output);
        exit();
    }
}