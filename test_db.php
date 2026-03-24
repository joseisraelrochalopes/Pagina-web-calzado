<?php
require_once 'config/db.php';

$db = Database::connect();

if($db){
    echo "<h1>¡Conexión Exitosa con la Base de Datos 'tienda_master'!</h1>";
} else {
    echo "<h1>Error de conexión</h1>";
}