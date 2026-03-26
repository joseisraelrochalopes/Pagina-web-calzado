<?php
require_once 'models/Categoria.php';
require_once 'models/Producto.php';

class CategoriaController {
    
    public function index(){
        Utils::isAdmin();
        $categoria = new Categoria();
        $categorias = $categoria->getAll();
        require_once 'views/categoria/index.php';
    }

    public function ver(){
        $categoria = null;
        $productos = null;

        if(isset($_GET['id'])){
            $id = $_GET['id'];
            
            $cat = new Categoria();
            $cat->setId($id);
            $categoria = $cat->getOne(); 
            
            if($categoria){
                $producto = new Producto();
                $producto->setCategoria_id($id);
                $productos = $producto->getAllCategory(); 
            }
        }
        
        require_once 'views/categoria/ver.php';
    }

    public function crear(){
        Utils::isAdmin();
        require_once 'views/categoria/crear.php';
    }

    public function save(){
        Utils::isAdmin();
        if(isset($_POST) && isset($_POST['nombre'])){
            $categoria = new Categoria();
            $categoria->setNombre($_POST['nombre']);
            
            if(isset($_FILES['imagen']) && !empty($_FILES['imagen']['name'])){
                $file = $_FILES['imagen'];
                $filename = $file['name'];
                $mimetype = $file['type'];

                if($mimetype == "image/jpg" || $mimetype == 'image/jpeg' || $mimetype == 'image/png' || $mimetype == 'image/gif'){
                    if(!is_dir('uploads')){ mkdir('uploads', 0777, true); }
                    move_uploaded_file($file['tmp_name'], 'uploads/'.$filename);
                    $categoria->setImagen($filename);
                }
            }

            if(isset($_GET['id'])){
                $categoria->setId($_GET['id']);
                $save = $categoria->update(); 
            } else {
                $save = $categoria->save(); 
            }
        }
        header("Location:".base_url."categoria/index");
    }

    public function editar(){
        Utils::isAdmin();
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $edit = true; 
            
            $categoria = new Categoria();
            $categoria->setId($id);
            $cat = $categoria->getOne(); 
            
            require_once 'views/categoria/crear.php'; 
        }else{
            header("Location:".base_url."categoria/index");
        }
    }

    // 🔥 ACTUALIZADO PARA CREAR EL MENSAJE DE ERROR SI FALLA 🔥
    public function eliminar(){
        Utils::isAdmin();
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $categoria = new Categoria();
            $categoria->setId($id);
            $delete = $categoria->delete(); 
            
            if($delete){
                $_SESSION['delete'] = 'complete';
            }else{
                $_SESSION['delete'] = 'failed_fk'; // El error de la llave foránea
            }
        }
        header("Location:".base_url."categoria/index");
    }
}