<?php
/**
 * Application starter
 *
 * @author      George Azevedo <george@fenix.rio.br>
 * @copyright   Copyright (c) 2023 Fênix Comunicação (https://fenix.rio.br)
 */

#opcache_reset();
use App\Kernel\Core;

/*
 * Setup bootstrap loader
 */
$setup = require "bootstrap.php";

/*
 * SPL Autoloader
 */
require $setup['paths']['application'] . "/Kernel/Helpers/Autoloader.php";

/*
 * Third-party composer autoloader
 */
require $setup['paths']['application'] . "/vendor/autoload.php";

/*
 * Application boot
 */
return Core::getInstance($setup);