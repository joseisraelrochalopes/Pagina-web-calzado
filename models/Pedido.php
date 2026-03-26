<?php
class Pedido {
    private $id;
    private $usuario_id;
    private $provincia;
    private $localidad;
    private $direccion;
    private $coste;
    private $coste_envio;
    private $metodo_pago;
    private $estado;
    private $fecha;
    private $hora;
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // SETTERS
    function setId($id) { $this->id = $id; }
    function setUsuario_id($usuario_id) { $this->usuario_id = $usuario_id; }
    function setProvincia($provincia) { $this->provincia = $this->db->real_escape_string($provincia); }
    function setLocalidad($localidad) { $this->localidad = $this->db->real_escape_string($localidad); }
    function setDireccion($direccion) { $this->direccion = $this->db->real_escape_string($direccion); }
    function setCoste($coste) { $this->coste = $coste; }
    function setCosteEnvio($coste_envio) { $this->coste_envio = $coste_envio; }
    function setMetodoPago($metodo_pago) { $this->metodo_pago = $metodo_pago; }
    function setEstado($estado) { $this->estado = $estado; }
    function setFecha($fecha) { $this->fecha = $fecha; }
    function setHora($hora) { $this->hora = $hora; }
    
    // GETTERS
    function getId() { return $this->id; }
    function getUsuario_id() { return $this->usuario_id; }
    function getProvincia() { return $this->provincia; }
    function getLocalidad() { return $this->localidad; }
    function getDireccion() { return $this->direccion; }
    function getCoste() { return $this->coste; }
    function getCosteEnvio() { return $this->coste_envio; }
    function getMetodoPago() { return $this->metodo_pago; }
    function getEstado() { return $this->estado; }
    function getFecha() { return $this->fecha; }
    function getHora() { return $this->hora; }

    public function save(){
        $sql = "INSERT INTO pedidos VALUES(NULL, {$this->usuario_id}, '{$this->provincia}', '{$this->localidad}', '{$this->direccion}', {$this->coste}, {$this->coste_envio}, '{$this->metodo_pago}', 'confirm', CURDATE(), CURTIME());";
        $save = $this->db->query($sql);
        return $save ? true : false;
    }

    public function save_linea(){
        $sql = "SELECT LAST_INSERT_ID() as 'pedido';";
        $query = $this->db->query($sql);
        $pedido_id = $query->fetch_object()->pedido;
        
        foreach($_SESSION['carrito'] as $elemento){
            $producto = $elemento['producto'];
            $talla = $elemento['talla'];
            $unidades = $elemento['unidades'];
            
            $insert = "INSERT INTO lineas_pedidos VALUES(NULL, {$pedido_id}, {$producto->id}, {$unidades}, '{$talla}')";
            $this->db->query($insert);
            
            $sql_talla = "UPDATE productos_tallas SET stock = stock - {$unidades} WHERE producto_id={$producto->id} AND talla='{$talla}'";
            $this->db->query($sql_talla);

            $sql_global = "UPDATE productos SET stock = stock - {$unidades} WHERE id={$producto->id}";
            $this->db->query($sql_global);
        }
        return true;
    }

    public function restaurarStock(){
        $productos = $this->getProductosByPedido($this->id);
        while($pro = $productos->fetch_object()){
            $talla = $pro->talla;
            $unidades = $pro->unidades;
            
            $sql_talla = "UPDATE productos_tallas SET stock = stock + {$unidades} WHERE producto_id={$pro->id} AND talla='{$talla}'";
            $this->db->query($sql_talla);

            $sql_global = "UPDATE productos SET stock = stock + {$unidades} WHERE id={$pro->id}";
            $this->db->query($sql_global);
        }
    }

    public function getOneByUser(){
        $sql = "SELECT * FROM pedidos WHERE usuario_id = {$this->usuario_id} ORDER BY id DESC LIMIT 1";
        $pedido = $this->db->query($sql);
        return $pedido->fetch_object();
    }

    public function getProductosByPedido($id){
        $sql = "SELECT pr.*, lp.unidades, lp.talla FROM productos pr "
             . "INNER JOIN lineas_pedidos lp ON pr.id = lp.producto_id "
             . "WHERE lp.pedido_id={$id}";
        $productos = $this->db->query($sql);
        return $productos;
    }

    public function getAllByUser(){
        $sql = "SELECT * FROM pedidos WHERE usuario_id = {$this->usuario_id} ORDER BY id DESC";
        $pedidos = $this->db->query($sql);
        return $pedidos;
    }

    public function getOne(){
        $sql = "SELECT * FROM pedidos WHERE id = {$this->id}";
        $pedido = $this->db->query($sql);
        return $pedido->fetch_object();
    }

    public function getAll(){
        $sql = "SELECT * FROM pedidos ORDER BY id DESC";
        $pedidos = $this->db->query($sql);
        return $pedidos;
    }

    // 🔥 NUEVA FUNCIÓN MAGICA: SOLO TRAE LOS PEDIDOS ACTIVOS 🔥
    public function getActivosAdmin(){
        // Oculta los que ya están entregados o cancelados
        $sql = "SELECT * FROM pedidos WHERE estado NOT IN ('delivered', 'cancelled') ORDER BY id DESC";
        return $this->db->query($sql);
    }

    public function getById($id){
        $id = $this->db->real_escape_string($id);
        $sql = "SELECT * FROM pedidos WHERE id = {$id}";
        return $this->db->query($sql);
    }

    public function getByStatus($status){
        $status = $this->db->real_escape_string($status);
        $sql = "SELECT * FROM pedidos WHERE estado = '{$status}' ORDER BY id DESC";
        return $this->db->query($sql);
    }

    public function edit(){
        $sql = "UPDATE pedidos SET estado='{$this->estado}' WHERE id={$this->id}";
        $save = $this->db->query($sql);
        return $save ? true : false;
    }

    public function getTotalVentas(){
        $sql = "SELECT SUM(coste) as 'total' FROM pedidos";
        $result = $this->db->query($sql);
        return $result->fetch_object()->total;
    }

    public function getCountPedidos(){
        $sql = "SELECT COUNT(id) as 'total' FROM pedidos";
        $result = $this->db->query($sql);
        return $result->fetch_object()->total;
    }

    public function getVentasUltimaSemana(){
        $sql = "SELECT fecha, SUM(coste) as total 
                FROM pedidos 
                WHERE fecha >= DATE(NOW()) - INTERVAL 7 DAY 
                GROUP BY fecha 
                ORDER BY fecha ASC";
        return $this->db->query($sql);
    }

    public function cancelar(){
        $sql = "UPDATE pedidos SET estado='cancelled' WHERE id={$this->id}";
        $save = $this->db->query($sql);
        if($save){
            $this->restaurarStock();
        }
        return $save ? true : false;
    }

    public function getBestSellers(){
        $sql = "SELECT p.nombre, p.imagen, SUM(lp.unidades) as total_vendido 
                FROM lineas_pedidos lp 
                INNER JOIN productos p ON lp.producto_id = p.id 
                GROUP BY p.id 
                ORDER BY total_vendido DESC 
                LIMIT 5";
        return $this->db->query($sql);
    }
}