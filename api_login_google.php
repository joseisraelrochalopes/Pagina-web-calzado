<?php
// 1. Cabeceras CORS (Indispensables para que Ionic tenga permiso)
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json');

// 2. IMPORTAR TU CONEXIÓN REAL
// Según tu api_login.php, esta es la ruta correcta:
require_once 'config/db.php';

// Conectamos usando tu clase Database
$db = Database::connect();

// 3. Leer datos del frontend
$json = file_get_contents('php://input');
$params = json_decode($json);

$respuesta = [
    'status' => 'error',
    'message' => 'No se recibieron datos de Google'
];

if(isset($params->email)) {
    $email = $db->real_escape_string($params->email);
    $nombre = $db->real_escape_string($params->nombre);
    $apellidos = isset($params->apellido) ? $db->real_escape_string($params->apellido) : ''; 
    $imagenUrl = isset($params->imagen) ? $db->real_escape_string($params->imagen) : ''; 

    // Verificar si el usuario ya existe
    $sql_check = "SELECT * FROM usuarios WHERE email = '$email'";
    $query_check = $db->query($sql_check);

    if($query_check && $query_check->num_rows > 0) {
        // ACTUALIZAR: El usuario ya existe, actualizamos su info de Google
        $db->query("UPDATE usuarios SET imagen = '$imagenUrl', nombre = '$nombre', apellidos = '$apellidos' WHERE email = '$email'");
        
        $usuario = $query_check->fetch_object();
        $respuesta = [
            'status' => 'success',
            'usuario' => [
                'id' => $usuario->id,
                'email' => $usuario->email,
                'nombre' => $usuario->nombre,
                'rol' => $usuario->rol,
                'imagen' => $imagenUrl
            ]
        ];
    } else {
        // INSERTAR: Es un usuario nuevo que entra con Google
        $password = password_hash("google_" . uniqid(), PASSWORD_DEFAULT);
        $sql_insert = "INSERT INTO usuarios (nombre, apellidos, email, password, rol, imagen) 
                       VALUES ('$nombre', '$apellidos', '$email', '$password', 'cliente', '$imagenUrl')";
        
        if($db->query($sql_insert)) {
            $nuevoId = $db->insert_id;
            $respuesta = [
                'status' => 'success',
                'usuario' => [
                    'id' => $nuevoId,
                    'email' => $email,
                    'nombre' => $nombre,
                    'rol' => 'cliente',
                    'imagen' => $imagenUrl
                ]
            ];
        } else {
            $respuesta['message'] = 'Error al registrar en la base de datos: ' . $db->error;
        }
    }
}

echo json_encode($respuesta);
?>