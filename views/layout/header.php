<?php
    // CARGAR CONFIGURACIÓN GLOBAL DE LA EMPRESA
    require_once 'models/Configuracion.php';
    $conf_header = new Configuracion();
    $empresa_data = $conf_header->getAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$empresa_data->nombre_empresa?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?=base_url?>assets/css/styles.css">
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-custom mb-4 sticky-top shadow-lg">
        <div class="container">
            
            <a class="navbar-brand text-uppercase fw-bold d-flex align-items-center text-white" href="<?=base_url?>">
                <?php if(!empty($empresa_data->logo)): ?>
                    <img src="<?=base_url?>assets/img/logo/<?=$empresa_data->logo?>" alt="Logo" height="35" class="me-2 bg-white rounded p-1 shadow-sm">
                <?php else: ?>
                    <i class="bi bi-shop me-2 text-warning"></i>
                <?php endif; ?>
                <span><?=$empresa_data->nombre_empresa?></span>
            </a>

            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <i class="bi bi-list fs-1"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active px-3" href="<?=base_url?>">Inicio</a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-3" href="#" id="navbarCategorias" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Categorías
                        </a>
                        <ul class="dropdown-menu mt-2" aria-labelledby="navbarCategorias">
                            <?php 
                                $categorias = Utils::showCategorias();
                                while($cat = $categorias->fetch_object()): 
                            ?>
                                <li>
                                    <a class="dropdown-item py-2" href="<?=base_url?>categoria/ver?id=<?=$cat->id?>">
                                        <i class="bi bi-star-fill text-warning me-2" style="font-size: 0.8rem;"></i> <?=$cat->nombre?>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?=base_url?>contacto/index">Contacto</a>
                    </li>
                </ul>

                <form class="d-flex mx-auto my-2 my-lg-0 search-container" action="<?=base_url?>producto/buscar" method="POST" role="search" style="max-width: 320px; width: 100%;">
                    <div class="input-group">
                        <input class="form-control search-zapateria px-4" type="search" name="busqueda" placeholder="Buscar..." required>
                        <button class="btn btn-search-custom px-3" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <ul class="navbar-nav ms-auto mobile-icons-row align-items-center mt-3 mt-lg-0">
                    
                    <?php $stats = Utils::statsCarrito(); ?>
                    <li class="nav-item me-lg-4">
                        <a class="nav-link position-relative d-flex align-items-center text-white" href="<?=base_url?>carrito/index">
                            <i class="bi bi-cart-fill fs-4"></i> 
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill badge-latido">
                                <?=$stats['count']?>
                            </span>
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" role="button" data-bs-toggle="dropdown">
                            <?php if(isset($_SESSION['identity'])): ?>
                                <?php if(!empty($_SESSION['identity']->imagen)): ?>
                                    <?php if(strpos($_SESSION['identity']->imagen, 'http') === 0): ?>
                                        <img src="<?=$_SESSION['identity']->imagen?>" class="rounded-circle shadow-sm me-2" width="30" height="30" style="object-fit: cover; border: 2px solid #b89324;">
                                    <?php else: ?>
                                        <img src="<?=base_url?>assets/img/users/<?=$_SESSION['identity']->imagen?>" class="rounded-circle shadow-sm me-2" width="30" height="30" style="object-fit: cover; border: 2px solid #b89324;">
                                    <?php endif; ?>
                                <?php else: ?>
                                    <i class="bi bi-person-circle fs-4 text-warning me-2"></i>
                                <?php endif; ?>
                                <span><?=$_SESSION['identity']->nombre?></span>
                            <?php else: ?>
                                <i class="bi bi-person-circle fs-4 text-warning me-2"></i>
                                <span>Iniciar Sesión</span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end mt-2">
                            <?php if(isset($_SESSION['identity'])): ?>
                                <li><h6 class="dropdown-header fw-bold border-bottom">Hola, <?=$_SESSION['identity']->nombre?></h6></li>
                                
                                <?php if(isset($_SESSION['admin'])): ?>
                                    <li><a class="dropdown-item fw-bold text-primary" href="<?=base_url?>admin/dashboard"><i class="bi bi-speedometer2 me-2"></i> Panel de control</a></li>
                                    <li><a class="dropdown-item fw-bold" href="<?=base_url?>configuracion/index"><i class="bi bi-gear-fill me-2"></i> Configuración</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    <li><a class="dropdown-item" href="<?=base_url?>categoria/index">Gestionar Categorías</a></li>
                                    <li><a class="dropdown-item" href="<?=base_url?>producto/gestion">Gestionar Productos</a></li>
                                    <li><a class="dropdown-item" href="<?=base_url?>cupon/gestion">Gestionar Cupones</a></li>
                                    <li><a class="dropdown-item" href="<?=base_url?>pedido/gestion">Gestionar Pedidos</a></li>
                                    <li><a class="dropdown-item" href="<?=base_url?>usuario/gestion">Gestionar Usuarios</a></li>
                                    <li><a class="dropdown-item" href="<?=base_url?>contacto/admin">Bandeja de Mensajes</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>

                                <li><a class="dropdown-item" href="<?=base_url?>pedido/mis_pedidos">Mis Pedidos</a></li>
                                <li><a class="dropdown-item" href="<?=base_url?>favorito/index">Mis Favoritos</a></li>
                                <li><a class="dropdown-item" href="<?=base_url?>usuario/mis_datos">Mis Datos</a></li>
                                <li><a class="dropdown-item text-danger fw-bold" href="<?=base_url?>usuario/logout">Cerrar sesión</a></li>

                            <?php else: ?>
                                <li><a class="dropdown-item" href="<?=base_url?>usuario/login">Iniciar Sesión</a></li>
                                <li><a class="dropdown-item" href="<?=base_url?>usuario/registro">Registrarse</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container main-content" style="min-height: 500px;">
        <div class="row">