<form action="<?php echo RUTA_URL;?>/logins/acceder" method="POST">
  Email: <input type="text" name="email"><br>
  Contraseña: <input type="password" name="contrasena"><br>
  <button type="submit">Entrar</button>
  </form>


  <?php 
     echo  RUTA_URL;
     echo "<br>";
     echo dirname(dirname(__FILE__));
  ?>
 

