<h1 class="mb-4">Panel de Control</h1>

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card text-white bg-success shadow h-100">
            <div class="card-header fw-bold d-flex justify-content-between">
                <span><i class="bi bi-currency-dollar"></i> Ingresos Totales</span>
                <small>Histórico</small>
            </div>
            <div class="card-body">
                <h2 class="card-title display-4"><?= Utils::formatPrice($ingresos ?? 0) ?></h2>
                <p class="card-text">Dinero acumulado en ventas confirmadas.</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card text-white bg-primary shadow h-100">
            <div class="card-header fw-bold d-flex justify-content-between">
                <span><i class="bi bi-receipt"></i> Total Pedidos</span>
                <small>Histórico</small>
            </div>
            <div class="card-body">
                <h2 class="card-title display-4"><?= $total_pedidos ?? 0 ?></h2>
                <p class="card-text">Pedidos realizados en la tienda.</p>
                <a href="<?=base_url?>pedido/gestion" class="btn btn-light btn-sm text-primary fw-bold">Ver todos</a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-8 mb-3">
        <div class="card shadow h-100">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-graph-up"></i> Ventas de la Última Semana
            </div>
            <div class="card-body">
                <canvas id="ventasChart" style="max-height: 350px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-3">
        <div class="card shadow h-100">
            <div class="card-header bg-warning text-dark fw-bold">
                <i class="bi bi-trophy-fill"></i> Top 5 Más Vendidos
            </div>
            <ul class="list-group list-group-flush">
                <?php if(isset($best_sellers) && $best_sellers->num_rows > 0): ?>
                    <?php while($top = $best_sellers->fetch_object()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="<?=Utils::showImage($top->imagen)?>" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                <span class="small fw-bold text-truncate" style="max-width: 150px;"><?=$top->nombre?></span>
                            </div>
                            <span class="badge bg-primary rounded-pill"><?=$top->total_vendido?> un.</span>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li class="list-group-item text-center text-muted">Aún no hay ventas.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-exclamation-triangle-fill"></i> Alerta de Stock Bajo (Menos de 5 unidades)
            </div>
            <div class="card-body">
                <?php if(isset($stock_bajo) && $stock_bajo->num_rows > 0): ?>
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Stock Restante</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($pro = $stock_bajo->fetch_object()): ?>
                                <tr>
                                    <td><?=$pro->id?></td>
                                    <td><?=$pro->nombre?></td>
                                    <td class="text-danger fw-bold"><?=$pro->stock?></td>
                                    <td>
                                        <a href="<?=base_url?>producto/editar?id=<?=$pro->id?>" class="btn btn-sm btn-outline-danger">
                                            Reponer Stock
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-success m-0"><i class="bi bi-check-circle"></i> ¡Todo en orden! No hay productos con stock crítico.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const fechas = <?php echo $fechas_json; ?>;
    const totales = <?php echo $totales_json; ?>;
    const ctx = document.getElementById('ventasChart').getContext('2d');
    if(fechas.length > 0){
        new Chart(ctx, {
            type: 'bar', 
            data: {
                labels: fechas,
                datasets: [{
                    label: 'Ventas',
                    data: totales,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
        });
    } else {
        document.getElementById('ventasChart').parentNode.innerHTML = '<p class="text-center text-muted py-5">No hay ventas recientes.</p>';
    }
</script>