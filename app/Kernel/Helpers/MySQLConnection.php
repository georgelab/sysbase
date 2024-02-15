<?php
namespace App\Kernel\Helpers;
use PDO, PDOException;
use App\Kernel\Helpers\Interfaces\DBConnectionInterface;

/**
 * Class MySQLConnection implementing DBConnectionInterface
 */
class MySQLConnection implements DBConnectionInterface
{
    private $host;
    private $database_name;
    private $database_user;
    private $database_password;

    /**
     * MySQLConnection constructor
     */
    public function __construct(){
        $appSetup = json_decode(CoreSetup);
        $this->host = $appSetup->database->host;
        $this->database_name = $appSetup->database->name;
        $this->database_user = $appSetup->database->username;
        $this->database_password = $appSetup->database->password;
    }

    /**
     * Sets up a database connection
     *
     * @author George Azevedo <georgelab@gmail.com>
     * @param string $host, $database_name, $database_user, $database_password
     * @return PDO Connection $connection
     */ 
    public function connect(
        $host,
        $database_name,
        $database_user,
        $database_password
    )
    {
        $connection = null;
        try {
            $connection = new PDO("mysql:host=" . $host . ";dbname=" . $database_name, $database_user, $database_password);
            $connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
        } catch (PDOException $exception) {
            die("Database connection failed: " . $exception->getMessage());
        }
        return $connection;
    }

    /**
     * Fetches data from current database connection
     *
     * @author George Azevedo <georgelab@gmail.com>
     * @param string $sqlQuery, Array $args
     * @return array
     */ 
    public function fetch($sqlQuery="", $args=[])
    {
        $response = [];
        $connection = $this->connect(
            $this->host,
            $this->database_name,
            $this->database_user,
            $this->database_password
        );
        $stmt = $connection->prepare($sqlQuery);
        try {
            if (count($args) > 0) {
                $stmt->bindParam($args['attribute'], $args['value']);
            }
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = [
                'status'=>'success',
                'message'=>'success',
                'data'=>$results
            ];
        } catch (PDOException $exception) {
            $response = [
                'status'=>'error',
                'message'=>$exception->getMessage()
            ];
        }
        return $response;
    }

    /**
     * Execute Create and Delete actions on current database connection
     *
     * @author George Azevedo <georgelab@gmail.com>
     * @param string $sqlQuery, Array $args
     * @return array
     */ 
    public function execute ($sqlQuery="", $args=[])
    {
        $response = [];
        $connection = $this->connect(
            $this->host,
            $this->database_name,
            $this->database_user,
            $this->database_password
        );
        $stmt = $connection->prepare($sqlQuery);
        try {
            if (count($args) > 0) {
                foreach( $args as $arg) 
                    $stmt->bindParam($arg['attribute'], $arg['value']);
            }
            $results = $stmt->execute();
            $response = [
                'status'=>'success',
                'message'=>"Executed query: {$sqlQuery}"
            ];
        } catch (PDOException $exception) {
            $response = [
                'status'=>'error',
                'message'=>$exception->getMessage()
            ];
        }
        return $response;
    }
}