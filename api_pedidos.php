<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once 'config/db.php';
$db = Database::connect();

$usuario_id = isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : null;

if($usuario_id) {
    // 1. Buscamos las órdenes
    $stmt = $db->prepare("SELECT id, total, fecha, estado, metodo_pago FROM ordenes WHERE usuario_id = ? ORDER BY id DESC");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $pedidos = [];
    while($fila = $resultado->fetch_assoc()){
        $orden_id = $fila['id'];

        // 2. Buscamos los productos de ESTA orden específica
        $sql_detalles = "SELECT p.nombre, d.cantidad, d.precio, p.imagen 
                         FROM orden_detalle d 
                         JOIN productos p ON d.producto_id = p.id 
                         WHERE d.orden_id = $orden_id";
        
        $res_detalles = $db->query($sql_detalles);
        $productos = [];
        
        if($res_detalles){
            while($prod = $res_detalles->fetch_assoc()){
                $productos[] = $prod;
            }
        }

        // Agregamos la lista de productos dentro del pedido
        $fila['productos'] = $productos;
        $pedidos[] = $fila;
    }

    echo json_encode(['status' => 'success', 'pedidos' => $pedidos]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID de usuario no proporcionado']);
}
?>