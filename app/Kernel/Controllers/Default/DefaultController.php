<?php
/**
 * @author George Azevedo <george@fenix.rio.br>
 * @copyright Copyright (c) 2023 Fênix Comunicação (https://www.fenix.rio.br)
 */
declare(strict_types=1);
namespace App\Kernel\Controllers\Default;

use App\Kernel\Helpers\{Render,Request};

/**
 * Class DefaultController
 */
class DefaultController
{

    /**
     * Default setup variable
     */
    public $Render;

    /**
     * Default setup variable
     */
    public $Request;

    /**
     * Router constructor
     */
    public function __construct()
    {
        $this->Render = new Render();
        $this->Request = new Request();
    }
}