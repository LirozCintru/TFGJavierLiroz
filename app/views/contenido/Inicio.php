<?php require RUTA_APP . '/views/inc/headerMain.php'; ?>

<?php
$total_paginas = $datos['total_paginas'] ?? 1;
$pagina = $datos['pagina'] ?? 1;
$limite = $datos['limite'] ?? 10;

$publicaciones = $datos['publicaciones'] ?? [];
$filtro_tipo = $datos['filtro_tipo'] ?? '';
$filtro_busqueda = $datos['filtro_busqueda'] ?? '';
$filtro_departamento = $datos['filtro_departamento'] ?? '';
$orden = $datos['orden'] ?? 'desc';
$departamentos = $datos['departamentos'] ?? [];
?>

<div class="container py-4">
    <div class="rounded-4 overflow-hidden shadow border border-2 bg-white">

        <!-- Cabecera decorada -->
        <div class="encabezado-edicion px-4 py-3 d-flex justify-content-between align-items-center">
            <h5 class="titulo-edicion mb-0">
                <i class="bi bi-megaphone-fill me-2"></i>Publicaciones
            </h5>

            <?php if (in_array($_SESSION['usuario']['id_rol'], [ROL_ADMIN, ROL_JEFE])): ?>
                <a href="<?= RUTA_URL ?>/PublicacionesControlador/crear"
                    class="btn btn-success rounded-pill d-flex align-items-center gap-2 px-3 py-1">
                    <i class="bi bi-plus-circle"></i>
                    <span>Nueva publicación</span>
                </a>
            <?php endif; ?>
        </div>


        <div class="px-4 pt-3 pb-4">

            <!-- Alertas -->
            <?php if (!empty($_SESSION['mensajeExito'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <?= htmlspecialchars($_SESSION['mensajeExito']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
                <?php unset($_SESSION['mensajeExito']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['errorPublicacion'])): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <?= htmlspecialchars($_SESSION['errorPublicacion']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
                <?php unset($_SESSION['errorPublicacion']); ?>
            <?php endif; ?>

            <!-- Filtros sin márgenes laterales -->
            <div class="mt-0 mb-4" style="margin-left: -1.5rem; margin-right: -1.5rem;">
                <form method="GET"
                    class="row g-3 align-items-end bg-light border-top border-bottom py-3 px-4 m-0 filtros-usuarios">
                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select rounded-pill">
                            <option value="">-- Todos --</option>
                            <option value="General" <?= $filtro_tipo === 'General' ? 'selected' : '' ?>>General</option>
                            <option value="Urgente" <?= $filtro_tipo === 'Urgente' ? 'selected' : '' ?>>Urgente</option>
                            <option value="Departamental" <?= $filtro_tipo === 'Departamental' ? 'selected' : '' ?>>
                                Departamental</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">Límite</label>
                        <select name="limite" class="form-select rounded-pill" onchange="this.form.submit()">
                            <option value="5" <?= $limite == 5 ? 'selected' : '' ?>>5</option>
                            <option value="10" <?= $limite == 10 ? 'selected' : '' ?>>10</option>
                            <option value="20" <?= $limite == 20 ? 'selected' : '' ?>>20</option>
                            <option value="50" <?= $limite == 50 ? 'selected' : '' ?>>50</option>
                        </select>
                    </div>

                    <?php if (!empty($departamentos)): ?>
                        <div class="col-md-3">
                            <label class="form-label">Departamento</label>
                            <select name="departamento" class="form-select rounded-pill">
                                <option value="">-- Todos --</option>
                                <?php foreach ($departamentos as $dep): ?>
                                    <option value="<?= $dep->id_departamento ?>" <?= $filtro_departamento == $dep->id_departamento ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dep->nombre) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="busqueda" class="form-control rounded-pill" placeholder="Título"
                            value="<?= htmlspecialchars($filtro_busqueda) ?>">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Orden</label>
                        <select name="orden" class="form-select rounded-pill">
                            <option value="desc" <?= $orden === 'desc' ? 'selected' : '' ?>>↓ Recientes</option>
                            <option value="asc" <?= $orden === 'asc' ? 'selected' : '' ?>>↑ Antiguas</option>
                        </select>

                    </div>


                    <div class="col-md-2 d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-outline-primary rounded-pill">
                            <i class="bi bi-search me-1"></i> Buscar
                        </button>
                        <a href="<?= RUTA_URL ?>/ContenidoControlador/inicio"
                            class="btn btn-outline-secondary rounded-pill">
                            Limpiar
                        </a>
                    </div>
                    <!-- <div class="col-md-2 d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-outline-primary rounded-pill">
                            Filtrar
                        </button>
                        <a href="<?= RUTA_URL ?>/ContenidoControlador/inicio"
                            class="btn btn-outline-secondary rounded-pill">
                            Limpiar filtros
                        </a>
                    </div> -->
                </form>
            </div>

            <!-- Publicaciones -->
            <div class="bg-white rounded p-0" id="contenedorPublicaciones">
                <?php require RUTA_APP . '/views/publicaciones/index.php'; ?>
            </div>

            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item m-1 <?= $i == $pagina ? 'active' : '' ?>">
                                <a class="page-link rounded-pill"
                                    href="<?= $_SERVER['PHP_SELF'] . '?' . http_build_query(array_merge($_GET, ['pagina' => $i])) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.toggle-contenido').forEach(btn => {
            btn.addEventListener('click', () => {
                const card = btn.closest('.card-text');
                card.querySelector('.contenido-completo').classList.toggle('d-none');
                card.querySelector('.contenido-resumen').classList.toggle('d-none');
                btn.textContent = btn.textContent === 'Ver más' ? 'Ver menos' : 'Ver más';
            });
        });
    });
</script>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>