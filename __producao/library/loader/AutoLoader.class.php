<?php

if (!defined('ANALIZZE_LIBRARY')) {
    die('Acesso direto não permitido');
}

class Autoloader {

    public static $loader;
    private static $dirs = Array(
        'entidades',
        'config',
        'controller',
        'dao',
        'exception',
        'util',
        'log',
        'xoad', 
        'xoad/classes', 
        'fpdf'
    );

    private function __construct() {
        if (function_exists('__autoload')) {
            spl_autoload_register('__autoload');
        }
        spl_autoload_register(Array($this, 'addClass'));
    }

    public static function init() {
        if (!function_exists('spl_autoload_register')) {
            throw new Exception("AnalizzeLibrary: Standard PHP Library (SPL) is required.");
            return false;
        }
        if (self::$loader == null) {
            self::$loader = new Autoloader ();
        }
        return self::$loader;
    }

    private function addClass($class) {
        foreach (self::$dirs as $key => $dir) {
            $file = AnalizzeLibrary::getPath() . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $class . '.class.php';
            if (file_exists($file) && is_file($file)) {
                require_once $file;
            }
        }
    }

}

?>