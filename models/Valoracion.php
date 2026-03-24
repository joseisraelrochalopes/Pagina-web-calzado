<?php
class Valoracion {
    private $id;
    private $usuario_id;
    private $producto_id;
    private $nota;
    private $comentario;
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    function setUsuario_id($usuario_id) { $this->usuario_id = $usuario_id; }
    function setProducto_id($producto_id) { $this->producto_id = $producto_id; }
    function setNota($nota) { $this->nota = $nota; }
    function setComentario($comentario) { $this->comentario = $this->db->real_escape_string($comentario); }

    // Guardar nueva valoración
    public function save(){
        $sql = "INSERT INTO valoraciones VALUES(NULL, {$this->usuario_id}, {$this->producto_id}, {$this->nota}, '{$this->comentario}', CURDATE())";
        $save = $this->db->query($sql);
        return $save ? true : false;
    }

    // Obtener todas las valoraciones de un producto
    public function getByProducto(){
        $sql = "SELECT v.*, u.nombre, u.imagen FROM valoraciones v "
             . "INNER JOIN usuarios u ON v.usuario_id = u.id "
             . "WHERE v.producto_id = {$this->producto_id} ORDER BY v.id DESC";
        return $this->db->query($sql);
    }

    // Obtener la nota media (Promedio)
    public function getMedia(){
        $sql = "SELECT AVG(nota) as 'media', COUNT(id) as 'total' FROM valoraciones WHERE producto_id = {$this->producto_id}";
        $stats = $this->db->query($sql);
        return $stats->fetch_object();
    }
}