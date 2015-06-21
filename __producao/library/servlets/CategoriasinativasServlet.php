
<?php
require ("../AnalizzeLibrary.php");
$id = $_GET["id"];
$dados = $_POST["dados"];

$servlet = new EmpresaController(new Empresa($id, $dados));
echo $servlet->getOut();     
        