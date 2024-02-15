<?php
/**
 * @category Core
 * @package Kernel/Core
 * @author George Azevedo <george@fenix.rio.br>
 * @copyright Copyright (c) 2023 Fênix Comunicação (https://www.fenix.rio.br)
 */
declare(strict_types=1);
namespace App\Kernel;

use App\Kernel\Helpers\{Request};


/**
 * Class Core
 */
class Core
{
    /**
     * Singleton static variable
     */
    public static $instance = null;

    /**
     * Singleton getInstance
     */
    static public function getInstance($setup = []): Core
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c($setup);
        }
        return self::$instance;
    }

    /**
     * App constructor
     *
     * @param array $setup
     */
    public function __construct($setup = [])
    {
        define('CoreSetup', json_encode($setup, JSON_FORCE_OBJECT));
    }

    /**
     * __call
     */
    public function __call($method, $args)
    {
        $arglist = implode(', ', $args);
        # TODO
        var_dump(
            [
                'title' => 'unknown_method_called',
                'msg' => [
                    'method' => $method,
                    'arglist' => $arglist,
                    'caller' => basename(__FILE__)
                ]
            ]
        );
    }

    /**
     * boot
     */
    public function boot()
    {
        $appSetup = json_decode(CoreSetup);
        if (!session_id()) {
            session_name($appSetup->info->session_name);
            session_start();
        }
        $Request = new Request();
        $Request->listen();
    }
}