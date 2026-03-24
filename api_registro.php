<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');

require_once 'config/db.php';
$db = Database::connect();

$post = json_decode(file_get_contents("php://input"));

if(isset($post->email) && isset($post->password) && isset($post->nombre)) {
    
    $nombre = $db->real_escape_string($post->nombre);
    // Usamos 'apellidos' (plural)
    $apellidos = isset($post->apellidos) ? $db->real_escape_string($post->apellidos) : '';
    $email = $db->real_escape_string($post->email);
    $telefono = isset($post->telefono) ? $db->real_escape_string($post->telefono) : '';
    
    $password = password_hash($post->password, PASSWORD_BCRYPT);
    
    $check = $db->query("SELECT id FROM usuarios WHERE email = '$email'");
    if($check->num_rows > 0){
        echo json_encode(['status' => 'error', 'message' => 'El correo ya existe']);
    } else {
        $sql = "INSERT INTO usuarios (nombre, apellidos, email, telefono, password, rol, imagen) 
                VALUES ('$nombre', '$apellidos', '$email', '$telefono', '$password', 'cliente', '')";
        
        if($db->query($sql)){
            echo json_encode(['status' => 'success', 'message' => 'Registro exitoso']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar']);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
}
?>