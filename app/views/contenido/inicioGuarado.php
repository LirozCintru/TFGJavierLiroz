<!-- <?php require_once RUTA_APP . '/config/roles.php'; ?> -->
<?php if (isset($_SESSION['usuario'])): ?>

<p>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>!</p>

<?php if ($_SESSION['usuario']['id_rol'] == 1): ?>
    <a href="<?php echo RUTA_URL; ?>/admin/panel">Panel de Administración</a>
<?php elseif ($_SESSION['usuario']['id_rol'] == 2): ?>
    <a href="<?php echo RUTA_URL; ?>/jefe/departamento">Gestión de Departamento</a>
<?php endif; ?>

<?php if ($_SESSION['usuario']['id_rol'] == ROL_ADMIN): ?>
    <a href="<?php echo RUTA_URL; ?>/admin/panel">Administración</a>
<?php elseif ($_SESSION['usuario']['id_rol'] == ROL_JEFE): ?>
    <a href="<?php echo RUTA_URL; ?>/jefe/publicaciones">Publicaciones del Departamento</a>
<?php endif; ?>

<a href="<?php echo RUTA_URL; ?>/logins/salir">Cerrar Sesión</a>
<?php endif; ?>