<?php 
  // Cargamos el header previamente
  require RUTA_APP . '/views/inc/header.php';

//print_r($datos); // Esta variable viene del controlador
?>
<div class="container mt-5">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Teléfono</th>
      </tr>
    </thead>
    <tbody>
      <?php
        // Recorrer el array $datos y crear las filas de la tabla
        foreach ($datos['usuarios'] as $usuario) {
          
            echo "<tr>";
            echo "<td>" . $usuario->id_usuario. "</td>";
            echo "<td>" . $usuario->nombre . "</td>";
            echo "<td>" . $usuario->email . "</td>";
            echo "<td>" . $usuario->telefono . "</td>";
            echo "<td> <a href=\"paginas/editar/$usuario->id_usuario\">Editar</a></td>";
            echo "<td> <a href=\"paginas/borrar/$usuario->id_usuario\">Borrar</a></td>";
            echo "</tr>";
        }
      ?>
    </tbody>
  </table>
</div>

 <?php
  // Cargamos el footer al final de la pagina
  require RUTA_APP . '/views/inc/footer.php';
?>