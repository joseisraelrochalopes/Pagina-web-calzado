<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config/db.php';
$db = Database::connect();

ob_clean(); 

$sql = "SELECT o.id, o.fecha, o.total, o.estado, o.metodo_pago, o.direccion, u.nombre as cliente 
        FROM ordenes o 
        LEFT JOIN usuarios u ON o.usuario_id = u.id 
        ORDER BY o.fecha DESC";

$result = $db->query($sql);
$pedidos = [];

if($result){
    while($row = $result->fetch_assoc()) {
        $orden_id = $row['id'];
        
        // Etiqueta de tipo de venta
        $row['tipo_venta'] = (strpos($row['direccion'], 'Admin') !== false) ? 'Física / Apartado 🏪' : 'Online 🌐';

        // Buscar detalles
        $sql_detalles = "SELECT p.nombre, d.cantidad, d.precio, p.imagen 
                         FROM orden_detalle d 
                         JOIN productos p ON d.producto_id = p.id 
                         WHERE d.orden_id = $orden_id";
                         
        $res_detalles = $db->query($sql_detalles);
        $lista_productos = [];
        if($res_detalles){
            while($prod = $res_detalles->fetch_assoc()){
                $lista_productos[] = $prod;
            }
        }
        
        $row['productos'] = $lista_productos;
        $pedidos[] = $row;
    }
}

echo json_encode(['status' => 'success', 'pedidos' => $pedidos]);
?>