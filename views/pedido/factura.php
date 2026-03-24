<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #<?=$pedido->id?> - <?=$empresa->nombre_empresa?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #e9ecef;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 20px;
        }
        .invoice-container {
            background-color: #ffffff;
            max-width: 850px;
            margin: 0 auto;
            padding: 50px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .invoice-header {
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #333;
            margin: 0;
        }
        .invoice-title {
            font-size: 36px;
            font-weight: bold;
            color: #333;
            text-align: right;
            line-height: 1;
        }
        .table-invoice thead th {
            background-color: #333 !important;
            color: #fff !important;
            border: none;
        }
        .total-section {
            font-size: 18px;
            text-align: right;
            margin-top: 30px;
        }
        .grand-total {
            font-size: 24px;
            font-weight: bold;
            color: #198754;
            border-top: 2px solid #ccc;
            padding-top: 10px;
            margin-top: 10px;
        }

        @media print {
            .no-print { display: none !important; }
            @page { margin: 1cm; size: A4; }
            body {
                background-color: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact;
            }
            .invoice-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            .table-invoice thead th {
                background-color: #333 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
            }
            a { text-decoration: none; color: #000; }
        }
    </style>
</head>
<body>

    <div class="container no-print mb-4 text-center">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow">
            <i class="bi bi-printer"></i> Imprimir Factura
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-lg shadow ms-2">
            Cerrar
        </button>
    </div>

    <div class="invoice-container">
        
        <div class="row invoice-header align-items-center">
            <div class="col-7">
                <?php if(!empty($empresa->logo)): ?>
                    <img src="<?=base_url?>assets/img/logo/<?=$empresa->logo?>" class="company-logo" alt="Logo">
                <?php else: ?>
                    <h1 class="company-name"><?=$empresa->nombre_empresa?></h1>
                <?php endif; ?>
                
                <address class="mt-2 mb-0 text-muted">
                    <strong><?=$empresa->nombre_empresa?></strong><br>
                    <?=$empresa->direccion?><br>
                    RUC: <?=$empresa->ruc?><br>
                    Tel: <?=$empresa->telefono?><br>
                    Email: <?=$empresa->email?>
                </address>
            </div>
            
            <div class="col-5 text-end">
                <div class="invoice-title">FACTURA</div>
                <div class="fs-5 text-muted">Nº F001-<?=str_pad($pedido->id, 6, '0', STR_PAD_LEFT)?></div>
                <div class="mt-2">
                    <strong>Fecha:</strong> <?=$pedido->fecha?><br>
                    <strong>Pago:</strong> <?=strtoupper($pedido->metodo_pago ?? 'No especificado')?>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-6">
                <h5 class="border-bottom pb-2">Facturado a:</h5>
                <p class="mb-1"><strong><?=$_SESSION['identity']->nombre?> <?=$_SESSION['identity']->apellidos?></strong></p>
                <p class="mb-1"><?=$_SESSION['identity']->email?></p>
            </div>
            <div class="col-6">
                <h5 class="border-bottom pb-2">Enviado a:</h5>
                <p class="mb-1"><?=$pedido->direccion?></p>
                <p class="mb-1"><?=$pedido->localidad?> - <?=$pedido->provincia?></p>
            </div>
        </div>

        <table class="table table-invoice table-striped">
            <thead>
                <tr>
                    <th scope="col">Descripción</th>
                    <th scope="col" class="text-center">Precio Unit.</th>
                    <th scope="col" class="text-center">Cant.</th>
                    <th scope="col" class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $subtotal_productos = 0;
                while ($producto = $productos->fetch_object()): 
                    $total_linea = $producto->precio * $producto->unidades;
                    $subtotal_productos += $total_linea;
                ?>
                    <tr>
                        <td>
                            <?=$producto->nombre?>
                            <?php if(isset($producto->talla) && $producto->talla != 'Única'): ?>
                                <small class="text-muted">(Talla: <?=$producto->talla?>)</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?=Utils::formatPrice($producto->precio)?></td>
                        <td class="text-center"><?=$producto->unidades?></td>
                        <td class="text-end"><?=Utils::formatPrice($total_linea)?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="row">
            <div class="col-6">
                <div class="mt-4 p-3 bg-light rounded border no-print">
                    <strong>Nota:</strong> Gracias por su preferencia.
                </div>
            </div>
            <div class="col-6">
                <div class="total-section">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal Productos:</span>
                        <span><?=Utils::formatPrice($subtotal_productos)?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Costo de Envío:</span>
                        <span><?=Utils::formatPrice($pedido->coste_envio)?></span>
                    </div>

                    <?php 
                        $esperado = $subtotal_productos + $pedido->coste_envio;
                        $descuento = $esperado - $pedido->coste;
                        if($descuento > 0.01):
                    ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Descuento Cupón:</span>
                            <span>- <?=Utils::formatPrice($descuento)?></span>
                        </div>
                    <?php endif; ?>

                    <div class="grand-total d-flex justify-content-between">
                        <span>TOTAL PAGADO:</span>
                        <span><?=Utils::formatPrice($pedido->coste)?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5 pt-4 border-top text-muted small">
            <p>Este documento es una representación impresa de una factura electrónica generada por <strong><?=$empresa->nombre_empresa?></strong>.</p>
        </div>

    </div>

</body>
</html>