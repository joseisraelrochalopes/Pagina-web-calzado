<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');

require_once 'config/db.php';
$db = Database::connect();

if(isset($_GET['usuario_id'])) {
    
    $usuario_id = (int)$_GET['usuario_id'];

    // Hacemos un JOIN para unir carrito con productos Y con productos_tallas 
    // para obtener el precio real de la talla seleccionada
    $sql = "SELECT 
                c.id as id_carrito, 
                c.cantidad, 
                c.talla,
                c.producto_id,
                p.nombre, 
                p.imagen,
                COALESCE(pt.precio, p.precio) as precio
            FROM carrito c 
            JOIN productos p ON c.producto_id = p.id 
            LEFT JOIN productos_tallas pt ON c.producto_id = pt.producto_id AND c.talla = pt.talla
            WHERE c.usuario_id = $usuario_id";

    $resultado = $db->query($sql);

    if($resultado){
        $items = [];
        while($row = $resultado->fetch_assoc()){
            // Formatear numéricamente para evitar errores en la App
            $row['precio'] = (float)$row['precio'];
            $row['cantidad'] = (int)$row['cantidad'];
            $items[] = $row;
        }
        echo json_encode(['status' => 'success', 'items' => $items]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al consultar el carrito']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Falta el ID de usuario']);
}
?>