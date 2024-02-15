<?php
/**
 * @author George Azevedo <george@fenix.rio.br>
 * @copyright Copyright (c) 2023 Fênix Comunicação (https://www.fenix.rio.br)
 */
declare(strict_types=1);
namespace App\Kernel\Helpers\Interfaces;

interface DBConnectionInterface
{
    /**
     * Database connection method
     * @param string $host, $database_name, $database_user, $database_password
     * @return PDO Connection $connection
     */ 
    public function connect(
        $host,
        $database_name,
        $database_user,
        $database_password
    );

    /**
     * Database fetch data method
     * @return PDO Connection $connection
     */ 
    public function fetch();

    /**
     * Database execute queries method
     * @return PDO Connection $connection
     */ 
    public function execute();
}