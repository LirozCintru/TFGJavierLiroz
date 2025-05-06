<?php require RUTA_APP . '/views/inc/header.php'; ?>

<div class="container mt-4">
    <h3 class="mb-4">Nueva Publicación</h3>

    <form action="<?php echo RUTA_URL; ?>/publicaciones/crear" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" class="form-control" id="titulo" name="titulo" required>
        </div>

        <div class="mb-3">
            <label for="contenido" class="form-label">Contenido</label>
            <textarea class="form-control" id="contenido" name="contenido" rows="5" required></textarea>
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo de publicación</label>
            <select class="form-select" id="tipo" name="tipo" required>
                <option value="General">General</option>
                <option value="Urgente">Urgente</option>
                <option value="Departamental">Departamental</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="imagen_destacada" class="form-label">Imagen destacada (opcional)</label>
            <input type="file" class="form-control" name="imagen_destacada" accept="image/*">
        </div>

        <div class="mb-3">
            <label for="imagenes" class="form-label">Imágenes adicionales (opcional)</label>
            <input type="file" class="form-control" name="imagenes[]" accept="image/*" multiple>
        </div>

        <button type="submit" class="btn btn-success">Publicar</button>
        <a href="<?php echo RUTA_URL; ?>/contenido/inicio" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>
