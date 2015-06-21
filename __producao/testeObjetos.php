<?php
require ('./library/AnalizzeLibrary.php');
$_GET['action'] = 'save';
$id = false;
$_POST['dados'] = array(
    "nome" => "Cristofer"
);

$servlet = new EmpresaController(new Empresa($id, $_POST));
echo $servlet->getOut();  

/*
$ret = file_get_contents(Config::getData('local', 'url') . "/__producao/library/servlets/EmpresaServlet.php?action=$action&id=$id");

echo $ret;
 * 
 * */
