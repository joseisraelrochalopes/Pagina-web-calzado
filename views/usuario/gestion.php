<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Gestión de Usuarios</h1>
    <a href="<?=base_url?>usuario/registro" class="btn btn-primary">
        <i class="bi bi-person-plus-fill"></i> Crear Usuario
    </a>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Avatar</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol Actual</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $usuarios->fetch_object()): ?>
                        <tr>
                            <td><?=$user->id?></td>
                            <td>
                                <?php 
                                    $avatar = 'assets/img/users/' . $user->imagen;
                                    if(!empty($user->imagen) && file_exists($avatar)){
                                        $img_url = base_url . $avatar;
                                    } else {
                                        $img_url = "https://via.placeholder.com/40?text=U"; 
                                    }
                                ?>
                                <img src="<?=$img_url?>" class="rounded-circle border" width="40" height="40" style="object-fit: cover;">
                            </td>
                            <td><?=$user->nombre?> <?=$user->apellidos?></td>
                            <td><?=$user->email?></td>
                            <td>
                                <?php if($user->rol == 'admin'): ?>
                                    <span class="badge bg-danger">ADMINISTRADOR</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">CLIENTE</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <?php if($user->id != $_SESSION['identity']->id): ?>
                                        
                                        <a href="<?=base_url?>usuario/verFavoritosAdmin&id=<?=$user->id?>" class="btn btn-sm btn-warning" title="Ver Favoritos del Cliente">
                                            <i class="bi bi-heart-fill"></i>
                                        </a>

                                        <?php if($user->rol == 'user'): ?>
                                            <a href="<?=base_url?>usuario/rol?id=<?=$user->id?>&rol=admin" class="btn btn-sm btn-outline-danger" title="Ascender a Admin" onclick="return confirm('¿Seguro que quieres hacer ADMIN a este usuario?');">
                                                <i class="bi bi-arrow-up-circle"></i> Ascender
                                            </a>
                                        <?php else: ?>
                                            <a href="<?=base_url?>usuario/rol?id=<?=$user->id?>&rol=user" class="btn btn-sm btn-outline-secondary" title="Degradar a Cliente">
                                                <i class="bi bi-arrow-down-circle"></i> Degradar
                                            </a>
                                        <?php endif; ?>

                                    <?php else: ?>
                                        <span class="text-muted small italic">Tu usuario (Sesión activa)</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>