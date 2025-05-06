<?php
require RUTA_APP.'/views/inc/header.php';

// sesión iniciada antes de usar $_SESSION
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }
echo "<br>";
echo RUTA_APP . '/views/inc/header.php';
?>
<div class="login-background d-flex justify-content-center align-items-center">
   <div class="login-container">
      <h3 class="login-title">Iniciar Sesión</h3>

      <!-- mensaje de error si las credenciales son incorrectas -->
      <?php if (!empty($datos['errorUnique'])): ?>
         <div class="alert alert-danger text-center">
            <?php echo htmlspecialchars($datos['errorUnique']); ?>
         </div>
      <?php endif; ?>

      <!--  mensaje de error si el usuario no tiene permisos -->
      <?php if (isset($_SESSION['errorPermiso'])): ?>
         <div class="alert alert-danger text-center">
            <?php
            echo htmlspecialchars($_SESSION['errorPermiso']);
            unset($_SESSION['errorPermiso']); // Eliminar el mensaje después de mostrarlo
            ?>
         </div>
      <?php endif; ?>

      <!-- Formulario de inicio de sesión -->
      <form action="<?php echo RUTA_URL; ?>/loginsControlador/acceder" method="POST">
         <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control" id="email" name="email"
               value="<?php echo isset($datos['email']) ? htmlspecialchars($datos['email']) : ''; ?>" required>
            <span
               class="text-danger"><?php echo isset($datos['errorEmail']) ? htmlspecialchars($datos['errorEmail']) : ''; ?></span>
         </div>
         <div class="form-group">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="contrasena" name="contrasena" required>
            <span
               class="text-danger"><?php echo isset($datos['errorContrasena']) ? htmlspecialchars($datos['errorContrasena']) : ''; ?></span>
         </div>
         <button type="submit" class="btn btn-primary login-btn mt-3">Ingresar</button>
      </form>
   </div>
</div>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>
<!-- <form action="<?php echo RUTA_URL; ?>/logins/acceder" method="POST">
   Email: <input type="text" name="email"><br>
   Contraseña: <input type="password" name="contrasena"><br>
   <button type="submit">Entrar</button>
</form>



<?php echo 'RUTA_URL: ' . RUTA_URL . "<br>"; ?>
<?php echo 'RUTA_APP: ' . RUTA_URL . "<br>"; ?>
<?php echo 'dirname' . dirname(dirname(__FILE__)) . "<br>"; ?>

<p>RUTA_URL actual: <?php echo RUTA_URL; ?></p>
<p>Ruta del formulario: <?php echo RUTA_URL; ?>/logins/acceder</p>

?> -->