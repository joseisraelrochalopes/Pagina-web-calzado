<?php
class Favorito {
    private $id;
    private $usuario_id;
    private $producto_id;
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    function setId($id) { $this->id = $id; }
    function setUsuario_id($usuario_id) { $this->usuario_id = $usuario_id; }
    function setProducto_id($producto_id) { $this->producto_id = $producto_id; }

    // Obtener todos los favoritos de un usuario (con datos del producto)
    public function getAllByUser() {
        $sql = "SELECT f.id as 'favorito_id', p.* FROM favoritos f "
             . "INNER JOIN productos p ON f.producto_id = p.id "
             . "WHERE f.usuario_id = {$this->usuario_id} "
             . "ORDER BY f.id DESC";
        $favoritos = $this->db->query($sql);
        return $favoritos;
    }

    // Lógica de "Toggle" (Me gusta / Ya no me gusta)
    public function save() {
        // 1. Comprobar si ya existe
        $sql_check = "SELECT * FROM favoritos WHERE usuario_id = {$this->usuario_id} AND producto_id = {$this->producto_id}";
        $exists = $this->db->query($sql_check);

        if ($exists && $exists->num_rows > 0) {
            // SI EXISTE -> LO BORRAMOS (Dislike)
            $sql = "DELETE FROM favoritos WHERE usuario_id = {$this->usuario_id} AND producto_id = {$this->producto_id}";
            $this->db->query($sql);
            return "removed";
        } else {
            // NO EXISTE -> LO GUARDAMOS (Like)
            // CAMBIO: Especificamos las columnas y añadimos CURDATE() para la fecha
            $sql = "INSERT INTO favoritos (usuario_id, producto_id, fecha) 
                    VALUES({$this->usuario_id}, {$this->producto_id}, CURDATE())";
            
            $save = $this->db->query($sql);
            return $save ? "added" : "failed";
        }
    }
}