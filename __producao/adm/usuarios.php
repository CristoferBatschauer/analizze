<?php
session_start();
/*
if (!isset($_SESSION['idUser'])) {
    header("Location:login.php");
    die("Acesso nao permitido");
}
*/
require ('../library/AnalizzeLibrary.php');

$admin = new Admin();
$empresas = Empresa::getRelacaoEmpresas();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Gerenciador Financeiro Analizze - By VizzualPontoCom</title>
        <meta name="viewport" content="initial-scale = 1.0, maximum-scale = 1.0, user-scalable = no, width = device-width">

        <link href="../jquery-mobile/jquery.mobile.theme-1.0.min.css" rel="stylesheet" type="text/css" />
        <link href="../jquery-mobile/jquery.mobile.structure-1.0.min.css" rel="stylesheet" type="text/css" />
        <script src="../jquery-mobile/jquery-1.6.4.min.js" type="text/javascript"></script>
        <script src="../jquery-mobile/jquery.mobile-1.0.min.js" type="text/javascript"></script>
    </head>
    <body>

        <div data-role="page" id="home" data-theme="d">
            <div data-role="header" data-theme="b"><div align="center"><h2>Administração Analizze</h2></div></div>
            <div data-role="content" data-theme="d">
                <ul data-role="listview">
                    <li><a href="#empresas">Clientes</a></li>
                    <li><a href="#">Estatisticas</a></li>
                    <li><a href="#">Anotações</a></li>
                </ul>
            </div>
            <div data-role="footer" data-theme="b">
                <div align="center" id="logoff">
                    <a href="../index.php" data-ajax="false">
                        <img src="../images/logoff.gif" />
                    </a></div>
            </div>
        </div>



        <div data-role="page" id="empresas" data-theme="d">
            <?= $admin->geraHeader(array("texto" => "Home", 'link' => '#home', 'icone' => 'home'), "Usuários", array("class" => "btnAddRegistro", "icone" => "plus", "texto" => "Add", "link" => "clienteCadastro.php"))
            ?>
            <div data-role="content" data-theme="d">
                <?php
                foreach ($empresas as $empresa) {
                    ?>

                    <ul data-role="listview" data-inset="true" data-split-icon="minus">
                        <li>
                            <a href="clienteCadastro.php?i=<?= $empresa->getIdEmpresa() ?>">
                                <h3><?= $empresa->getNome() ?></h3>
                                <p>Responsável: <?= $empresa->getResponsavel() ?><br />Email: <?= $empresa->getEmail() ?></p>
                                <p>Data cadastro: <?= RegNegocios::arrumaData($empresa->getData(), 'mostrar') ?></p>
                            </a><a href="javascript:void(0)" onclick="rem(<?= $empresa->getIdEmpresa() ?>)">Padrão</a>
                        </li>
                    </ul>
                <?php } ?>
            </div>   
            <?= $admin->geraFooter(); ?>
        </div>

        <script>
            function rem(id) {
                if (confirm("Confirma remover este cliente?\n\nATENÇÃO: Este processo é irreversivel. Todos os registros deste cliente serão removidos e não existe como recuperar.")) {
                    window.location = "clienteCadastro.php?r=1&i=" + id;
                }
            }

        </script>

    </body>
</html>