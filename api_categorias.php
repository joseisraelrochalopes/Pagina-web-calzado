<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');

require_once 'config/db.php';
$db = Database::connect();

// Seleccionamos todas las categorías
$sql = "SELECT * FROM categorias"; // <--- CAMBIA 'categorias' SI TU TABLA TIENE OTRO NOMBRE
$query = $db->query($sql);

$datos = [];

if($query){
    while($row = $query->fetch_assoc()){
        // Si tienes imágenes de categorías, arregla la URL aquí igual que hicimos antes
        // Ejemplo: $row['imagen'] = 'http://localhost/mi_tienda/uploads/' . $row['imagen'];
        $datos[] = $row;
    }
}

echo json_encode(['status' => 'success', 'categorias' => $datos]);
?>