<div class="d-flex align-items-start mb-2 comentario-item" data-id="<?= $comentario->id_comentario ?>">
    <img src="<?= RUTA_URL . '/public/img/usuarios/' . ($comentario->imagen ?? 'default.png') ?>"
        class="rounded-circle me-2" width="40" height="40" alt="img">

    <div class="bg-light rounded p-2 w-100">
        <strong><?= htmlspecialchars($comentario->nombre) ?></strong>
        <small class="text-muted ms-2"><?= date('d/m/Y H:i', strtotime($comentario->fecha)) ?></small>
        <p class="mb-1"><?= nl2br(htmlspecialchars($comentario->contenido)) ?></p>

        <?php if ($_SESSION['usuario']['id'] == $comentario->id_usuario): ?>
            <form method="POST" class="form-eliminar-comentario d-inline" data-id="<?= $comentario->id_comentario ?>"
                action="<?= RUTA_URL ?>/PublicacionesControlador/eliminarComentario/<?= $comentario->id_comentario ?>">
                <button type="submit" class="btn btn-sm btn-link text-danger p-0 btn-eliminar-comentario"
                    data-id="<?= $comentario->id_comentario ?>">
                    Eliminar
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>