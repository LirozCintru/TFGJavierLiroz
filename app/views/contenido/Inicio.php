<?php require RUTA_APP . '/views/inc/headerMain.php'; ?>

<?php
$total_paginas = $datos['total_paginas'] ?? 1;
$pagina = $datos['pagina'] ?? 1;
$limite = $datos['limite'] ?? 10;

$publicaciones = $datos['publicaciones'] ?? [];
$filtro_tipo = $datos['filtro_tipo'] ?? '';
$filtro_busqueda = $datos['filtro_busqueda'] ?? '';
$filtro_departamento = $datos['filtro_departamento'] ?? '';
$departamentos = $datos['departamentos'] ?? [];
?>

<div class="container py-4">
    <div class="bg-light p-4 rounded shadow-sm border">

        <!-- Encabezado -->
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
            <h3 class="fw-bold mb-0">📢 Publicaciones</h3>
            <a href="<?= RUTA_URL ?>/PublicacionesControlador/crear" class="btn btn-primary shadow-sm">
                + Nueva publicación
            </a>
        </div>

        <!-- Alertas -->
        <?php if (isset($_SESSION['mensajeExito'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['mensajeExito']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            <?php unset($_SESSION['mensajeExito']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['errorPublicacion'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['errorPublicacion']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            <?php unset($_SESSION['errorPublicacion']); ?>
        <?php endif; ?>

        <!-- Filtros -->
        <div class="bg-white p-3 rounded shadow-sm mb-4 border">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">-- Todos --</option>
                        <option value="General" <?= $filtro_tipo === 'General' ? 'selected' : '' ?>>General</option>
                        <option value="Urgente" <?= $filtro_tipo === 'Urgente' ? 'selected' : '' ?>>Urgente</option>
                        <option value="Departamental" <?= $filtro_tipo === 'Departamental' ? 'selected' : '' ?>>
                            Departamental</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Límite</label>
                    <select name="limite" class="form-select" onchange="this.form.submit()">
                        <option value="5" <?= $limite == 5 ? 'selected' : '' ?>>5</option>
                        <option value="10" <?= $limite == 10 ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= $limite == 20 ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= $limite == 50 ? 'selected' : '' ?>>50</option>
                    </select>
                </div>

                <?php if (!empty($departamentos)): ?>
                    <div class="col-md-3">
                        <label class="form-label">Departamento</label>
                        <select name="departamento" class="form-select">
                            <option value="">-- Todos --</option>
                            <?php foreach ($departamentos as $dep): ?>
                                <option value="<?= $dep->id_departamento ?>" <?= ($filtro_departamento == $dep->id_departamento) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dep->nombre) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="busqueda" class="form-control" placeholder="Título o contenido..."
                        value="<?= htmlspecialchars($filtro_busqueda) ?>">
                </div>

                <div class="col-md-1">
                    <label class="form-label">Orden</label>
                    <select name="orden" class="form-select">
                        <option value="desc" <?= ($_GET['orden'] ?? '') === 'desc' ? 'selected' : '' ?>>↓ Recientes
                        </option>
                        <option value="asc" <?= ($_GET['orden'] ?? '') === 'asc' ? 'selected' : '' ?>>↑ Antiguas</option>
                    </select>
                </div>

                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                </div>
            </form>
        </div>

        <!-- Publicaciones -->
        <div class="bg-white shadow-sm rounded p-4" id="contenedorPublicaciones">
            <?php require RUTA_APP . '/views/publicaciones/index.php'; ?>
        </div>

        <!-- Paginación -->
        <?php if ($total_paginas > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                            <a class="page-link"
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