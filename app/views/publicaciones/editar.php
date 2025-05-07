<?php require RUTA_APP . '/views/inc/headerMain.php'; ?>

<?php
$publicacion = $datos['publicacion'];
$imagenes_adicionales = $datos['imagenes_adicionales'];
?>

<div class="container mt-5">
    <h2 class="mb-4">Editar publicación</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" id="titulo"
                value="<?php echo htmlspecialchars($publicacion->titulo); ?>" required>
        </div>

        <div class="mb-3">
            <label for="contenido" class="form-label">Contenido</label>
            <textarea name="contenido" id="contenido" rows="6"
                class="form-control"><?php echo htmlspecialchars($publicacion->contenido); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select name="tipo" id="tipo" class="form-select">
                <option value="General" <?php echo ($publicacion->tipo === 'General') ? 'selected' : ''; ?>>General
                </option>
                <option value="Urgente" <?php echo ($publicacion->tipo === 'Urgente') ? 'selected' : ''; ?>>Urgente
                </option>
                <option value="Departamental" <?php echo ($publicacion->tipo === 'Departamental') ? 'selected' : ''; ?>>
                    Departamental</option>
            </select>
        </div>

        <!-- Imagen destacada actual -->
        <?php if (!empty($publicacion->imagen_destacada)): ?>
            <div class="mb-3">
                <label class="form-label">Imagen destacada actual:</label><br>
                <img src="<?php echo RUTA_URL . '/public/img/publicaciones/' . $publicacion->imagen_destacada; ?>"
                    class="img-thumbnail mb-2" style="width: 200px;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="eliminar_imagen" id="eliminar_imagen">
                    <label class="form-check-label" for="eliminar_imagen">
                        Eliminar imagen destacada
                    </label>
                </div>
            </div>
        <?php endif; ?>

        <!-- Nueva imagen destacada -->
        <div class="mb-3">
            <label for="imagen_destacada" class="form-label">Nueva imagen destacada (opcional)</label>
            <input type="file" name="imagen_destacada" class="form-control" id="imagen_destacada">
        </div>

        <!-- Imágenes adicionales actuales -->
        <?php if (!empty($imagenes_adicionales)): ?>
            <div class="mb-3">
                <label class="form-label">Imágenes adicionales actuales:</label><br>
                <div class="d-flex flex-wrap gap-3">
                    <?php foreach ($imagenes_adicionales as $img): ?>
                        <div>
                            <img src="<?php echo RUTA_URL . '/public/img/publicaciones/' . $img->ruta_imagen; ?>"
                                class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="eliminar_imagenes[]"
                                    value="<?php echo htmlspecialchars($img->ruta_imagen); ?>"
                                    id="img_<?php echo md5($img->ruta_imagen); ?>">
                                <label class="form-check-label" for="img_<?php echo md5($img->ruta_imagen); ?>">
                                    Eliminar
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Añadir nuevas imágenes adicionales -->
        <div class="mb-3">
            <label for="imagenes" class="form-label">Añadir nuevas imágenes adicionales</label>
            <input type="file" name="imagenes[]" id="imagenes" class="form-control" multiple
                onchange="previewNuevasImagenes()">
        </div>

        <!-- Preview de nuevas imágenes seleccionadas -->
        <div class="mb-3" id="previewImagenes" style="display: flex; gap: 10px; flex-wrap: wrap;"></div>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="<?php echo RUTA_URL; ?>/ContenidoControlador/inicio" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    function previewNuevasImagenes() {
        const input = document.getElementById('imagenes');
        const preview = document.getElementById('previewImagenes');
        preview.innerHTML = '';

        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail';
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }
</script>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>