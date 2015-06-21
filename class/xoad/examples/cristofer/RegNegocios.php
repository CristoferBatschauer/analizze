<?
$HOST     = "mysql.Eurocredito.com.br";
$USER     = "renas2br";
$PW    = "peixoto";
$DBNOME   = "renas2br";

/*
 * @package ComunidadeMayte
 * @author = Cristofer Batschauer
 * @version = 1.0
 * Padronização de returns:
 *  1 ou true: true
 * -1: Dados obrigatórios não informados
 * -2: Tipo de dados para variáveis incorreto
 * -3: Registro não localizado pelo id informado
 * -4: Transação não efetuada: pg_error()
 */
class RegNegocios   {
	/*
	 * Variavel que armazena o identificador da conexão
	 */
	var $IdtLink = 0;
	/*
	 * Variavel que armazena o identificador de query
	 */
	var $IdtQuery = 0;
	/*
	 * Variavel que recebe array do metodo proxReg()
	 */
	var $dd = array();
	/*
	 * Quantidade de linhas afetadas por executeQuery()
	 */
	var $numRows;
	/*
	 * Acumula query utilizada no métofo executeQuery
	 */
	var $query;
	/*
	 * Metodo responsável por registrar os erros ocorridos na classe
	 * @param  String $msg Mensagem a ser exibida/Inserida
	 * @param String $query Comando query que gerou o problema
	 * @return String Alerta Javascript
	*/
	function erroPadrao($msg, $query='') {  // codigo de erro SI0801
		$headers = "MIME-Version: 1.0\n"; 
		$headers .="Content-type: text/html; \"MULTIPART/related\"; charset=ISO-8859-1\n";
		$headers .="Content-transfer-encoding: 8BIT\n";
		$headers .="From: Painel de Controle<atendimento@vizzuall.com.br>\n";
		$query = str_replace('\'', '', $query);
		@$this->executeQuery("INSERT INTO sistema_erros (id, mensagem, query) VALUES (NULL, '$msg', '$query')"); 
		@mail($GLOBALS["administrador"], $GLOBALS["nomeSite"]." - Erro no Sistema", "<strong>Função erro: </strong> $msg<br><strong>Query: </strong> $query<br><strong>Erro Mysql: </strong> ".pg_error(), $headers);
		?>
		<script language="JavaScript">
			alert("Ocorreu um erro no sistema.\n\nFoi enviado email ao administrador para correção. Pedimos desculpas e sua compreensão.");
			window.location = "http://<?= $GLOBALS["CONFIG_URL"] ?>";
		</script>
		<?
		die();
	}  

	/*
	 * Metodo para conexão com banco MySQL. 
	 * @acess private
	*/
	function conecta() {  // codigo de erro SI0802
		if (0 == $this->IdtLink) {  
			$this->IdtLink = pg_connect($GLOBALS["HOST"], $GLOBALS["USER"], $GLOBALS["PW"]);  
			if (!$this->IdtLink) {  
				$this->erroPadrao("SI0802 - 01");
			}  
			if (!pg_query(sprintf("use %s", $GLOBALS["DBNOME"]), $this->IdtLink)) {
				$this->erroPadrao("SI0802 - 02 DB");  
			}  
		} 
	} // fecha function conecta  
	
	/*
	 * Método para execução de comando querys
	 * @param String $CmdQuery Comando Query
	 * @return String  Codigo identificador de conexão/execução
	*/
	function executeQuery($CmdQuery) {  // codigo de erro SI0803
		$this->conecta();
		$this->IdtQuery = pg_query($CmdQuery, $this->IdtLink);  
		$this->numRows = 0;  
		if (!$this->IdtQuery) {  
			$this->erroPadrao("SI0803", "$CmdQuery-".pg_error());
		}  
		@$this->numRows = pg_num_rows($this->IdtQuery);
		$this->query = $CmdQuery;
		return $this->IdtQuery;  
	}   // fecha function localiza
	
	/*
	 * Metodo para saltar os registros encontrados pelo  * Metodo executeQuery
	 * @return Array na variavel $this->dd[]
	*/
	function proxReg() {  // codigo de erro SI0804
	    $this->dd = pg_fetch_array($this->IdtQuery);  
		$FimDados = is_array($this->dd);  
		if (!$FimDados) {  
			pg_free_result($this->IdtQuery);
			$this->IdtQuery = 0;  
		}  
		return $FimDados;  
	}

	/*
	 * Metodo para extrair os valor atraves de linha e campo
	 * @param  int $linha Linha para pesquisa
	 * @param String $campo Nome do campo no BD
	 * @return String  
	*/
	function getString($linha, $campo)   { // si 0805
		return pg_fetch_result($this->IdtQuery, $linha, "$campo");
	}

	function getParameters($keyInvalid="")   {
		$parametros = array();
		$i=0;
		foreach ($_GET as $key => $val) {
			if ($keyInvalid != $key)   {
				$parametros[$i] = "$key=$val";
				$i++;
			}
		}
		$param = implode("&", $parametros);
		return $param;
	}

	/*
	 * Metodo para formatar a data
	 * @param  String $data Data a ser arrumada
	 * @param String $escolha arrumar para BD e mostrar para exibição
	 * @return String  Data Corrigida
	*/
	function arrumaData($data, $escolha)   {  // código de erro SI0806
		$data = $this->parseInt($data);
		if ($escolha == "arrumar")   { // Concertar o que vem fo form par inserir no BD
			$ano = substr("$data", -4);
			$mes = substr("$data", 2, 2);
			$dia = substr("$data", 0, 2);
			$dataAtual = $ano."-".$mes."-".$dia;
		}
		elseif ($escolha == "mostrar")   { // Arrumar o que vem do Banco para imprimir na data Bras.
			$ano = substr("$data", 0, 4);
			$mes = substr("$data", 4, 2);
			$dia = substr("$data", -2);
			$dataAtual = $dia."/".$mes."/".$ano;
		}
		else $this->erroPadrao("SI0805 - Opção Incorreta");
		return $dataAtual;
	}

	/*
	 * Metodo para formatar o CPF
	 * @param  String $cpf CPF
	 * @return String  Retorna CPF para exibição 123.456.789-00
	*/
	function arrumaCpf($cpf)   {
		$cpf = $this->parseInt($cpf);
		$a= substr("$cpf", 0,3); 
		$b= substr("$cpf", 3,3); 
		$c1= substr("$cpf", 6,3); 
		$d= substr("$cpf", 9,2); 
		$temp = "$a.$b.$c1-$d"; 
		return $temp;
	}

	/*
	 * Metodo para formatar o CNPJ	
	 * @param  String $cnpj CNPJ
	 * @return String  Novo CNPJ para exibição 00.123.456/0001-99
	*/
	function arrumaCnpj($cnpj)   { 
		$cnpj = $this->parseInt($cnpj);
		$a= substr("$cnpj", 0,2); 
		$b= substr("$cnpj", 2,3); 
		$c= substr("$cnpj", 5,3); 
		$d= substr("$cnpj", 8,4); 
		$e= substr("$cnpj", 12,2); 	  
		$temp = "$a.$b.$c/$d-$e"; 
		return $this->temp;
	}
	
	/*
	 * Metodo para formatar o CEP	
	 * @param  String $cep CEP para ser formatado
	 * @return String  Novo CEP para exibição
	*/
	function arrumaCep($cep)   {
		$cep = $this->parseInt($cep);
		$a= substr("$cep", 0,5); 
		$b= substr("$cep", 5,3); 
		$temp = "$a-$b";
		return $this->temp;
	}
	
	/*
	 * Metodo para formatar os valores padrão ISO-8859-1 (1.256,36)
	 * @param  int $var Valor a ser formatado
	 * @return String  Retorna o valor com casa decimais padrão ISO-8859-1
	*/
	function arrumaValor($var)   { // código de erro SI0808
		return number_format($var, 2, '.', ',');
	}
	
	/*
	 * Metodo para auto tabulação em forms. Tem depêndencia com function em Javascript
	 * @return String  Codigo HTML para auto tabulação. Tem dependencia do Javascript
	*/
	function autoTab()   { // código de erro SI0807
		echo " input onKeyUp='javascript: return auto_tab(this, this.maxLength, event);'";
	}


	/*
	 * Metodo para inserir links em http e mailto. Pega a variavel txt e retorna o mesmo txt linkado
	 * @param  String $var Variavel a ser examinada
	 * @return String  A varivel inserida com os campos para link ja linkados..
	*/
	function addLink($var, $css="")   {
		$var = str_replace("http://", "", $var);
		$var = "http://".$var;
		// Link para http
		$var = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]",
                     "<a class='$css' href=\"\\0\">\\0</a>", $var);
		// Link para mailto
		$var = ereg_replace("[[:alpha:]]+@[^<>[:space:]]+[[:alnum:]/]",
                     "<a class='$css' href=\"mailto: \\0\">\\0</a>", $var);
		return $var;
	} // fecha addLink
		
	/*
	 * Metodo para exibição de resultados e posterior navegação
	 * @param  String Mensagem a ser exibida
	 * @param String Página para retorno
	 * @return String  Codigo Javascript
	*/
	function printResult($mensagem, $retorno)   {
		echo "<script language='javascript'>\n
				alert('$mensagem');\n
				window.location.href='$retorno';
			  </script>
			  ";
	}

	/*
	 * Metodo para simples exibição de erro
	 * @return String  Alerta de erro em javascript
	*/
	function erro()   {
		echo "<script language='javascript'>\n
				alert('Ocorreu um erro no sistema\nFavor informar ao WebMaster');\n
			  </script>
			  ";
	}

	/*
	 * Metodo para fechar popups. 
	 * Exibe mensagem e depois fecha
	 * @param  String $mens Mensagem a ser exibida
	 * @return String  Codigo Javascript
	*/
	function closePop($mens="")   {
		if ($mens == "")   $mens = "Operação Efetuada";
		echo "
		<script language='javascript'>
		alert('$mens'); 
		window.close();
		</script>
		";
	}
	
	/*
	 * Metodo para inclusão de botão "voltar". Simples texto escrito com possibilidade de voltar mais de uma página.
	 * @param  int $pg Quantidade de paginas a retornar
	 * @return String  Codigo HTML para voltar páginas
	*/
	function btVoltar($pg=1, $classe)   {
		return "[<a href='javascript:history.back(-$pg);' class='$classe'>::Voltar::</a>]";
	}	

	/*
	 * Metodo para retirar tudo que for String de um Inteiro
	 * @param String $var Variavel a ser modulada para inteiro
	 * @return int Retorna var somente com numeros inteiros
	 */
	function parseInt($var)   {
		return ereg_replace("[^0-9]", "", $var);
	} // fecha parseInt
	
	function isInt($var)   {
		// APRIMORAR MELHOR ESTE MÉTODO.. EMERGENCIAL
		$t = ereg_replace("[^0-9]", "", $var);
		if ($t == $var)   return true;
		else return false;
	}
	
	function isBoolean($var)   {
		if (($var == true) || ($var == false))   return true;
		else return false;
	}
	/*
	 * Metodo para formatar um numero qualquer no formato 1124,50. Usado para inserção no BD.
	 * @param String $var Variavel a ser modulada
	 * @return double Retorna var no formato 1124,50
	 */
	function parseDouble($var)   {
		$var = $this->parseInt($var);
		$out = substr($var, 0, strlen($var)-2).",".substr($var, strlen($var)-2, 2);
		return $out;
	}
	
	/*
	 * Grava Logs de eventos
	 * @param String $nomeArquivo Nome do arquivo para gravação do log. Não informar extensão
	 * @param String $texto Texto a ser escrito no arquivo
	 * @return int Retorna:   1 para tudo certo, -1 Se arquivo não existe, -2 para erro genérico.
	 */
	function gravaLog($nomeArquivo, $texto)   {
		$arquivo = $GLOBALS['DIR_LOCAL']."/logs/".$nomeArquivo.".txt";
		if (!file_exists($arquivo))   return ("-1 ".$arquivo);
		$texto = "[".date("d-m-Y")."]".$texto;
		$fp = fopen ($arquivo, "a+");
		if (fputs($fp, $texto."\r\n"))   {
		   fclose($fp);
		   return 1;
		}
		else   return -2;
	}
	
	function deletaArquivo($nomeArquivo)   {	
		if (file_exists($nomeArquivo))   {
			if (@unlink($nomeArquivo))   return true;
			else  return false;
		}
	}
	
	function getValueById($table, $cpo, $id)   {
		$this->executeQuery("SELECT $cpo FROM $table WHERE id= $id");
		return $this->getString(0, $cpo);
	}
	
	/**
	 * Cria um novo registro para a tabela inserindo apenas Id e Data. Retorna o id inserido
	 * Método Private
	 *@param String $tabela Nome da tabela que deve ser inserido registro
	 *@return int Retorna o ID inserido pelo método
	 */
	function novoRegistro($tabela)   {
	   $this->executeQuery("INSERT INTO $tabela (id, data) VALUES (null, now())");
	   $this->executeQuery("SELECT MAX(id) as id FROM $tabela");
       return $this->getString(0, 'id');
	}

	/**
	 * Executa Update na tabela enviada pelo form criado com classe Html
	 *@param Array $ex_aut Excessões que não devem ser feitas automaticamente. Deverão ser processadas manualmente
	 *@param String $tabela Tabela a ser executado registro
	 *@param int $idRegistro Id do registro em questão. Para novo registro, informar '' que será criado ID
	 *@param int $priField, $ultField Primeiro e ultimo campo do BD a ser processado
	 *@param Array $cposReal Campos que devem ser convertidos para tipo double.
	 */
	function executeUpdateTable($ex_aut, $tabela, $idRegistro, $priField, $ultField, $cposReal)   {
		// Campos do tipo double
		for ($i=0; $i<count($cposReal); $i++)
			$_POST[$cposReal] = $this->parseDouble($_POST[$cposReal]);
		// dados post
		$valor = array();
		$chave = array();
		$count = 0;
		// Dados enviados por _POST
		foreach($_POST as $key=> $val)	{
			$insere = true;
			if ($key == "FIM") $end = 1;
			if ($end != 1)   {
				for ($i=0; $i<count($ex_aut); $i++)   {
					if ($key == $ex_aut[$i])   {
					   $insere = false;
					   $i = count($ex_aut) + 1;
					}
				}
				if ($insere)   {
				   $valor[$count] = $val;
				   $count++;
				}
			}
		}
		// Dados dos campos no BD
		$this->conecta();
		$fields = pg_list_fields($GLOBALS['DBNOME'], $tabela, $this->IdtLink);
		$end = 1;
		$count = 0;
		for ($i = $priField; $i<=$ultField; $i++) $campos[$i] = pg_field_name($fields, $i);
		foreach ($campos as $key)   {
			$insere = true;
			for ($i=0; $i<count($ex_aut); $i++)   {
				if ($key == $ex_aut[$i])   {
				   $insere = false;
				   $i = count($ex_aut) + 1;
				}
			}
			if ($insere)   {
			   $field[$count] = "$key";
			   $count++;
			}
		}
		$count = 0;
		$post = "";
		//juntando
		while($field[$count])   {
			$post[$count] = "$field[$count]='$valor[$count]'";
			$count++;
		}
		$post = implode(", ", $post);
		// Executando atualizações
		$query = "UPDATE $tabela SET $post WHERE id= ".$idRegistro;
		// Gera log da atualização
		$temp = $this->gravaLog('alteracoes', 'Query: '.$query.', Usuario: '.$_COOKIE['USER_NOME']);
		if ($temp < 0)   $this->gravaLog('error', $temp);
		// query
		if ($this->executeQuery($query))   return true;
		else   return false;
	}
	
	/**
	 * Método que retorna o nome do browser utilizado
	 */
	function getBrowser()   {
		$agent = strtolower($_SERVER["HTTP_USER_AGENT"]);
		if (count(explode("msie", $agent)) > 1)   $br = "IE";
		elseif (count(explode("firefox", $agent)) > 1)   $br = "Firefox";
		else   $br = "Outros";
		return ($br);
	}
} // fecha class
?>
