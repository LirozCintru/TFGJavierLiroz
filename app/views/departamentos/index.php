<?php require RUTA_APP . '/views/inc/headerMain.php'; ?>

<div class="container py-4 seccion-departamentos">

    <div class="rounded-4 overflow-hidden shadow border border-2 bg-white">

        <!-- Cabecera -->
        <div class="encabezado-usuarios-index px-4 py-3 d-flex justify-content-between align-items-center">
            <h5 class="titulo-edicion mb-0">
                <i class="bi bi-building me-2"></i>Gestión de departamentos
            </h5>

            <a href="<?= RUTA_URL ?>/DepartamentoControlador/crear"
                class="btn btn-success rounded-pill d-flex align-items-center gap-2 px-3 py-1">
                <i class="bi bi-plus-circle"></i>
                <span>Añadir departamento</span>
            </a>
        </div>

        <!-- Buscador -->
        <form method="GET" class="row g-3 filtros-usuarios align-items-end px-4 pt-4">
            <div class="col-md-6">
                <label class="form-label mb-1">Nombre del departamento</label>
                <input type="text" name="busqueda" value="<?= htmlspecialchars($datos['busqueda'] ?? '') ?>"
                    class="form-control rounded-pill" placeholder="Buscar por nombre">
            </div>
            <div class="col-md-6 d-flex gap-2 justify-content-end">
                <button type="submit" class="btn btn-outline-primary rounded-pill">
                    <i class="bi bi-search me-1"></i> Buscar
                </button>
                <a href="<?= RUTA_URL ?>/DepartamentoControlador/index" class="btn btn-outline-secondary rounded-pill">
                    Limpiar
                </a>
            </div>
        </form>

        <!-- Resultados -->
        <div class="px-4 pb-4 pt-3">
            <?php if (!empty($datos['departamentos'])): ?>
                <div class="table-responsive shadow-sm border rounded">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($datos['departamentos'] as $d): ?>
                                <tr>
                                    <td><?= htmlspecialchars($d->nombre) ?></td>
                                    <td><?= nl2br(htmlspecialchars($d->descripcion)) ?></td>
                                    <td class="text-end">
                                        <a href="<?= RUTA_URL ?>/DepartamentoControlador/editar/<?= $d->id_departamento ?>"
                                            class="btn btn-sm btn-outline-primary me-1">
                                            ✏️ Editar
                                        </a>

                                        <?php if (!empty($d->bloqueado)): ?>
                                            <button class="btn btn-sm btn-outline-secondary" disabled
                                                title="Tiene usuarios asignados">
                                                🛑 No se puede eliminar
                                            </button>
                                        <?php else: ?>
                                            <form method="POST"
                                                action="<?= RUTA_URL ?>/DepartamentoControlador/eliminar/<?= $d->id_departamento ?>"
                                                class="d-inline" onsubmit="return confirm('¿Eliminar este departamento?');">
                                                <button class="btn btn-sm btn-outline-danger">🗑️ Eliminar</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center mt-4">No se encontraron departamentos.</div>
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