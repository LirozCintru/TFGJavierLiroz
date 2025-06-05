<?php require RUTA_APP . '/views/inc/headermain.php'; ?>

<div class="container py-4">
    <div class="position-relative rounded-4 overflow-hidden shadow tarjeta-editar-usuario border border-2">
        <!-- Franja azul decorativa -->
        <div class="position-absolute top-0 start-0 h-100" style="width: 6px; background-color: #0b5ed7;"></div>

        <!-- Encabezado -->
        <div class="encabezado-edicion px-4 py-3">
            <h4 class="titulo-edicion mb-0">👤 Mi perfil</h4>
        </div>

        <div class="p-4">

            <!-- Éxito -->
            <?php if (!empty($_SESSION['perfil_actualizado'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    ✅ Perfil actualizado correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
                <?php unset($_SESSION['perfil_actualizado']); ?>
            <?php endif; ?>

            <!-- Errores -->
            <?php if (!empty($datos['errores'])): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($datos['errores'] as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <form method="POST" enctype="multipart/form-data" action="<?= RUTA_URL ?>/PerfilControlador/editar">
                <div class="row g-4 align-items-center mb-4">
                    <!-- Columna izquierda -->
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label label-edicion">Nombre</label>
                            <input type="text" name="nombre" class="form-control rounded-pill" required
                                value="<?= htmlspecialchars($datos['valores']['nombre'] ?? $datos['usuario']->nombre) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label label-edicion">Email</label>
                            <input type="email" name="email" class="form-control rounded-pill"
                                value="<?= htmlspecialchars($datos['valores']['email'] ?? $datos['usuario']->email) ?>">
                        </div>
                    </div>

                    <!-- Columna derecha - Imagen -->
                    <div class="col-md-4 text-center">
                        <img src="<?= RUTA_URL ?>/public/img/usuarios/<?= $datos['usuario']->imagen ?>" width="96"
                            height="96" class="rounded-circle border mb-2 shadow-sm">
                        <div>
                            <input type="file" name="imagen" accept="image/*"
                                class="form-control form-control-sm rounded-pill mt-2">
                            <small class="text-muted">Cambiar imagen</small>
                        </div>
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="mb-4">
                    <label class="form-label label-edicion">Nueva contraseña</label>
                    <input type="password" name="contrasena" class="form-control rounded-pill"
                        placeholder="password">
                </div>

                <!-- Botón -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>