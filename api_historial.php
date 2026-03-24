<?php
// 1. BLINDAJE
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Content-Type: application/json; charset=UTF-8");

require_once 'config/db.php';
$db = Database::connect();

$usuario_id = isset($_GET['usuario_id']) ? $_GET['usuario_id'] : null;

// 2. LIMPIAR BASURA
ob_clean();

if($usuario_id){
    // A. BUSCAMOS LAS ÓRDENES (Ocultando las del Admin)
    $sql = "SELECT id, fecha, total, estado, metodo_pago 
            FROM ordenes 
            WHERE usuario_id = $usuario_id 
            AND (direccion IS NULL OR direccion NOT LIKE '%Admin%')
            ORDER BY fecha DESC";

    $result = $db->query($sql);

    $historial = [];

    if($result){
        while($row = $result->fetch_assoc()) {
            $orden_id = $row['id'];
            
            // B. BUSCAR PRODUCTOS (CORREGIDO: 'orden_detalle' sin 's')
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
            $historial[] = $row;
        }
    }

    echo json_encode(['status' => 'success', 'historial' => $historial]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Falta ID de usuario']);
}
?>