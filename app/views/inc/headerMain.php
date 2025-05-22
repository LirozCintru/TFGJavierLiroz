<!-- cabecera de las páginas del site -->
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Intralink</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo RUTA_URL ?>/public/css/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo RUTA_URL ?>/public/css/main.css" rel="stylesheet">
  <!-- Importar FontAwesome desde la CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">


</head>

<body>
  <?php
  require(RUTA_APP . '/librerias/Funciones.php');

  $usuario = $_SESSION['usuario'];
  $fotoPerfil = $usuario['imagen'] ?? 'default.png';
  $rutaFoto = RUTA_URL . '/public/img/usuarios/' . $fotoPerfil;

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
          <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/ContenidoControlador/inicio"
              data-section="inicio">Inicio</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/EventosControlador/index"
              data-section="calendario">Calendario</a></li>
          <li class="nav-item"><a class="nav-link" href="#" data-section="perfil">Mi perfil</a></li>
          <li class="nav-item position-relative">
            <a href="#" id="notificaciones-link">
              <i class="bi bi-bell"></i>
              <span id="contador-notificaciones" class="badge bg-danger d-none">0</span>
            </a>
          </li>

        </ul>
      </div>

      <!-- Derecha: cerrar sesión -->
      <div>
        <a href="#" class="btn btn-outline-secondary me-2">Configuración</a>
        <a href="<?= RUTA_URL ?>/logins/salir" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
      </div>
    </nav>