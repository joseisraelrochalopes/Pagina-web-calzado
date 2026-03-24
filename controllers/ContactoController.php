<?php
require_once 'models/Mensaje.php';

class ContactoController {
    
    // Mostrar formulario al público
    public function index(){
        require_once 'views/contacto/index.php';
    }

    // Procesar el envío del formulario
    public function enviar(){
        if(isset($_POST)){
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : false;
            $email = isset($_POST['email']) ? $_POST['email'] : false;
            $asunto = isset($_POST['asunto']) ? $_POST['asunto'] : false;
            $mensaje_texto = isset($_POST['mensaje']) ? $_POST['mensaje'] : false;
            
            if($nombre && $email && $asunto && $mensaje_texto){
                $mensaje = new Mensaje();
                $mensaje->setNombre($nombre);
                $mensaje->setEmail($email);
                $mensaje->setAsunto($asunto);
                $mensaje->setMensaje($mensaje_texto);
                
                $save = $mensaje->save();
                
                if($save){
                    $_SESSION['contacto'] = "complete";
                }else{
                    $_SESSION['contacto'] = "failed";
                }
            }else{
                $_SESSION['contacto'] = "failed";
            }
        }
        header("Location:".base_url."contacto/index");
    }

    // --- ZONA ADMIN: BANDEJA DE ENTRADA ---
    
    public function admin(){
        Utils::isAdmin();
        $mensaje = new Mensaje();
        $mensajes = $mensaje->getAll();
        
        require_once 'views/contacto/admin.php';
    }

    public function leer(){
        Utils::isAdmin();
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $mensaje = new Mensaje();
            $mensaje->setId($id);
            $mensaje->markAsRead();
        }
        header("Location:".base_url."contacto/admin");
    }
}