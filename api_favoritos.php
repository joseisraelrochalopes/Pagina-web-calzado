<?php
// 1. SILENCIAR ERRORES VISUALES (Esto arregla el "Http failure parsing")
error_reporting(0);
ini_set('display_errors', 0);

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json; charset=UTF-8'); // Forzamos UTF-8

require_once 'config/db.php';
$db = Database::connect();

$data = json_decode(file_get_contents("php://input"));

$usuario_id = isset($_GET['usuario_id']) ? $_GET['usuario_id'] : (isset($data->usuario_id) ? $data->usuario_id : null);
$producto_id = isset($data->producto_id) ? $data->producto_id : null;
$accion = isset($_GET['accion']) ? $_GET['accion'] : (isset($data->accion) ? $data->accion : null);

// Permitir acceso si es admin o si hay usuario_id
if(strpos($accion, 'admin_') === false && !$usuario_id){
    echo json_encode(['status' => 'error', 'message' => 'Falta el usuario']);
    exit;
}

// 1. LISTAR (Usuario)
if($accion == 'listar'){
    $sql = "SELECT f.id as fav_id, p.* FROM favoritos f 
            JOIN productos p ON f.producto_id = p.id 
            WHERE f.usuario_id = $usuario_id ORDER BY f.id DESC";
    $query = $db->query($sql);
    $favoritos = [];
    if($query){ while($row = $query->fetch_assoc()){ $favoritos[] = $row; } }
    echo json_encode(['status' => 'success', 'favoritos' => $favoritos]);
    exit;
}

// 2. AGREGAR
if($accion == 'agregar' && $producto_id){
    $sqlCount = "SELECT COUNT(*) as total FROM favoritos WHERE usuario_id = $usuario_id";
    $resCount = $db->query($sqlCount);
    $row = $resCount->fetch_assoc();
    if($row['total'] >= 5){ echo json_encode(['status' => 'error', 'message' => 'Límite alcanzado']); exit; }

    $sqlCheck = "SELECT id FROM favoritos WHERE usuario_id = $usuario_id AND producto_id = $producto_id";
    if($db->query($sqlCheck)->num_rows > 0){ echo json_encode(['status' => 'error', 'message' => 'Ya existe']); exit; }

    $sql = "INSERT INTO favoritos (usuario_id, producto_id) VALUES ($usuario_id, $producto_id)";
    if($db->query($sql)){ echo json_encode(['status' => 'success']); } 
    else { echo json_encode(['status' => 'error']); }
    exit;
}

// 3. ELIMINAR UNO
if($accion == 'eliminar' && $producto_id){
    $sql = "DELETE FROM favoritos WHERE usuario_id = $usuario_id AND producto_id = $producto_id";
    if($db->query($sql)){ echo json_encode(['status' => 'success']); }
    else { echo json_encode(['status' => 'error']); }
    exit;
}

// 4. ADMIN: LISTAR USUARIOS
if($accion == 'admin_usuarios'){
    $sql = "SELECT u.id, u.nombre, u.email, COUNT(f.id) as cantidad
            FROM usuarios u
            JOIN favoritos f ON u.id = f.usuario_id
            GROUP BY u.id
            ORDER BY cantidad DESC";
    $query = $db->query($sql);
    $lista = [];
    if($query){ while($row = $query->fetch_assoc()){ $lista[] = $row; } }
    echo json_encode(['status' => 'success', 'lista' => $lista]);
    exit;
}

// 5. ADMIN: ELIMINAR TODO
if($accion == 'admin_eliminar_todo' && $usuario_id){
    $sql = "DELETE FROM favoritos WHERE usuario_id = $usuario_id";
    if($db->query($sql)){ echo json_encode(['status' => 'success', 'message' => 'Apartados eliminados']); } 
    else { echo json_encode(['status' => 'error', 'message' => 'Error al eliminar']); }
    exit;
}

// --- ACCIÓN 6: ADMIN - CONFIRMAR VENTA (VERSIÓN BLINDADA) ---
if($accion == 'admin_confirmar_venta' && $usuario_id && $producto_id){
    
    // A. Stock
    $prod = $db->query("SELECT precio, stock FROM productos WHERE id = $producto_id")->fetch_assoc();
    if(!$prod || $prod['stock'] < 1) {
         echo json_encode(['status' => 'error', 'message' => 'Error: Stock insuficiente']); 
         exit;
    }

    // B. Crear Orden
    $total = $prod['precio'];
    $fecha = date('Y-m-d H:i:s'); 
    
    // Usamos una consulta simple primero para evitar errores de columnas extrañas
    // Asegúrate de haber ejecutado el SQL del PASO 1 antes de esto
    $sqlOrden = "INSERT INTO ordenes (usuario_id, total, fecha, estado, direccion) 
                 VALUES ($usuario_id, $total, '$fecha', 'entregado', 'Venta Admin')";
    
    if($db->query($sqlOrden)){
        $orden_id = $db->insert_id;
        
        // C. Detalle
        $sqlDetalle = "INSERT INTO orden_detalles (orden_id, producto_id, cantidad, precio) 
                       VALUES ($orden_id, $producto_id, 1, $total)";
        
        if(!$db->query($sqlDetalle)){
             echo json_encode(['status' => 'error', 'message' => 'Error SQL Detalle: ' . $db->error]);
             exit;
        }
        
        // D. Restar Stock y Borrar Favorito
        $db->query("UPDATE productos SET stock = stock - 1 WHERE id = $producto_id");
        $db->query("DELETE FROM favoritos WHERE usuario_id = $usuario_id AND producto_id = $producto_id");
        
        echo json_encode(['status' => 'success', 'message' => 'Venta registrada con éxito']);
    } else {
         echo json_encode(['status' => 'error', 'message' => 'Error SQL Orden: ' . $db->error]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Accion no valida']);
?>