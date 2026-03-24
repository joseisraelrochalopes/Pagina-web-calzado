<?php
class Configuracion {
    private $id;
    private $nombre;
    private $valor;
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // --- MÉTODOS CRUD GENÉRICOS ---

    // Obtener TODA la configuración como un objeto simple
    // Ejemplo de uso: $config->nombre_empresa
    public function getAll() {
        $sql = "SELECT * FROM configuracion";
        $result = $this->db->query($sql);
        
        $settings = [];
        if($result){
            while($row = $result->fetch_object()){
                $settings[$row->nombre] = $row->valor;
            }
        }
        // Convertimos el array en objeto para usarlo como $config->moneda
        return (object)$settings;
    }

    // Obtener un valor específico
    public function getValue($key) {
        $sql = "SELECT valor FROM configuracion WHERE nombre = '$key'";
        $res = $this->db->query($sql);
        if($res && $res->num_rows > 0){
            return $res->fetch_object()->valor;
        }
        return null;
    }

    // Actualizar un valor específico
    public function setValue($key, $val) {
        $val = $this->db->real_escape_string($val);
        $sql = "UPDATE configuracion SET valor = '$val' WHERE nombre = '$key'";
        return $this->db->query($sql);
    }

    // Método legacy para mantener compatibilidad con el helper
    public function getMoneda() {
        return $this->getValue('moneda');
    }
}