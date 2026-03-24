<?php
class Database {
    public static function connect(){
        // 1. CREDENCIALES
        $host = 'localhost';
        $user = 'root';
        $pass = ''; 
        
        // ¡OJO! Asegúrate que este sea el nombre EXACTO de tu base de datos en phpMyAdmin
        $db_name = 'tienda_master'; 
        // Si tu base se llama 'mi_tienda', cambia la línea de arriba a: $db_name = 'mi_tienda';

        // 2. CONEXIÓN
        $conexion = new mysqli($host, $user, $pass, $db_name);
        
        // 3. VERIFICAR SI HUBO ERROR (Esto evita que mande HTML basura si falla)
        if ($conexion->connect_error) {
            // Matamos el proceso pero mandamos un JSON de error para que la App entienda
            die(json_encode(['status' => 'error', 'message' => 'Error Conexión DB: ' . $conexion->connect_error]));
        }

        // 4. CARACTERES ESPECIALES
        $conexion->query("SET NAMES 'utf8'");

        return $conexion;
    }
}
