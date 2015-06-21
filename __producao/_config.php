<?php
$ip = getenv ("REMOTE_ADDR");
umask(000);
setlocale(LC_CTYPE, "pt_BR");
setlocale(LC_TIME, "pt_BR");
define ("PATH", dirname(__FILE__));
define("GRAVARLOGQUERYS", true);
define("TOKEN", "qw*&sd%%{!cv2E5R63SA54aedfgbtghnol>)(*&%");

if (($ip == "127.0.0.1") || ($ip=="::1"))   {
	$HOST     = "127.0.0.1";
	$USER     = "root";
	$PW    = "";
	$DBNOME   = "analizze";
}
else   {
	/* DAdos Hostinger */
	$HOST     = "localhost";
	$USER     = "u695258772_anz";
	$PW    = "nGsZBG810pIKPPBo3e";
	$DBNOME   = "u695258772_anz";
	
	/* livre 
	$HOST     = "localhost";
	$USER     = "livreemj_sistema";
	$PW    = "C2u-[vE+E3(n";
	$DBNOME   = "livreemj_livre";
	$TIPODADOS = "REMOTO";
	$URL = "www.livreemjesus.com.br";
/*	*/
	ini_set("display_errors", false);
/*
	define("FATAL", E_USER_ERROR);
	define("ERROR", E_USER_WARNING);
	define("WARNING", E_USER_NOTICE);
	error_reporting(ERROR|WARNING|FATAL);
	*/
}

