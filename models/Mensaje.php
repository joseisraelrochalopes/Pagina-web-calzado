<?php
class Mensaje {
    private $id;
    private $nombre;
    private $email;
    private $asunto;
    private $mensaje;
    private $fecha;
    private $estado;
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // Setters
    function setId($id) { $this->id = $id; }
    function setNombre($nombre) { $this->nombre = $this->db->real_escape_string($nombre); }
    function setEmail($email) { $this->email = $this->db->real_escape_string($email); }
    function setAsunto($asunto) { $this->asunto = $this->db->real_escape_string($asunto); }
    function setMensaje($mensaje) { $this->mensaje = $this->db->real_escape_string($mensaje); }
    function setEstado($estado) { $this->estado = $estado; }

    // Getters
    function getId() { return $this->id; }
    function getNombre() { return $this->nombre; }
    function getEmail() { return $this->email; }
    function getAsunto() { return $this->asunto; }
    function getMensaje() { return $this->mensaje; }
    function getFecha() { return $this->fecha; }
    function getEstado() { return $this->estado; }

    // Guardar mensaje nuevo (Cliente)
    public function save(){
        $sql = "INSERT INTO mensajes VALUES(NULL, '{$this->nombre}', '{$this->email}', '{$this->asunto}', '{$this->mensaje}', CURDATE(), 'pendiente');";
        $save = $this->db->query($sql);
        return $save ? true : false;
    }

    // Obtener todos los mensajes (Admin)
    public function getAll(){
        $sql = "SELECT * FROM mensajes ORDER BY id DESC";
        return $this->db->query($sql);
    }

    // Marcar como leído (Admin)
    public function markAsRead(){
        $sql = "UPDATE mensajes SET estado='leido' WHERE id={$this->id}";
        return $this->db->query($sql);
    }
    
    // Contar no leídos (Para notificaciones)
    public function getUnreadCount(){
        $sql = "SELECT COUNT(id) as 'total' FROM mensajes WHERE estado='pendiente'";
        $result = $this->db->query($sql);
        return $result->fetch_object()->total;
    }
}