<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <style>
    table { border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; }
  </style>
</head>
<body>
  <h1>Bienvenido al Panel de Control</h1>

  <table>
    <tr>
      <th>Nombre</th>
      <td><?= htmlspecialchars($usuario['nombre']) ?></td>
    </tr>
    <tr>
      <th>Email</th>
      <td><?= htmlspecialchars($usuario['email']) ?></td>
    </tr>
    <tr>
      <th>Departamento</th>
      <td><?= htmlspecialchars($usuario['nombre_departamento']) ?></td>
    </tr>
    <tr>
      <th>Rol</th>
      <td><?= htmlspecialchars($usuario['nombre_rol']) ?></td>
    </tr>
  </table>

  <p><a href="/auth/logout">Cerrar sesión</a></p>
</body>
</html>
