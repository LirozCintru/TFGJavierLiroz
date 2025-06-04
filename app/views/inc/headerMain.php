<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>IntraLink</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="<?php echo RUTA_URL ?>/public/css/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="<?php echo RUTA_URL ?>/public/css/main.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <!-- Estilos adaptados: navbar más clara (#2f3b4c) -->
  <style>
    /* ──────────────────────────────────────────────────────────────
   1) Fondo de la navbar en un azul-grisáceo claro
────────────────────────────────────────────────────────────── */
    .navbar {
      background-color: rgba(100, 115, 135, 0.65) !important;
      /* Azul-grisáceo algo más claro */
    }

    /* ──────────────────────────────────────────────────────────────
   2) Imagen de perfil: borde blanco nítido
────────────────────────────────────────────────────────────── */
    .navbar img.rounded-circle.border {
      border: 2px solid #ffffff !important;
    }

    /* ──────────────────────────────────────────────────────────────
   3) Nombre de usuario (izquierda): texto casi blanco
────────────────────────────────────────────────────────────── */
    .navbar .d-flex.align-items-center span {
      color: rgb(35, 35, 35) !important;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    /* ──────────────────────────────────────────────────────────────
   4) Enlaces centrales (Inicio, Calendario, Mi perfil)
────────────────────────────────────────────────────────────── */
    .navbar .nav-link {
      color: #ffffff !important;
      font-size: 1rem;
      padding: 0.4rem 0.6rem;
      transition: all 0.2s ease;
    }

    .navbar .nav-link:hover {
      background-color: rgba(255, 255, 255, 0.65);
      /* Más visible */
      color: rgb(0, 92, 183) !important;
      /* Sin cambio de color */
      border-radius: 18px;
    }

    /* .navbar .nav-link.activa {
      background-color: rgba(77, 184, 255, 0.56);
      color: #ffffff !important;
      font-weight: 600;
      border-bottom: 2px solidrgb(0, 106, 177);
      border-radius: 18px;
    } */

    .navbar .nav-link.activa {
      background-color: rgba(255, 255, 255, 0.65);
      color: rgb(0, 92, 183) !important;
      font-weight: 600;
      border-bottom: 2px solidrgb(0, 106, 177);
      border-radius: 18px;
      letter-spacing: 0.2px;
    }

    /* ──────────────────────────────────────────────────────────────
   5) Iconos de notificaciones y chat: blancos nítidos
────────────────────────────────────────────────────────────── */
    .navbar .nav-item .bi {
      color: rgb(255, 255, 255);
      font-size: 1.5rem;
      transition: all 0.2s ease;
    }

    .navbar .nav-item:hover .bi {
      color: rgb(219, 37, 0) !important;
      border-radius: 25px !important;
    }

    /* ──────────────────────────────────────────────────────────────
   6) Badge de notificaciones/chat: rojo vivo con texto blanco
────────────────────────────────────────────────────────────── */
    #contador-notificaciones,
    #badge-chat {
      font-size: 0.8rem;
      padding: 4px 6px;
      background-color: #e03131;
      color: #ffffff !important;
    }

    /* ──────────────────────────────────────────────────────────────
   7) Botones “Usuarios” y “Departamentos”
      - Outline blanco, fondo semitransparente al pasar
      - Activo en azul cielo
────────────────────────────────────────────────────────────── */
    .navbar .btn-outline-secondary {
      color: rgb(255, 255, 255);
      border-color: #e9ecef;
      background-color: rgba(131, 172, 213, 0.7) !important;
      transition: all 0.2s ease;
      padding: 0.35rem 0.75rem;

    }

    .navbar .btn-outline-secondary:hover {
      background-color: rgb(41, 148, 255) !important;
      color: rgb(0, 0, 0);
      border-color: #ffffff;
    }

    .navbar .btn-primary,
    .navbar .btn-primary:hover {
      color: rgb(0, 0, 0) !important;
      background-color: rgb(41, 148, 255) !important;
      border: 1px rgb(255, 255, 255) solid !important;
      padding: 0.35rem 0.75rem;
      font-weight: 600;
      letter-spacing: 0.3px;

    }

    /* ──────────────────────────────────────────────────────────────
   8) Botón “Cerrar sesión”: texto blanco y borde blanco por defecto,
      y al hacer hover un fondo rojo vivo
────────────────────────────────────────────────────────────── */
    .navbar .btn-cerrar-sesion {
      color: #f1f1f1;
      /* blanco suave */
      border: 1px solid #f1f1f1;
      /* borde blanco */
      background-color: rgb(221, 95, 95);
      transition: all 0.2s ease;
      padding: 0.35rem 0.75rem;
    }

    .navbar .btn-cerrar-sesion:hover {
      background-color: rgb(255, 0, 0) !important;
      /* rojo vivo al pasar */
      color: rgb(0, 0, 0);

    }
  </style>
</head>

<body>
  <?php
  require RUTA_APP . '/librerias/Funciones.php';
  verificarSesionActiva();
  $usuario = $_SESSION['usuario'];
  $fotoPerfil = $usuario['imagen'] ?? 'default.png';
  $rutaFoto = RUTA_URL . '/public/img/usuarios/' . $fotoPerfil;
  $rutaActual = $_SERVER['REQUEST_URI'];
  ?>

  <div class="container-fluid p-0">
    <nav class="navbar navbar-expand-lg bg-light bg-gradient border-bottom px-4 py-3 position-relative">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <!-- 🧑 Usuario a la izquierda -->
        <div class="d-flex align-items-center gap-2">
          <img src="<?= $rutaFoto ?>" alt="Usuario" width="64" height="64" class="rounded-circle border border-2" />
          <span class="fw-bold"><?= htmlspecialchars($usuario['nombre']) ?></span>
        </div>

        <!-- 🔗 Menú en el centro -->
        <div class="position-absolute top-50" style="left: 48%; transform: translate(-50%, -50%); z-index: 1;">
          <ul class="navbar-nav flex-row gap-4 align-items-center m-0">
            <li class="nav-item">
              <a class="nav-link px-2 <?= str_contains($rutaActual, '/ContenidoControlador/inicio') ? 'activa' : '' ?>"
                href="<?= RUTA_URL ?>/ContenidoControlador/inicio">
                🏠 Inicio
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link px-2 <?= str_contains($rutaActual, '/EventosControlador') ? 'activa' : '' ?>"
                href="<?= RUTA_URL ?>/EventosControlador/index">
                📅 Calendario
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link px-2 <?= str_contains($rutaActual, '/PerfilControlador/editar') ? 'activa' : '' ?>"
                href="<?= RUTA_URL ?>/PerfilControlador/editar">
                👤 Mi perfil
              </a>
            </li>

            <li class="nav-item position-relative">
              <a class="nav-link px-2 position-relative" href="#" id="notificaciones-link">
                <i class="bi bi-bell"></i>
                <span id="contador-notificaciones" class="position-absolute top-0 start-100 badge rounded-pill d-none"
                  style="transform: translate(-50%, 30%);"></span>
              </a>
            </li>

            <!-- Icono de chat (el badge se actualizará desde chat.js) -->
            <li class="nav-item position-relative">
              <a id="chat-link" class="nav-link px-2" href="<?= RUTA_URL ?>/ChatControlador/index">
                <i class="bi bi-chat-dots"></i>
                <span id="badge-chat" class="position-absolute top-0 start-100 badge rounded-pill d-none"
                  style="transform: translate(-50%, 30%);"></span>
              </a>
            </li>
          </ul>
        </div>

        <!-- ⚙️ Botones derechos -->
        <div class="d-flex align-items-center gap-2">
          <?php if ($usuario['id_rol'] == 1): ?>
            <?php
            $esUsuarios = str_contains($rutaActual, '/UsuariosControlador');
            $esDepartamentos = str_contains($rutaActual, '/DepartamentoControlador');
            $claseUsuarios = $esUsuarios ? 'btn-primary text-white' : 'btn-outline-secondary';
            $claseDepartamentos = $esDepartamentos ? 'btn-primary text-white' : 'btn-outline-secondary';
            ?>
            <a href="<?= RUTA_URL ?>/UsuariosControlador/index" class="btn <?= $claseUsuarios ?> btn-sm">
              👥 Usuarios
            </a>
            <a href="<?= RUTA_URL ?>/DepartamentoControlador/index" class="btn <?= $claseDepartamentos ?> btn-sm">
              🏢 Departamentos
            </a>
          <?php endif; ?>

          <a href="<?= RUTA_URL ?>/logins/salir" class="btn btn-cerrar-sesion btn-sm">
            Cerrar sesión
          </a>

        </div>
      </div>
    </nav>
  </div>