<?php
// Controlador principal
// Carga el modelo y la vista necesaria
class Controlador {
    // Cargamos el modelo
    public function modelo($m) {
        require_once '../app/models/' . $m . '.php';
        return new $m();
    }

     // Cargamos el vista
     public function vista($v, $datos=[]) {        
        if (file_exists('../app/views/' . $v . '.php')) {
            require_once '../app/views/' . $v . '.php';           
        } else {
            // No existe la vista que se demanda, no es posible cargarla
            // mostramos error
            die ('La vista no existe');
        }
  
    }

}