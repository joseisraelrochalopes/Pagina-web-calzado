<?php
require_once 'models/Configuracion.php';

class ConfiguracionController {

    public function index() {
        Utils::isAdmin();
        
        $confModel = new Configuracion();
        $config = $confModel->getAll();
        
        require_once 'views/configuracion/gestion.php';
    }

    public function save() {
        Utils::isAdmin();
        
        if(isset($_POST)){
            $confModel = new Configuracion();

            // 1. Guardar campos de texto
            $campos = ['moneda', 'nombre_empresa', 'ruc', 'direccion', 'telefono', 'email'];
            
            foreach($campos as $campo){
                if(isset($_POST[$campo])){
                    $confModel->setValue($campo, $_POST[$campo]);
                }
            }

            // Actualizar sesión de moneda
            if(isset($_POST['moneda'])){
                $_SESSION['moneda_simbolo'] = $_POST['moneda'];
            }

            // 2. Guardar Logo (CORREGIDO: Nombre dinámico para evitar caché)
            if(isset($_FILES['logo']) && !empty($_FILES['logo']['name'])){
                $file = $_FILES['logo'];
                $filename = $file['name'];
                $mimetype = $file['type'];

                if($mimetype == "image/jpg" || $mimetype == 'image/jpeg' || $mimetype == 'image/png' || $mimetype == 'image/gif'){
                    if(!is_dir('assets/img/logo')){
                        mkdir('assets/img/logo', 0777, true);
                    }
                    
                    // Generamos un nombre único con el tiempo actual
                    $new_filename = 'logo_' . time() . '_' . $filename;
                    
                    // Movemos el archivo
                    move_uploaded_file($file['tmp_name'], 'assets/img/logo/'.$new_filename);
                    
                    // Guardamos el nuevo nombre en la BD
                    $confModel->setValue('logo', $new_filename);
                }
            }

            $_SESSION['config_success'] = "Datos actualizados correctamente.";
        }
        
        header("Location:" . base_url . "configuracion/index");
    }
}