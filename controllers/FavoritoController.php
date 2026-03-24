<?php
require_once 'models/Favorito.php';

class FavoritoController {

    public function index() {
        if (isset($_SESSION['identity'])) {
            $usuario_id = $_SESSION['identity']->id;
            
            $favorito = new Favorito();
            $favorito->setUsuario_id($usuario_id);
            $favoritos = $favorito->getAllByUser();
            
            require_once 'views/favorito/index.php';
        } else {
            header('Location:' . base_url . 'usuario/login');
        }
    }

    public function add() {
        if (isset($_SESSION['identity'])) {
            if (isset($_GET['id'])) {
                $producto_id = $_GET['id'];
                $usuario_id = $_SESSION['identity']->id;

                $favorito = new Favorito();
                $favorito->setProducto_id($producto_id);
                $favorito->setUsuario_id($usuario_id);

                // Guardar o borrar según corresponda
                $save = $favorito->save();
                
                // Redirigir a la misma página donde estaba el usuario
                if(isset($_SERVER['HTTP_REFERER'])){
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                } else {
                    header('Location:' . base_url);
                }
            }
        } else {
            header('Location:' . base_url . 'usuario/login');
        }
    }
}