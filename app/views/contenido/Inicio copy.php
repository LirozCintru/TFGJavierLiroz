<?php require RUTA_APP . '/views/inc/headerMain.php'; ?>

<?php
$total_paginas = $datos['total_paginas'] ?? 1;
$pagina = $datos['pagina'] ?? 1;
$limite = $datos['limite'] ?? 10;

$publicaciones = $datos['publicaciones'] ?? [];
$usuario = $_SESSION['usuario'];

$fotoPerfil = $usuario['imagen'] ?? 'default.png';
$rutaFoto = RUTA_URL . '/public/img/usuarios/' . $fotoPerfil;

$filtro_tipo = $datos['filtro_tipo'] ?? '';
$filtro_busqueda = $datos['filtro_busqueda'] ?? '';
$filtro_departamento = $datos['filtro_departamento'] ?? '';
$departamentos = $datos['departamentos'] ?? [];
?>

<div class="container-fluid p-0">
    <!-- Barra de navegación superior -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom px-4 justify-content-between">
        <!-- Izquierda: usuario -->
        <div class="d-flex align-items-center">
            <img src="<?= $rutaFoto ?>" alt="Usuario" width="40" height="40" class="rounded-circle me-2 border">
            <span class="fw-bold"><?= htmlspecialchars($usuario['nombre']) ?></span>
        </div>

        <!-- Centro: navegación -->
        <div class="text-center">
            <ul class="navbar-nav flex-row gap-3">
                <li class="nav-item"><a class="nav-link" href="#" data-section="inicio">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-section="notificaciones">Notificaciones</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/EventosControlador/index"
                        data-section="calendario">Calendario</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-section="perfil">Mi perfil</a></li>
            </ul>
        </div>

        <!-- Derecha: cerrar sesión -->
        <div>
            <a href="#" class="btn btn-outline-secondary me-2">Configuración</a>
            <a href="<?= RUTA_URL ?>/logins/salir" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
        </div>
    </nav>



    <div class="row g-0" style="height: calc(100vh - 60px);">
        <!-- Zona central -->
        <main class="container-fluid p-4" id="contenedorPrincipal">

            <!-- Mensajes flash -->
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

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold">Publicaciones</h4>
                <a href="<?= RUTA_URL ?>/PublicacionesControlador/crear" class="btn btn-primary">
                    + Nueva publicación
                </a>
            </div>

            <!-- Filtros -->
            <form method="GET" class="row mb-3">
                <div class="col-md-3">
                    <select name="tipo" class="form-select">
                        <option value="">-- Tipo de publicación --</option>
                        <option value="General" <?= $filtro_tipo === 'General' ? 'selected' : '' ?>>General</option>
                        <option value="Urgente" <?= $filtro_tipo === 'Urgente' ? 'selected' : '' ?>>Urgente</option>
                        <option value="Departamental" <?= $filtro_tipo === 'Departamental' ? 'selected' : '' ?>>
                            Departamental</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="limite" class="form-select" onchange="this.form.submit()">
                        <option value="5" <?= $limite == 5 ? 'selected' : '' ?>>5</option>
                        <option value="10" <?= $limite == 10 ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= $limite == 20 ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= $limite == 50 ? 'selected' : '' ?>>50</option>
                    </select>
                </div>

                <?php if (!empty($departamentos)): ?>
                    <div class="col-md-3">
                        <select name="departamento" class="form-select">
                            <option value="">-- Departamento --</option>
                            <?php foreach ($departamentos as $dep): ?>
                                <option value="<?= $dep->id_departamento ?>" <?= ($filtro_departamento == $dep->id_departamento) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dep->nombre) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="col-md-4">
                    <input type="text" name="busqueda" class="form-control"
                        placeholder="Buscar por título o contenido..."
                        value="<?= htmlspecialchars($filtro_busqueda) ?>">
                </div>

                <div class="col-md-2">
                    <select name="orden" class="form-select">
                        <option value="desc" <?= ($_GET['orden'] ?? '') === 'desc' ? 'selected' : '' ?>>Más recientes
                        </option>
                        <option value="asc" <?= ($_GET['orden'] ?? '') === 'asc' ? 'selected' : '' ?>>Más antiguas</option>
                    </select>
                </div>


                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
                </div>
            </form>

            <!-- Contenido -->
            <div id="contenedorPublicaciones">
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
        </main>
    </div>
</div>

<!-- Script Ver más / Ver menos -->
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