<?php
// Cargamos librerias
require_once 'config/configurar.php';

require_once 'helpers/url_helper.php';
//require_once 'librerias/DataBase.php';
//require_once 'librerias/Controlador.php';
//require_once 'librerias/Core.php';

// Autoload php
// Hemos sustituido las lineas anteriores comentadas por
// la función autoload que nos permite cargar 
// todos los ficheros que se encuentren en la carpeta librerias
// sin tener que indicarlos uno a uno
spl_autoload_register(function($nombreClase){
    require_once 'librerias/' . $nombreClase. '.php';
});