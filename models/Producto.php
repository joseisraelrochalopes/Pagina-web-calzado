<?php
class Producto {
    private $id;
    private $categoria_id;
    private $nombre;
    private $descripcion;
    private $precio;
    private $stock;
    private $oferta;
    private $fecha;
    private $imagen;
    private $activo;
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // Setters
    function setId($id) { $this->id = $id; }
    function setCategoria_id($categoria_id) { $this->categoria_id = $categoria_id; }
    function setNombre($nombre) { $this->nombre = $this->db->real_escape_string($nombre); }
    function setDescripcion($descripcion) { $this->descripcion = $this->db->real_escape_string($descripcion); }
    function setPrecio($precio) { $this->precio = $this->db->real_escape_string($precio); }
    function setStock($stock) { $this->stock = $this->db->real_escape_string($stock); }
    function setOferta($oferta) { $this->oferta = $this->db->real_escape_string($oferta); }
    function setFecha($fecha) { $this->fecha = $fecha; }
    function setImagen($imagen) { $this->imagen = $imagen; }
    function setActivo($activo) { $this->activo = $activo; }

    // Getters
    function getId() { return $this->id; }
    function getNombre() { return $this->nombre; }
    function getPrecio() { return $this->precio; }
    function getStock() { return $this->stock; }
    function getCategoria_id() { return $this->categoria_id; }
    function getDescripcion() { return $this->descripcion; }
    function getImagen() { return $this->imagen; }
    function getActivo() { return $this->activo; }
    function getOferta() { return $this->oferta; }

    // --- MÉTODOS CRUD BÁSICOS ---

    public function getAll() {
        return $this->db->query("SELECT * FROM productos ORDER BY id DESC");
    }

    public function getOne(){
        $sql = "SELECT * FROM productos WHERE id={$this->id}";
        return $this->db->query($sql)->fetch_object();
    }

    public function getByIds($ids_array){
        $ids_string = implode(",", $ids_array);
        if(empty($ids_string)) return false;
        $sql = "SELECT * FROM productos WHERE id IN ($ids_string) AND activo = 1";
        return $this->db->query($sql);
    }

    // --- GESTIÓN DE TALLAS Y PRECIOS ---

    // Guardar stock Y PRECIO de una talla
    public function saveStockTalla($talla, $cantidad, $precio_talla){
        $talla = $this->db->real_escape_string($talla);
        $check = $this->db->query("SELECT id FROM productos_tallas WHERE producto_id={$this->id} AND talla='$talla'");
        
        if($check && $check->num_rows > 0){
            $sql = "UPDATE productos_tallas SET stock={$cantidad}, precio={$precio_talla} WHERE producto_id={$this->id} AND talla='$talla'";
        } else {
            $sql = "INSERT INTO productos_tallas VALUES(NULL, {$this->id}, '$talla', {$cantidad}, {$precio_talla})";
        }
        $this->db->query($sql);
    }

    // Obtener tallas, stocks Y PRECIOS
    public function getStocksTallas(){
        $sql = "SELECT talla, stock, precio FROM productos_tallas WHERE producto_id={$this->id}";
        $result = $this->db->query($sql);
        
        $data = [];
        if($result){
            while($row = $result->fetch_object()){
                $data[$row->talla] = [
                    'stock' => $row->stock,
                    'precio' => $row->precio
                ];
            }
        }
        return $data; 
    }

    // --- FUNCIÓN QUE SOLUCIONA EL ERROR DEL CARRITO ---
    public function getStockByTalla($talla) {
        $talla = $this->db->real_escape_string($talla);
        $sql = "SELECT stock FROM productos_tallas WHERE producto_id = {$this->id} AND talla = '{$talla}'";
        $result = $this->db->query($sql);
        
        if($result && $result->num_rows > 0){
            $res = $result->fetch_object();
            return (int)$res->stock;
        }
        return 0;
    }

    // Obtener datos específicos de una talla (Para el detalle del producto)
    public function getInfoByTalla($talla){
        $talla = $this->db->real_escape_string($talla);
        $sql = "SELECT stock, precio FROM productos_tallas WHERE producto_id={$this->id} AND talla='{$talla}'";
        $result = $this->db->query($sql);
        
        if($result && $result->num_rows > 0){
            return $result->fetch_object();
        } 
        return false;
    }

    public function updateStockTotal(){
        $sql = "UPDATE productos SET stock = (SELECT SUM(stock) FROM productos_tallas WHERE producto_id={$this->id}) WHERE id={$this->id}";
        $this->db->query($sql);
    }

    // --- GESTIÓN DE GALERÍA ---
    
    public function saveImage($filename){
        $sql = "INSERT INTO imagenes VALUES(NULL, {$this->id}, '$filename')";
        $this->db->query($sql);
    }

    public function getImages(){
        $sql = "SELECT * FROM imagenes WHERE producto_id={$this->id}";
        return $this->db->query($sql);
    }

    public function deleteImageById($image_id){
        $sql = "DELETE FROM imagenes WHERE id=$image_id";
        return $this->db->query($sql);
    }

    public function getRandom($limit) {
        return $this->db->query("SELECT * FROM productos WHERE activo = 1 ORDER BY RAND() LIMIT $limit");
    }

    public function getOfertas($limit = 3){
        $sql = "SELECT * FROM productos WHERE oferta = 'SI' AND activo = 1 ORDER BY RAND() LIMIT $limit";
        return $this->db->query($sql);
    }

    public function getCount($min = null, $max = null){
        $where = " WHERE activo = 1 ";
        if($min != null && $min > 0) $where .= " AND precio >= $min ";
        if($max != null && $max > 0) $where .= " AND precio <= $max ";
        $sql = "SELECT COUNT(id) as 'total' FROM productos $where";
        $result = $this->db->query($sql);
        return $result->fetch_object()->total;
    }

    public function getPaginated($limit, $offset, $min = null, $max = null, $sort = 'new'){
        $where = " WHERE activo = 1 ";
        if($min != null && $min > 0) $where .= " AND precio >= $min ";
        if($max != null && $max > 0) $where .= " AND precio <= $max ";
        
        $orderBy = " ORDER BY id DESC ";
        if($sort == 'price_asc') $orderBy = " ORDER BY precio ASC ";
        elseif($sort == 'price_desc') $orderBy = " ORDER BY precio DESC ";
        elseif($sort == 'name_asc') $orderBy = " ORDER BY nombre ASC ";

        $sql = "SELECT * FROM productos $where $orderBy LIMIT $offset, $limit";
        return $this->db->query($sql);
    }

    public function getAllCategory(){
        $sql = "SELECT p.*, c.nombre AS 'catnombre' FROM productos p "
             . "INNER JOIN categorias c ON c.id = p.categoria_id "
             . "WHERE p.categoria_id = {$this->categoria_id} AND p.activo = 1 "
             . "ORDER BY p.id DESC";
        return $this->db->query($sql);
    }

    public function busqueda($texto){
        $texto = $this->db->real_escape_string($texto);
        $sql = "SELECT * FROM productos WHERE nombre LIKE '%$texto%' AND activo = 1 ORDER BY id DESC";
        return $this->db->query($sql);
    }
    
    public function getAdminFiltered($search = null, $categoria_id = null){
        $sql = "SELECT * FROM productos WHERE 1=1 ";
        if(!empty($search)){
            $search = $this->db->real_escape_string($search);
            $sql .= " AND nombre LIKE '%$search%' ";
        }
        if(!empty($categoria_id)){
            $categoria_id = (int)$categoria_id;
            $sql .= " AND categoria_id = $categoria_id ";
        }
        $sql .= " ORDER BY id DESC";
        return $this->db->query($sql);
    }

    public function save() {
        $sql = "INSERT INTO productos (categoria_id, nombre, descripcion, precio, stock, oferta, fecha, imagen, activo) 
                VALUES ({$this->categoria_id}, '{$this->nombre}', '{$this->descripcion}', {$this->precio}, 0, '{$this->oferta}', CURDATE(), '{$this->imagen}', 1);";
        $save = $this->db->query($sql);
        if($save){ return $this->db->insert_id; }
        return false;
    }

    public function edit(){
        $sql = "UPDATE productos SET nombre='{$this->nombre}', descripcion='{$this->descripcion}', precio={$this->precio}, categoria_id={$this->categoria_id}, oferta='{$this->oferta}'";
        if($this->imagen != null){ $sql .= ", imagen='{$this->imagen}'"; }
        $sql .= " WHERE id={$this->id};";
        return $this->db->query($sql);
    }

    public function toggleStatus(){
        $sql = "UPDATE productos SET activo = (CASE WHEN activo = 1 THEN 0 ELSE 1 END) WHERE id={$this->id}";
        return $this->db->query($sql);
    }

    public function getLowStock(){
        $sql = "SELECT * FROM productos WHERE stock < 5 AND activo = 1 ORDER BY stock ASC";
        return $this->db->query($sql);
    }

    // 🔥 NUEVA FUNCIÓN PARA BORRAR EL PRODUCTO CON PROTECCIÓN 🔥
    public function delete(){
        try {
            // Se limpian tablas secundarias para que borre limpio si no hay pedidos
            $this->db->query("DELETE FROM productos_tallas WHERE producto_id={$this->id}");
            $this->db->query("DELETE FROM imagenes WHERE producto_id={$this->id}");
            
            $sql = "DELETE FROM productos WHERE id={$this->id}";
            $delete = $this->db->query($sql);
            return $delete ? true : false;
        } catch (mysqli_sql_exception $e) {
            // Si el producto está asociado a un pedido (lineas_pedidos), fallará y caerá aquí.
            return false;
        }
    }
}