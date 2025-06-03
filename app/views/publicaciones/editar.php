<?php
require RUTA_APP . '/views/inc/headerMain.php';
$categorias = require RUTA_APP . '/config/categorias_evento.php';
$publicacion = $datos['publicacion'];
$imagenes_adicionales = $datos['imagenes_adicionales'];
$departamentos = $datos['departamentos'] ?? [];
?>

<section class="container py-4">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <!-- Cabecera -->
        <!-- <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="bi bi-pencil-square me-2"></i>
            <h5 class="mb-0">Editar publicación</h5>
        </div> -->
        <!-- <div class="card-header bg-primary text-white d-flex align-items-center">
        </div> -->

        <!-- Franja azul decorativa -->
        <div class="position-absolute top-0 start-0 h-100" style="width: 6px; background-color: #0b5ed7;"></div>


        <div class="encabezado-edicion text-white px-4 py-3">
            <h5 class="titulo-edicion"> <i class="bi bi-pencil-square me-2"></i></i>Editar publicación</h5>
        </div>

        <div class="card-body bg-white">
            <form class="needs-validation" novalidate method="POST" enctype="multipart/form-data">
                <!-- Título -->
                <div class="form-floating mb-3">
                    <input type="text" name="titulo" id="titulo" class="form-control rounded-3" placeholder="Título"
                        value="<?= htmlspecialchars($publicacion->titulo); ?>" required>
                    <label for="titulo">Título</label>
                </div>

                <!-- Contenido -->
                <div class="mb-4">
                    <label for="contenido" class="form-label fw-semibold">Contenido</label>
                    <textarea name="contenido" id="contenido" rows="6" class="form-control rounded-3"
                        required><?= htmlspecialchars($publicacion->contenido); ?></textarea>
                </div>

                <!-- Tipo & Departamento -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="tipo" id="tipo" class="form-select rounded-3" required>
                                <option value="General" <?= $publicacion->tipo === 'General' ? 'selected' : ''; ?>>General
                                </option>
                                <option value="Urgente" <?= $publicacion->tipo === 'Urgente' ? 'selected' : ''; ?>>Urgente
                                </option>
                                <option value="Departamental" <?= $publicacion->tipo === 'Departamental' ? 'selected' : ''; ?>>Departamental</option>
                            </select>
                            <label for="tipo">Tipo</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="id_departamento" id="id_departamento" class="form-select rounded-3" required
                                <?= ($_SESSION['usuario']['id_rol'] == ROL_ADMIN) ? '' : 'disabled'; ?>>
                                <?php foreach ($departamentos as $d): ?>
                                    <option value="<?= $d->id_departamento ?>"
                                        <?= $d->id_departamento == $publicacion->id_departamento ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($d->nombre) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="id_departamento">Departamento</label>
                        </div>
                        <?php if ($_SESSION['usuario']['id_rol'] != ROL_ADMIN): ?>
                            <input type="hidden" name="id_departamento" value="<?= $publicacion->id_departamento; ?>">
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Imagen destacada -->
                <div class="mb-4">
                    <?php if (!empty($publicacion->imagen_destacada)): ?>
                        <label class="form-label fw-semibold d-block">Imagen destacada actual</label>
                        <img src="<?= RUTA_URL . '/public/img/publicaciones/' . $publicacion->imagen_destacada; ?>"
                            class="img-thumbnail mb-2" style="width:200px;max-height:140px;object-fit:cover;">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="eliminar_imagen" id="eliminar_imagen">
                            <label class="form-check-label" for="eliminar_imagen">Eliminar imagen destacada</label>
                        </div>
                    <?php endif; ?>
                    <label for="imagen_destacada" class="form-label">Nueva imagen destacada (opcional)</label>
                    <input type="file" name="imagen_destacada" id="imagen_destacada" accept="image/*"
                        class="form-control">
                </div>

                <!-- Imágenes adicionales -->
                <?php if (!empty($imagenes_adicionales)): ?>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Imágenes adicionales actuales</label>
                        <div class="d-flex flex-wrap gap-3">
                            <?php foreach ($imagenes_adicionales as $img): ?>
                                <div class="text-center">
                                    <img src="<?= RUTA_URL . '/public/img/publicaciones/' . $img->ruta_imagen; ?>"
                                        class="img-thumbnail mb-1" style="width:120px;height:120px;object-fit:cover;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="eliminar_imagenes[]"
                                            value="<?= htmlspecialchars($img->ruta_imagen); ?>"
                                            id="img_<?= md5($img->ruta_imagen); ?>">
                                        <label class="form-check-label"
                                            for="img_<?= md5($img->ruta_imagen); ?>">Eliminar</label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <label for="imagenes" class="form-label">Añadir nuevas imágenes adicionales</label>
                    <input type="file" name="imagenes[]" id="imagenes" class="form-control" accept="image/*" multiple
                        onchange="previewNuevasImagenes()">
                </div>

                <div class="mb-4" id="previewImagenes" style="display:flex;gap:10px;flex-wrap:wrap;"></div>

                <!-- Evento vinculado -->
                <?php if (!empty($publicacion->evento)): ?>
                    <hr>
                    <h6 class="fw-bold mb-3"><i class="bi bi-calendar-event me-2"></i>Evento vinculado</h6>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="evento_titulo" name="evento_titulo" placeholder="Título"
                            value="<?= htmlspecialchars($publicacion->evento->titulo) ?>">
                        <label for="evento_titulo">Título del evento</label>
                    </div>

                    <?php $categoria_actual = $publicacion->evento->categoria ?? 'General'; ?>
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-select" name="evento_categoria" id="evento_categoria">
                                    <?php foreach ($categorias as $nombre => $cat): ?>
                                        <option value="<?= htmlspecialchars($nombre) ?>" <?= $nombre === $categoria_actual ? 'selected' : '' ?>><?= $nombre ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="evento_categoria">Categoría</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-floating">
                                <input type="url" class="form-control" id="evento_url" name="evento_url" placeholder="URL"
                                    value="<?= htmlspecialchars($publicacion->evento->url) ?>">
                                <label for="evento_url">URL asociada (opcional)</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" id="evento_fecha" name="evento_fecha" class="form-control"
                                    value="<?= $publicacion->evento->fecha ?>">
                                <label for="evento_fecha">Fecha inicio</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="time" id="evento_hora" name="evento_hora" class="form-control"
                                    value="<?= substr($publicacion->evento->hora, 0, 5) ?>">
                                <label for="evento_hora">Hora inicio</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" id="evento_fecha_fin" name="evento_fecha_fin" class="form-control"
                                    value="<?= $publicacion->evento->fecha_fin ?>">
                                <label for="evento_fecha_fin">Fecha fin</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="time" id="evento_hora_fin" name="evento_hora_fin" class="form-control"
                                    value="<?= substr($publicacion->evento->hora_fin, 0, 5) ?>">
                                <label for="evento_hora_fin">Hora fin</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="evento_todo_el_dia" name="evento_todo_el_dia"
                            <?= $publicacion->evento->todo_el_dia ? 'checked' : '' ?>>
                        <label class="form-check-label" for="evento_todo_el_dia">Evento de todo el día</label>
                    </div>

                    <div class="mb-4">
                        <label for="evento_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="evento_descripcion" name="evento_descripcion"
                            rows="3"><?= htmlspecialchars($publicacion->evento->descripcion) ?></textarea>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="eliminar_evento" name="eliminar_evento">
                        <label class="form-check-label" for="eliminar_evento">Eliminar este evento vinculado</label>
                    </div>
                <?php endif; ?>

                <!-- CTA -->
                <div class="d-flex justify-content-between">
                    <a href="<?= RUTA_URL ?>/ContenidoControlador/inicio"
                        class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill">
                        <i class="bi bi-save"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
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