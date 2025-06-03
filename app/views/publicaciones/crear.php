<?php
require RUTA_APP . '/views/inc/headermain.php';
$categorias = require RUTA_APP . '/config/categorias_evento.php';
$departamentos = $datos['departamentos'] ?? [];
?>

<section class="container py-4">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <!-- Cabecera -->
        <div class="card-header bg-success text-white d-flex align-items-center">
            <i class="bi bi-pen me-2"></i>
            <h5 class="mb-0">Crear nueva publicación</h5>
        </div>

        <div class="card-body bg-white">
            <form class="needs-validation" novalidate action="<?= RUTA_URL ?>/PublicacionesControlador/crear"
                method="POST" enctype="multipart/form-data">
                <!-- Título -->
                <div class="form-floating mb-3">
                    <input type="text" class="form-control rounded-3" id="titulo" name="titulo" placeholder="Título"
                        required>
                    <label for="titulo">Título</label>
                </div>

                <!-- Contenido -->
                <div class="mb-4">
                    <label for="contenido" class="form-label fw-semibold">Contenido</label>
                    <textarea class="form-control rounded-3" id="contenido" name="contenido" rows="6"
                        required></textarea>
                </div>

                <!-- Tipo & Departamento -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select rounded-3" id="tipo" name="tipo" required>
                                <option value="General">General</option>
                                <option value="Urgente">Urgente</option>
                                <option value="Departamental">Departamental</option>
                            </select>
                            <label for="tipo">Tipo de publicación</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select rounded-3" id="id_departamento" name="id_departamento"
                                <?= ($_SESSION['usuario']['id_rol'] == ROL_ADMIN) ? '' : 'disabled'; ?> required>
                                <?php foreach ($departamentos as $d): ?>
                                    <option value="<?= $d->id_departamento ?>"
                                        <?= $d->id_departamento == $_SESSION['usuario']['id_departamento'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($d->nombre) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="id_departamento">Departamento</label>
                        </div>
                        <?php if ($_SESSION['usuario']['id_rol'] != ROL_ADMIN): ?>
                            <input type="hidden" name="id_departamento"
                                value="<?= $_SESSION['usuario']['id_departamento']; ?>">
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Imagenes -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label for="imagen_destacada" class="form-label">Imagen destacada</label>
                        <input type="file" class="form-control" id="imagen_destacada" name="imagen_destacada"
                            accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label for="imagenes" class="form-label">Imágenes adicionales</label>
                        <input type="file" class="form-control" id="imagenes" name="imagenes[]" accept="image/*"
                            multiple onchange="previewNuevasImagenes()">
                    </div>
                </div>

                <div class="mb-4" id="previewImagenes" style="display:flex;gap:10px;flex-wrap:wrap;"></div>

                <!-- Evento toggle -->
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="toggleEvento" name="activar_evento">
                    <label class="form-check-label" for="toggleEvento">¿Vincular un evento a esta publicación?</label>
                </div>

                <!-- Evento campos -->
                <div id="camposEvento" class="border rounded p-3 bg-light-subtle mb-4" style="display:none;">
                    <h6 class="fw-bold mb-3"><i class="bi bi-calendar-event me-2"></i>Detalles del evento</h6>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="evento_titulo" name="evento_titulo"
                            placeholder="Título del evento">
                        <label for="evento_titulo">Título del evento</label>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-select" name="evento_categoria" id="evento_categoria">
                                    <?php foreach ($categorias as $nombre => $color): ?>
                                        <option value="<?= htmlspecialchars($nombre) ?>" <?= $nombre === 'General' ? 'selected' : '' ?>><?= $nombre ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="evento_categoria">Categoría</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-floating">
                                <input type="url" class="form-control" id="evento_url" name="evento_url"
                                    placeholder="URL asociada">
                                <label for="evento_url">URL asociada (opcional)</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="evento_fecha" name="evento_fecha">
                                <label for="evento_fecha">Fecha inicio</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="time" class="form-control" id="evento_hora" name="evento_hora">
                                <label for="evento_hora">Hora inicio</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="evento_fecha_fin" name="evento_fecha_fin">
                                <label for="evento_fecha_fin">Fecha fin</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="time" class="form-control" id="evento_hora_fin" name="evento_hora_fin">
                                <label for="evento_hora_fin">Hora fin</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="evento_todo_el_dia"
                            name="evento_todo_el_dia">
                        <label class="form-check-label" for="evento_todo_el_dia">Evento de todo el día</label>
                    </div>

                    <div class="mb-3">
                        <label for="evento_descripcion" class="form-label fw-semibold">Descripción</label>
                        <textarea class="form-control" id="evento_descripcion" name="evento_descripcion"
                            rows="3"></textarea>
                    </div>
                </div>

                <!-- CTA -->
                <div class="d-flex justify-content-between">
                    <a href="<?= RUTA_URL ?>/contenidoControlador/inicio"
                        class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success rounded-pill">
                        <i class="bi bi-upload"></i> Publicar
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    document.getElementById('toggleEvento').addEventListener('change', (e) => {
        document.getElementById('camposEvento').style.display = e.target.checked ? 'block' : 'none';
    });

    function previewNuevasImagenes() {
        const input = document.getElementById('imagenes');
        const preview = document.getElementById('previewImagenes');
        preview.innerHTML = '';
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
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