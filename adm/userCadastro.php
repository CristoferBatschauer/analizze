<?php
session_start();

if (!isset($_SESSION['idUser'])) {
    header("Location:login.php");
    die("Acesso nao permitido");
}

require ('../_config.php');
require ('../class/RegNegocios.php');
require ('../class/Admin.php');
require ('../class/entidades/Empresa.php');
$admin = new Admin();

// remoção
if ($_GET['r'] == 1)   {
    $user = new User((int)$_GET['u']);
    $retorno = "clienteCadastro.php?i=" . $user->getIdEmpresa();
    $user->remove();
    ?><script>window.location="<?=$retorno?>"</script><?php
    header("Location:" . $retorno);    
    die("Removido com sucesso");
}

if ($_POST) {
    $user = new User(false, $_POST);
    $user->save();
    header("Location:clienteCadastro.php?i=" . $user->getIdEmpresa());
    ?><script>window.location="clienteCadastro.php?i=<?=$user->getIdEmpresa()?>"</script><?php
} else {
    if ($_GET['id']) {
        $id = (int) $_GET['id'];
        $user = new User($id);
    } else {
        $idEmpresa = (int) $_GET['e'];
        $user = new User(false);
        $user->setIdEmpresa($idEmpresa);
        if (!$user->getIdEmpresa())   die("Erro E");
    }
}

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


        <div data-role="page" id="userCadastro" data-theme="d">
            <?= $admin->geraHeader(array("texto" => "Voltar", 'link' => 'javascript:history.back()', 'icone' => 'arrow-l'), "Usuário", array('texto' => 'Salvar', 'link' => 'javascript:document.getElementById(\'userCadastroForm\').submit()', 'icone' => 'check'))
            ?>
            <div data-role="content" data-theme="d">
                <form method="post" id="userCadastroForm">
                    <div data-role="fieldcontain">
                        <label for="nome">Nome:</label>
                        <input type="text" name="nome" id="nome" value="<?= $user->getNome() ?>"  />
                            <label for="login">Email:</label>
                            <input type="text" name="login" id="login" value="<?= $user->getLogin() ?>"  />
                            <label for="diaInicioContabil">Dia inicio Contábil:</label>
                            <input type="text" name="diaInicioContabil" id="diaInicioContabil" value="<?= $user->getDiaInicioContabil() ?>"  />
                            <label for="senha">Senha:</label>
                            <input type="password" name="senha" id="senha" value="<?= $user->getSenha() ?>"  />
                    </div>
                    <input name="idEmpresa" type="hidden" value="<?= $user->getIdEmpresa() ?>">
                    <input name="idUser" type="hidden" value="<?= $user->getIdUser() ?>">
                </form>
            </div>   
            <?= $admin->geraFooter(); ?>
        </div>



    </body>
</html>