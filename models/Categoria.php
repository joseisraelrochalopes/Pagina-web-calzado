<?php
class Categoria {
    public $id;
    public $nombre;
    public $imagen; // NUEVA PROPIEDAD
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // Getters y Setters
    function getId() { return $this->id; }
    function getNombre() { return $this->nombre; }
    function getImagen() { return $this->imagen; } // NUEVO GETTER

    function setId($id) { $this->id = $id; }
    function setNombre($nombre) { $this->nombre = $this->db->real_escape_string($nombre); }
    function setImagen($imagen) { $this->imagen = $imagen; } // NUEVO SETTER

    // Listar todas las categorías
    public function getAll() {
        $categorias = $this->db->query("SELECT * FROM categorias ORDER BY id ASC");
        return $categorias;
    }

    // Obtener una categoría específica
    public function getOne(){
        $sql = "SELECT * FROM categorias WHERE id={$this->id}";
        $categoria = $this->db->query($sql);
        return $categoria->fetch_object();
    }

    // Guardar categoría (Modificado para incluir imagen)
    public function save(){
        // Usamos la propiedad imagen en el SQL
        $sql = "INSERT INTO categorias VALUES(NULL, '{$this->nombre}', '{$this->imagen}');";
        $save = $this->db->query($sql);
        return $save ? true : false;
    }
    
    // Editar categoría (Modificado por si cambias el nombre/imagen)
    public function edit(){
        $sql = "UPDATE categorias SET nombre='{$this->nombre}'";
        
        // Solo actualizamos la imagen si se envió una nueva
        if($this->imagen != null){
            $sql .= ", imagen='{$this->imagen}'";
        }
        
        $sql .= " WHERE id={$this->id}";
        
        $save = $this->db->query($sql);
        return $save ? true : false;
    }
}