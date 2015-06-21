<?php
$sessaoLivre = true;
require ('./library/AnalizzeLibrary.php');

/*
require ('_config.php');
require ('class/RegNegocios.php');
require ('class/Analizze.php');
require ('class/Login.php');
 * 
 */
$mob = new Analizze();
$login = new Login();


define('XOAD_AUTOHANDLE', true);
require_once('./library/xoad/xoad.php');
// Criando arquivo xoad.js
$fp = fopen('./library/js/login.js', 'w+');
fputs($fp, 'var user = ' . XOAD_Client::register(new Login()));
fclose($fp);
/** AJAX com XOAD * */


if ($_POST) {
    $user = new Login();
    $ret = $user->logar($_POST['login'], $_POST['pass'], $_POST['idEmpresa']);
    if ($ret['result'] == 1)   {
        ?><script>window.location = "index.php";</script><?php
    }
}

?>

<?= '<?' ?>xml version="1.0" encoding="utf-8"?>

<!DOCTYPE html> 
<html>
    <head>
        <meta charset="utf-8">
        <title>Gerenciador Financeiro Analizze - By VizzualPontoCom</title>
        <?= XOAD_Utilities::header('./library/xoad/'); ?>
        <meta name="viewport" content="initial-scale = 1.0, maximum-scale = 1.0, user-scalable = no, width = device-width">
        <link href="./jquery-mobile/jquery.mobile.theme-1.0.min.css" rel="stylesheet" type="text/css"/>
        <link href="./jquery-mobile/jquery.mobile.structure-1.0.min.css" rel="stylesheet" type="text/css"/>
        <script src="./jquery-mobile/jquery-1.6.4.min.js" type="text/javascript"></script>
        <script src="./jquery-mobile/jquery.mobile-1.0.min.js" type="text/javascript"></script>
        
        <script src="./library/js/login.js" type="text/javascript"></script>

        <style>
            .Erros {
                font-family: Verdana, Geneva, sans-serif;
                font-size: 12px;
                font-weight: bold;
                color: #F00;
                padding: 5px;
                height: 35px;
                width: 100%;
                display:none;
            }
            #div_login {
                height: 100%;
                width: 100%;
                border: 1px solid black;
                text-align: center;
                background-attachment: fixed;
                background-position: center top;
                background-image: url(images/bglogin.png);
                background-repeat: no-repeat;
            }
            .tamanhoMaximo {
                width: 100px;
                text-align: center;
            }
            @media only screen and (min-width: 768px) { /*<- isso eh chamado de Media Features */
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
                    top: 10;
                    left: 0;
                    background-color:#666;		
                }

            }
            }

        </style>

    </head> 
    <body>
        <div data-role="page" id="div_login" style="">
            <?= Analizze::geraHeader(array(), "Analizze Gerenciador Financeiro", array(), array()) ?>
            <div style="text-align:center;">
                <h2>Gerenciador Financeiro <br>Analizze</h2>
                <h4>By PrC Systems</h4>
            </div>
            <div data-role="content" id="login_content">
                <form method="post" id="userLogin">
                    <span id="texto"></span>
                    <div data-role="fieldcontain">
                        <p id="pLogin">
                            <label for="textinput">Login:</label><br>
                            <input type="text" name="login" id="login" value="cibaecris@gmail.com<?=$ret['login']?>" style="max-width: 300px;" /><br>
                        </p>
                        <p id="senha">
                            <label for="passwordinput">Senha:</label><br>
                            <input type="password" name="pass" id="pass" value="Luana001<?=$ret['pass']?>" style="max-width: 300px;" /><br>
                        </p>

                        <?= $ret['empresas'] ?>

                        <!--  nova senha -->
                        <p id="novaSenha" style="display:none">
                            <label for="passwordinput">Nova senha:</label><br>
                            <input type="password" name="newPass1" id="newPass1" value="" style="max-width: 300px;" /><br>
                            <label for="passwordinput">Repita nova senha:</label><br>
                            <input type="password" name="newPass2" id="newPass2" value="" style="max-width: 300px;" /><br>
                        </p>

                        <div align="center" id="DivBtEntrar">
                            <!-- <a href="#"  onclick="javascript:document.getElementById('userLogin').submit();" data-role="button" class="btEntrar21" style="max-width: 200px;"><img src="images/administrator.png" width="18" height="18"> <br>Entrar</a></div> -->
                            <a href="#" data-role="button" class="btEntrar" style="max-width: 200px;"><img src="images/administrator.png" width="18" height="18"> <br>Entrar</a></div>
                        <div id="loading" style="display:none"><img src="images/loading.gif" width="33" height="33"> Carregando...</div>

                        <div align="center">
                            <a href="#" data-role="button1" class="btEsqueciSenha" style="max-width: 200px;">Esqueci minha senha</a>
                            <a href="#" data-role="button1" class="btTrocarSenha" style="max-width: 200px;">Trocar minha senha</a>      
                            <!--<a href="#" data-role="button1" class="btEsqueciLogin" style="max-width: 200px;">Esqueci meu login</a>                -->
                        </div>
                    </div>
                </form>

            </div>

            <?= Analizze::geraFooter(false) ?>
        </div>
        <script>
            $(document).ready(function() {
                var WinSizeHor = 320;
                var WinSizeVert = 440;
                posHoriz = parseInt((screen.availWidth / 2) - parseInt(WinSizeHor / 2))
                posVert = parseInt((screen.availHeight / 2) - parseInt(WinSizeVert / 2))
                self.moveTo(posHoriz, posVert);
                self.resizeTo(WinSizeHor, WinSizeVert);

                if (screen.availWidth > 500) {
                    //		$("#login").addClass("tamanhoMaximo");
                }


                $(".btEntrar").click(function() {
                    //$("#userLogin").submit();
                    //return true;
                    $("#DivBtEntrar").hide();
                    $("#loading").fadeIn();
                    $(".btEsqueciSenha").fadeOut();
                    user.logar($("#login").attr('value'), $("#pass").attr('value'), function(ret) {
                        if (ret['result'] == 0) {
                            $("#texto").removeClass().addClass("Erros").html("Não localizado. <br><br>Entre com seu login e senha:").fadeIn();
                            $("#login").attr('value', '');
                            $("#pass").attr('value', '');
                            $("#DivBtEntrar").fadeIn();
                            $("#loading").hide();
                            $(".btEsqueciSenha").fadeIn();
                            return false;
                        }
                        else if (ret['result'] == 99) { // mais de um usuario para empresa
                            $("#userLogin").submit();
                            /*
                             $("#loading").hide();
                             $("#pLogin").hide();
                             $("#novaSenha").fadeIn().css('background', '#FFF').html("Escolha em qual conta irá atuar: " + ret['empresas']);
                             return true;
                             */
                        }
                        else {
                            // verificar se tem troca senha
                            if ($("#newPass1").val() != "") {
                                if ($("#newPass1").val() != $("#newPass2").val()) {
                                    $("#DivBtEntrar").fadeIn();
                                    $("#loading").fadeOut();
                                    $("#texto").removeClass().addClass("Erros").html("As senhas digitadas n�o conferem!").fadeIn();
                                    return false;
                                }
                                else {
                                    $("#texto").removeClass().addClass("Erros").html("Alterando Senha...").fadeIn();
                                    user.atualizasenha(ret['idUser'], $("#newPass1").attr('value'));
                                    $("#texto").removeClass().addClass("Erros").html("Continuando login...").fadeIn();
                                }
                            }
                            window.location = "index.php";
                        }

                    });
                });
                $(".btEsqueciSenha").click(function() {
                    $("#texto").html('<?= $mob->getLoading(100) ?>');
                    if ($("#login").val() == "") {
                        $("#texto").removeClass().html("Informe seu login para proceder com nova senha").fadeIn();
                        $("#senha").fadeOut();
                        $(".btEntrar").fadeOut();
                        $(".btEsqueciSenha").html('<input name="Enviar Nova Senha" type="button" class="btEsqueciSenha2" value="Enviar Nova Senha">');
                    }
                    else { // proceder envio
                        user.reenviapw($("#login").val(), function(ret) {
                            if (ret)
                                $("#texto").removeClass().html("Nova senha enviada para email de cadastro. Favor verificar.").fadeIn();
                            else
                                $("#texto").removeClass().html("Nova senha não enviada. Verifique seu usuário.").fadeIn();
                            $("#pLogin").fadeOut();
                            $("#senha").fadeOut();
                            $(".btEntrar").fadeOut();
                            $(".btEsqueciSenha").fadeOut();
                        });
                    }
                });

                $(".btTrocarSenha").click(function() {
                    if ($("#newPass1").val() == "") {
                        $("#novaSenha").fadeIn();
                    }
                });
            });
        </script>
    </body>
</html>