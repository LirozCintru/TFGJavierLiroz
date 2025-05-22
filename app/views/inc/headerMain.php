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
    <nav class="navbar navbar-expand-lg bg-light border-bottom px-4 py-3"> <!-- antes era py-2 -->
      <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- 🧑 Usuario a la izquierda -->
        <div class="d-flex align-items-center gap-2">
          <img src="<?= $rutaFoto ?>" alt="Usuario" width="64" height="64" class="rounded-circle border border-2 ">
          <span class="fw-bold"><?= htmlspecialchars($usuario['nombre']) ?></span>
        </div>

        <!-- 🔗 Menú en el centro -->
        <ul class="navbar-nav flex-row gap-4 align-items-center m-0">
          <li class="nav-item"><a class="nav-link px-2" href="<?= RUTA_URL ?>/ContenidoControlador/inicio">Inicio</a>
          </li>
          <li class="nav-item"><a class="nav-link px-2" href="<?= RUTA_URL ?>/EventosControlador/index">Calendario</a>
          </li>
          <li class="nav-item"><a class="nav-link px-2" href="#" data-section="perfil">Mi perfil</a></li>
          <li class="nav-item position-relative">
            <a class="nav-link px-2 position-relative" href="#" id="notificaciones-link">
              <i class="bi bi-bell" style="font-size: 1.5rem;"></i>
              <span id="contador-notificaciones"
                class="position-absolute top-0 start-100 badge rounded-pill bg-danger d-none"
                style="transform: translate(-50%, 30%); font-size: 0.7rem;">
              </span>
            </a>
          </li>
        </ul>

        <!-- ⚙️ Cierre de sesión -->
        <div class="d-flex align-items-center gap-2">
          <a href="#" class="btn btn-outline-secondary btn-sm">Configuración</a>
          <a href="<?= RUTA_URL ?>/logins/salir" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
        </div>

      </div>
    </nav>