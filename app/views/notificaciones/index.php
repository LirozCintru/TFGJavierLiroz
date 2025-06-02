<?php require RUTA_APP . '/views/inc/headerMain.php'; ?>

<div class="container py-4 seccion-notificaciones">

    <div class="rounded-4 overflow-hidden shadow border border-2 bg-white">

        <!-- Cabecera con franja -->
        <div class="encabezado-edicion px-4 py-3 d-flex justify-content-between align-items-center">
            <h5 class="titulo-edicion mb-0">
                <i class="bi bi-bell-fill me-2"></i>Notificaciones
            </h5>

            <?php if (!empty($datos['notificaciones'])): ?>
                <div class="d-flex gap-2">
                    <button type="submit" form="form-notificaciones" name="accion" value="marcar"
                        class="btn btn-success rounded-pill btn-sm px-3">
                        ✅ Marcar leídas
                    </button>
                    <button type="submit" form="form-notificaciones" name="accion" value="eliminar"
                        class="btn btn-danger rounded-pill btn-sm px-3"
                        onclick="return confirm('¿Eliminar las notificaciones seleccionadas?')">
                        🗑️ Eliminar
                    </button>
                </div>
            <?php endif; ?>
        </div>


        <div class="px-4 pt-3 pb-4">
            <?php if (!empty($datos['notificaciones'])): ?>
                <form method="POST" id="form-notificaciones"
                    action="<?= RUTA_URL ?>/NotificacionesControlador/accionesMasivas">

                    <!-- Seleccionar todas -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="seleccionar-todas">
                        <label class="form-check-label" for="seleccionar-todas">
                            Seleccionar todas
                        </label>
                    </div>

                    <!-- Lista -->
                    <ul class="list-group list-group-flush">

                        <?php foreach ($datos['notificaciones'] as $n): ?>
                            <li class="list-group-item shadow-sm mb-2 rounded border border-light
                              d-flex justify-content-between align-items-start
                                 <?= $n->leida ? '' : 'fw-bold bg-light' ?>" style="transition: background 0.2s;">
                                <!-- Checkbox -->
                                <div class="form-check me-3 pt-1">
                                    <input class="form-check-input seleccionable" type="checkbox" name="seleccionadas[]"
                                        value="<?= $n->id_notificacion ?>">
                                </div>

                                <!-- Contenido -->
                                <div class="me-auto">
                                    <?php if (!empty($n->tipo)): ?>
                                        <span class="badge bg-secondary me-1"><?= ucfirst($n->tipo) ?></span>
                                    <?php endif; ?>

                                    <div class="mt-1">
                                        <?php if (!empty($n->id_referencia)): ?>
                                            <a href="<?= RUTA_URL ?>/ContenidoControlador/verPublicacion/<?= $n->id_referencia ?>"
                                                class="text-decoration-none <?= $n->leida ? 'text-secondary' : 'text-dark fw-bold' ?>">
                                                <?= htmlspecialchars($n->mensaje) ?>
                                            </a>
                                        <?php else: ?>
                                            <?= htmlspecialchars($n->mensaje) ?>
                                        <?php endif; ?>
                                    </div>

                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i', strtotime($n->fecha)) ?>
                                    </small>
                                </div>

                                <!-- Botones -->
                                <div class="ms-3 d-flex gap-2 pt-1">
                                    <?php if (!$n->leida): ?>
                                        <form method="POST"
                                            action="<?= RUTA_URL ?>/NotificacionesControlador/marcarLeida/<?= $n->id_notificacion ?>">
                                            <button class="btn btn-sm btn-outline-success" title="Marcar como leída">
                                                <i class="bi bi-check2-circle"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <form method="POST"
                                        action="<?= RUTA_URL ?>/NotificacionesControlador/eliminar/<?= $n->id_notificacion ?>"
                                        onsubmit="return confirm('¿Eliminar esta notificación?')">
                                        <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </form>
            <?php else: ?>
                <div class="alert alert-info mb-0 text-center">📭 No tienes notificaciones por el momento.</div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Pie -->
    <div class="text-end mt-4">
        <a href="<?= RUTA_URL ?>/ContenidoControlador/seccion/inicio" class="btn btn-outline-primary rounded-pill">
            ← Volver a publicaciones
        </a>
    </div>

</div>

<style>
    .list-group-item {
        transition: background-color 0.2s ease, box-shadow 0.2s ease;
    }

    .list-group-item:hover {
        background-color: #f0f4f8 !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        cursor: pointer;
    }
</style>



<!-- Script seleccionar todas -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const seleccionarTodas = document.getElementById("seleccionar-todas");
        const checkboxes = document.querySelectorAll('.seleccionable');

        if (seleccionarTodas) {
            seleccionarTodas.addEventListener("change", () => {
                checkboxes.forEach(c => c.checked = seleccionarTodas.checked);
            });
        }
    });
</script>