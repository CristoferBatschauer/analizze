<?php
session_start();

if (!$sessaoLivre)   {
    if (!isset($_SESSION['idUser'])) {
        header("Location:login.php");
        die("Acesso nao permitido");
    }
}


define('ANALIZZE_LIBRARY', TRUE); // constante que garante acesso as classes unicamente após este script
require_once "loader" . DIRECTORY_SEPARATOR . "AutoLoader.class.php";

/**
 * Biblioteca do sistema TrilhasBR
 */
class AnalizzeLibrary {

    const VERSION = "07-02-2015";

    private static $library;
    private static $path;
    
    public static $config;
    public static $log;
    public static $js;

    private function __construct() {
        self::$path = (dirname(__FILE__));
        Autoloader::init();
        /*
        define('XOAD_AUTOHANDLE', true);
        require_once "xoad" . DIRECTORY_SEPARATOR . "xoad.php";
         * 
         */
        self::$config = Config::init();
        self::$log = Log::init();
        /*
        self::$js = TrilhasBRLibrary::includeJs();
         * */
    }

    public static function init() {
        self::verifyDependencies();
        if (self::$library == null) {
            self::$library = new AnalizzeLibrary();
        }
        return self::$library;
    }

    private static function verifyDependencies() {

        $dependencies = true;

        if (!function_exists('spl_autoload_register')) {
            die("Library: Standard PHP Library (SPL) is required.");
            throw new Exception("Library: Standard PHP Library (SPL) is required.");
            $dependencies = false;
        }

        if (!function_exists('curl_init')) {
            die('Library: cURL library is required.');
            throw new Exception('Library: cURL library is required.');
            $dependencies = false;
        }

        if (!class_exists('DOMDocument')) {
            die('Library: DOM XML extension is required.');
            throw new Exception('Library: DOM XML extension is required.');
            $dependencies = false;
        }
        return $dependencies;
    }

    private static function includeJs() {
        $out = '';
        $js = glob(self::getPath() . "/js/controllers/{*.js}", GLOB_BRACE);
        for ($i = 0; $i < count($js); $i++) {
            $nomeJs = explode("/", $js[$i]);
            $out .= '<script src="'.Config::getData('local', 'url'). '/library/js/controllers/' . $nomeJs[(count($nomeJs) - 1)] . '"></script>';
        }
        return $out;
    }

    public final static function getPath() {
        return self::$path;
    }
    
    public final static function getJs()   {
        return self::$js;
    }

}

AnalizzeLibrary::init();
