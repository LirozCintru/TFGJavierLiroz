<form action="<?php echo RUTA_URL; ?>/logins/acceder" method="POST">
   Email: <input type="text" name="email"><br>
   Contraseña: <input type="password" name="contrasena"><br>
   <button type="submit">Entrar</button>
</form>



<?php echo 'RUTA_URL: ' . RUTA_URL . "<br>"; ?>
<?php echo 'RUTA_APP: ' . RUTA_URL . "<br>"; ?>
<?php echo 'dirname'. dirname(dirname(__FILE__))."<br>"; ?>

<p>RUTA_URL actual: <?php echo RUTA_URL; ?></p>
<p>Ruta del formulario: <?php echo RUTA_URL; ?>/logins/acceder</p>

?>