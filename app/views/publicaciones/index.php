<?php if (!empty($publicaciones)): ?>
    <?php foreach ($publicaciones as $publicacion): ?>
        <div class="card mb-4 shadow-sm">
            <!-- Imagen destacada -->
            <?php if (!empty($publicacion->imagen_destacada)): ?>
                <img src="<?php echo RUTA_URL . '/public/img/publicaciones/' . $publicacion->imagen_destacada; ?>"
                    class="card-img-top" alt="Imagen destacada">
            <?php endif; ?>

            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($publicacion->titulo); ?></h5>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($publicacion->contenido)); ?></p>

                <p class="card-text">
                    <small class="text-muted">
                        Publicado por <?php echo htmlspecialchars($publicacion->autor); ?> |
                        <?php echo date('d/m/Y H:i', strtotime($publicacion->fecha)); ?> |
                        Tipo: <?php echo htmlspecialchars($publicacion->tipo); ?>
                    </small>
                </p>

                <!-- Mostrar departamento si es departamental -->
                <?php if ($publicacion->tipo === 'Departamental' && !empty($publicacion->nombre_departamento)): ?>
                    <p class="card-text">
                        <small class="text-muted">
                            Departamento: <?php echo htmlspecialchars($publicacion->nombre_departamento); ?>
                        </small>
                    </p>
                <?php endif; ?>

                <!-- Imágenes adicionales -->
                <?php
                $modelo = new \PublicacionModelo(); // instanciar modelo directamente
                $imagenesAdicionales = $modelo->obtenerImagenesPublicacion($publicacion->id_publicacion);
                if (!empty($imagenesAdicionales)):
                    ?>
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <?php foreach ($imagenesAdicionales as $img): ?>
                            <img src="<?php echo RUTA_URL . '/public/img/publicaciones/' . $img->ruta_imagen; ?>" class="img-thumbnail"
                                style="width: 120px; height: 120px; object-fit: cover;" alt="Imagen adicional">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Botones solo si es autor y además Admin o Jefe -->
                <?php
                $usuario = $_SESSION['usuario'];
                if (
                    $usuario['id'] == $publicacion->id_autor &&
                    in_array($usuario['id_rol'], [ROL_ADMIN, ROL_JEFE])
                ): ?>
                    <div class="mt-3">
                        <a href="<?php echo RUTA_URL . '/publicaciones/editar/' . $publicacion->id_publicacion; ?>"
                            class="btn btn-sm btn-outline-primary">Editar</a>
                        <a href="<?php echo RUTA_URL . '/publicaciones/eliminar/' . $publicacion->id_publicacion; ?>"
                            class="btn btn-sm btn-outline-danger">Eliminar</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">
        No hay publicaciones disponibles.
    </div>
<?php endif; ?>