<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');

require_once 'config/db.php';
$db = Database::connect();

$post = json_decode(file_get_contents("php://input"));

if(isset($post->id_carrito)) {
    
    $id = $post->id_carrito;
    
    // Borramos solo esa línea del carrito
    $sql = "DELETE FROM carrito WHERE id = $id";

    if($db->query($sql)){
        echo json_encode(['status' => 'success', 'message' => 'Eliminado']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Falta ID']);
}
?>