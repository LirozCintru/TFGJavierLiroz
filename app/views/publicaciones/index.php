<?php if (!empty($publicaciones)): ?>
    <?php foreach ($publicaciones as $publicacion): ?>
        <?php
        switch ($publicacion->tipo) {
            case 'Urgente':
                $tipoClase = 'border-danger';
                $badgeClass = 'bg-danger';
                break;
            case 'Departamental':
                $tipoClase = 'border-warning';
                $badgeClass = 'bg-warning text-dark';
                break;
            default:
                $tipoClase = 'border-secondary';
                $badgeClass = 'bg-secondary';
        }

        $contenidoCompleto = nl2br(htmlspecialchars($publicacion->contenido));
        $resumen = mb_strimwidth(strip_tags($contenidoCompleto), 0, 300, '...');
        $comentarios = $publicacion->comentarios ?? [];
        $contador = count($comentarios);
        ?>

        <div class="card mb-4 shadow-sm <?= $tipoClase ?>" id="pub-<?= $publicacion->id_publicacion ?>">
            <?php if (!empty($publicacion->imagen_destacada)): ?>
                <img src="<?= RUTA_URL . '/public/img/publicaciones/' . $publicacion->imagen_destacada ?>" class="card-img-top"
                    alt="Imagen destacada">
            <?php endif; ?>

            <div class="card-body">
                <h5 class="card-title toggle-detalle" style="cursor: pointer;"
                    data-target="detalle-<?= $publicacion->id_publicacion ?>">
                    <?= htmlspecialchars($publicacion->titulo) ?>
                    <span class="badge <?= $badgeClass ?> ms-2"><?= $publicacion->tipo ?></span>
                </h5>

                <p class="card-text text-muted"><?= $resumen ?></p>

                <div class="card-text">
                    <small class="text-muted">
                        Publicado por <?= htmlspecialchars($publicacion->autor) ?> |
                        <?= date('d/m/Y H:i', strtotime($publicacion->fecha)) ?>
                        <?php if ($publicacion->tipo === 'Departamental' && !empty($publicacion->nombre_departamento)): ?>
                            | Departamento: <?= htmlspecialchars($publicacion->nombre_departamento) ?>
                        <?php endif; ?>
                    </small>
                </div>

                <!-- Contenido expandido -->
                <div id="detalle-<?= $publicacion->id_publicacion ?>"
                    class="expandible mt-3 <?= isset($expandir_publicacion) && $expandir_publicacion == $publicacion->id_publicacion ? 'mostrar' : '' ?>">
                    <hr>

                    <div class="mb-3"><?= $contenidoCompleto ?></div>

                    <!-- Imágenes adicionales -->
                    <?php
                    $imagenesAdicionales = (new \PublicacionModelo())->obtenerImagenesPublicacion($publicacion->id_publicacion);
                    if (!empty($imagenesAdicionales)): ?>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <?php foreach ($imagenesAdicionales as $img): ?>
                                <img src="<?= RUTA_URL . '/public/img/publicaciones/' . $img->ruta_imagen ?>" class="img-thumbnail"
                                    style="width: 120px; height: 120px; object-fit: cover;" alt="Imagen adicional">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Comentarios -->
                    <div class="comentarios">
                        <h6>Comentarios (<?= $contador ?>)</h6>

                        <?php if (!empty($comentarios)): ?>
                            <?php foreach ($comentarios as $comentario): ?>
                                <div class="d-flex align-items-start mb-2">
                                    <img src="<?= RUTA_URL . '/public/img/usuarios/' . ($comentario->imagen ?? 'default.png') ?>"
                                        class="rounded-circle me-2" width="40" height="40" alt="img">
                                    <div class="bg-light rounded p-2 w-100">
                                        <strong><?= htmlspecialchars($comentario->nombre) ?></strong>
                                        <small class="text-muted ms-2"><?= date('d/m/Y H:i', strtotime($comentario->fecha)) ?></small>
                                        <p class="mb-1"><?= nl2br(htmlspecialchars($comentario->contenido)) ?></p>

                                        <?php if ($_SESSION['usuario']['id'] == $comentario->id_usuario): ?>
                                            <form
                                                action="<?= RUTA_URL ?>/PublicacionesControlador/eliminarComentario/<?= $comentario->id_comentario ?>"
                                                method="POST" class="d-inline">
                                                <button class="btn btn-sm btn-link text-danger p-0"
                                                    onclick="return confirm('¿Eliminar comentario?')">Eliminar</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No hay comentarios.</p>
                        <?php endif; ?>

                        <form method="POST"
                            action="<?= RUTA_URL ?>/PublicacionesControlador/comentar/<?= $publicacion->id_publicacion ?>">
                            <div class="input-group mt-2">
                                <input type="text" name="contenido" class="form-control" placeholder="Escribe un comentario..."
                                    required>
                                <button type="submit" class="btn btn-outline-primary">Enviar</button>
                            </div>
                        </form>
                    </div>

                    <?php
                    $usuario = $_SESSION['usuario'];
                    if (
                        $usuario['id'] == $publicacion->id_autor &&
                        in_array($usuario['id_rol'], [ROL_ADMIN, ROL_JEFE])
                    ): ?>
                        <div class="mt-3">
                            <a href="<?= RUTA_URL . '/PublicacionesControlador/editar/' . $publicacion->id_publicacion ?>"
                                class="btn btn-sm btn-outline-primary">Editar</a>
                            <a href="<?= RUTA_URL . '/PublicacionesControlador/eliminar/' . $publicacion->id_publicacion ?>"
                                class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('¿Seguro que quieres eliminar esta publicación?');">
                                Eliminar
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">No hay publicaciones disponibles.</div>
<?php endif; ?>