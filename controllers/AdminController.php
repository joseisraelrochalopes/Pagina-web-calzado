<?php
require_once 'models/Producto.php';
require_once 'models/Pedido.php';

class AdminController {
    
    public function dashboard(){
        Utils::isAdmin();
        
        // KPIs
        $pedido = new Pedido();
        $ingresos = $pedido->getTotalVentas();
        $total_pedidos = $pedido->getCountPedidos();
        
        // Alertas
        $producto = new Producto();
        $stock_bajo = $producto->getLowStock();

        // Gráfico
        $ventas_dias = $pedido->getVentasUltimaSemana();
        $fechas = [];
        $totales = [];
        if($ventas_dias){
            while($fila = $ventas_dias->fetch_object()){
                $fechas[] = $fila->fecha;
                $totales[] = $fila->total;
            }
        }
        $fechas_json = json_encode($fechas);
        $totales_json = json_encode($totales);

        // NUEVO: MEJORES VENDEDORES
        $best_sellers = $pedido->getBestSellers();
        
        require_once 'views/admin/dashboard.php';
    }
}