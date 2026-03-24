<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');

require_once 'config/db.php';
$db = Database::connect();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$producto = null;
$tallas = [];

if($id != null){
    // Obtenemos los datos base del producto
    $sql = "SELECT * FROM productos WHERE id = $id";
    $query = $db->query($sql);
    
    if($query && $row = $query->fetch_assoc()){
        $producto = $row;
        
        // Buscamos las tallas disponibles para este producto
        $sql_tallas = "SELECT talla, stock, precio FROM productos_tallas WHERE producto_id = $id ORDER BY talla ASC";
        $query_tallas = $db->query($sql_tallas);
        
        if($query_tallas) {
            while($t = $query_tallas->fetch_assoc()) {
                $tallas[] = [
                    'talla' => $t['talla'],
                    'stock' => (int)$t['stock'],
                    'precio' => (float)$t['precio']
                ];
            }
        }
    }
}

if($producto){
    // Enviamos el producto Y la lista de tallas
    echo json_encode(['status' => 'success', 'producto' => $producto, 'tallas' => $tallas]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Producto no encontrado']);
}
?>