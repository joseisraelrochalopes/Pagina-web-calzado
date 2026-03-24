<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json');
require_once 'config/db.php';
$db = Database::connect();

$json = file_get_contents('php://input');
$params = json_decode($json);

if(!$params) {
    echo json_encode(['status'=>'error', 'message'=>'PHP no recibio el JSON']);
    exit;
}

$respuesta = ['status' => 'error', 'message' => 'Faltan datos'];

if(isset($params->email) && isset($params->password)){
    // Ya no necesitamos real_escape_string porque el prepare hace ese trabajo
    $email = $params->email; 
    $password = trim($params->password); 

    // 1. PREPARAMOS LA SENTENCIA (El molde)
    $stmt = $db->prepare("SELECT id, email, password, nombre, rol, imagen FROM usuarios WHERE email = ?");
    
    // 2. VINCULAMOS EL PARÁMETRO ("s" significa que el dato es un string)
    $stmt->bind_param("s", $email);
    
    // 3. EJECUTAMOS
    $stmt->execute();
    
    // 4. OBTENEMOS EL RESULTADO
    $resultado = $stmt->get_result();

    if($resultado && $resultado->num_rows == 1){
        $usuario = $resultado->fetch_object();
        
        $esValido = false;

        // Verificación dual (Hash o Texto plano)
        if (password_verify($password, $usuario->password)) {
            $esValido = true;
        } 
        else if ($password == $usuario->password) {
            $esValido = true;
        }

        if($esValido){
            $respuesta = [
                'status' => 'success',
                'user' => [
                    'id' => $usuario->id,
                    'email' => $usuario->email,
                    'nombre' => $usuario->nombre,
                    'rol' => $usuario->rol,
                    'imagen' => $usuario->imagen
                ]
            ];
        } else {
            $respuesta['message'] = "Contraseña incorrecta.";
        }
    } else {
        $respuesta['message'] = 'El usuario no existe';
    }
    
    // Cerramos la sentencia por seguridad
    $stmt->close();
}

echo json_encode($respuesta);
?>