<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Inventario - <?=date('d/m/Y')?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ESTILOS PARA LA PANTALLA (PC) */
        body { 
            background: #525659; 
            padding: 30px; 
            font-family: Arial, sans-serif; 
        }
        .page {
            background: white;
            width: 21cm;
            min-height: 29.7cm;
            margin: 0 auto;
            padding: 2cm;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
            border-radius: 5px;
        }
        .header { border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: bold; text-transform: uppercase; }
        .meta { font-size: 14px; color: #666; text-align: right; }
        
        table { font-size: 12px; width: 100%; }
        th { background: #f0f0f0 !important; }
        
        .total-box {
            margin-top: 20px;
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            border-top: 1px solid #333;
            padding-top: 10px;
        }

        /* ✨ ESTILOS MÁGICOS PARA IMPRESIÓN Y PDF ✨ */
        @media print {
            /* 1. OCULTAR TODO EL SITIO WEB (Menú, Footer, etc.) */
            body * {
                visibility: hidden;
            }
            
            /* 2. MOSTRAR SOLO EL REPORTE Y SU CONTENIDO */
            .page, .page * {
                visibility: visible;
            }
            
            /* 3. MOVER EL REPORTE HASTA LA ESQUINA SUPERIOR IZQUIERDA */
            .page {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            @page {
                size: A4; 
                margin: 1.5cm; /* Márgenes de la hoja física */
            }
            
            body { 
                background: white !important; 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important;
            }
            
            .no-print { display: none !important; } /* Ocultar botones */
            
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
    </style>
</head>
<body>

    <div class="text-center mb-4 no-print">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow-sm fw-bold">
            <i class="bi bi-printer-fill"></i> Imprimir Reporte
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-lg shadow-sm ms-2 fw-bold">
            Cerrar Ventana
        </button>
    </div>

    <div class="page">
        <div class="row header align-items-center">
            <div class="col-8">
                <div class="title">Reporte de Inventario</div>
                <div class="fs-5 fw-bold"><?=$empresa->nombre_empresa?></div>
                <div class="small text-muted">RUC: <?=$empresa->ruc?></div>
            </div>
            <div class="col-4 meta">
                <strong>Fecha:</strong> <?=date('d/m/Y')?><br>
                <strong>Hora:</strong> <?=date('H:i')?><br>
                <strong>Generado por:</strong> <?=$_SESSION['identity']->nombre?>
            </div>
        </div>

        <table class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th class="text-center">ID</th>
                    <th>Producto</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Stock</th>
                    <th class="text-end">Precio Unit.</th>
                    <th class="text-end">Valor Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_stock = 0;
                $total_valor = 0;
                
                while($pro = $productos->fetch_object()): 
                    $valor_producto = $pro->precio * $pro->stock;
                    $total_valor += $valor_producto;
                    $total_stock += $pro->stock;
                ?>
                    <tr>
                        <td class="text-center fw-bold"><?=$pro->id?></td>
                        <td><?=$pro->nombre?></td>
                        <td class="text-center">
                            <?= ($pro->activo == 1) ? 'Activo' : 'Inactivo' ?>
                        </td>
                        <td class="text-center <?= ($pro->stock < 5) ? 'fw-bold text-danger' : '' ?>">
                            <?=$pro->stock?>
                        </td>
                        <td class="text-end"><?=Utils::formatPrice($pro->precio)?></td>
                        <td class="text-end fw-bold"><?=Utils::formatPrice($valor_producto)?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="total-box">
            <p>Total Unidades en Almacén: <span class="fw-bold"><?=$total_stock?> pares</span></p>
            <p class="text-success fs-5">VALORIZACIÓN TOTAL: <?=Utils::formatPrice($total_valor)?></p>
        </div>
        
        <div class="text-center mt-5 small text-muted">
            --- Fin del Documento ---
        </div>
    </div>

</body>
</html>