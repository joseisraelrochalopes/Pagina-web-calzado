<?php
class Cupon {
    private $id;
    private $codigo;
    private $porcentaje;
    private $estado;
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // Setters y Getters
    function setId($id) { $this->id = $id; }
    function setCodigo($codigo) { $this->codigo = $this->db->real_escape_string(strtoupper($codigo)); } // Siempre mayúsculas
    function setPorcentaje($porcentaje) { $this->porcentaje = (int)$porcentaje; }
    function setEstado($estado) { $this->estado = $estado; }

    function getId() { return $this->id; }
    function getCodigo() { return $this->codigo; }
    function getPorcentaje() { return $this->porcentaje; }
    function getEstado() { return $this->estado; }

    // --- MÉTODOS CRUD ---

    // 1. Listar todos (Para el Admin)
    public function getAll(){
        $sql = "SELECT * FROM cupones ORDER BY id DESC";
        return $this->db->query($sql);
    }

    // 2. Guardar nuevo cupón
    public function save(){
        $sql = "INSERT INTO cupones VALUES(NULL, '{$this->codigo}', {$this->porcentaje}, 'activo')";
        $save = $this->db->query($sql);
        return $save ? true : false;
    }

    // 3. Eliminar cupón
    public function delete(){
        $sql = "DELETE FROM cupones WHERE id={$this->id}";
        $delete = $this->db->query($sql);
        return $delete ? true : false;
    }

    // 4. Buscar para aplicar en el carrito
    public function getByName(){
        $sql = "SELECT * FROM cupones WHERE codigo = '{$this->codigo}' AND estado = 'activo'";
        $cupon = $this->db->query($sql);
        
        if($cupon && $cupon->num_rows == 1){
            return $cupon->fetch_object();
        }
        return false;
    }
}