<?php
class Categoria {
    public $id;
    public $nombre;
    public $imagen; 
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // Getters y Setters
    function getId() { return $this->id; }
    function getNombre() { return $this->nombre; }
    function getImagen() { return $this->imagen; } 

    function setId($id) { $this->id = $id; }
    function setNombre($nombre) { $this->nombre = $this->db->real_escape_string($nombre); }
    function setImagen($imagen) { $this->imagen = $imagen; } 

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

    // Guardar categoría 
    public function save(){
        $sql = "INSERT INTO categorias VALUES(NULL, '{$this->nombre}', '{$this->imagen}');";
        $save = $this->db->query($sql);
        return $save ? true : false;
    }
    
    // Editar categoría 
    public function update(){
        $sql = "UPDATE categorias SET nombre='{$this->nombre}'";
        
        if($this->imagen != null){
            $sql .= ", imagen='{$this->imagen}'";
        }
        
        $sql .= " WHERE id={$this->id}";
        
        $save = $this->db->query($sql);
        return $save ? true : false;
    }

    // 🔥 ELIMINAR CON PROTECCIÓN CONTRA ERRORES FATALES 🔥
    public function delete(){
        try {
            $sql = "DELETE FROM categorias WHERE id={$this->id}";
            $delete = $this->db->query($sql);
            return $delete ? true : false;
        } catch (mysqli_sql_exception $e) {
            // Si salta el error de las llaves foráneas, devolvemos false para mostrar el mensaje
            return false;
        }
    }
}
?>