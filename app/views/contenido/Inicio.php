<?php require RUTA_APP . '/views/inc/headerMain.php'; ?>

<?php
$fotoPerfil = $_SESSION['usuario']['imagen'] ?? 'default.png';
$rutaFoto = RUTA_URL . '/public/img/usuarios/' . $fotoPerfil;
?>

<div class="container-fluid p-0">
    <!-- Barra superior -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom px-4">
        <div class="d-flex align-items-center">
            <img src="<?php echo $rutaFoto; ?>" alt="Usuario" width="40" height="40" class="rounded-circle me-2 border">
            <span class="fw-bold"><?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
        </div>
        <div class="ms-auto">
            <a href="#" class="btn btn-outline-secondary me-2">Configuración</a>
            <a href="<?php echo RUTA_URL; ?>/logins/salir" class="btn btn-outline-danger">Cerrar sesión</a>
        </div>
    </nav>

    <div class="row g-0" style="height: calc(100vh - 60px);">
        <!-- Menú lateral izquierdo -->
        <div class="col-3 col-md-2 bg-light border-end p-3">
            <h6 class="text-uppercase fw-bold mb-3">Menú</h6>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="#">Chats</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Notificaciones</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Eventos</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Mi perfil</a></li>
            </ul>
        </div>

        <!-- Zona central -->
        <div class="col-9 col-md-10 p-4 overflow-auto">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold">Publicaciones</h4>
                <a href="<?php echo RUTA_URL; ?>/PublicacionesControlador/crear" class="btn btn-primary">
                    + Nueva publicación
                </a>
            </div>

            <!-- Filtro y buscador -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <select class="form-select" id="filtroTipo">
                        <option value="">-- Filtrar por tipo --</option>
                        <option value="General">General</option>
                        <option value="Urgente">Urgente</option>
                        <option value="Departamental">Departamental</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <input type="text" id="buscador" class="form-control" placeholder="Buscar por título o contenido...">
                </div>
            </div>

            <!-- Contenedor de publicaciones -->
            <div id="contenedorPublicaciones">
                <!-- <?php require RUTA_APP . '/views/publicaciones/index.php'; ?> -->
            </div>
        </div>
    </div>
</div>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>
