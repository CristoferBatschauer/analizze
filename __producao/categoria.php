<?php
require ('./library/AnalizzeLibrary.php');
$categoria = new Categorias($_GET['id']);

if ($_POST)   {
    $categoria = new Categorias($_POST['id']);
    $categoria->setLabel($_POST['addCatNome']);
    $categoria->setIdStatus($_POST['catStatus']);
    $categoria->setTipo($_POST['addCatTipo']);
    $controller = new CategoriasController($categoria);
    $controller->save();
    $erro = $controller->getOut();
    
}
echo '<?xml version="1.0" encoding="utf-8"?>';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?= Config::getData('sistema', 'tagTitle') ?></title>
        <?= XOAD_Utilities::header('./library/xoad/'); ?>
        <meta name="viewport" content="initial-scale = 1.0, maximum-scale = 1.0, user-scalable = no, width = device-width">

        <link href="./jquery-mobile/jquery.mobile.theme-1.0.min.css" rel="stylesheet" type="text/css" />
        <link href="./jquery-mobile/jquery.mobile.structure-1.0.min.css" rel="stylesheet" type="text/css" />
        <script src="./jquery-mobile/jquery-1.6.4.min.js" type="text/javascript"></script>
        <script src="./jquery-mobile/jquery.mobile-1.0.min.js" type="text/javascript"></script>

        <script src="./library/js/xoad.js" type="text/javascript"></script>
        <script src="./library/js/jquery.maskMoney.js" type="text/javascript"></script>
        <script src="./library/js/jquery.maskInput.js" type="text/javascript"></script>


        <style>
            @media only screen and (min-width: 768px) { 
                .ui-mobile [data-role=page],.ui-mobile [data-role=dialog],.ui-page {
                    max-width:768px;
                    margin: 0 auto;
                    top: 0;
                    left: 0;
                    position:relative;
                    border: 0;

                }
                body   {
                    margin: 0 auto;
                    top: 0;
                    left: 0;
                    background-color:#666;		
                }
            }

        </style>

    </head>
    <body>
        <div data-role="page" id="addCategoria">
            <?= Analizze::geraHeader(array("texto" => "Voltar", 'link' => 'index.php', 'icone' => 'arrow-l'), "Adicionar Categoria", array('texto' => 'Concluir', 'link' => 'javascript:document.getElementById(\'categoriaForm\').submit();', 'icone' => 'check', 'class' => 'btnConcluirAddCat')) ?>
            <div data-role="content">
                <?= $erro ?>
                <form method="post" name="categoriaForm" id="categoriaForm" action="categoria.php">
                    <div data-role="fieldcontain">
                        <span id="addCatTipo-erro" class="Erros"></span>
                        <fieldset data-role="controlgroup" data-type="horizontal">
                            <legend>Tipo de categoria: </legend>
                            <input type="radio" name="addCatTipo" id="addCatTipo_0" value="1" <?= (($categoria->getTipo()==1)?"checked":"") ?>/>
                            <label for="addCatTipo_0">Entrada</label> 
                            <input type="radio" name="addCatTipo" id="addCatTipo_1" value="2" <?= (($categoria->getTipo()==2)?"checked":"") ?>/> 
                            <label for="addCatTipo_1">Saida</label>
                        </fieldset>

                        <span id="addCatNome-erro" class="Erros"></span><br> 
                        <label for="textinput">Nome: </label> 
                        <input type="text" name="addCatNome" id="addCatNome" value="<?= $categoria->getLabel() ?>" <?= (($categoria->getIdEmpresa() == 2)? "disabled" : "") ?>/>


                            <fieldset data-role="controlgroup" data-type="horizontal">
                                <legend>Status: </legend>
                                <input type="radio" name="catStatus" class="catStatus" id="catStatus_1" value="1" <?= (($categoria->getIdStatus()==1)?"checked":"") ?> /> 
                                <label for="catStatus_1">Ativo</label>
                                <input type="radio" name="catStatus" class="catStatus" id="catStatus_2" value="2" <?= (($categoria->getIdStatus()==2)?"checked":"") ?>/> 
                                <label for="catStatus_2">Inativo</label>
                            </fieldset>
                        
                        <input type="hidden" name="id" id="id" value="<?= $categoria->getId()?>" />
                </form>

            </div>
        </div><!-- fecha content -->
        <?= Analizze::geraFooter() ?>
    </div>
</body>
</html>
