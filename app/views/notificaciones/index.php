<?php require RUTA_APP . '/views/inc/headerMain.php'; ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">🔔 Tus notificaciones</h4>
        <?php if (!empty($notificaciones)): ?>
            <form method="POST" action="<?= RUTA_URL ?>/NotificacionesControlador/marcarTodasLeidas">
                <button type="submit" class="btn btn-sm btn-outline-success">
                    ✅ Marcar todas como leídas
                </button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (!empty($notificaciones)): ?>
        <ul class="list-group shadow-sm">
            <?php foreach ($notificaciones as $n): ?>
                <li
                    class="list-group-item d-flex justify-content-between align-items-start <?= $n->leida ? '' : 'fw-bold bg-light' ?>">
                    <div class="me-auto">
                        <?= htmlspecialchars($n->mensaje) ?><br>
                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($n->fecha)) ?></small>
                    </div>

                    <div class="ms-3 d-flex gap-2">
                        <?php if (!$n->leida): ?>
                            <form method="POST" action="<?= RUTA_URL ?>/NotificacionesControlador/marcarLeida/<?= $n->id ?>">
                                <button class="btn btn-sm btn-outline-success" title="Marcar como leída">
                                    <i class="bi bi-check2-circle"></i>
                                </button>
                            </form>
                        <?php endif; ?>

                        <form method="POST" action="<?= RUTA_URL ?>/NotificacionesControlador/eliminar/<?= $n->id ?>"
                            onsubmit="return confirm('¿Eliminar esta notificación?')">
                            <button class="btn btn-sm btn-outline-danger" title="Eliminar notificación">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-info">No tienes notificaciones por el momento.</div>
    <?php endif; ?>

    <div class="mt-4 text-end">
        <a href="<?= RUTA_URL ?>/ContenidoControlador/seccion/inicio" class="btn btn-outline-primary">
            ← Volver a publicaciones
        </a>
    </div>
</div>