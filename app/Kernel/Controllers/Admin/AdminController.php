<?php
/**
 * @author George Azevedo <george@fenix.rio.br>
 * @copyright Copyright (c) 2023 Fênix Comunicação (https://www.fenix.rio.br)
 */
declare(strict_types=1);
namespace App\Kernel\Controllers\Admin;

use App\Kernel\Helpers\{Render};

/**
 * Class Router
 */
class AdminController
{

    /**
     * Default setup variable
     */
    public $Render;

    /**
     * Router constructor
     */
    public function __construct()
    {
        $this->Render = new Render();
    }
}