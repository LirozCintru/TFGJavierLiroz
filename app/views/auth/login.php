<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
</head>
<body>
  <h2>Iniciar sesión</h2>
  <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <form method="POST" action="index.php?controller=auth&action=procesarLogin">
    <label>Email:</label>
    <input type="email" name="email" required><br>
    <label>Contraseña:</label>
    <input type="password" name="password" required><br>
    <button type="submit">Entrar</button>
  </form>
</body>
</html>
