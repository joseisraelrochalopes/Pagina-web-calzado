<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config/db.php';
$db = Database::connect();

$post = json_decode(file_get_contents("php://input"));

if(isset($post->usuario_id) && isset($post->producto_id)) {
    
    $usuario_id = (int)$post->usuario_id;
    $producto_id = (int)$post->producto_id;
    
    // Atrapamos la talla y la cantidad enviada desde la App (con valores por defecto por si acaso)
    $talla = isset($post->talla) && !empty($post->talla) ? $db->real_escape_string($post->talla) : 'Única';
    $cantidad_pedida = isset($post->cantidad) ? (int)$post->cantidad : 1;

    // 1. Verificamos si ya lo tenía en el carrito (Validando ID Y TALLA)
    $stmt_check = $db->prepare("SELECT id, cantidad FROM carrito WHERE usuario_id = ? AND producto_id = ? AND talla = ?");
    $stmt_check->bind_param("iis", $usuario_id, $producto_id, $talla);
    $stmt_check->execute();
    $check = $stmt_check->get_result();

    if($check->num_rows > 0){
        // Si ya existe ESE zapato en ESA talla, le sumamos la cantidad nueva
        $fila = $check->fetch_assoc();
        $nueva_cantidad = $fila['cantidad'] + $cantidad_pedida;
        
        $stmt_upd = $db->prepare("UPDATE carrito SET cantidad = ? WHERE id = ?");
        $stmt_upd->bind_param("ii", $nueva_cantidad, $fila['id']);
        $resultado = $stmt_upd->execute();
    } else {
        // Si no existe, lo insertamos con la talla y la cantidad especificada
        $stmt_ins = $db->prepare("INSERT INTO carrito (usuario_id, producto_id, cantidad, talla) VALUES (?, ?, ?, ?)");
        $stmt_ins->bind_param("iiis", $usuario_id, $producto_id, $cantidad_pedida, $talla);
        $resultado = $stmt_ins->execute();
    }

    if($resultado){
        echo json_encode(['status' => 'success', 'message' => 'Producto agregado con éxito']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al guardar en el carrito']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos de usuario o producto']);
}
?>