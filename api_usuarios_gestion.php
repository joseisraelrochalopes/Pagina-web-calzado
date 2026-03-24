<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json');

require_once 'config/db.php';
require_once 'models/Usuario.php';
require_once 'models/Favorito.php';

$db = Database::connect();
$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

if ($accion == 'listar_todos') {
    // 1. Obtener todos los usuarios para la lista de gestión
    $usuario = new Usuario();
    $res = $usuario->getAll();
    $usuarios = [];

    while ($user = $res->fetch_object()) {
        $usuarios[] = $user;
    }
    echo json_encode($usuarios);

} elseif ($accion == 'ver_favoritos') {
    // 2. Obtener favoritos de un cliente específico (para el corazón amarillo)
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id > 0) {
        // Obtener datos del usuario
        $u = new Usuario();
        $u->setId($id);
        $user_data = $u->getOne();

        // Obtener sus favoritos
        $f = new Favorito();
        $f->setUsuario_id($id);
        $res_favs = $f->getAllByUser();
        $favoritos = [];

        while ($fav = $res_favs->fetch_object()) {
            $favoritos[] = $fav;
        }

        echo json_encode([
            'status' => 'success',
            'user_data' => $user_data,
            'favoritos' => $favoritos
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID no válido']);
    }
}