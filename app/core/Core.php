
<?php
// Página encartada del Manejado de la URL

/* Mapeamos la URL introducida en el navegador
   1-> Se referirá al controlador
   2-> Se refiere al método dentro de ese controlador
   3-> Parametro de este método
   Ejemplo:
   /articulo/actualizar/1
*/
class Core {
    
    const PATH_CONTROLADORES =  "../app/controllers/";

    // Establecemos controlador, metodo y parametros por defecto
    // En caso de que no venga indicado en la URL
    protected $controladorActual = 'paginas';
    protected $metodoActual = 'index';
    protected $parametros =[];

    public function __construct(){
        // Obtenemos el controlador
        $url = $this->getURL();
        // Comprobamos si existe el controlador indicado en la url
        if (!is_null ($url) && file_exists(self::PATH_CONTROLADORES . ucwords($url[0]). '.php')) { 
            // Si existe lo redireccionamos
            $this->controladorActual = ucwords($url[0]);
            // Como ya lo hemos asignado a la propiedad lo eliminamos del array
            unset($url[0]);
        }
        // cargamos el controlador
        $file = self::PATH_CONTROLADORES . $this->controladorActual . '.php';
        require_once $file;
        $this->controladorActual = new $this->controladorActual(); // Instanciamos el controlador elegido en la url

        // Obtenemos el método ahora
        if (isset($url[1])) {
            if (method_exists($this->controladorActual, $url[1])) {
                $this->metodoActual=$url[1];
                unset($url[1]); // Eliminamos el método
            } 
        }
        // Obtenemos los argumentos
        $this->parametros = $url?array_values($url):["nada"];
        // Invocamos a la clase->metodo->argumentos
        call_user_func_array([$this->controladorActual, $this->metodoActual], $this->parametros);
    }

    public function getURL() {
        //echo $_GET['url']; // Cogemos la variable url de la URL (ver fichero .htaccess $1)
        if (isset($_GET['url'])) { // Comprobamos si url tiene información
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL); // Limpiamos
            $url = explode('/', $url); // Separamos
            return $url;
        }
    }
    
}
//$_GET['url']="paginas/articulo/";
//$core = new Core();
