<?php 
  // Cargamos el header previamente
  require RUTA_APP . '/views/inc/header.php';
?>
<a href="<?php echo RUTA_URL; ?>/paginas">Volver</a>
<h2>Agregar usuario</h2>
<form action="<?php echo RUTA_URL; ?>/paginas/agregar" method="POST">
    <div clas="form-group">
        <label for="nombre"> Nombre <sup>*</sup></label>
        <input type="text" name="nombre">
    </div>
    <div clas="form-group">
        <label for="email"> Email <sup>*</sup></label>
        <input type="text" name="email">
    </div>
    <div clas="form-group">
        <label for="telefono"> Telefono <sup>*</sup></label>
        <input type="text" name="telefono">
    </div>
    <input type="submit" value="Agregar usuario">
</form>
 <?php
  // Cargamos el footer al final de la pagina
  require RUTA_APP . '/views/inc/footer.php';
?>