<?php if (!empty($publicaciones)): ?>
    <?php foreach ($publicaciones as $publicacion): ?>
        <?php
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
        $resumen = mb_strimwidth(strip_tags($contenidoCompleto), 0, 300, '...');
        $comentarios = $publicacion->comentarios ?? [];
        $contador = count($comentarios);
        ?>

        <div class="card publication-card shadow-sm mb-4 <?= $tipoClase ?>" id="pub-<?= $publicacion->id_publicacion ?>">
            <?php if (!empty($publicacion->imagen_destacada)): ?>
                <img src="<?= RUTA_URL . '/public/img/publicaciones/' . $publicacion->imagen_destacada ?>"
                    class="card-img-top publication-img" alt="Imagen destacada">
            <?php endif; ?>

            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-primary mb-1 toggle-detalle"
                            data-target="detalle-<?= $publicacion->id_publicacion ?>" style="cursor: pointer;">
                            <?= htmlspecialchars($publicacion->titulo) ?>
                            <span class="badge <?= $badgeClass ?> ms-2"><?= $publicacion->tipo ?></span>
                            <?php if (!empty($publicacion->evento)): ?>
                                <i class="bi bi-calendar-event text-primary ms-2" title="Tiene evento vinculado"></i>
                            <?php endif; ?>
                            <?php if (!empty($publicacion->nombre_departamento)): ?>
                                <span class="badge bg-light text-dark border ms-2">
                                    <?= htmlspecialchars($publicacion->nombre_departamento) ?>
                                </span>
                            <?php endif; ?>
                        </h5>
                        </h5>
                        <p class="text-muted small mb-2">
                            <?= date('d/m/Y H:i', strtotime($publicacion->fecha)) ?> |
                            <strong><?= htmlspecialchars($publicacion->autor) ?></strong>
                            <?php if ($publicacion->tipo === 'Departamental' && !empty($publicacion->nombre_departamento)): ?>
                                | <em>Depto: <?= htmlspecialchars($publicacion->nombre_departamento) ?></em>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <p class="card-text text-muted"><?= $resumen ?></p>
                <div class="text-end">
                    <button class="btn btn-sm btn-link toggle-detalle text-decoration-none text-primary"
                        data-target="detalle-<?= $publicacion->id_publicacion ?>">
                        <i class="bi bi-chevron-down me-1"></i> Ver más
                    </button>
                </div>

                <div id="detalle-<?= $publicacion->id_publicacion ?>" class="expandible mt-3">
                    <hr>
                    <div class="mb-3"><?= $contenidoCompleto ?></div>

                    <?php if (!empty($publicacion->evento)): ?>
                        <div class="alert alert-info small">
                            <strong>📅 <?= htmlspecialchars($publicacion->evento->titulo) ?></strong><br>
                            <?= nl2br(htmlspecialchars($publicacion->evento->descripcion)) ?><br>
                            <span class="text-muted">Fecha: <?= date('d/m/Y', strtotime($publicacion->evento->fecha)) ?> -
                                <?= substr($publicacion->evento->hora, 0, 5) ?></span>
                        </div>
                    <?php endif; ?>

                    <?php
                    $imagenesAdicionales = (new \PublicacionModelo())->obtenerImagenesPublicacion($publicacion->id_publicacion);
                    if (!empty($imagenesAdicionales)): ?>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <?php foreach ($imagenesAdicionales as $img): ?>
                                <img src="<?= RUTA_URL . '/public/img/publicaciones/' . $img->ruta_imagen ?>" class="img-thumbnail"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="Imagen adicional">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Comentarios -->
                    <div class="comentarios">
                        <h6 class="mb-2">💬 Comentarios (<span class="contador-comentarios"><?= $contador ?></span>)</h6>
                        <div class="comentarios-lista mb-3">
                            <?php if (!empty($comentarios)): ?>
                                <?php foreach ($comentarios as $comentario): ?>
                                    <?php require RUTA_APP . '/views/publicaciones/_comentario.php'; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted fst-italic">No hay comentarios aún.</p>
                            <?php endif; ?>
                        </div>

                        <form class="form-comentario" data-id="<?= $publicacion->id_publicacion ?>"
                            action="<?= RUTA_URL ?>/PublicacionesControlador/comentar/<?= $publicacion->id_publicacion ?>"
                            method="POST">
                            <div class="input-group">
                                <input type="text" name="contenido" class="form-control" placeholder="Escribe un comentario..."
                                    required>
                                <button type="submit" class="btn btn-outline-primary">Enviar</button>
                            </div>
                        </form>
                    </div>

                    <?php
                    $usuario = $_SESSION['usuario'];
                    if (in_array($usuario['id_rol'], [ROL_ADMIN, ROL_JEFE])): ?>
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
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info text-center">No hay publicaciones disponibles.</div>
<?php endif; ?>