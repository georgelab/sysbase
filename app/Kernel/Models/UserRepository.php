<?php
namespace App\Kernel\Models;
use App\Kernel\Helpers\Interfaces\DBConnectionInterface;

/**
 * Repository Class for Users
 */
class UserRepository
{
    private $dbconnection;
    private $dbtable;

    public function __construct(DBConnectionInterface $dbconnection)
    {
        $this->dbconnection = $dbconnection;
        $this->dbtable = 'Users';
    }

    /**
     * Adds a User into database
     */ 
    public function add($User)
    {
        if ($User) {

            $sqlQuery = "INSERT
                            INTO {$this->dbtable}(sku, name, price, type, type_attributes)
                            VALUES (:sku, :name, :price, :type, :type_attributes)";

            $chosenType = mb_strtolower($User->getType());

            $attributes = json_encode([
                'key'=>$User->getSpecs()[$chosenType]['key'],
                'value'=>implode('x', $User->getTypeArgs()),
                'unit'=>$User->getSpecs()[$chosenType]['unit']
            ], JSON_HEX_QUOT);

            $args = [
                ['attribute'=>'sku', 'value'=>$User->getSku()],
                ['attribute'=>'name', 'value' =>$User->getName()],
                ['attribute'=>'price', 'value'=>$User->getPrice()],
                ['attribute'=>'type', 'value'=>$User->getType()],
                ['attribute'=>'type_attributes', 'value'=>$attributes]
            ];

            $this->dbconnection->execute($sqlQuery, $args);
        }
    }

    /**
     * Delete records from related database table
     */ 
    function delete($args)
    {
        foreach($args['values'] as $value) {
            $User = (object)$this->get([
                'attribute'=>$args['attribute'],
                'value'=>$value
            ]);

            if ($User) {
                $sqlQuery = "DELETE
                                FROM {$this->dbtable}
                                WHERE {$args['attribute']} = {$value}";

                $this->dbconnection->execute($sqlQuery);
            }
        }
    }

    /**
     * Get a record from related database table
     */ 
    function get($args)
    {
        $sqlQuery = "SELECT *
                        FROM {$this->dbtable}
                        WHERE {$args['attribute']} = :{$args['attribute']}";
        return $this->dbconnection->fetch($sqlQuery, $args);
    }

    /**
     * Get all records from related database table
     */ 
    function getAll()
    {
        $sqlQuery = "SELECT *
                        FROM {$this->dbtable}";
        return $this->dbconnection->fetch($sqlQuery);
    }
}