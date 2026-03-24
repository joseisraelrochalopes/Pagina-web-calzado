<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

require_once 'config/db.php';
$db = Database::connect();

$data = json_decode(file_get_contents("php://input"));

if(isset($data->id_carrito) && isset($data->accion)) {
    
    $id_carrito = $data->id_carrito;
    $accion = $data->accion; // 'sumar' o 'restar'

    // 1. Averiguar qué producto es y cuánto stock tiene
    $sql_info = "SELECT c.cantidad, p.stock, p.nombre 
                 FROM carrito c 
                 JOIN productos p ON c.producto_id = p.id 
                 WHERE c.id = $id_carrito";
    
    $res = $db->query($sql_info);
    
    if($row = $res->fetch_assoc()){
        $cantidad_actual = $row['cantidad'];
        $stock_maximo = $row['stock'];
        $nombre_prod = $row['nombre'];

        // 2. Lógica de Sumar
        if($accion == 'sumar'){
            if($cantidad_actual < $stock_maximo){
                $sql = "UPDATE carrito SET cantidad = cantidad + 1 WHERE id = $id_carrito";
                $db->query($sql);
                echo json_encode(['status' => 'success', 'message' => 'Agregado']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No hay más stock de ' . $nombre_prod]);
            }
        }
        // 3. Lógica de Restar
        elseif($accion == 'restar'){
            if($cantidad_actual > 1){
                $sql = "UPDATE carrito SET cantidad = cantidad - 1 WHERE id = $id_carrito";
                $db->query($sql);
                echo json_encode(['status' => 'success', 'message' => 'Restado']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Mínimo 1 unidad (usa el bote de basura para eliminar)']);
            }
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Producto no encontrado']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
}
?>