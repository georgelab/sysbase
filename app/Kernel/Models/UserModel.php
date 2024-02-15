<?php
namespace App\Kernel\Models;
use App\Kernel\Models\UserRepository;
use App\Kernel\Helpers\MySQLConnection;

/**
 * Abstract Model Class for Users
 */
abstract class UserModel
{

    protected $sku, $name, $price, $type, $typeArgs, $specs, $userRepository;

    function __construct()
    {
        $dbcon = new MySQLConnection();
        $this->userRepository = new UserRepository($dbcon);
        $this->specs = [
            'book'  =>['key'=>'weight', 'unit'=>'Kg'],
            'dvd'   =>['key'=>'size', 'unit'=>'MB'],
            'furniture'  =>['key'=>'dimensions', 'unit'=>'cm']
        ];
    }

    /**
     * Returns SKU values
     */ 
    function getSku()
    {
        return $this->sku;
    }

    /**
     * Sets SKU values
     */ 
    function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * Returns Name values
     */ 
    function getName()
    {
        return $this->name;
    }

    /**
     * Sets Name values
     */ 
    function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns Price values
     */ 
    function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets price values
     */ 
    function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Returns Type values
     */ 
    function getType()
    {
        return $this->type;
    }

    /**
     * Sets Type values
     */ 
    function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns Type Arguments value
     */ 
    function getTypeArgs()
    {
        return $this->typeArgs;
    }

    /**
     * Sets Type Arguments value
     */ 
    function setTypeArgs($typeArgs)
    {
        $this->typeArgs = $typeArgs;
    }

    /**
     * Returns User Repository
     */ 
    function getUserRepository()
    {
        return $this->userRepository;
    }

    /**
     * Returns User Specifications
     */ 
    function getSpecs()
    {
        return $this->specs;
    }

    /**
     * get - Abtract function to get users from database repository
     */
    abstract protected function get($args=[]);

    /**
     * getList - Abstract function to get all users from database repository
     */
    abstract protected function getList();

    /**
     * add - Abstract function to add a user into database
     */
    abstract protected function add();

    /**
     * delete - Abstract function to delete users from database
     */
    abstract protected function delete($args=[]);

}