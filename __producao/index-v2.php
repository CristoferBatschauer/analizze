<?php
require ('./library/AnalizzeLibrary.php');
$mob = new Analizze();

/** AJAX com XOAD * */
define('XOAD_AUTOHANDLE', true);
require_once('./library/xoad/xoad.php');
// Criando arquivo xoad.js
$fp = fopen('./library/js/xoad.js', 'w+');
fputs($fp, 'var aut = ' . XOAD_Client::register(new Analizze()));
fclose($fp);
/** AJAX com XOAD * */
$saldoPassivos = $mob->getSaldoCC('', true);
$saldoLancamentos = $mob->getSaldoLancamentos();
$balanco = ($saldoLancamentos['entradas'] + $saldoLancamentos['saidas'] + $saldoGeral["Saldo"]);

$lancamentoController = new LancamentosController(new Lancamentos());
echo '<?xml version="1.0" encoding="utf-8"?>';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Gerenciador Financeiro Analizze - By VizzualPontoCom</title>
        <?= XOAD_Utilities::header('./library/xoad/'); ?>
        <meta name="viewport" content="initial-scale = 1.0, maximum-scale = 1.0, user-scalable = no, width = device-width">

        <link href="./jquery-mobile/jquery.mobile.theme-1.0.min.css" rel="stylesheet" type="text/css" />
        <link href="./jquery-mobile/jquery.mobile.structure-1.0.min.css" rel="stylesheet" type="text/css" />

        <script src="./jquery-mobile/jquery-1.6.4.min.js" type="text/javascript"></script>
        <script src="./jquery-mobile/jquery.mobile-1.0.min.js" type="text/javascript"></script>

        <script src="./library/js/xoad.js" type="text/javascript"></script>
        <script src="./library/js/login.js" type="text/javascript"></script>
        <script src="./library/js/jquery.maskMoney.js" type="text/javascript"></script>
        <script src="./library/js/jquery.maskInput.js" type="text/javascript"></script>

        <link rel="stylesheet" type="text/css" href="library/css/estilo.css" />


    </head>

    <body>
        <div style="display:none"><img src="images/loading.gif" width="48" height="48"></div>
        <div style="display:none"><img src="images/loading_15_15.gif" width="48" height="48"></div>
        <div data-role="page" id="home">
            <?= Analizze::geraHeader(array("icone" => "gear", "texto" => "Config", "link" => "#config"), "Analizze", array("class" => "btnAddRegistro", "icone" => "plus", "texto" => "Add", "link" => "#addRegistro")) ?>
            <div data-role="content">
                <p id="pBalancoGeral" align="center" style="font-size: 24px"
                   class="<?= (($balanco > 0) ? "positivo" : "negativo") ?>">
                    Balanço até <?= RegNegocios::arrumaData($mob->dataLimite, 'mostrar') ?>: R$
                    <?= number_format($balanco, 2, ',', '.') ?>
                </p>
                <div data-role="collapsible-set" style="width:100%">
                    <div data-role="collapsible" style="width:100%">
                        <h3>Contas a pagar</h3>
                        <ul data-role="listview" data-inset="true">
                            <?php foreach ($lancamentoController->getList() as $lancamento) { ?>
                                <li><a href="#">
                                        <h3><?= $lancamento->getNomeCategoria() ?></h3>
                                        <p><?= $lancamento->getDescricao() ?></p>
                                        <span class="ui-li-count"><?= RegNegocios::formataString('valor', $lancamento->getValor()) ?></span>
                                        <p class="ui-li-aside"><?= RegNegocios::formataString('date', $lancamento->getVencimento(), 'mostrar') ?></p>
                                    </a>
                                    <a href="#">Padrão</a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>




                    <div data-role="collapsible" data-collapsed="true">
                        <h3>Contas a receber</h3>
                        <p>
                            <?= $mob->getLancamentos(1) ?>
                        </p>
                    </div>
                    <div data-role="collapsible" data-collapsed="true">
                        <h3>Extrato de Contas</h3>
                        <div id="extratoConta_1">
                            <?= $mob->getExtratoCC() ?>
                            <?= "Saldo passivos: R$" . $saldoPassivos['saldoFormatado'] ?>
                        </div>

                    </div>
                    <div id="divBalancoGeral">
                    </div>

                </div>

            </div>
            <!-- close content home -->
            <?= Analizze::geraFooter(true, true) ?>
        </div>
        <!-- close home -->

    </body>
</html>
