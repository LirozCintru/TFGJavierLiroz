<?php require RUTA_APP . '/views/inc/headerMain.php'; ?>
<div class="container py-4">
    <div class="card shadow-lg border-0">
        <div class="card-header d-flex justify-content-between align-items-center bg-white">
            <h4 class="mb-0">🔔 Notificaciones</h4>
            <?php if (!empty($datos['notificaciones'])): ?>
                <div class="d-flex gap-2">
                    <button type="submit" form="form-notificaciones" name="accion" value="marcar"
                        class="btn btn-outline-success btn-sm">
                        ✅ Marcar como leídas
                    </button>
                    <button type="submit" form="form-notificaciones" name="accion" value="eliminar"
                        class="btn btn-outline-danger btn-sm"
                        onclick="return confirm('¿Eliminar las notificaciones seleccionadas?')">
                        🗑️ Eliminar
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-body">
            <?php if (!empty($datos['notificaciones'])): ?>
                <form method="POST" id="form-notificaciones"
                    action="<?= RUTA_URL ?>/NotificacionesControlador/accionesMasivas">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="seleccionar-todas">
                        <label class="form-check-label" for="seleccionar-todas">
                            Seleccionar todas
                        </label>
                    </div>

                    <ul class="list-group">
                        <?php foreach ($datos['notificaciones'] as $n): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start 
                                <?= $n->leida ? '' : 'fw-bold bg-light' ?>">

                                <div class="form-check me-3 pt-1">
                                    <input class="form-check-input seleccionable" type="checkbox" name="seleccionadas[]"
                                        value="<?= $n->id_notificacion ?>">
                                </div>

                                <div class="me-auto">
                                    <!-- Tipo como badge -->
                                    <?php if (!empty($n->tipo)): ?>
                                        <span class="badge bg-secondary me-1"><?= ucfirst($n->tipo) ?></span>
                                    <?php endif; ?>

                                    <!-- Mensaje con enlace si hay id_referencia -->
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

                                    <!-- Fecha -->
                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($n->fecha)) ?></small>
                                </div>

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
                <div class="alert alert-info mb-0">📭 No tienes notificaciones por el momento.</div>
            <?php endif; ?>
        </div>

        <div class="card-footer text-end bg-white">
            <a href="<?= RUTA_URL ?>/ContenidoControlador/seccion/inicio" class="btn btn-outline-primary">
                ← Volver a publicaciones
            </a>
        </div>
    </div>
</div>

<!-- ✅ Script para seleccionar todas -->
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