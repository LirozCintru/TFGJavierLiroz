<?php
// Configuración de la conexión a BD, usando PDO
class DataBase {
    private $host = DB_HOST;
    private $usuario = DB_USUARIO;
    private $password = DB_PASSWORD;
    private $nombre_BD = DB_NOMBRE; // Nombre del BD

    private $dbh;   // Data base handler
    private $stmt;  // Statement para ejecutar consultas
    private $error;

    public function __construct() {
        // configurar la cadena de conexion a la BD
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->nombre_BD;
        $opciones = array(
            PDO::ATTR_PERSISTENT=>true, //Permite la persistencia de los datos
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
        );
        // Crear una instancia de PDO
        try {
            $this->dbh = new PDO($dsn, $this->usuario, $this->password, $opciones); // Nos conectamos
            $this->dbh->exec('set names utf8'); // Establecemos la codigiacion del codigo
        } catch(PDOException $e){
                $this->error = $e->getMessage();
                echo $this->error;
        }

    }

    // Preparamos la consulta
    public function query($sql) {
        $this->stmt=$this->dbh->prepare($sql);
    }

    // Vinculamos la consulta con bind
    public function bind($parametro, $valor, $tipo=null) { // Enlazamos con la query
        if (is_null($tipo)) {
            switch (true) {
                case is_int($valor):
                    $tipo=PDO::PARAM_INT;
                    break;
                case is_bool($valor):
                    $tipo=PDO::PARAM_BOOL;
                    break;
                case is_null($valor):
                    $tipo=PDO::PARAM_INT;
                    break;
                default:
                    $tipo = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($parametro, $valor, $tipo); // bindValue es una función de PHP
    }

    // Funcion que ejecuta la consulta
    public function execute() {
        return $this->stmt->execute();
    }

    // Obtener los registros de la consulta
    public function registros() {
        $this->execute();
        return  $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Obtener un solo registro
    public function registro() {
        $this->execute();
        return  $this->stmt->fetch(PDO::FETCH_OBJ); // Un solo objeto
    }

    // Obtener numero de registro (rowCount)
    public function rowCount() {
        return  $this->stmt->rowCount(); // rowCount es una función de PHP
    }

}