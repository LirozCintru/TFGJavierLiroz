<?php require RUTA_APP . '/views/inc/headermain.php'; ?>

<div class="container py-4 seccion-departamentos">

    <!-- Tarjeta principal -->
    <div class="rounded-4 overflow-hidden shadow border border-2 bg-white">

        <!-- Cabecera con franja azul específica -->
        <div class="encabezado-usuarios-index px-4 py-3 d-flex justify-content-between align-items-center">
            <h5 class="titulo-edicion mb-0">
                <i class="bi bi-people-fill me-2"></i>Gestión de usuarios
            </h5>

            <a href="<?= RUTA_URL ?>/UsuariosControlador/crear"
                class="btn btn-success rounded-pill d-flex align-items-center gap-2 px-3 py-1">
                <i class="bi bi-plus-circle"></i>
                <span>Añadir usuario</span>
            </a>
        </div>
        <!-- Filtros -->
        <form method="GET" class="row g-3 filtros-usuarios align-items-end px-4 pt-4">
            <div class="col-md-4">
                <label class="form-label mb-1">Nombre o email</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($datos['filtro_nombre'] ?? '') ?>"
                    class="form-control rounded-pill" placeholder="Buscar por nombre o email">
            </div>

            <div class="col-md-4">
                <label class="form-label mb-1">Departamento</label>
                <select name="departamento" class="form-select rounded-pill">
                    <option value="">Todos los departamentos</option>
                    <?php foreach ($datos['departamentos'] as $dep): ?>
                        <option value="<?= $dep->id_departamento ?>" <?= ($datos['filtro_departamento'] ?? '') == $dep->id_departamento ? 'selected' : '' ?>>
                            <?= $dep->nombre ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4 d-flex gap-2 justify-content-end">
                <button type="submit" class="btn btn-outline-primary rounded-pill">
                    <i class="bi bi-search me-1"></i> Buscar
                </button>
                <a href="<?= RUTA_URL ?>/UsuariosControlador/index" class="btn btn-outline-secondary rounded-pill">
                    Limpiar
                </a>
            </div>
        </form>

        <!-- Listado -->
        <div class="listado-usuarios px-4 pb-4 pt-3">
            <?php if (!empty($datos['usuarios'])): ?>
                <div class="row g-3">
                    <?php foreach ($datos['usuarios'] as $u): ?>
                        <div class="col-md-6">
                            <div class="card-usuario d-flex align-items-center gap-3 p-3 shadow-sm rounded border">

                                <img src="<?= RUTA_URL ?>/public/img/usuarios/<?= $u->imagen ?>" width="64" height="64"
                                    class="rounded-circle border">

                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?= htmlspecialchars($u->nombre) ?></h6>
                                    <p class="mb-1 text-muted small"><?= htmlspecialchars($u->email) ?></p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-secondary"><?= $u->nombre_rol ?></span>
                                        <span class="badge bg-light text-dark border"><?= $u->nombre_departamento ?></span>
                                        <span class="badge <?= $u->activo ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $u->activo ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <a href="<?= RUTA_URL ?>/UsuariosControlador/editar/<?= $u->id_usuario ?>"
                                        class="btn btn-sm btn-outline-primary me-2" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST"
                                        action="<?= RUTA_URL ?>/UsuariosControlador/eliminar/<?= $u->id_usuario ?>"
                                        class="d-inline" onsubmit="return confirm('¿Eliminar este usuario?');">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center mt-4">No se encontraron usuarios.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Volver -->
    <div class="text-end mt-4">
        <a href="<?= RUTA_URL ?>/ContenidoControlador/seccion/inicio" class="btn btn-outline-primary rounded-pill">
            ← Volver a publicaciones
        </a>
    </div>
</div>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>