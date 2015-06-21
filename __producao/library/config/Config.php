<?php
if (! defined ( 'ANALIZZE_LIBRARY' )) {	die ( 'Acesso direto não permitido' ); }

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
	ini_set("display_errors", false);
}

$AnalizzeConfig = array();

$AnalizzeConfig['ambiente'] = array();
$AnalizzeConfig['ambiente']['criacao'] = true;

/** dados vem do config do sistema hospedeiro **/
$AnalizzeConfig['mysql'] = array();
$AnalizzeConfig['mysql']['host'] = $HOST;
$AnalizzeConfig['mysql']['user'] = $USER;
$AnalizzeConfig['mysql']['pwd'] = $PW;
$AnalizzeConfig['mysql']['database'] = $DBNOME;

$AnalizzeConfig['log'] = array();
$AnalizzeConfig['log']['active'] = true;
$AnalizzeConfig['log']['fileLocation'] = "/log";

$AnalizzeConfig['credenciais'] = array();
$AnalizzeConfig['credenciais']['token'] = "qw*&sd%%{!cv2E5R63SA54aedfgbtghnol>)(*&%";

$AnalizzeConfig['local'] = array();
$AnalizzeConfig['local']['url'] = 'http://'.$URL.'/analizze';
