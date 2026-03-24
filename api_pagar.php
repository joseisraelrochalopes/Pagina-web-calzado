<?php
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

ob_clean();

$usuario_id = isset($data->usuario_id) ? (int)$data->usuario_id : null;
$total      = isset($data->total)      ? $data->total      : 0;
$metodo     = isset($data->metodo)     ? $data->metodo     : 'efectivo'; 

if($usuario_id) {
    
    // 1. VERIFICAR SI HAY STOCK DE LA TALLA SELECCIONADA EN EL CARRITO
    $sql_check = "SELECT c.producto_id, c.cantidad, c.talla, pt.stock, p.nombre 
                  FROM carrito c 
                  JOIN productos p ON c.producto_id = p.id 
                  JOIN productos_tallas pt ON c.producto_id = pt.producto_id AND c.talla = pt.talla
                  WHERE c.usuario_id = ?";
    
    $stmt_check = $db->prepare($sql_check);
    $stmt_check->bind_param("i", $usuario_id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    
    $hay_productos = false;

    while($item = $res_check->fetch_object()){
        $hay_productos = true;
        // Revisamos si pide más de lo que hay en ese número de zapato
        if($item->cantidad > $item->stock) {
            echo json_encode([
                'status' => 'error', 
                'message' => "No hay suficiente stock de: {$item->nombre} en Talla {$item->talla}. Disponibles: {$item->stock}"
            ]);
            exit; // Detenemos todo si un solo producto/talla no tiene stock
        }
    }

    if(!$hay_productos) {
         echo json_encode(['status' => 'error', 'message' => 'Tu carrito está vacío o hubo un error con las tallas.']);
         exit;
    }

    // 2. CREAR LA ORDEN GENERAL
    $sql_orden = "INSERT INTO ordenes (usuario_id, total, metodo_pago, fecha, estado) VALUES (?, ?, ?, NOW(), 'pendiente')";
    $stmt_orden = $db->prepare($sql_orden);
    $stmt_orden->bind_param("ids", $usuario_id, $total, $metodo);
    
    if($stmt_orden->execute()){
        $orden_id = $db->insert_id;
        
        // 3. PROCESAR CADA PRODUCTO (Guardar detalle y RESTAR STOCK POR TALLA)
        $sql_carrito = "SELECT c.producto_id, c.cantidad, c.talla, pt.precio 
                        FROM carrito c 
                        JOIN productos_tallas pt ON c.producto_id = pt.producto_id AND c.talla = pt.talla 
                        WHERE c.usuario_id = ?";
        $stmt_car = $db->prepare($sql_carrito);
        $stmt_car->bind_param("i", $usuario_id);
        $stmt_car->execute();
        $items = $stmt_car->get_result();
        
        while($row = $items->fetch_object()){
            // A. Guardar Detalle (Ahora guardamos la TALLA para que tú sepas qué enviarle al cliente)
            $sql_det = "INSERT INTO orden_detalle (orden_id, producto_id, talla, cantidad, precio) VALUES (?, ?, ?, ?, ?)";
            $stmt_det = $db->prepare($sql_det);
            $stmt_det->bind_param("iisid", $orden_id, $row->producto_id, $row->talla, $row->cantidad, $row->precio);
            $stmt_det->execute();

            // B. RESTAR DEL INVENTARIO DE TALLAS (La magia sucede aquí)
            $sql_restar = "UPDATE productos_tallas SET stock = stock - ? WHERE producto_id = ? AND talla = ? AND stock >= ?";
            $stmt_res = $db->prepare($sql_restar);
            $stmt_res->bind_param("iisi", $row->cantidad, $row->producto_id, $row->talla, $row->cantidad);
            $stmt_res->execute();
        }

        // 4. VACIAR EL CARRITO
        $stmt_del = $db->prepare("DELETE FROM carrito WHERE usuario_id = ?");
        $stmt_del->bind_param("i", $usuario_id);
        $stmt_del->execute();
        
        echo json_encode(['status' => 'success', 'message' => 'Compra exitosa en Calzado San Miguel']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al crear orden']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
}