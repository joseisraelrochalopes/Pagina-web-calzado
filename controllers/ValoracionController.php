<?php
require_once 'models/Valoracion.php';

class ValoracionController {
    
    public function save(){
        if(isset($_SESSION['identity'])){
            if(isset($_POST['producto_id']) && isset($_POST['nota'])){
                
                $usuario_id = $_SESSION['identity']->id;
                $producto_id = $_POST['producto_id'];
                $nota = $_POST['nota'];
                $comentario = isset($_POST['comentario']) ? $_POST['comentario'] : '';

                $valoracion = new Valoracion();
                $valoracion->setUsuario_id($usuario_id);
                $valoracion->setProducto_id($producto_id);
                $valoracion->setNota($nota);
                $valoracion->setComentario($comentario);

                $valoracion->save();
                
                // Redirigir de vuelta al producto
                header("Location:".base_url."producto/ver?id=".$producto_id);
            }else{
                header("Location:".base_url);
            }
        }else{
            // Si no está logueado, al login
            header("Location:".base_url."usuario/login");
        }
    }
}