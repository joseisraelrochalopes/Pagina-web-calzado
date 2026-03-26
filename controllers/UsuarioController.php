<?php
require_once 'models/Usuario.php';

class UsuarioController {
    
    public function index(){
        echo "Controlador Usuarios, Acción index";
    }

    public function registro(){
        require_once 'views/usuario/registro.php';
    }

    public function save(){
        if(isset($_POST)){
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : false;
            $apellidos = isset($_POST['apellidos']) ? $_POST['apellidos'] : false;
            $email = isset($_POST['email']) ? $_POST['email'] : false;
            $password = isset($_POST['password']) ? $_POST['password'] : false;
            
            if($nombre && $apellidos && $email && $password){
                $usuario = new Usuario();
                
                // VALIDAMOS SI EL EMAIL EXISTE ANTES DE INSERTARLO
                if($usuario->findByEmail($email)){
                    $_SESSION['register'] = "failed";
                } else {
                    $usuario->setNombre($nombre);
                    $usuario->setApellidos($apellidos);
                    $usuario->setEmail($email);
                    
                    $password_segura = password_hash($password, PASSWORD_BCRYPT, ['cost'=>4]);
                    $usuario->setPassword($password_segura);

                    $save = $usuario->save();
                    
                    if($save){
                        $_SESSION['register'] = "complete";
                    }else{
                        $_SESSION['register'] = "failed";
                    }
                }
            }else{
                $_SESSION['register'] = "failed";
            }
        }else{
            $_SESSION['register'] = "failed";
        }
        header("Location:".base_url.'usuario/registro');
    }

    public function login(){
        require_once 'views/usuario/login.php';
    }

    public function identificar(){
        if(isset($_POST)){
            $usuario = new Usuario();
            $identity = $usuario->login($_POST['email'], $_POST['password']);
            
            if($identity && is_object($identity)){
                $_SESSION['identity'] = $identity;
                
                if($identity->rol == 'admin'){
                    $_SESSION['admin'] = true;
                }
                header("Location:".base_url);
            }else{
                $_SESSION['error_login'] = 'Identificación fallida !!';
                header("Location:".base_url.'usuario/login');
            }
        } else {
            header("Location:".base_url);
        }
    }

    public function logout(){
        if(isset($_SESSION['identity'])){
            unset($_SESSION['identity']);
        }
        if(isset($_SESSION['admin'])){
            unset($_SESSION['admin']);
        }
        
        // LIMPIAR EL CARRITO PARA QUE NO SE VEAN PRODUCTOS AL SALIR
        if(isset($_SESSION['carrito'])){
            unset($_SESSION['carrito']);
        }
        
        header("Location:".base_url);
    }

    public function mis_datos(){
        if(isset($_SESSION['identity'])){
            require_once 'views/usuario/mis_datos.php';
        } else {
            header("Location:".base_url);
        }
    }

    public function save_changes(){
        if(isset($_SESSION['identity'])){
            $usuario_id = $_SESSION['identity']->id;
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : false;
            $apellidos = isset($_POST['apellidos']) ? $_POST['apellidos'] : false;
            $email = isset($_POST['email']) ? $_POST['email'] : false;
            $password = isset($_POST['password']) ? $_POST['password'] : false;

            if($nombre && $apellidos && $email){
                $usuario = new Usuario();
                $usuario->setId($usuario_id);
                $usuario->setNombre($nombre);
                $usuario->setApellidos($apellidos);
                $usuario->setEmail($email);

                if(!empty($password)){
                    $password_segura = password_hash($password, PASSWORD_BCRYPT, ['cost'=>4]);
                    $usuario->setPassword($password_segura);
                }

                if(isset($_FILES['imagen'])){
                    $file = $_FILES['imagen'];
                    $filename = $file['name'];
                    $mimetype = $file['type'];

                    if($mimetype == "image/jpg" || $mimetype == 'image/jpeg' || $mimetype == 'image/png' || $mimetype == 'image/gif'){
                        if(!is_dir('assets/img/users')){ mkdir('assets/img/users', 0777, true); }
                        move_uploaded_file($file['tmp_name'], 'assets/img/users/'.$filename);
                        $usuario->setImagen($filename);
                        $_SESSION['identity']->imagen = $filename;
                    }
                }

                $save = $usuario->update();

                if($save){
                    $_SESSION['user_update'] = "complete";
                    $_SESSION['identity']->nombre = $nombre;
                    $_SESSION['identity']->apellidos = $apellidos;
                    $_SESSION['identity']->email = $email;
                }else{
                    $_SESSION['user_update'] = "failed";
                }
            }else{
                $_SESSION['user_update'] = "failed";
            }
        }
        header("Location:".base_url.'usuario/mis_datos');
    }

    public function olvide(){
        require_once 'views/usuario/olvide.php';
    }

    public function send_reset(){
        if(isset($_POST['email'])){
            $email = $_POST['email'];
            $token = bin2hex(random_bytes(50));
            
            $usuario = new Usuario();
            $usuario->setEmail($email);
            $usuario->setTokenReset($token);
            
            $save = $usuario->saveToken();
            
            if($save){
                $link = base_url . "usuario/restablecer?token=" . $token;
                $_SESSION['reset_status'] = "sent";
                $_SESSION['reset_link_simulation'] = $link;
            } else {
                $_SESSION['reset_status'] = "failed";
            }
        }
        header("Location:".base_url.'usuario/olvide');
    }

    public function restablecer(){
        if(isset($_GET['token'])){
            $token = $_GET['token'];
            $usuarioModel = new Usuario();
            $usuarioModel->setTokenReset($token);
            $usuario = $usuarioModel->getByToken();
            
            if($usuario){
                require_once 'views/usuario/restablecer.php';
            } else {
                echo "<h1 class='text-center mt-5 text-danger'>Token inválido.</h1>";
            }
        } else {
            header("Location:".base_url);
        }
    }

    public function save_new_password(){
        if(isset($_POST['token']) && isset($_POST['password'])){
            $token = $_POST['token'];
            $password = $_POST['password'];
            
            $usuario = new Usuario();
            $usuario->setTokenReset($token);
            $password_segura = password_hash($password, PASSWORD_BCRYPT, ['cost'=>4]);
            $usuario->setPassword($password_segura);
            
            $update = $usuario->updatePasswordByToken();
            
            if($update){
                $_SESSION['reset_complete'] = true;
                header("Location:".base_url."usuario/login");
            }
        } else {
            header("Location:".base_url);
        }
    }

    // --- MÉTODOS PARA GESTIÓN DE USUARIOS (ADMIN) ---

    public function gestion(){
        Utils::isAdmin(); 
        $usuario = new Usuario();
        $usuarios = $usuario->getAll();
        
        require_once 'views/usuario/gestion.php';
    }

    // NUEVO: Ver favoritos de un cliente específico para el Admin
    public function verFavoritosAdmin(){
        Utils::isAdmin();
        
        if(isset($_GET['id'])){
            $id = (int)$_GET['id'];
            
            // 1. Obtener datos del cliente
            $usuario = new Usuario();
            $usuario->setId($id);
            $user_data = $usuario->getOne();
            
            if(!$user_data){
                header("Location:".base_url."usuario/gestion");
                exit();
            }
            
            // 2. Obtener sus productos favoritos
            require_once 'models/Favorito.php';
            $favorito = new Favorito();
            $favorito->setUsuario_id($id);
            $mis_favoritos = $favorito->getAllByUser();
            
            // 3. Cargar la vista específica para el Admin
            require_once 'views/usuario/favoritos_admin.php';
        } else {
            header("Location:".base_url."usuario/gestion");
        }
    }

    public function rol(){
        Utils::isAdmin();
        if(isset($_GET['id']) && isset($_GET['rol'])){
            $id = $_GET['id'];
            $rol = $_GET['rol'];
            
            if($rol == 'admin' || $rol == 'user'){
                $usuario = new Usuario();
                $usuario->setId($id);
                $usuario->setRol($rol);
                $usuario->updateRol();
            }
        }
        header("Location:".base_url."usuario/gestion");
    }
}