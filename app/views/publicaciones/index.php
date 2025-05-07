<?php if (!empty($publicaciones)): ?>
    <?php foreach ($publicaciones as $publicacion): ?>

        <?php
        // Clase de borde según tipo
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
                break;
        }

        // Preparar resumen del contenido
        $contenidoCompleto = nl2br(htmlspecialchars($publicacion->contenido));
        $limite = 300;
        $tieneMas = mb_strlen(strip_tags($contenidoCompleto)) > $limite;
        $resumen = $tieneMas ? mb_substr($contenidoCompleto, 0, $limite) . '...' : $contenidoCompleto;
        ?>

        <div class="card mb-4 shadow-sm <?= $tipoClase ?>">
            <?php if (!empty($publicacion->imagen_destacada)): ?>
                <img src="<?= RUTA_URL . '/public/img/publicaciones/' . $publicacion->imagen_destacada ?>" class="card-img-top"
                    alt="Imagen destacada">
            <?php endif; ?>

            <div class="card-body">
                <h5 class="card-title d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($publicacion->titulo) ?>
                    <span class="badge <?= $badgeClass ?>">
                        <?= htmlspecialchars($publicacion->tipo) ?>
                    </span>
                </h5>

                <div class="card-text">
                    <div class="contenido-resumen"><?= $resumen ?></div>

                    <?php if ($tieneMas): ?>
                        <button class="btn btn-sm btn-link mt-2 toggle-contenido">Ver más</button>
                        <div class="contenido-completo d-none">
                            <?= $contenidoCompleto ?>
                        </div>
                    <?php endif; ?>
                </div>

                <p class="card-text mt-2">
                    <small class="text-muted">
                        Publicado por <?= htmlspecialchars($publicacion->autor) ?> |
                        <?= date('d/m/Y H:i', strtotime($publicacion->fecha)) ?>
                        <?php if ($publicacion->tipo === 'Departamental' && !empty($publicacion->nombre_departamento)): ?>
                            | Departamento: <?= htmlspecialchars($publicacion->nombre_departamento) ?>
                        <?php endif; ?>
                    </small>
                </p>

                <?php
                $modelo = new \PublicacionModelo();
                $imagenesAdicionales = $modelo->obtenerImagenesPublicacion($publicacion->id_publicacion);
                if (!empty($imagenesAdicionales)): ?>
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <?php foreach ($imagenesAdicionales as $img): ?>
                            <img src="<?= RUTA_URL . '/public/img/publicaciones/' . $img->ruta_imagen ?>" class="img-thumbnail"
                                style="width: 120px; height: 120px; object-fit: cover;" alt="Imagen adicional">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

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

    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">No hay publicaciones disponibles.</div>
<?php endif; ?>