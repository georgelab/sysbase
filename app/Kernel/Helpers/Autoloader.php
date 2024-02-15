<?php
/**
 * Application autoloader
 *
 * @author      George Azevedo <george@fenix.rio.br>
 * @copyright   Copyright (c) 2023 Fênix Comunicação (https://fenix.rio.br)
 */
class Autoloader
{
    public static function register() : bool
    {
        return spl_autoload_register(function ($class) {
            $prefix = str_replace('/public', '/', $_SERVER['DOCUMENT_ROOT']);
            $class = str_replace('App\\', '', $class);
            $file = $prefix . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            if (file_exists($file)) {
                require $file;
                return true;
            }
            return false;
        });
    }
}
Autoloader::register();