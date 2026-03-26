<?php

class Usuario {
    private $id;
    private $nombre;
    private $apellidos;
    private $email;
    private $password;
    private $rol;
    private $imagen;
    private $telefono; // 🔥 Nuevo
    private $google_id; // 🔥 Nuevo
    private $token_reset;
    private $fecha_token;
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // --- GETTERS ---
    function getId() { return $this->id; }
    function getNombre() { return $this->nombre; }
    function getApellidos() { return $this->apellidos; }
    function getEmail() { return $this->email; }
    function getPassword() { return $this->password; }
    function getRol() { return $this->rol; }
    function getImagen() { return $this->imagen; }
    function getTelefono() { return $this->telefono; }
    function getGoogleId() { return $this->google_id; }
    function getTokenReset() { return $this->token_reset; }

    // --- SETTERS ---
    function setId($id) { $this->id = $id; }
    function setNombre($nombre) { $this->nombre = $nombre; }
    function setApellidos($apellidos) { $this->apellidos = $apellidos; }
    function setEmail($email) { $this->email = $email; }
    function setPassword($password) { $this->password = $password; }
    function setRol($rol) { $this->rol = $rol; }
    function setImagen($imagen) { $this->imagen = $imagen; }
    function setTelefono($telefono) { $this->telefono = $telefono; }
    function setGoogleId($google_id) { $this->google_id = $google_id; }
    function setTokenReset($token_reset) { $this->token_reset = $token_reset; }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if($result && $result->num_rows == 1) {
            return $result->fetch_object();
        }
        return false;
    }

    public function save(){
        // Insertamos con el campo teléfono incluido
        $sql = "INSERT INTO usuarios (nombre, apellidos, email, password, telefono, rol) VALUES(?, ?, ?, ?, ?, 'user')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssss", $this->nombre, $this->apellidos, $this->email, $this->password, $this->telefono);
        
        $save = $stmt->execute();
        $stmt->close();
        return $save;
    }

    public function login($email, $password) {
        $result = false;
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $login = $stmt->get_result();
        
        if($login && $login->num_rows == 1) {
            $usuario = $login->fetch_object();
            $verify = false;
            if(password_verify($password, $usuario->password)) {
                $verify = true;
            } else if($password == $usuario->password) {
                $verify = true;
            }

            if($verify) {
                $result = $usuario;
            }
        }
        $stmt->close();
        return $result;
    }

    public function update(){
        $nombre = $this->db->real_escape_string($this->nombre);
        $apellidos = $this->db->real_escape_string($this->apellidos);
        $email = $this->db->real_escape_string($this->email);
        $telefono = $this->db->real_escape_string($this->telefono);
        
        $sql = "UPDATE usuarios SET nombre='{$nombre}', apellidos='{$apellidos}', email='{$email}', telefono='{$telefono}' ";
        
        if($this->password != null){ 
            $pw = $this->db->real_escape_string($this->password);
            $sql .= ", password='{$pw}'"; 
        }
        if($this->imagen != null){ 
            $img = $this->db->real_escape_string($this->imagen);
            $sql .= ", imagen='{$img}'"; 
        }
        
        $sql .= " WHERE id={$this->id};";
        $save = $this->db->query($sql);
        return $save ? true : false;
    }

    public function saveToken(){
        $sql = "UPDATE usuarios SET token_reset=?, fecha_token=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $this->token_reset, $this->email);
        $save = $stmt->execute();
        $stmt->close();
        return $save;
    }

    public function getByToken(){
        $sql = "SELECT * FROM usuarios WHERE token_reset=? AND fecha_token > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $this->token_reset);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if($result && $result->num_rows == 1){ return $result->fetch_object(); }
        return false;
    }

    public function updatePasswordByToken(){
        $sql = "UPDATE usuarios SET password=?, token_reset=NULL, fecha_token=NULL WHERE token_reset=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $this->password, $this->token_reset);
        $update = $stmt->execute();
        $stmt->close();
        return $update;
    }

    public function getAll(){
        $sql = "SELECT * FROM usuarios ORDER BY id DESC";
        return $this->db->query($sql);
    }

    public function getOne() {
        $sql = "SELECT * FROM usuarios WHERE id = {$this->id}";
        $usuario = $this->db->query($sql);
        return $usuario->fetch_object();
    }

    public function updateRol(){
        $sql = "UPDATE usuarios SET rol=? WHERE id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $this->rol, $this->id);
        $save = $stmt->execute();
        $stmt->close();
        return $save;
    }
}