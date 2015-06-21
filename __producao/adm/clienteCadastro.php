<?php
/*
session_start();
if (!isset($_SESSION['idUser'])) {
    header("Location:../login.php");
    die("Acesso nao permitido");
}
*/
require ('../library/AnalizzeLibrary.php');

$admin = new Admin();
if ($_POST)   {
    $empresa = new Empresa(false, $_POST);
    $empresa->save();
    header("Location:usuarios.php#empresas");
    ?><script>window.location="usuarios.php#empresas"</script><?php
}
else   {
    $idEmpresa = (int) $_GET['i'];
    $empresa = new Empresa($idEmpresa);
}
$users = $empresa->getUsers();

// remoção
if ($_GET['r'] == 1)   {
    $empresa->remove();
    header("Location:usuarios.php#empresas");
    ?><script>window.location="usuarios.php#empresas"</script><?php
    die("Removido com sucesso");
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


        <div data-role="page" id="empresaCadastro" data-theme="d">
            <?php echo $admin->geraHeader(array("texto" => "Voltar", 'link' => 'usuarios.php#empresas', 'icone' => 'arrow-l'), 
                    "Cadastro", array('texto' => 'Salvar', 'link' => 'javascript: document.getElementById(\'clienteCadastro\').submit()', 'icone' => 'check', 'class' => 'btnConcluir')) ?>
            <div data-role="content" data-theme="d">
                <form method="post" id="clienteCadastro">
                <div data-role="fieldcontain">
                    <label for="nome">Nome Cliente:</label>
                    <input type="text" name="nome" id="nome" value="<?= $empresa->getNome() ?>"  />
                        <label for="responsavel">Nome responsável:</label>
                        <input type="text" name="responsavel" id="responsavel" value="<?= $empresa->getResponsavel() ?>"  />
                        <label for="email">E-mail responsável:</label>
                        <input type="text" name="email" id="email" value="<?= $empresa->getEmail() ?>"  />
                        <label for="logo">Logo:</label>
                        <input type="text" name="logo" id="logo" value="<?= $empresa->getLogo() ?>"  />
                </div>
                    <input name="idEmpresa" type="hidden" value="<?= $empresa->getIdEmpresa() ?>">
                </form>
                <hr>
                <h3>Usuários
                    <a href="userCadastro.php?e=<?=$empresa->getIdEmpresa()?>" data-ajax="false"><img src="../images/add.png" width="18" height="18"	alt="Adicionar Usuários"> </a>
                </h3>
                <ul data-role="listview" data-theme="d" data-inset="true" data-split-icon="minus">
                    <?php
                    foreach ($users as $user) {
                        echo '
                            <li>
                                <a href="userCadastro.php?id='.$user->getIdUser().'">
                                    <h3>'.$user->getNome().'</h3>
                                    <p>Login: '.$user->getLogin().'</p>
                                    <p>Último acesso: '.$user->getUltAcesso().'</p>
                                </a>
                                <a data-ajax="false" href="javascript:void(0)" onclick="javascript: rem('.$user->getIdUser().')">Padrão</a>
                            </li>
                ';
                    }
                    ?>
                </ul>

            </div>   
            <?php echo $admin->geraFooter(); ?>
        </div>
        <script>
        function rem(id)   {
            if (confirm("Confirma remover este usuário?"))   {
                window.location = "userCadastro.php?r=1&u="+id;
            }
        }
        
        </script>
    </body>
</html>