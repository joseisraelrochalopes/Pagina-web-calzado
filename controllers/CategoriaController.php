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
        // Inicializamos las variables como null para evitar el error de "Undefined variable"
        $categoria = null;
        $productos = null;

        if(isset($_GET['id'])){
            $id = $_GET['id'];
            
            // 1. Conseguir la categoría
            $cat = new Categoria();
            $cat->setId($id);
            $categoria = $cat->getOne(); // Esto devuelve el objeto de la categoría
            
            if($categoria){
                // 2. Conseguir los productos de esa categoría
                $producto = new Producto();
                $producto->setCategoria_id($id);
                $productos = $producto->getAllCategory(); // Esto devuelve el resultado de la DB
            }
        }
        
        // Cargamos la vista. Ahora $categoria y $productos existen (aunque sean null)
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
            $save = $categoria->save();
        }
        header("Location:".base_url."categoria/index");
    }
}