<?php

if (!defined('ANALIZZE_LIBRARY')) {
    die('No direct script access allowed');
}

/**
 * Helper functions
 */
class Helper {

    public static function formatDate($date) {
        $format = "Y-m-d\TH:i:sP";
        if ($date instanceof DateTime) {
            $d = $date->format($format);
        } elseif (is_numeric($date)) {
            $d = date($format, $date);
        } else {
            $d = (String) "$date";
        }
        return $d;
    }

    public static function decimalFormat($var) {
        $var = self::parseInt($var);
        $out = substr($var, 0, strlen($var) - 2) . "." . substr($var, strlen($var) - 2, 2);
        return $out;
    }

    public static function parseInt($var) {
        return @ereg_replace("[^0-9]", "", $var);
    }

    public static function subDays($date, $days) {
        $d = self::formatDate($date);
        $d = date_parse($d);
        $d = mktime($d['hour'], $d['minute'], $d['second'], $d['month'], $d['day'] - $days, $d['year']);
        return self::formatDate($d);
    }

    public static function print_rr($var, $dump = null) {
        if (is_array($var) || is_object($var)) {
            echo "<pre>";
            if ($dump) {
                var_dump($var);
            } else {
                print_r($var);
            }
            echo "</pre>";
        }
    }

}

?>