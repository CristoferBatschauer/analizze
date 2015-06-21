<script>
var idRegistro = false;
var idCat = false;
var idConta = false;

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
	atualiza();

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
		$("#vencimento").attr('value', '');
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
		dados['dataInicio'] = $("#relDataInicio").val();
		dados['dataFim'] = $("#relDataFim").val();
		dados['tipo'] = $("input:radio[name=relTipo]:checked" ).val();
		dados['status'] = $("input:radio[name=relStatus]:checked" ).val();
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