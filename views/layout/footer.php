</div> </div> <footer class="text-white pt-5 pb-4 mt-5" style="background-color: #0a192f; border-top: 4px solid #b89324;">
    <div class="container text-center text-md-start">
        <div class="row text-center text-md-start">

            <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 fw-bold" style="color: #b89324;">
                    <i class="bi bi-shield-check me-2"></i> <?=$empresa_data->nombre_empresa?>
                </h5>
                <p class="text-light opacity-75" style="font-size: 0.9rem; line-height: 1.6;">
                    Calzado de la mejor calidad para toda la familia. Elegancia, comodidad y confianza en cada paso que das. Visítanos y conoce nuestras nuevas colecciones.
                </p>
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 fw-bold" style="color: #b89324; font-size: 1rem;">Servicios</h5>
                <p><a href="<?=base_url?>" class="text-white text-decoration-none footer-link">Inicio</a></p>
                <p><a href="<?=base_url?>pedido/mis_pedidos" class="text-white text-decoration-none footer-link">Mis Pedidos</a></p>
                <p><a href="<?=base_url?>contacto/index" class="text-white text-decoration-none footer-link">Contacto</a></p>
            </div>

            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3 text-light opacity-75" style="font-size: 0.9rem;">
                <h5 class="text-uppercase mb-4 fw-bold" style="color: #b89324; font-size: 1rem;">Atención</h5>
                <p><i class="bi bi-house-door-fill me-2 text-warning"></i> Calle Principal #123, Veracruz</p>
                <p><i class="bi bi-envelope-fill me-2 text-warning"></i> contacto@calsadoshop.com</p>
                <p><i class="bi bi-whatsapp me-2 text-warning"></i> +52 123 456 7890</p>
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3 text-center">
                <h5 class="text-uppercase mb-4 fw-bold" style="color: #b89324; font-size: 1rem;">Síguenos</h5>
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle social-icon"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle social-icon"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle social-icon"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>

        </div>

        <hr class="mb-4 mt-5" style="background-color: #b89324; opacity: 0.3;">

        <div class="row align-items-center">
            <div class="col-md-12 text-center">
                <p class="mb-2 text-light opacity-50" style="font-size: 0.85rem;">
                    © <?= date('Y') ?> <strong class="text-white">Calsado Shop</strong>. Todos los derechos reservados.
                </p>
                <a href="<?=base_url?>creditos.php" class="btn-equipo shadow-sm">
                    <i class="bi bi-code-slash me-1"></i> Equipo de Desarrollo
                </a>
            </div>
        </div>
    </div>
</footer>

<style>
    /* ✨ EFECTOS PARA EL FOOTER ✨ */
    .footer-link {
        transition: all 0.3s ease;
        display: inline-block;
    }
    .footer-link:hover {
        color: #b89324 !important;
        transform: translateX(5px);
    }
    .social-icon {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border-color: rgba(255,255,255,0.2);
    }
    .social-icon:hover {
        background-color: #b89324;
        border-color: #b89324;
        transform: translateY(-5px);
        color: white;
    }
    .btn-equipo {
        text-decoration: none;
        color: #e6f1ff;
        background: rgba(255,255,255,0.05);
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 0.8rem;
        border: 1px solid rgba(184, 147, 36, 0.3);
        transition: all 0.3s ease;
        display: inline-block;
        margin-top: 10px;
    }
    .btn-equipo:hover {
        background: #b89324;
        color: white;
        transform: scale(1.05);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>