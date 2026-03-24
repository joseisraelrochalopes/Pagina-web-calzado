<?php
ob_start(); // <--- 1. INICIO DEL BUFFER (Magia para que funcionen las redirecciones)
session_start();

// 1. CARGA DE ARCHIVOS ESENCIALES
require_once 'autoload.php';
require_once 'config/db.php';
require_once 'config/parameters.php';
require_once 'helpers/utils.php';
require_once 'views/layout/header.php'; // Se carga en memoria, no se envía aún

// Configuración de errores (Solo para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Función de error 404
function show_error(){
    $error = new ErrorController();
    $error->index();
}

// 2. LÓGICA DEL ROUTER

if(isset($_GET['controller'])){
    $nombre_controlador = $_GET['controller'].'Controller';
} elseif(!isset($_GET['controller']) && !isset($_GET['action'])){
    $nombre_controlador = controller_default;
} else {
    show_error();
    exit();
}

if(class_exists($nombre_controlador)){	
    $controlador = new $nombre_controlador();
    
    if(isset($_GET['action']) && method_exists($controlador, $_GET['action'])){
        $action = $_GET['action'];
        $controlador->$action();
    } elseif(!isset($_GET['controller']) && !isset($_GET['action'])){
        $action_default = action_default;
        $controlador->$action_default();
    } else {
        show_error();
    }
} else {
    show_error();
}

// 3. CARGA DEL FOOTER Y ENVÍO FINAL
require_once 'views/layout/footer.php';

ob_end_flush(); // <--- 2. FIN DEL BUFFER (Aquí se envía todo el HTML junto al navegador)
?>