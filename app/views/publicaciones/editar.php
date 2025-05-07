<?php require RUTA_APP . '/views/inc/headerMain.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4">Editar publicación</h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" class="form-control" name="titulo" required
                value="<?php echo htmlspecialchars($publicacion->titulo); ?>">
        </div>

        <div class="mb-3">
            <label for="contenido" class="form-label">Contenido</label>
            <textarea name="contenido" rows="5"
                class="form-control"><?php echo htmlspecialchars($publicacion->contenido); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select name="tipo" class="form-select" required>
                <option value="General" <?php echo $publicacion->tipo === 'General' ? 'selected' : ''; ?>>General</option>
                <option value="Urgente" <?php echo $publicacion->tipo === 'Urgente' ? 'selected' : ''; ?>>Urgente</option>
                <option value="Departamental" <?php echo $publicacion->tipo === 'Departamental' ? 'selected' : ''; ?>>
                    Departamental</option>
            </select>
        </div>

        <!-- Imagen destacada actual -->
        <?php if (!empty($publicacion->imagen_destacada)): ?>
            <div class="mb-3">
                <label class="form-label fw-bold">Imagen destacada actual:</label><br>
                <img src="<?php echo RUTA_URL . '/public/img/publicaciones/' . $publicacion->imagen_destacada; ?>"
                    class="img-thumbnail mb-2" style="width: 150px; height: auto;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="eliminar_imagen" id="eliminar_imagen">
                    <label class="form-check-label" for="eliminar_imagen">Eliminar imagen destacada</label>
                </div>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="imagen_destacada" class="form-label">Nueva imagen destacada (opcional)</label>
            <input type="file" class="form-control" name="imagen_destacada" accept="image/*">
        </div>

        <!-- Imágenes adicionales -->
        <?php if (!empty($imagenes_adicionales)): ?>
            <div class="mb-3">
                <label class="form-label fw-bold">Imágenes adicionales actuales:</label><br>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($imagenes_adicionales as $img): ?>
                        <div class="position-relative">
                            <img src="<?php echo RUTA_URL . '/public/img/publicaciones/' . $img->ruta_imagen; ?>"
                                class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                            <div class="form-check text-center mt-1">
                                <input type="checkbox" class="form-check-input" name="eliminar_imagenes[]"
                                    value="<?php echo $img->ruta_imagen; ?>">
                                <label class="form-check-label small">Eliminar</label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="imagenes" class="form-label">Añadir nuevas imágenes adicionales</label>
            <input type="file" class="form-control" name="imagenes[]" multiple accept="image/*">
        </div>

        <button type="submit" class="btn btn-success">Guardar cambios</button>
        <a href="<?php echo RUTA_URL . '/ContenidoControlador/inicio'; ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>