<?php

class Utils {
    
    public static function deleteSession($name){
        if(isset($_SESSION[$name])){
            $_SESSION[$name] = null;
            unset($_SESSION[$name]);
        }
        return $name;
    }

    public static function isAdmin(){
        if(!isset($_SESSION['admin'])){
            header("Location:".base_url);
        } else {
            return true;
        }
    }

    public static function showCategorias(){
        require_once 'models/Categoria.php';
        $categoria = new Categoria();
        $categorias = $categoria->getAll();
        return $categorias;
    }

    public static function showImage($nombre_archivo){
        $ruta_fisica = 'assets/img/' . $nombre_archivo;
        if(!empty($nombre_archivo) && file_exists($ruta_fisica)){
            return base_url . $ruta_fisica;
        } else {
            return "https://via.placeholder.com/300x300?text=Sin+Imagen";
        }
    }

    public static function statsCarrito(){
        $stats = array(
            'count' => 0,
            'total' => 0,
            'descuento' => 0,
            'total_con_descuento' => 0
        );
        
        if(isset($_SESSION['carrito'])){
            $stats['count'] = count($_SESSION['carrito']);
            
            foreach($_SESSION['carrito'] as $producto){
                $stats['total'] += $producto['precio'] * $producto['unidades'];
            }

            $stats['total_con_descuento'] = $stats['total'];
            
            if(isset($_SESSION['cupon'])){
                $porcentaje = $_SESSION['cupon']['porcentaje'];
                $dinero_descontado = ($stats['total'] * $porcentaje) / 100;
                
                $stats['descuento'] = $dinero_descontado;
                $stats['total_con_descuento'] = $stats['total'] - $dinero_descontado;
            }
        }
        
        return $stats;
    }

    // --- NUEVO: OBTENER SOLO EL SÍMBOLO (Para formularios) ---
    public static function getMonedaSymbol(){
        if(!isset($_SESSION['moneda_simbolo'])){
            require_once 'models/Configuracion.php';
            $conf = new Configuracion();
            $_SESSION['moneda_simbolo'] = $conf->getMoneda();
        }
        return $_SESSION['moneda_simbolo'];
    }

    // --- FORMATEAR PRECIO COMPLETO (Símbolo + Cantidad) ---
    public static function formatPrice($amount){
        $simbolo = self::getMonedaSymbol();
        return $simbolo . " " . number_format($amount, 2);
    }
}