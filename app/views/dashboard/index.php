<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
</head>
<body>
  <h1>Bienvenido al Panel de Control</h1>

  <p>Hola, <strong><?= htmlspecialchars($nombre) ?></strong>. Tu rol es <strong><?= htmlspecialchars($rol) ?></strong>.</p>

  <p><a href="/auth/logout">Cerrar sesión</a></p>
</body>
</html>
