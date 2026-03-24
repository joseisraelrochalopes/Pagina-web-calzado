<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');

require_once 'config/db.php';
$db = Database::connect();

$texto = isset($_GET['q']) ? $db->real_escape_string($_GET['q']) : '';

$respuesta = ['status' => 'error', 'resultados' => []];

if($texto != ''){
    // Buscamos coincidencias en Nombre de producto, Marca o Nombre de Categoría
    $sql = "SELECT p.*, c.nombre as categoria_nombre 
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id
            WHERE p.nombre LIKE '%$texto%' 
            OR p.marca LIKE '%$texto%' 
            OR c.nombre LIKE '%$texto%'
            LIMIT 25";
    
    $result = $db->query($sql);

    if($result){
        while($row = $result->fetch_assoc()){
            $respuesta['resultados'][] = $row;
        }
        $respuesta['status'] = 'success';
    }
}

echo json_encode($respuesta);
?>