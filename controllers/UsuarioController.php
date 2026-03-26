<?php
require_once 'models/Usuario.php';

class UsuarioController {
    
    public function registro(){
        require_once 'views/usuario/registro.php';
    }

    public function save(){
        if(isset($_POST)){
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : false;
            $apellidos = isset($_POST['apellidos']) ? $_POST['apellidos'] : false;
            $email = isset($_POST['email']) ? $_POST['email'] : false;
            $password = isset($_POST['password']) ? $_POST['password'] : false;
            $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : false; 
            
            if($nombre && $apellidos && $email && $password && $telefono){
                $usuario = new Usuario();
                
                if($usuario->findByEmail($email)){
                    $_SESSION['register'] = "failed";
                } else {
                    $usuario->setNombre($nombre);
                    $usuario->setApellidos($apellidos);
                    $usuario->setEmail($email);
                    $usuario->setTelefono($telefono);
                    
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
                if($identity->rol == 'admin'){ $_SESSION['admin'] = true; }
                header("Location:".base_url);
            }else{
                $_SESSION['error_login'] = 'Identificación fallida !!';
                header("Location:".base_url.'usuario/login');
            }
        }
    }

    public function logout(){
        unset($_SESSION['identity']);
        unset($_SESSION['admin']);
        if(isset($_SESSION['carrito'])){ unset($_SESSION['carrito']); }
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
            $usuario = new Usuario();
            $usuario->setId($_SESSION['identity']->id);
            $usuario->setNombre($_POST['nombre']);
            $usuario->setApellidos($_POST['apellidos']);
            $usuario->setEmail($_POST['email']);
            $usuario->setTelefono($_POST['telefono']); 

            if(!empty($_POST['password'])){
                $password_segura = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost'=>4]);
                $usuario->setPassword($password_segura);
            }

            $save = $usuario->update();

            if($save){
                $_SESSION['user_update'] = "complete";
                $_SESSION['identity']->nombre = $_POST['nombre'];
                $_SESSION['identity']->apellidos = $_POST['apellidos'];
                $_SESSION['identity']->email = $_POST['email'];
                $_SESSION['identity']->telefono = $_POST['telefono'];
            }else{
                $_SESSION['user_update'] = "failed";
            }
        }
        header("Location:".base_url.'usuario/mis_datos');
    }

    public function olvide(){ require_once 'views/usuario/olvide.php'; }
    
    public function send_reset(){
        if(isset($_POST['email'])){
            $token = bin2hex(random_bytes(50));
            $usuario = new Usuario();
            $usuario->setEmail($_POST['email']);
            $usuario->setTokenReset($token);
            if($usuario->saveToken()){
                $_SESSION['reset_status'] = "sent";
                $_SESSION['reset_link_simulation'] = base_url . "usuario/restablecer?token=" . $token;
            }
        }
        header("Location:".base_url.'usuario/olvide');
    }

    public function gestion(){
        Utils::isAdmin();
        $usuarios = (new Usuario())->getAll();
        require_once 'views/usuario/gestion.php';
    }

    public function google_callback() {
        if (isset($_GET['code'])) {
            // --- CONFIGURACIÓN ---
            $client_id = "520144432766-h1tl34gmborl48sqahct3h42ls4tptdg.apps.googleusercontent.com"; 
            $client_secret = "****yZ0k"; // <-- PEGA AQUÍ TU SECRETO DE LA IMAGEN
            
            // Forzamos la URL de internet para evitar el error de localhost
            $redirect_uri = "https://calsado.shop/usuario/google_callback";

            $post_data = [
                'code' => $_GET['code'],
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri,
                'grant_type' => 'authorization_code'
            ];

            $ch = curl_init('https://oauth2.googleapis.com/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            $response = curl_exec($ch);
            $data = json_decode($response, true);
            curl_close($ch);

            if (isset($data['access_token'])) {
                $user_info_url = 'https://www.googleapis.com/oauth2/v3/userinfo?access_token=' . $data['access_token'];
                $user_info = json_decode(file_get_contents($user_info_url), true);

                if (isset($user_info['email'])) {
                    $usuario = new Usuario();
                    $identity = $usuario->findByEmail($user_info['email']);

                    if ($identity) {
                        $_SESSION['identity'] = $identity;
                        if($identity->rol == 'admin'){ $_SESSION['admin'] = true; }
                    } else {
                        $usuario->setNombre($user_info['given_name']);
                        $usuario->setApellidos($user_info['family_name'] ?? '');
                        $usuario->setEmail($user_info['email']);
                        $usuario->setGoogleId($user_info['sub']);
                        $usuario->setPassword('google_auth_' . bin2hex(random_bytes(4)));
                        $usuario->setTelefono(''); 
                        
                        $save = $usuario->save();
                        if($save) {
                            $_SESSION['identity'] = $usuario->findByEmail($user_info['email']);
                        }
                    }
                    header("Location:" . base_url);
                    exit();
                }
            }
        }
        header("Location:" . base_url . "usuario/login");
    }
}