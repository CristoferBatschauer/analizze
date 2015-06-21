<?php
$_SESSION['idEmpresa'] = 1;
$_SESSION['idUser'] = 10;
require ('_config.php');
require ('./class/Analizze.php');
require ('./class/newEmptyPHP.php');


$var = new Analizze();
$dados = 	array( // row #3
		'valor' => '23000,00',
	);



//echo $var->catGeraRelacao();
//echo $var->addRegistro($dados);
//echo $var->deleteRegistro(5, 1);
//var_dump($var->getSaldoLancamentos());
//var_dump($var->getLancamentos());
//var_dump($var->getDadosRegistro(7921));
//var_dump($var->getDadosConta(1));
//var_dump($var->finalizarRegistro(7921, 10, false));
//var_dump ($var->getSaldoCC());





$ctaOrigem = 12;
$ctaDestino = 15;
$valor = "1184,45";

$result = $var->transferenciaEntreContas($ctaOrigem, $ctaDestino, $valor);

if ($result) {
    echo "TEF efetuada com sucesso.";
} else {
    echo "Erro na TEF";
}