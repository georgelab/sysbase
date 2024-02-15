<?php
/**
 * @category Controller
 * @package Kernel/Controllers/Controller
 * @author George Azevedo <george@fenix.rio.br>
 * @copyright Copyright (c) 2023 Fênix Comunicação (https://www.fenix.rio.br)
 */
declare(strict_types=1);
namespace App\Kernel\Controllers;

use App\Kernel\Helpers\{Router, Render};

/**
 * Class Controller
 */
class Controller
{
    /**
     * Controller constructor
     */
    public function __construct()
    {
    }

    public function parse()
    {
        $appSetup = json_decode(CoreSetup);

        $Router = new Router();
        $scope = $Router->current();
        $view = $appSetup->paths->root . $appSetup->paths->view;

        if ($scope) {
            $content = $view . '/' . $scope->file;
            if (file_exists($content)) {
                $Render = new Render();
                $Render->display($scope);

            } else {
                if ((isset($_GET) && count($_GET) >= 1) || strpos($_SERVER['REQUEST_URI'], '?') !== false) {
                    Router::toURL('/');
                } else {
                    //Soft 404
                    http_response_code(404);
                    $Render = new Render();
                    $Render->display((object)['file' => $appSetup->core_views->{'404'}, 'params' => $_SERVER['REQUEST_URI']]);
                }
            }
        } else {
            Router::toURL('/');
        }

    }
}