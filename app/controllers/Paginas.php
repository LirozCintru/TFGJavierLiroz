<?php
class Paginas extends Controlador{
    public function __construct() {
      //1) Acceso al modelo
      $this->usuarioModelo = $this->modelo('Usuario');
    }
    
    public function index() {
        // Podemos pasar parametros a la vista que queramos
        // Para ello nos creamos un array con los parámetros
       $usuarios = $this->usuarioModelo->obtenerUsuarios();
        $datos =[
            'usuarios'=> $usuarios // Array con todos los usuarios
        ];
        // Le pasamos a la vista los parametros
        $this->vista('paginas/inicio', $datos);
    }

    public function agregar() {
        if ($_SERVER['REQUEST_METHOD']=='POST') {
            $datos =[
                'nombre'=> trim($_POST['nombre']),
                'email'=> trim($_POST['email']),
                'telefono'=> trim($_POST['telefono']),
            ];

            if ($this->usuarioModelo->agregarUsuario($datos)){
                redireccionar('/paginas');
            } else {
                die ("No se pudo realizar el alta");
            }
        } else {
            $datos =[
                'nombre'=> '',
                'email'=> '',
                'telefono'=> '',
            ];
            $this->vista('paginas/agregar', $datos);
        }
    }
   
}