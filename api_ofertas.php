<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');

require_once 'config/db.php';
$db = Database::connect();

// ✅ Ahora traemos todos los que tengan 'SI'
$sql = "SELECT id, nombre, precio, imagen FROM productos WHERE oferta = 'SI' ORDER BY id DESC";
$result = $db->query($sql);

$ofertas = [];
if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $ofertas[] = $row;
    }
}

echo json_encode(['status' => 'success', 'ofertas' => $ofertas]);
?>