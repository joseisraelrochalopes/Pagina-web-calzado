<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

require_once 'config/db.php';
$db = Database::connect();

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

// 1. OBTENER DATOS
if ($accion == 'obtener') {
    $id = $_GET['id'];
    $sql = "SELECT id, nombre, apellidos, email, telefono, imagen, rol FROM usuarios WHERE id = $id";
    $res = $db->query($sql);
    
    if ($res && $row = $res->fetch_assoc()) {
        echo json_encode(['status' => 'success', 'usuario' => $row]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
    }
}

// 2. EDITAR DATOS
if ($accion == 'editar') {
    $data = json_decode(file_get_contents("php://input"));
    
    $id = $data->id;
    $nombre = $db->real_escape_string($data->nombre);
    $apellidos = isset($data->apellidos) ? $db->real_escape_string($data->apellidos) : '';
    $telefono = isset($data->telefono) ? $db->real_escape_string($data->telefono) : '';

    $sql = "UPDATE usuarios SET nombre='$nombre', apellidos='$apellidos', telefono='$telefono' WHERE id=$id";
    
    if (!empty($data->password)) {
        $passHash = password_hash($data->password, PASSWORD_BCRYPT);
        $sql = "UPDATE usuarios SET nombre='$nombre', apellidos='$apellidos', telefono='$telefono', password='$passHash' WHERE id=$id";
    }

    if ($db->query($sql)) {
        $nuevo = $db->query("SELECT id, nombre, apellidos, email, telefono, imagen, rol FROM usuarios WHERE id = $id")->fetch_assoc();
        echo json_encode(['status' => 'success', 'usuario' => $nuevo]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar']);
    }
}

// 3. SUBIR FOTO
if ($accion == 'subir_foto') {
    $id = $_POST['id'];
    if (isset($_FILES['foto'])) {
        $nombreArchivo = time() . "_" . basename($_FILES["foto"]["name"]);
        $target_dir = "assets/img/";
        $target_file = $target_dir . $nombreArchivo;
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $sql = "UPDATE usuarios SET imagen = '$nombreArchivo' WHERE id = $id";
            $db->query($sql);
            echo json_encode(['status' => 'success', 'message' => 'Foto subida', 'imagen' => $nombreArchivo]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al mover archivo']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No llegó imagen']);
    }
}
?>