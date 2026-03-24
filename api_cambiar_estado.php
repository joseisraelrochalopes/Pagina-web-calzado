<?php
// 1. BLINDAJE ANTI-BASURA
ob_start();

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

$json = file_get_contents("php://input");
$data = json_decode($json);

// Detectamos el ID ya sea que venga como 'id' o como 'orden_id'
$id_recibido = null;
if(isset($data->id)) { 
    $id_recibido = $data->id; 
} else if(isset($data->orden_id)) { 
    $id_recibido = $data->orden_id; 
}

$estado_recibido = isset($data->estado) ? $data->estado : null;

// 2. LIMPIEZA TOTAL
ob_clean();

if($id_recibido && $estado_recibido) {
    $id = (int)$id_recibido;
    $st = $db->real_escape_string($estado_recibido);
    
    $sql = "UPDATE ordenes SET estado = '$st' WHERE id = $id";
    
    if($db->query($sql)){
        echo json_encode([
            'status' => 'success', 
            'message' => "Estado actualizado correctamente"
        ]);
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Error SQL: ' . $db->error
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Faltan datos (ID o Estado)'
    ]);
}