<?php
/**
 * @author George Azevedo <george@fenix.rio.br>
 * @copyright Copyright (c) 2023 Fênix Comunicação (https://www.fenix.rio.br)
 */
declare(strict_types=1);
namespace App\Kernel\Helpers;

use App\Kernel\Helpers\{Mailer,RestAPI};

/**
 * Class Router
 */
class Router
{

    /**
     * Default setup variable
     */
    public $scope = [];

    /**
     * Router constructor
     */
    public function __construct()
    {
        $this->run();
    }

    /**
     * toURL
     * 
     * @param string $redirect
     * @param int $type 
     */
    public static function toURL($redirect = '', $type = 303)
    {
        if ($redirect != '') {
            if (!headers_sent()) {
                header("Location: " . $redirect, TRUE, $type);
                exit;
            } else {
                $_script = "<script>window.location.href ='" . $redirect . "';</script>";
                echo $_script;
                exit;
            }
        }
    }

    /**
     * run
     * 
     * @param object $setup
     */
    public function run()
    {

        $appSetup = json_decode(CoreSetup);

        $_s = $_SERVER;
        $scope = [];
        $urlparts = [];
        $params = [];

        if (isset($_s)) {

            #allow campaign Ads
            $_reqURI = $_SERVER['REQUEST_URI'];
            if (strpos($_reqURI, '?') !== false && isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
                $_reqURI = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_reqURI);
            }

            #devtoken test
            if (
                isset($_GET[$appSetup->info->devtoken])
                && !isset($_COOKIE[$appSetup->info->devtoken])
            ) {
                setcookie($appSetup->info->devtoken, 'fnxauth', strtotime("now") + (60 * intval($appSetup->info->devtoken_leasetime)));
                header("Refresh:0; url=" . $_SERVER['SCRIPT_URI']);
            }

            $urlparts = explode('/', $_reqURI);

            foreach ($urlparts as $pos => $part):
                if (empty($part))
                    unset($urlparts[$pos]);
            endforeach;
            $urlparts = array_values($urlparts);

            #fix 2 part if is qs
            if (isset($urlparts[1]) && (strpos($urlparts[1], '=') !== false || strpos($urlparts[1], '&') !== false)) {
                $urlparts[1] = str_replace('?', '', $urlparts[1]);
                array_splice($urlparts, 1, 0, ['']);
            }

            #params
            if (count($urlparts) >= 3):
                $arr_params = array_slice($urlparts, 2);
                $params = [];
                foreach ($arr_params as $part) {
                    $part = str_replace('?', '', $part);
                    $p = explode('=', $part);
                    foreach ($p as $k => $_p)
                        $p[$k] = is_numeric($_p) ? intval($_p) : trim($_p);
                    if (count($p) > 1) {
                        $params[$p[0]] = $p[1];
                    } else {
                        $params[$p[0]] = '';
                    }
                }
            endif;

            #def params from Query String
            $defQS = [];
            $tmpQS = (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) ? explode('&', str_replace('/', '&', $_SERVER['QUERY_STRING'])) : '';
            if ($tmpQS) {
                foreach ($tmpQS as $partQS) {
                    $arr = explode('=', $partQS);
                    if ($arr && count($arr) >= 2) {
                        list($k, $v) = $arr;
                        $defQS[$k] = $v;
                    }
                }
            }
            $params = ($defQS) ? array_merge($defQS, $params) : $params;

            #scope
            if ($urlparts):

                if ($urlparts[0] == 'mailer') {
                    $mailer = new Mailer();
                    $mailer->process();
                } else if ($urlparts[0] == 'api') {
                    $restAPI = new RestAPI();
                    $restAPI->listen();
                } else {
                    $filepath = implode("/", $urlparts);
                    $scope = (object) [
                        'file' => $filepath . '.php',
                        'name' => mb_strtolower(implode("-", $urlparts)),
                        'singlename' => mb_strtolower($urlparts[count($urlparts) - 1]),
                        'link' => implode("/", $urlparts),
                        'params' => $params,
                        'layout' => $urlparts[0]
                    ];
                }
                    

            else:
                $scope = (object) [
                    'file' => 'frontpage.php',
                    'name' => 'home',
                    'singlename' => 'frontpage',
                    'link' => '',
                    'params' => $params,
                    'layout' => 'default'
                ];
                $_GET = $scope->params;

            endif;
        }
        return $this->scope = $scope;
    }

    /**
     * current
     */
    public function current()
    {
        return $this->scope;
    }

    /**
     * getURL
     * 
     * @param bool $geturi
     */
    public function getURL($geturi = false)
    {
        $port = (isset($_SERVER["SERVER_PORT"]) && intval($_SERVER["SERVER_PORT"]) == 443) ? 'https://' : 'http://';
        $host = $_SERVER["HTTP_HOST"];
        $uri = ($geturi && isset($_SERVER["REQUEST_URI"]) && !empty($_SERVER["REQUEST_URI"])) ? $_SERVER["REQUEST_URI"] : '/';
        return ($port && $host && $uri) ? rtrim($port . $host . $uri, '/') : '';
    }


}