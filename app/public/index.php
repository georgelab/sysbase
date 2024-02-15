<?php
/**
 * Main Application
 *
 * @category    core
 * @package     core/app
 * @author      George Azevedo <george@fenix.rio.br>
 * @copyright   Copyright (c) 2023 Fênix Comunicação (https://fenix.rio.br)
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$app = require "../etc/start.php";
$app->boot();