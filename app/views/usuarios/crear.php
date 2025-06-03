<?php require RUTA_APP . '/views/inc/headermain.php'; ?>

<div class="container py-4">
    <div class="position-relative rounded-4 overflow-hidden shadow tarjeta-editar-usuario border border-2">

        <!-- Franja azul decorativa -->
        <!-- <div class="position-absolute top-0 start-0 h-100" style="width: 6px; background-color: #0b5ed7;"></div> -->

        <!-- Cabecera -->
        <div class="encabezado-edicion  bg-success text-white px-4 py-3">
            <h5 class="titulo-edicion text-white"><i class="bi bi-person-plus-fill me-2"></i>Crear usuario</h5>
        </div>

        <div class="p-4 bg-white">
            <?php if (!empty($datos['errores'])): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($datos['errores'] as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" action="<?= RUTA_URL ?>/UsuariosControlador/crear">

                <!-- Nombre, Email, Imagen -->
                <div class="row g-4 mb-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label label-edicion">Nombre</label>
                            <input type="text" name="nombre" class="form-control rounded-pill" required
                                value="<?= htmlspecialchars($datos['valores']['nombre'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label label-edicion">Email</label>
                            <input type="email" name="email" class="form-control rounded-pill" required
                                value="<?= htmlspecialchars($datos['valores']['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="col-md-4 text-center">
                        <label class="form-label label-edicion d-block">Imagen de perfil</label>
                        <input type="file" name="imagen" accept="image/*" class="form-control form-control-sm mt-2">
                        <small class="text-muted">Opcional</small>
                    </div>
                </div>

                <!-- Contraseña, Rol, Departamento -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label class="form-label label-edicion">Contraseña</label>
                        <input type="password" name="contrasena" class="form-control rounded-pill" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label label-edicion">Rol</label>
                        <select name="id_rol" class="form-select rounded-pill" required>
                            <?php foreach ($datos['roles'] as $rol): ?>
                                <option value="<?= $rol->id_rol ?>" <?= (isset($datos['valores']['id_rol']) && $datos['valores']['id_rol'] == $rol->id_rol) ? 'selected' : '' ?>>
                                    <?= $rol->nombre_rol ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label label-edicion">Departamento</label>
                        <select name="id_departamento" class="form-select rounded-pill" required>
                            <?php foreach ($datos['departamentos'] as $dep): ?>
                                <option value="<?= $dep->id_departamento ?>" <?= (isset($datos['valores']['id_departamento']) && $datos['valores']['id_departamento'] == $dep->id_departamento) ? 'selected' : '' ?>>
                                    <?= $dep->nombre ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Usuario activo -->
                <div class="switch-activo form-switch mb-4">
                    <label class="form-check-label label-edicion" for="activo">Usuario activo</label>
                    <input class="form-check-input ms-2" type="checkbox" role="switch" name="activo" id="activo"
                        <?= (!isset($datos['valores']) || (isset($datos['valores']['activo']) && $datos['valores']['activo'])) ? 'checked' : '' ?>>
                </div>

                <!-- Botones -->
                <div class="d-flex justify-content-between">
                    <a href="<?= RUTA_URL ?>/UsuariosControlador/index" class="btn btn-outline-secondary rounded-pill">←
                        Cancelar</a>
                    <button type="submit" class="btn btn-success rounded-pill">Crear usuario</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Volver -->
    <div class="mt-4 text-end">
        <a href="<?= RUTA_URL ?>/ContenidoControlador/seccion/inicio" class="btn btn-outline-primary rounded-pill">
            ← Volver a publicaciones
        </a>
    </div>
</div>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>