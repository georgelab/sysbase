<?php
/**
 * @author George Azevedo <george@fenix.rio.br>
 * @copyright Copyright (c) 2023 Fênix Comunicação (https://www.fenix.rio.br)
 */
declare(strict_types=1);
namespace App\Kernel\Helpers;

use App\Kernel\Helpers\{Router};
use App\Kernel\Controllers\{Controller};

/**
 * Class Request
 */
class Request
{

    public $CSRF;
    public $appSetup;

    /**
     * Request constructor
     */
    public function __construct()
    {
        $this->appSetup = json_decode(CoreSetup);
    }

    /**
     * listen
     */
    public function listen()
    {
        if ($this->isSafe()) {
            $Controller = new Controller();
            $Controller->parse();
        } else {
            Router::toURL('/');
        }
    }

    /**
     * isSafe
     */
    public function isSafe(): bool
    {
        $valid = true;

        #any requests?
        if (count($_REQUEST) > 0) {

            # referral
            if (!isset($_SERVER['HTTP_REFERER'])) {
                $valid = false;
            } else {
                if (
                    !strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']) !== false
                    && !array_key_exists($_SERVER['HTTP_REFERER'], $this->appSetup->authorized_requesters)
                ) {
                    $valid = false;
                }
            }

            #injection
            foreach ($_REQUEST as $key => $value) {
                $key = str_replace([" ","_"],' ', $key);
                preg_match('/(select\ +.*from|insert\ +.*into|update\ +.*set|delete\ +.*from)/i', $key . $value, $matches);
                if (count($matches) >= 1)
                    $valid = false;
            }
        }

        return $valid;
    }

    /**
     * getCSRF
     */
    public function getCSRF(): string
    {
        if (
            !isset($this->CSRF)
            || empty($this->CSRF)
        ) {
            $this->CSRF = bin2hex(random_bytes(35));
            $_SESSION['app']['csrf'] = $this->CSRF;
        }
        return $this->CSRF;
    }


}