<?php require RUTA_APP . '/views/inc/headerMain.php'; ?>

<div class="container py-4 seccion-departamentos">

    <div class="card shadow border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 titulo-edicion text-primary">
                <?= $datos['modo'] === 'editar' ? '✏️ Editar departamento' : '➕ Nuevo departamento' ?>
            </h5>
            <a href="<?= RUTA_URL ?>/DepartamentoControlador/index" class="btn btn-outline-secondary rounded-pill">
                ← Volver
            </a>
        </div>

        <div class="card-body">
            <form method="POST" action="">

                <!-- Nombre -->
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del departamento <span
                            class="text-danger">*</span></label>
                    <input type="text" id="nombre" name="nombre"
                        value="<?= htmlspecialchars($datos['datos']['nombre']) ?>"
                        class="form-control rounded-pill <?= !empty($datos['errores']['nombre']) ? 'is-invalid' : '' ?>"
                        required>

                    <?php if (!empty($datos['errores']['nombre'])): ?>
                        <div class="invalid-feedback"><?= $datos['errores']['nombre'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Descripción -->
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción (opcional)</label>
                    <textarea id="descripcion" name="descripcion" class="form-control rounded"
                        rows="3"><?= htmlspecialchars($datos['datos']['descripcion']) ?></textarea>
                </div>

                <!-- Botones -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <?= $datos['modo'] === 'editar' ? 'Guardar cambios' : 'Crear departamento' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>