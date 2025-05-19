<?php require RUTA_APP . '/views/inc/header.php'; ?>

<div class="container py-4">
    <div class="card border-0 shadow-lg">
        <div class="card-body">
            <h2 class="mb-4 text-primary fw-bold">
                ✍️ Crear nueva publicación
            </h2>

            <form action="<?php echo RUTA_URL; ?>/PublicacionesControlador/crear" method="POST"
                enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título</label>
                    <input type="text" class="form-control shadow-sm" id="titulo" name="titulo" required>
                </div>

                <div class="mb-3">
                    <label for="contenido" class="form-label">Contenido</label>
                    <textarea class="form-control shadow-sm" id="contenido" name="contenido" rows="5"
                        required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tipo" class="form-label">Tipo de publicación</label>
                        <select class="form-select shadow-sm" id="tipo" name="tipo" required>
                            <option value="General">General</option>
                            <option value="Urgente">Urgente</option>
                            <option value="Departamental">Departamental</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="imagen_destacada" class="form-label">Imagen destacada</label>
                        <input type="file" class="form-control shadow-sm" name="imagen_destacada" accept="image/*">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="imagenes" class="form-label">Imágenes adicionales</label>
                    <input type="file" class="form-control shadow-sm" name="imagenes[]" accept="image/*" multiple>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="toggleEvento" name="activar_evento">
                    <label class="form-check-label" for="toggleEvento">¿Vincular un evento a esta publicación?</label>
                </div>

                <div id="camposEvento" class="border rounded p-3 bg-white shadow-sm mb-4" style="display: none;">
                    <h5 class="text-dark border-bottom pb-2 mb-3">
                        📅 Detalles del evento
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="evento_titulo" class="form-label">Título del evento</label>
                            <input type="text" class="form-control shadow-sm" id="evento_titulo" name="evento_titulo">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="evento_fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control shadow-sm" id="evento_fecha" name="evento_fecha">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="evento_hora" class="form-label">Hora</label>
                            <input type="time" class="form-control shadow-sm" id="evento_hora" name="evento_hora">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="evento_descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control shadow-sm" id="evento_descripcion" name="evento_descripcion"
                                rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-outline-success">
                        <i class="bi bi-upload"></i> Publicar
                    </button>
                    <a href="<?php echo RUTA_URL; ?>/contenidoControlador/inicio" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('toggleEvento').addEventListener('change', function () {
        document.getElementById('camposEvento').style.display = this.checked ? 'block' : 'none';
    });
</script>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>