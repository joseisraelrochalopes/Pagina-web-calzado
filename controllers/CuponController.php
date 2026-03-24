<?php
require_once 'models/Cupon.php';

class CuponController {
    
    // Listar cupones
    public function gestion(){
        Utils::isAdmin();
        $cupon = new Cupon();
        $cupones = $cupon->getAll();
        
        require_once 'views/cupon/gestion.php';
    }

    // Formulario de creación
    public function crear(){
        Utils::isAdmin();
        require_once 'views/cupon/crear.php';
    }

    // Guardar en BD
    public function save(){
        Utils::isAdmin();
        if(isset($_POST)){
            $codigo = isset($_POST['codigo']) ? $_POST['codigo'] : false;
            $porcentaje = isset($_POST['porcentaje']) ? $_POST['porcentaje'] : false;
            
            if($codigo && $porcentaje){
                $cupon = new Cupon();
                $cupon->setCodigo($codigo);
                $cupon->setPorcentaje($porcentaje);
                
                $save = $cupon->save();
                
                if($save){
                    $_SESSION['cupon_status'] = "complete";
                }else{
                    $_SESSION['cupon_status'] = "failed";
                }
            }else{
                $_SESSION['cupon_status'] = "failed";
            }
        }
        header("Location:".base_url."cupon/gestion");
    }

    // Eliminar cupón
    public function borrar(){
        Utils::isAdmin();
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $cupon = new Cupon();
            $cupon->setId($id);
            $delete = $cupon->delete();
            
            if($delete){
                $_SESSION['delete'] = 'complete';
            }else{
                $_SESSION['delete'] = 'failed';
            }
        }
        header("Location:".base_url."cupon/gestion");
    }
}