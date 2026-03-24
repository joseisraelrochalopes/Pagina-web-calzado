<?php
// 1. PERMISOS (Vital para que el celular entre)
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json');

require_once 'config/db.php';
$db = Database::connect();

$id_categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;
$datos = [];

if($id_categoria != null){
    $sql = "SELECT * FROM productos WHERE categoria_id = $id_categoria";
} else {
    $sql = "SELECT * FROM productos";
}

$query = $db->query($sql);

if($query){
    while($row = $query->fetch_assoc()){
        $datos[] = $row;
    }
}

// --- EL CAMBIO QUE ARREGLA TODO ---
// Antes enviabas: { status: 'success', productos: [...] } -> ERROR
// Ahora enviamos: [...] -> CORRECTO
echo json_encode($datos);
?>