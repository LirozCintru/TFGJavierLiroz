<?php
require RUTA_APP . '/views/inc/headermain.php';
/* ======================= */
/* Sacamos los datos       */
/* ======================= */
$publicacion = $datos['publicacion'] ?? null;
$comentarios = $datos['comentarios'] ?? [];
$evento = $datos['evento'] ?? null;

if (!$publicacion) {
    echo '<div class="alert alert-danger">No se ha encontrado la publicación.</div>';
    return;
}

/* ----------------------- */
/* Clases por tipo         */
/* ----------------------- */
switch ($publicacion->tipo) {
    case 'Urgente':
        $tipoClase = 'border-start border-danger';
        $badgeClass = 'bg-danger text-white';
        break;
    case 'Departamental':
        $tipoClase = 'border-start border-warning';
        $badgeClass = 'bg-warning text-dark';
        break;
    default:
        $tipoClase = 'border-start border-secondary';
        $badgeClass = 'bg-secondary text-white';
}

$contenidoCompleto = nl2br(htmlspecialchars($publicacion->contenido));
$contador = count($comentarios);
?>

<div class="container py-4">
    <div class="card publication-card shadow-sm <?= $tipoClase ?>">
        <?php if (!empty($publicacion->imagen_destacada)): ?>
            <img src="<?= RUTA_URL . '/public/img/publicaciones/' . $publicacion->imagen_destacada ?>"
                class="card-img-top publication-img" alt="Imagen destacada">
        <?php endif; ?>

        <div class="card-body">
            <h5 class="card-title text-primary mb-1">
                <?= htmlspecialchars($publicacion->titulo) ?>
                <span class="badge <?= $badgeClass ?> ms-2"><?= $publicacion->tipo ?></span>
                <?php if ($evento): ?>
                    <i class="bi bi-calendar-event text-primary ms-2" title="Tiene evento vinculado"></i>
                <?php endif; ?>
            </h5>

            <p class="text-muted small mb-2">
                <?= date('d/m/Y H:i', strtotime($publicacion->fecha)) ?> |
                <strong><?= htmlspecialchars($publicacion->autor) ?></strong>
                <?php if ($publicacion->tipo === 'Departamental' && !empty($publicacion->nombre_departamento)): ?>
                    | <em>Depto: <?= htmlspecialchars($publicacion->nombre_departamento) ?></em>
                <?php endif; ?>
            </p>

            <div class="mb-3"><?= $contenidoCompleto ?></div>

            <?php if ($evento): ?>
                <div class="alert alert-info small">
                    <strong>📅 <?= htmlspecialchars($evento->titulo) ?></strong><br>
                    <?= nl2br(htmlspecialchars($evento->descripcion)) ?><br>
                    <span class="text-muted">
                        Fecha: <?= date('d/m/Y', strtotime($evento->fecha)) ?> -
                        <?= substr($evento->hora, 0, 5) ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php
            $imagenesAdicionales = (new \PublicacionModelo())
                ->obtenerImagenesPublicacion($publicacion->id_publicacion);
            if ($imagenesAdicionales): ?>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php foreach ($imagenesAdicionales as $img): ?>
                        <img src="<?= RUTA_URL . '/public/img/publicaciones/' . $img->ruta_imagen ?>" class="img-thumbnail"
                            style="width:100px;height:100px;object-fit:cover;" alt="Imagen adicional">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Comentarios -->
            <div class="comentarios">
                <h6 class="mb-2">💬 Comentarios (<span class="contador-comentarios"><?= $contador ?></span>)</h6>

                <div class="comentarios-lista mb-3">
                    <?php if ($comentarios): ?>
                        <?php foreach ($comentarios as $comentario): ?>
                            <?php require RUTA_APP . '/views/publicaciones/_comentario.php'; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted fst-italic">No hay comentarios aún.</p>
                    <?php endif; ?>
                </div>

                <form class="form-comentario"
                    action="<?= RUTA_URL ?>/PublicacionesControlador/comentar/<?= $publicacion->id_publicacion ?>"
                    method="POST">
                    <div class="input-group">
                        <input type="text" name="contenido" class="form-control" placeholder="Escribe un comentario..."
                            required>
                        <button class="btn btn-outline-primary">Enviar</button>
                    </div>
                </form>
            </div>

            <?php
            $usuario = $_SESSION['usuario'];
            if (
                $usuario['id'] == $publicacion->id_autor ||
                in_array($usuario['id_rol'], [ROL_ADMIN, ROL_JEFE])
            ): ?>
                <div class="mt-3 text-end">
                    <a href="<?= RUTA_URL ?>/PublicacionesControlador/editar/<?= $publicacion->id_publicacion ?>"
                        class="btn btn-sm btn-outline-primary me-2">Editar</a>
                    <a href="<?= RUTA_URL ?>/PublicacionesControlador/eliminar/<?= $publicacion->id_publicacion ?>"
                        class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('¿Seguro que quieres eliminar esta publicación?');">Eliminar</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-4 text-end">
        <a href="<?= RUTA_URL ?>/ContenidoControlador/inicio" class="btn btn-outline-secondary">← Volver</a>
    </div>
</div>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>