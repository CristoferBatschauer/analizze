<?php
require ('./library/AnalizzeLibrary.php');

//require ('_config.php');
//require ('class/RegNegocios.php');
//require ('class/Analizze.php');
//require ('class/Relatorios.php');
//require ('class/Login.php');

$mob = new Analizze();
//$_SESSION['idEmpresa'] = 10;
//$_SESSION['idUser'] = 10;

/** AJAX com XOAD **/
define('XOAD_AUTOHANDLE', true);
require_once('./library/xoad/xoad.php');
// Criando arquivo xoad.js
$fp = fopen('./library/js/xoad.js', 'w+');
fputs ($fp, 'var aut = '.XOAD_Client::register(new Analizze()));
fclose($fp);

/** AJAX com XOAD **/

//$saldoGeral =  $mob->getSaldoCC('', true);
$saldoPassivos = $mob->getSaldoCC('', true);
$saldoLancamentos = $mob->getSaldoLancamentos();
$balanco = ($saldoLancamentos['entradas'] + $saldoLancamentos['saidas'] + $saldoGeral["Saldo"]);

?>
<?= '<?' ?>
xml version="1.0" encoding="utf-8"?>

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


<style>
.Erros {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #F00;
	padding: 5px;
	height: 35px;
	width: 100%;
	display: none;
}

.positivo {
	color: #00F;
}

.negativo {
	color: #F00;
}

#step2Valor {
	font-size: 24px;
	font-weight: bold;
	text-decoration: none;
}
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
	#extratoConta_1   {
		max-width: 640px;
	}
}

</style>

</head>
<body>
<div style="display:none"><img src="images/loading.gif" width="48" height="48"></div>
<div style="display:none"><img src="images/loading_15_15.gif" width="48" height="48"></div>
<div data-role="page" id="home">
		<?= Analizze::geraHeader(array("icone"=>"gear", "texto"=>"Config", "link"=>"#config"), "Analizze", array("class"=>"btnAddRegistro", "icone"=>"plus", "texto"=>"Add", "link"=>"#addRegistro") ) ?>
		<div data-role="content">
			<p id="pBalancoGeral" align="center" style="font-size: 24px"
				class="<?= (($balanco>0)?"positivo":"negativo") ?>">
				Balanço até <?= RegNegocios::arrumaData($mob->dataLimite, 'mostrar') ?>: R$
				<?= number_format($balanco, 2, ',', '.') ?>
			</p>
			<div data-role="collapsible-set" style="width:100%">
				<div data-role="collapsible" style="width:100%">
					<h3>Contas a pagar&nbsp;&nbsp;&nbsp;&nbsp;</h3>
					<p>
						<?= $mob->getLancamentos(2) ?>
					</p>
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



	<div data-role="page" id="config">
		<?= Analizze::geraHeader(array("texto"=>"Home", 'link'=>'#home', 'icone'=>'home'), "Configurações", array()) ?>
		<div data-role="content">
			<ul data-role="listview" data-split-icon="forward">
            	<?php if ($_SESSION['idUser'] == 1)   { // Cristofer ?>
                <li>
                <a href="./adm/usuarios.php#empresas" data-ajax="false">
                <h3>Administração Sistema</h3>
                <p>Administração do Sistema Analizze</p>
                </a>
                </li>                              
                <?php } ?>
                
				<li><a href="#catRelacao">
						<h3>Categorias</h3>
						<p>Exibe as categorias atuais e possibilita criar novas</p>
				</a></li>
				<li><a href="#contas">
						<h3>Contas</h3>
						<p>Exibe as contas atuais e possibilita criação de novas contas</p>
				</a></li>
				<li><a href="#relatorios">
						<h3>Relatórios</h3>
						<p>Relatórios mensais, contas a pagar, receber</p>
				</a></li>
				<li><a href="#config_data">
						<h3>Dia de incio  do Ciclo Contábil</h3>
						<p>Escolha o dia de início do ciclo contábil. Dia atual: <?= $_SESSION['diaInicioContabil']?></p>
				</a></li>                              
			</ul>

		</div>
		<?= Analizze::geraFooter() ?>
	</div>

	<div data-role="page" id="finalizarRegistro">
		<?= Analizze::geraHeader(array("texto"=>"Home", 'link'=>'#home', 'icone'=>'home'), "Finalizar Registro", array()) ?>
		<div data-role="content">
			<p>
				<b>Tipo: </b> <span id="spTipo"></span>
			</p>
			<p>
				<b>Categoria: </b> <span id="spCategoria"></span>
			</p>
			<p>
				<b>Notas: </b> <span id="spNotas"></span>
			</p>
			<p>
				<b>Vencimento: </b><span id="spVencimento"></span>
			</p>
			<p>
				<b>Valor: </b><span id="spValor"></span>
			</p>
			<p>
			
			
			<div data-role="fieldcontain">
				<label for="selectmenu" class="select"><b>Escolha Conta: </b> </label>
				<select name="relacaoContas" id="relacaoContas"><?= $mob->getRelacaoContas() ?>
				</select> <a href="javascript:void(0)" id="btFinalizarRegistro"
					data-role="button" data-icon="check">Finalizar Registro</a>

			</div>
			</p>

		</div>
		<?= Analizze::geraFooter() ?>
	</div>

	<div data-role="page" id="addRegistro">
		<?= Analizze::geraHeader(array("texto"=>"Home", 'link'=>'#home', 'icone'=>'home'), "Adicionar Registro", array("texto"=>"Próx", "link"=>"javascript:void(0)", 'icone'=>'arrow-r', 'class'=>'avancaRegistro')) ?>
		<div data-role="content">
			<div data-role="fieldcontain">
				<span id="cat-erro" class="Erros"></span>
				<fieldset data-role="controlgroup" data-type="horizontal">
					<legend>Opção: </legend>
					<input type="radio" name="opcao" id="opcao_0" class="btnOpcao"
						value="+" /> <label for="opcao_0">Entrada</label> <input
						type="radio" name="opcao" id="opcao_1" class="btnOpcao" value="-" />
					<label for="opcao_1">Saida</label>
				</fieldset>

				<span id="valor-erro" class="Erros"></span> <label for="valor">Valor:</label>
				<input name="valor" style="width:150px;" type="text" id="valor" value="" size="20" />

				<div id="btnDelete" style="width: 100%; display: none">
					<hr>
					<div id="qualExcluir">
					<fieldset data-role="controlgroup" data-type="horizontal">
						<legend><h3>Excluir Registro: </h3></legend>
						<input type="radio" name="excluir_ocorrencia" id="excluir_ocorrencia_0" value="1"
							checked/> <label  style="font-size: 12px;float:left;margin:2px;padding:0;width: 100px;height:40px;text-align: center;" for="excluir_ocorrencia_0">Este</label>
						<input type="radio" name="excluir_ocorrencia" id="excluir_ocorrencia_1" value="2"
							 /> <label style="font-size: 12px;float:left;margin:2px;padding:0;width: 100px;height:40px;text-align: center;" for="excluir_ocorrencia_1">Este + Futuros</label>
                        <!-- Tirei "Todos" para evitar erros de contabilidade
						<input type="radio" name="excluir_ocorrencia" id="excluir_ocorrencia_2" value="3"
							 /> <label style="font-size: 12px;float:left;margin:2px;padding:0;width: 100px;height:40px;text-align: center;" for="excluir_ocorrencia_2">Todos</label>
                             -->
					</fieldset>
					</div>
					<button data-icon="delete" data-iconpos="right" class="removeRegistro" id="btRemoverRegistro">Remover Registro</button>
					
				</div>
			</div>
		</div>
		<!-- fecha addRegistroContent -->
		<?= Analizze::geraFooter() ?>
	</div>

	<div data-role="page" id="addRegistroStep2">
		<?= Analizze::geraHeader(array("texto"=>"Voltar", 'link'=>'javascript:history.back()', 'icone'=>'arrow-l'), "Finalizar Registro", array('texto'=>'Concluir', 'link'=>'javascript: void(0)', 'icone'=>'check', 'class'=>'btnConcluir')) ?>
		<div data-role="content">
			<div class="ui-grid-a">
				<div class="ui-block-a"></div>
				<div class="ui-block-b">
					<div align="right" id="step2Valor"></div>
				</div>
			</div>
			<span class="Erros" id="addRegistroIdCat-erro"></span><br> Categoria:
			<a href="#" onClick="javascript:catSetRegistro(false)"><img
				src="images/add.png" width="18" height="18"
				alt="Adicionar Categoria"> </a>
			<?= $mob->catGeraRelacao(1) ?>
			<?= $mob->catGeraRelacao(2) ?>

			<div data-role="fieldcontain">
				<span class="Erros" id="addRegistroVencimento-erro"></span><br> <label
					for="vencimento">Data vencimento: </label> <input name="vencimento"	type="text" id="vencimento" value="" maxlength="10" style="width:130px;" />
					
					
				<div id="reocorrencia_escolhe" class="reocorrencia">
				<fieldset data-role="controlgroup" data-type="horizontal">
					<legend>Ocorrência: </legend>
					<input type="radio" name="ocorrencias" id="ocorrencias_0" value="1"
						class="btnEscolheOcorrencia" /> <label for="ocorrencias_0">Único</label>
					<input type="radio" name="ocorrencias" id="ocorrencias_1"
						value="12" class="btnEscolheOcorrencia" /> <label
						for="ocorrencias_1">Repetir Mensal</label><br> <br> <br>
					<div id="qtdeOcorrencias" style="display: none">
						<label for="textinput">Quantidade meses:</label> <input
							type="text" name="ocorrencias_quantidade"
							id="ocorrencias_quantidade" value="1" />
					</div>

				</fieldset>
				</div>
				
				<div id="qualAlterar" style="display:none" class="reocorrencia">
				<fieldset data-role="controlgroup" data-type="horizontal">
					<legend>Alterar: </legend>
					<input type="radio" name="alterar_ocorrencia" id="alterar_ocorrencia_0" value="1"
						class="btnEscolheOcorrencia" checked/> <label style="font-size: 12px;float:left;margin:2px;padding:0;width: 100px;height:40px;text-align: center;" for="alterar_ocorrencia_0">Este</label>
					<input type="radio" name="alterar_ocorrencia" id="alterar_ocorrencia_1" value="2"
						class="btnEscolheOcorrencia" /> <label style="font-size: 12px;float:left;margin:2px;padding:0;width: 100px;height:40px;text-align: center;" for="alterar_ocorrencia_1">Este + futuros</label>
                        <!-- Tirei "Todos" para evitar erros de contabilidade
					<input type="radio" name="alterar_ocorrencia" id="alterar_ocorrencia_2" value="3"
						class="btnEscolheOcorrencia" /> <label style="font-size: 12px;float:left;margin:2px;padding:0;width: 100px;height:40px;text-align: center;" for="alterar_ocorrencia_2">Todos</label>
                        -->
				</fieldset>
				</div>
				
				
				
				<label for="textarea">Notas: </label>
				<textarea cols="40" rows="8" name="notas" id="notas"></textarea>
			</div>

		</div>
		<!-- fecha addRegistroStep2Content -->
		<?= Analizze::geraFooter() ?>
	</div>


	<div data-role="page" id="catRelacao">
		<?= Analizze::geraHeader(array("texto"=>"Voltar", 'link'=>'#config', 'icone'=>'arrow-l'), "Categorias", array("class"=>"btnAddCategoria", "icone"=>"plus", "texto"=>"Add", "link"=>"#addCategoria") ) ?>
		<div data-role="content">
			<h2>Categorias ativas:</h2>
			<div data-role="collapsible-set">
				<div data-role="collapsible"data-collapsed="false">
					<h3>Categorias para Entradas</h3>
					<?= $mob->catGeraRelacao(1, 'addCatCategorias11', 'onclick="javascript:catSetRegistro(this.value)"') ?>
				</div>
				<div data-role="collapsible" data-collapsed="true">
					<h3>Categorias para Saídas</h3>

					<?= $mob->catGeraRelacao(2, 'addCatCategorias12', 'onclick="javascript:catSetRegistro(this.value)"') ?>

				</div>
			</div>
			<hr>
			<h2>Categorias inativas:</h2>
			<div data-role="collapsible-set">
				<div data-role="collapsible" >
					<h3>Categorias para Entradas</h3>
					
						<?= $mob->catGeraRelacao(1, 'addCatCategorias21', 'onclick="javascript:catSetRegistro(this.value)"', 2) ?>
					
				</div>
				<div data-role="collapsible" data-collapsed="true">
					<h3>Categorias para Saídas</h3>
					
						<?= $mob->catGeraRelacao(2, 'addCatCategorias22', 'onclick="javascript:catSetRegistro(this.value)"', 2) ?>
					
				</div>
			</div>


		</div>
		<?= Analizze::geraFooter() ?>
	</div>

	<div data-role="page" id="addCategoria">
		<?= Analizze::geraHeader(array("texto"=>"Voltar", 'link'=>'#catRelacao', 'icone'=>'arrow-l'), "Adicionar Categoria", array('texto'=>'Concluir', 'link'=>'javascript: void(0)', 'icone'=>'check', 'class'=>'btnConcluirAddCat')) ?>
		<div data-role="content">
			<div data-role="fieldcontain">
				<span id="addCatTipo-erro" class="Erros"></span>
				<fieldset data-role="controlgroup" data-type="horizontal">
					<legend>Tipo de categoria: </legend>
					<input type="radio" name="addCatTipo" id="addCatTipo_0" value="1" />
					<label for="addCatTipo_0">Entrada</label> <input type="radio"
						name="addCatTipo" id="addCatTipo_1" value="2" /> <label
						for="addCatTipo_1">Saida<br>
					</label>
				</fieldset>
				<span id="addCatNome-erro" class="Erros"></span><br> <label
					for="textinput">Nome: </label> <input type="text" name="addCatNome"
					id="addCatNome" value="" />

				<div id="btnDeleteCat" style="display: none">
					<fieldset data-role="controlgroup" data-type="horizontal">
						<legend>Status: </legend>
						<input type="radio" name="catStatus" class="catStatus"
							id="catStatus_1" value="1" /> <label for="catStatus_1">Ativo</label>
						<input type="radio" name="catStatus" class="catStatus"
							id="catStatus_2" value="2" /> <label for="catStatus_2">Inativo</label>
					</fieldset>
				</div>
			</div>
		</div>
		<?= Analizze::geraFooter() ?>
	</div>


	<div data-role="page" id="contas">
		<?= Analizze::geraHeader(array("texto"=>"Voltar", 'link'=>'#config', 'icone'=>'arrow-l'), "Contas", array("class"=>"btnAddConta", "icone"=>"plus", "texto"=>"Add", "link"=>"#addConta") ) ?>
		<div data-role="content">
			<h3>Extrato de Contas</h3>
			<div id="contas_extratoConta_1">
				<?= $mob->getExtratoCC(false, false, false, true) ?>
			</div>

		</div>
		<?= Analizze::geraFooter() ?>
	</div>

	<div data-role="page" id="addConta">
		<?= Analizze::geraHeader(array("texto"=>"Voltar", 'link'=>'#contas', 'icone'=>'arrow-l'), "Adicionar Contas", array('texto'=>'Concluir', 'link'=>'javascript: void(0)', 'icone'=>'check', 'class'=>'btnConcluirAddConta')) ?>
		<div data-role="content">
			<div data-role="fieldcontain">
				<p>
					<span class="Erros" id="addContaNome-erro"></span>Nome conta:<br> <input
						type="text" name="addContaNome" id="addContaNome" value="" />
				</p>
				<p>
					Banco: <br> <input type="text" name="addContaBanco"
						id="addContaBanco" value="" />
				</p>
				<p>
					Número agência: <br> <input type="text" name="addContaAgencia"
						id="addContaAgencia" value="" />
				</p>
				<p>
					Número conta: <br> <input type="text" name="addContaConta"
						id="addContaConta" value="" />
				</p>
				<p>
					Nome titular: <br> <input type="text" name="addContaTitular"
						id="addContaTitular" value="" />
				<div data-role="fieldcontain">
				    <fieldset data-role="controlgroup" data-type="horizontal">
				        <legend>Tipo: </legend>
				        <input type="radio" name="passivo" id="passivo_0" value="0" />
				        <label for="passivo_0">Ativo</label>
				        <input type="radio" name="passivo" id="passivo_1" value="1" />
				        <label for="passivo_1">Passivo</label>
				    </fieldset>
			    </div>
			</div>
		</div>
		<?= Analizze::geraFooter() ?>
	</div>

	<div data-role="page" id="relatorios">
		<?= Analizze::geraHeader(array("texto"=>"Home", 'link'=>'#home', 'icone'=>'home'), "Relatórios", array() ) ?>
		<div data-role="content">
			<label>Data inicio: </label> 
            <input type="text" name="relDataInicio"	id="relDataInicio" value="<?= date('d/m/Y') ?>" style="width:130px;" /> 
            
            <label>Data fim:</label> 
            <input type="text" name="relDataFim" id="relDataFim" value="<?= date('d/m/Y', $mob->dataLimiteMktime-1) ?>" style="width:130px;" />
			<fieldset data-role="controlgroup" data-type="horizontal">
				<legend>Tipo: </legend>
				<input type="radio" name="relTipo" id="relTipo_0" value="1" onClick="javascript:relSelecionaCategoria(this.value)" />
				<label for="relTipo_0">Entrada</label> 
                <input type="radio" name="relTipo" id="relTipo_1" value="2" onClick="javascript:relSelecionaCategoria(this.value)" />
                <label for="relTipo_1">Saida</label>
				<input type="radio" name="relTipo" id="relTipo_2" value="3" onClick="javascript:relSelecionaCategoria(this.value)" checked /> 
                <label for="relTipo_2">Ambos</label>
			</fieldset>

			<fieldset data-role="controlgroup" data-type="horizontal">
				<legend>Status pagamento: </legend>
				<input type="radio" name="relStatus" id="relStatus_0" value="1" /> <label for="relStatus_0">Pendente</label> 
                <input type="radio" name="relStatus" id="relStatus_1" value="4" /> <label for="relStatus_1">Liquidado</label>
                <input type="radio" name="relStatus" id="relStatus_2" value="99"  checked /> <label for="relStatus_2">Todos</label>                
			</fieldset>

			<fieldset data-role="controlgroup" data-type="horizontal">
                <div id="relCategoriasDebito" class="relCategoriasClass" style="display:none">
      				<legend>Por categorias de débito (Opcional): </legend>
                <input type="radio" name="relCategoriasDebitoCheck" id="relCategoriasDebitoCheck_998" value="">
                <label for="relCategoriasDebitoCheck_998"  style="font-size: 12px;float:left;margin:2px;width: 140px;height:40px;text-align: center;">Nenhum</label>
                <?= $mob->catGeraRelacao(2, 'relCategoriasDebitoCheck')  ?>
                </div>
                <div id="relCategoriasCredito" class="relCategoriasClass" style="display:none">
      				<legend>Por categorias de crédito (Opcional): </legend>                
                <input type="radio" name="relCategoriasCreditoCheck" id="relCategoriasCreditoCheck_999" value="">
                <label for="relCategoriasCreditoCheck_999"  style="font-size: 12px;float:left;margin:2px;width: 140px;height:40px;text-align: center;">Nenhum</label>
                <?= $mob->catGeraRelacao(1, 'relCategoriasCreditoCheck')  ?>
                </div>
			</fieldset>


			<a href="#" data-role="button" class="btRelatorio">Gerar Relatório</a>

		</div>
		<?= Analizze::geraFooter() ?>
	</div>
    
<div data-role="page" id="config_data">
		<?= Analizze::geraHeader(array("texto"=>"Voltar", 'link'=>'#config', 'icone'=>'arrow-l'), "Ciclo Contábil", array() ) ?>
		<div data-role="content">
			<h3>Dia de inicio do ciclo contábil</h3>
			<p>Os lançamento serão exibidos na relação e no balanço até o dia anterior ao dia escolhido como inicio do ciclo. </p>
			<p>Este é o dia inicial utilizado até então nesta sessão: <?= $_SESSION['diaInicioContabil']?>            
			
			<div data-role="fieldcontain">
			    <label for="textinput">Alterar Dia para:</label>
			    <input type="text" name="diaCorteText" id="diaCorteText" value="<?= date('d') ?>" style="width:50px;" />
       			<fieldset data-role="controlgroup" data-type="horizontal">
				<legend>Como? </legend>
				<input type="radio" name="diaCorte" id="diaCorte_0" value="1" checked/>
				<label for="diaCorte_0">Somente esta sessão</label> 
                <input type="radio"	name="diaCorte" id="diaCorte_1" value="2" /> 
                <label for="diaCorte_1">Alterar meu cadastro</label>
			</fieldset>
                <a href="#" data-role="button" class="btAlteraDiaCorte">Atualizar</a>
                <div id="btAlterarCorteLoading" style="display:none"><img src="images/loading.gif" width="252" height="252" alt="Loading"></div>
		<?= Analizze::geraFooter() ?>

    </div>

<script>
var idRegistro = false;
var idCat = false;
var idConta = false;

function relSelecionaCategoria(tipo)   {
	$(".relCategoriasClass").hide();
	if (tipo == 1)   {
		$("#relCategoriasCredito").fadeIn();
		return true;
	}
	if (tipo == 2)   {
		$("#relCategoriasDebito").fadeIn();
		return true;
	}

}

function setRegistro(id, loc, ret)   {
	if (id != "")   {
			idRegistro = ret['idLancamento'];	
			// tratamento da reocorrencia
			$(".reocorrencia").hide();	
			$("#qualExcluir").hide();
			if (ret['idLancPai'] != "")   {
				$("#qualAlterar").fadeIn();
				$("#qualExcluir").fadeIn();
			}
			$("#valor").attr('value', ret['valorFormatado']);
			if (ret['valor'] > 0)    {
				document.getElementById("opcao_0").checked = true;
				$("#opcao_0").trigger('click');
			}
			else  {
				document.getElementById("opcao_1").checked = true;
				$("#opcao_1").trigger('click');
			}
			$('input[type="radio"][name="categoria"][value="'+ret['idCat']+'"]').checked = true;
			$('input[type="radio"][name="categoria"][value="'+ret['idCat']+'"]').trigger("click");
			$('input[type="radio"][name="categoria"][value="'+ret['idCat']+'"]').checked = true;
			$('input[type="radio"][name="categoria"][value="'+ret['idCat']+'"]').trigger("click");

			$("#vencimento").attr('value', ret['vencimento']);
			$("#notas").attr("value", ret['descricao']);
			$("#btnDelete").fadeIn();
			
			// Finalizar Registro
			$("#spCategoria").html(ret['catLabel']);
			$("#spNotas").html(ret['descricao']);
			$("#spValor").html(ret['valorFormatado']);
			if (ret['valor'] > 0)   {$("#spValor").removeClass().addClass("positivo"); $("#spTipo").html("Entrada"); }
			else                    {$("#spValor").removeClass().addClass("negativo"); $("#spTipo").html("Saída");   }

			$("#spVencimento").html(ret['vencimento']);
	}
	else   {
	}
}

/** Acao ao clicar em uma categoria, em addCAt **/
function catSetRegistro(id)   {
	if (id)   {
		aut.getdados('anz_categorias', 'idCat', id, function(ret)   {
			$("#addCatNome").attr('value', ret['label']);
			$('input[type="radio"][name="addCatTipo"][value="'+ret['tipo']+'"]').checked = true;
			$('input[type="radio"][name="addCatTipo"][value="'+ret['tipo']+'"]').trigger("click");
			$('input[type="radio"][name="addCatTipo"][value="'+ret['tipo']+'"]').checked = true;
			$('input[type="radio"][name="addCatTipo"][value="'+ret['tipo']+'"]').trigger("click");
			idCat = id;
			
			$('input[type="radio"][name="catStatus"][value="'+ret['idStatus']+'"]').checked = true;
			$('input[type="radio"][name="catStatus"][value="'+ret['idStatus']+'"]').trigger("click");
			$('input[type="radio"][name="catStatus"][value="'+ret['idStatus']+'"]').checked = true;
			$('input[type="radio"][name="catStatus"][value="'+ret['idStatus']+'"]').trigger("click");
			
			$("#btnDeleteCat").fadeIn();
			window.location = "#addCategoria";
		});
	}
	else   {
		$("#addCatNome").attr('value', '');
		idCat = false;
		$("#btnDeleteCat").hide();
		window.location = "#addCategoria";
	}
	
}

function contasSetRegistro(id)   {
	if (id)   {
		aut.getdados('anz_contas', 'idConta', id, function(ret)   {
			$("#addContaNome").attr('value', ret['nome']);
			$("#addContaBanco").attr('value', ret['banco']);
			$("#addContaAgencia").attr('value', ret['agencia']);
			$("#addContaConta").attr('value', ret['conta']);
			$("#addContaTitular").attr('value', ret['titular']);

			$('input[type="radio"][name="passivo"][value="'+ret['passivo']+'"]').checked = true;
			$('input[type="radio"][name="passivo"][value="'+ret['passivo']+'"]').trigger("click");
			$('input[type="radio"][name="passivo"][value="'+ret['passivo']+'"]').checked = true;
			$('input[type="radio"][name="passivo"][value="'+ret['passivo']+'"]').trigger("click");
		});
		idConta = id;
	}
	else   {
		idConta = false;
	}
}

function atualiza()   {
	$("#divBalancoGeral").html('<?= $mob->getLoading() ?>');
	$("#pBalancoGeral").html('<?= $mob->getLoading() ?>');

	aut.gerabalanco(function(ret)   {
		$("#divBalancoGeral").html(ret['div']);
		$("#pBalancoGeral").html(ret['p']);		
	});
	
}

$(document).ready(function()   {
	// tratar recarga de pagina
	var r = <?= (($_GET['r'])?$_GET['r']:0) ?>;
	switch (r)   {
		case 2:
			window.location = "index.php#catRelacao";
		break;
		case 3:
			window.location = "index.php#contas";
		break;
	}
	var forb = Array("#addRegistro", "#addRegistroStep2", "#finalizarRegistro"); // se hash for uma dessas, transferir para home, com recarga
	forb.forEach (function(e)   {
		if (window.location.hash == e)   
			window.location = "index.php";
	});
	// fim trata recarga paginas
        atualiza();

	// Escolhendo tipo de navegador
	var deviceAgent = navigator.userAgent.toLowerCase();
	var agentID = deviceAgent.match(/(iphone|ipod|ipad|android)/);
	if (agentID) { // mobile
		$("#vencimento").keyup(function()   {
				var valAtual = $("#vencimento").val();
				if (valAtual.length == 2)   $("#vencimento").attr('value', valAtual+ "/");
				if (valAtual.length == 5)   $("#vencimento").attr('value', valAtual+ "/");
		});
		$("#relDataInicio").keyup(function()   {
				var valAtual = $("#relDataInicio").val();
				if (valAtual.length == 2)   $("#relDataInicio").attr('value', valAtual+ "/");
				if (valAtual.length == 5)   $("#relDataInicio").attr('value', valAtual+ "/");
		});
		$("#relDataFim").keyup(function()   {
				var valAtual = $("#relDataFim").val();
				if (valAtual.length == 2)   $("#relDataFim").attr('value', valAtual+ "/");
				if (valAtual.length == 5)   $("#relDataFim").attr('value', valAtual+ "/");
		});

	} else { // pc
		$("#valor").maskMoney({showSymbol:true, prefix: "R$ ", decimal:",", thousands:"."});
		$("#vencimento").mask("99/99/9999");
		$("#relDataInicio").mask("99/99/9999");
		$("#relDataFim").mask("99/99/9999");
	}
	
	// setar Lancamento em operação
	$(".lancamentos_registros").click(function()   {
		var id = this.id.replace("liLancamentos_", "");
		$("#loadingLancamentos"+id).fadeIn();
		aut.getdadosregistro(id, function(ret)   {
			setRegistro(id, "addRegistro", ret);
			$("#loadingLancamentos"+id).fadeOut();
			window.location = "#addRegistro";
		});
	});
	// setar para finalizar registro em conta corrente
	$(".lancamentos_finalizar").click(function()   {
		var id = this.id.replace("liLancamentosFinalizar_", "");
		$("#loadingLancamentos"+id).fadeIn();
		aut.getdadosregistro(id, function(ret)   {
			setRegistro(id, "addRegistro", ret);
			$("#loadingLancamentos"+id).fadeOut();
			window.location = "#finalizarRegistro";
		});
	});
	
	// logoff
	$("#logoff").click(function()   {
		if (confirm("Deseja encerrar esta sessão?"))   {
			user.logoff();
			window.location = "index.php";
			return true;
		}
		else   return false;
	});
	
	// Metodo para atualizar o status de categoria
	$(".catStatus").click(function()   {
		aut.atualizastatus('anz_categorias', $(this).val(), 'idCat', idCat)
		//window.location = "#catRelacao"
	});


	// botaum remover registro
	$(".removeRegistro").click(function()   {
		var qualExcluir = $("input:radio[name=excluir_ocorrencia]:checked" ).val();
		var confimar = '';
		if (qualExcluir == 1) confirmar = "Confirma a exclusão deste registro?";
		else if (qualExcluir == 2)   confirmar = "Confirma a exclusão deste e dos futuros lançamentos?";
		else   confirmar = "Confirma a exclusão de todas as ocorrências? \n\nATENÇÃO: Este processo é irreversível!";    
		if (confirm(confirmar + "\n\nValor: " + $("#valor").val()))   {
			aut.deleteregistro(idRegistro, qualExcluir, function(ret)   {
				window.location = "index.php";				
			});
		}
	});

	
	$("#btFinalizarRegistro").click(function()   {
		if  ($("#relacaoContas").select().val() == "")   {
			alert("Escolha conta");
			return false;
		}
		if (confirm("Confirma finalizar lançamento?"))  {
			aut.finalizarregistro(idRegistro, $("#relacaoContas").select().val(), function(ret)   {
				if (ret[0] == true)   {
					$("#liLancamentos_"+idRegistro).hide();
					aut.getextratocc($("#relacaoContas").select().val(), function(ret)   {
						$("#extratoConta_"+$("#relacaoContas").select().val()).html(ret);
					});
					atualiza();
					window.location = "#home";
				}
				else   {
					alert(ret[1]);
				}
			});
		}
		else return false
//		
	});
	
	$(".btnAddRegistro").click(function()   {
		idRegistro = false;
		// tratamento da reocorrencia
		$(".reocorrencia").hide();	
		$("#reocorrencia_escolhe").fadeIn();
		$("#btnDelete").hide();
		$("#valor").attr('value', '');
		$("#vencimento").attr('value', '<?= date('d/m/Y') ?>');
		$("#notas").attr("value", '');
		document.getElementById("opcao_0").checked = false;
		document.getElementById("opcao_1").checked = false;
		idCat = false;
	});
	
	$(".btnEscolheOcorrencia").click(function()   {
		if (this.value == 1)     {
			$("#qtdeOcorrencias").hide();
			$("#ocorrencias_quantidade").attr('value', '1');
		}
		else   {
			$("#qtdeOcorrencias").fadeIn();
			$("#ocorrencias_quantidade").attr('value', '12');			
		}
	});

	/**
	Metodo resposavel por andar a primeira parte do registro. 
	valida e preenche formatacoes na pagina 2
	 */
	$(".avancaRegistro").click(function()   {
		var cat = $("input:radio[name=opcao]:checked" ).val();				
		var erro = false;

		$(".Erros").html('');
		if (cat != "+" && cat != "-")   {
			$("#cat-erro").html("Favor escolher uma categoria");
			erro = true;
		}
		else   {
			$("#step2Valor").removeClass();
			$("#RelacaoDeCategorias").html('');
			$(".relacaoCategorias").hide();
			if (cat == "+")   {
				$("#step2Valor").addClass("positivo");
				$("#step2Valor").html($("#valor").val());
				$("#relacao1").fadeIn();
			}
			else if (cat == "-")   {
				$("#step2Valor").addClass("negativo");
				$("#step2Valor").html("(" + $("#valor").val()+")")
				$("#relacao2").fadeIn();
			}
		}
		
		if ($("#valor").val() == "")   {
			$("#valor-erro").html("Favor informar o valor.");
			erro = true;
		}
		if (erro == false)   {	
			window.location = "#addRegistroStep2";
			$(".Erros").fadeOut();
		}
		else   {
			$(".Erros").fadeIn();
		}
	});
	
	$(".btnConcluir").click(function()   {
		var dados = Array();
		$(".Erros").html("").hide();
		$("#addRegistroIdCat-erro").html('<?= $mob->getLoading() ?> Executando Registro ...').fadeIn();

		var erros = false;
		dados['idCat'] = false;
		dados['valor'] = $("#valor").attr('value');
		dados['opcao'] = $("input:radio[name=opcao]:checked" ).val();
		dados['idCat'] = $("input:radio[name=categoria]:checked" ).val();		
		dados['vencimento'] = $("#vencimento").val();
		dados['ocorrencias'] = $("#ocorrencias_quantidade").val(); //;$("input:radio[name=ocorrencias]:checked" ).val();				
		dados['descricao'] = $("#notas").val();
		dados['qualAlterar'] = 	$("input:radio[name=alterar_ocorrencia]:checked" ).val();
		if (dados['vencimento'] == '')   {
			$("#addRegistroVencimento-erro").html("Data de vencimento ou entrada é obrigatório")
			erros = true;
		}
		if (!dados['idCat'])   {
			$("#addRegistroIdCat-erro").html("Selecionar uma categoria");
			erros = true;
		}
		if (erros)   {
			$(".Erros").fadeIn();
			return false;
		}
		aut.addregistro(dados, idRegistro, function(ret)   {
			if (ret)    {
				idRegistro = false;
				window.location = "index.php"
			}
		});
	});

	$(".btnFinalizarRegistro").click(function()   {
		var idRegistro = this.id.replace("finalizar_", "");
		alert("Confirma registrar recebimento da conta?");
		window.location = "#finalizarRegistro";
	});
	
	/** botam que conclui entrada de nova categoria **/
	$(".btnConcluirAddCat").click(function()   {
		var cat = $("input:radio[name=addCatTipo]:checked" ).val();				
		var label = $("#addCatNome").val();
		var erro = false;
		// Validação do form - start
		$(".Erros").html('');
		if (cat != "1" && cat != "2")   {
			$("#addCatTipo-erro").html("Favor escolher uma categoria");
			erro = true;
		}
		if (label == "")   {
			$("#addCatNome-erro").html("Nome é obrigatório");
			erro = true;
		}
		
		if (erro)   {
			$(".Erros").fadeIn();
			return false;
		}
		// End
		
		aut.addcat(cat, label, idCat, function(ret)   {
			if (ret[0] == false)   {
				alert(ret[1]);
				return false
			}
			else
				window.location = "index.php?r=2"
		});
	});
	
	/** Metodo para adicionar nova conta. Zerar valores **/
	$(".btnAddConta").click(function()   {
		$("#addContaNome").attr('value', '');
		$("#addContaBanco").attr('value', '');
		$("#addContaAgencia").attr('value', '');
		$("#addContaConta").attr('value', '');
		$("#addContaTitular").attr('value', '');
		idConta = false;
	});
	
	/** Metodo para registrar e atualizar contas do sistema **/
	$(".btnConcluirAddConta").click(function()   {
		var dados = Array();
		$(".Erros").html("").hide();
		dados['nome'] = $("#addContaNome").val();
		dados['banco'] = $("#addContaBanco").val();
		dados['agencia'] = $("#addContaAgencia").val();
		dados['conta'] = $("#addContaConta").val();
		dados['titular'] = $("#addContaTitular").val();
		dados['passivo'] = $("input:radio[name=passivo]:checked" ).val();	
		
		if(dados['nome'] == '')   {
			$("#addContaNome-erro").html("O nome da conta é obrigatório");
			$(".Erros").fadeIn();
			return false;
		}
		
		aut.addconta(dados, idConta, function(ret)   {
			if (ret[0] == false)   {
				alert(ret[1]);
				return false;
			}
			else  window.location = "index.php?r=3"
		});
	});

	$(".btRelatorio").click(function()   {
		var dados = Array();
		dados['idCatDebito'] = false;
		dados['idCatCredito'] = false;
		aut.set(dados);
		dados['dataInicio'] = $("#relDataInicio").val();
		dados['dataFim'] = $("#relDataFim").val();
		dados['tipo'] = $("input:radio[name=relTipo]:checked" ).val();
		dados['status'] = $("input:radio[name=relStatus]:checked" ).val();
		dados['idCatDebito'] = $("input:radio[name=relCategoriasDebitoCheck]:checked" ).val();
		dados['idCatCredito'] = $("input:radio[name=relCategoriasCreditoCheck]:checked" ).val();
		aut.set(dados, function(ret)   {
			window.open('relatorio.php');
		});
		
	});
	
	$(".btAlteraDiaCorte").click(function()   {
		if ($("#diaCorteText").val() != "")   {
			$(this).hide();
			$("#btAlterarCorteLoading").html('<?= $mob->getLoading ?>').fadeIn();
			var dados = Array();
			dados['diaInicioContabil'] = $("#diaCorteText").val();
			if (dados['diaInicioContabil']>31 || dados['diaInicioContabil']<1)   {
				$("#btAlterarCorteLoading").fadeOut();
				$(this).fadeIn();
				$("#diaCorteText").attr('value', '');
				alert("Data inválida. Escolha uma data válida!");
				return false;
			}
			aut.set(dados);			
			if ($("input:radio[name=diaCorte]:checked" ).val() == 2)   { // alterar cadastro
				aut.cadastrouseratualizadiacorte($("#diaCorteText").val());
			}
			window.location = "index.php";
			return true;
			
		}
		else   {
			alert("Preecnha o novo dia");
			return false;
		}
	});
});

</script>
</body>
</html>
