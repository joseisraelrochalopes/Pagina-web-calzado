<h1 class="mb-4">Bandeja de Entrada</h1>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Remitente</th>
                        <th>Asunto</th>
                        <th>Mensaje</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($msg = $mensajes->fetch_object()): ?>
                        <tr class="<?= $msg->estado == 'pendiente' ? 'table-warning' : '' ?>">
                            <td style="white-space: nowrap;"><?=$msg->fecha?></td>
                            <td>
                                <strong><?=$msg->nombre?></strong><br>
                                <small class="text-muted"><?=$msg->email?></small>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?=$msg->asunto?></span>
                            </td>
                            <td><?=$msg->mensaje?></td>
                            <td>
                                <?php if($msg->estado == 'pendiente'): ?>
                                    <span class="badge bg-danger">No leído</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Leído</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($msg->estado == 'pendiente'): ?>
                                    <a href="<?=base_url?>contacto/leer?id=<?=$msg->id?>" class="btn btn-sm btn-outline-success" title="Marcar como atendido">
                                        <i class="bi bi-check-lg"></i> Atender
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-light" disabled><i class="bi bi-check2-all"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>