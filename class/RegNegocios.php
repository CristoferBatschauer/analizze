<?php

/*
 * @package Gerenciador de Analizze
 * @author = Cristofer Batschauer
 * @version = 1.0
 * Padronização de returns:
 * 	1 ou true: true
 * -1: Dados obrigatórios não informados
 * -2: Tipo de dados para variáveis incorreto
 * -3: Registro não localizado pelo id informado
 * -4: Transação não efetuada: mysql_error()
 */
require_once 'Conexao.php';
class RegNegocios {
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
    var $ucWord = false; // defini se altera os valores constantes em ad-atualizaVAlores
    var $lastIdInsert;

    /*
     * Constructor
     */

    function RegNegocios() {
        
    }

    /*
     * Metodo responsável por registrar os erros ocorridos na classe
     * @param   String $msg Mensagem a ser exibida/Inserida
     * @param String $query Comando query que gerou o problema
     * @return String Alerta Javascript
     */

    function myErrorHandler($errno, $errmsg, $filename, $linenum, $vars) {
        $headers = "MIME-Version: 1.0\n";
        $headers .="Content-type: text/html; \"MULTIPART/related\"; charset=ISO-8859-1\n";
        $headers .="Content-transfer-encoding: 8BIT\n";
        $headers .="From: Painel de Controle<life4u@life4u.com.br>\n";
// timestamp para a entrada do erro
        $dt = date("d/m/Y H:i:s");
        $err .= $this->erro;
        $err .= "<strong>ScriptName: </strong>" . $filename . "<br>";
        $err .= "<strong>Script Linha Numero: </strong>" . $linenum . "<br>";
        @mail("cristofer.batschauer@gmail.com", "Analizze - Erro no Sistema", $err, $headers);
        $this->gravaLog('erroPadrao', str_replace("<br>", "\\n", $err));
        $err = "";
        $this->erro = "";
    }

    /*
     * Metodo responsável por registrar os erros ocorridos na classe
     * @param   String $msg Mensagem a ser exibida/Inserida
     * @param String $query Comando query que gerou o problema
     * @return String Alerta Javascript
     */

    function erroPadrao($msg, $query = '') { // codigo de erro SI0801
        $headers = "MIME-Version: 1.0\n";
        $headers .="Content-type: text/html; \"MULTIPART/related\"; charset=ISO-8859-1\n";
        $headers .="Content-transfer-encoding: 8BIT\n";
        $headers .="From: Analizze-SystemControl<atendimento@vizzuall.com.br>\n";
        $query = str_replace('\'', '', $query);
        $this->erro = "<strong>IP: </strong>" . getenv("REMOTE_ADDR") . "<br>"
                . "<strong>Data: </strong>" . date("d/m/Y H:i:s") . "<br>"
                . "<strong>MySqlError</strong>: " . mysql_error() . "<br>"
                . "<strong>Pagina</strong>: " . $_SERVER['PHP_SELF'] . "<br>"
                . "<strong>Mensagem</strong>: " . $msg . "<br>"
                . "<strong>Query</strong>: " . $query . "<br>"
                . "<strong>URL</strong>: " . $_ENV["REQUEST_URI"] . "<br>"
// para PHP5: . "Last Erro: ". print_r(error_get_last());
        ;
        if ($GLOBALS['PRINT_ERROR']) {
            echo $this->erro;
            die();
        }
        define("FATAL", E_USER_ERROR);
        define("ERROR", E_USER_WARNING);
        define("WARNING", E_USER_NOTICE);
// set the error reporting level for this script
        error_reporting(0); // | ERROR | WARNING);
        $old_error_handler = set_error_handler(array(&$this, 'myErrorHandler'));
        trigger_error('Complementando', E_USER_ERROR);
        restore_error_handler();
        ?>
        <script language="JavaScript">
            alert("Ocorreu um erro no sistema.\n\nFoi enviado email ao administrador para correção. Pedimos desculpas e sua compreensão.");
            window.location = "index.php";
        </script>

        <?php

        return false;
        die();
    }

    /*
     * Metodo para conexão com banco MySQL.
     * @acess private
     */

    function conecta() {  // codigo de erro SI0802
        if ($this->IdtLink == 0) {
            $this->IdtLink = new Conexao();
            /*
            $this->IdtLink = mysql_connect($GLOBALS["HOST"], $GLOBALS["USER"], $GLOBALS["PW"]);
            if (!$this->IdtLink) {
                $this->erroPadrao("SI0802 - 01");
            }
            if (!mysql_query(sprintf("use %s", $GLOBALS["DBNOME"]), $this->IdtLink)) {
                $this->erroPadrao("SI0802 - 02 DB");
            }
             * */
        }
    }

// fecha  function conecta



    /*
     * Método para execução de comando querys
     * @param String $CmdQuery Comando Query
     * @return String   Codigo identificador de conexão/execução
     */

    function executeQuery($CmdQuery, $gravarLog = true) { // codigo de erro SI0803
        $this->conecta();
        $temp = explode(" ", $CmdQuery);
        if (GRAVARLOGQUERYS && $gravarLog) { // && strtoupper($temp[0]) != "SELECT") {
            $tipo = explode(" ", $CmdQuery);
            RegNegocios::gravaLog($tipo[0], addslashes($CmdQuery));
        }

        $this->IdtQuery = $this->IdtLink->executeQuery($CmdQuery);
        $this->numRows = 0;
        if (!$this->IdtQuery) {
            $this->erroPadrao("SI0803", $CmdQuery);
        }
        
        $this->lastIdInsert = $this->IdtLink->affected_rows;
        $this->query = $CmdQuery;
        return $this->IdtQuery;
    }

// fecha  function 

    public function getLastIdInsert() {
        return $this->lastIdInsert;
    }

    /*
     * Metodo para saltar os registros encontrados pelo	 * Metodo executeQuery
     * @return String   Array na variavel $this->dd[]
     */

    function proxReg() {  // codigo de erro SI0804
        $this->dd = mysql_fetch_array($this->IdtQuery);
        $FimDados = is_array($this->dd);
        if (!$FimDados) {
            mysql_free_result($this->IdtQuery);
            $this->IdtQuery = 0;
        }
        return $FimDados;
    }

    /**
     * Metodo para controlar escapes em Strings

      function escapeString($b) {
      return (!get_magic_quotes_gpc()) ? addslashes($b) : $b);
      }
     */
    /*
     * Metodo para formatar a data
     * @param   String $data Data a ser arrumada
     * @param String $escolha arrumar para BD e mostrar para exibição
     * @return String   Data Corrigida
     */
    function arrumaData($data, $escolha) {  // código de erro SI0806
        $data = RegNegocios::parseInt($data);
        if ($escolha == "arrumar") { // Concertar o que vem fo form par inserir no BD
            $ano = substr("$data", -4);
            $mes = substr("$data", 2, 2);
            $dia = substr("$data", 0, 2);
            $dataAtual = $ano . "-" . $mes . "-" . $dia;
        } elseif ($escolha == "mostrar") { // Arrumar o que vem do Banco para imprimir na data Bras.
            $ano = substr("$data", 0, 4);
            $mes = substr("$data", 4, 2);
            $dia = substr("$data", -2);
            $dataAtual = $dia . "/" . $mes . "/" . $ano;
        } elseif ($escolha == "soData") { // tipo date time no banco, retorna só data
            $ano = substr("$data", 0, 4);
            $mes = substr("$data", 4, 2);
            $dia = substr("$data", 6, 2);
            $dataAtual = $dia . "/" . $mes . "/" . $ano;
        } else
            $this->erroPadrao("SI0805 - Opção Incorreta");
        return $dataAtual;
    }

    /*
     * Metodo para formatar o CPF
     * @param   String $cpf CPF
     * @return String   Retorna CPF para exibição 123.456.789-00
     */

    function arrumaCpf($cpf) {
        $cpf = $this->parseInt($cpf);
        $a = substr("$cpf", 0, 3);
        $b = substr("$cpf", 3, 3);
        $c1 = substr("$cpf", 6, 3);
        $d = substr("$cpf", 9, 2);
        $temp = "$a.$b.$c1-$d";
        return $temp;
    }

    /*
     * Metodo para formatar o CNPJ
     * @param   String $cnpj CNPJ
     * @return String   Novo CNPJ para exibição 00.123.456/0001-99
     */

    function arrumaCnpj($cnpj) {
        $cnpj = $this->parseInt($cnpj);
        $a = substr("$cnpj", 0, 2);
        $b = substr("$cnpj", 2, 3);
        $c = substr("$cnpj", 5, 3);
        $d = substr("$cnpj", 8, 4);
        $e = substr("$cnpj", 12, 2);
        $temp = "$a.$b.$c/$d-$e";
        return $this->temp;
    }

    /*
     * Metodo para formatar o CEP
     * @param   String $cep CEP para ser formatado
     * @return String   Novo CEP para exibição
     */

    function arrumaCep($cep) {
        $cep = $this->parseInt($cep);
        $a = substr("$cep", 0, 5);
        $b = substr("$cep", 5, 3);
        $temp = "$a-$b";
        return $this->temp;
    }

    /*
     * Metodo para formatar os valores padrão ISO-8859-1 (1.256,36)
     * @param   int $var Valor a ser formatado
     * @return String   Retorna o valor com casa decimais padrão ISO-8859-1
     */

    function arrumaValor($var) { // código de erro SI0808
        return number_format($var, 2, '.', ',');
    }

    /*
     * Metodo para auto tabulação em forms. Tem depêndencia com  function em Javascript
     * @return String   Codigo HTML para auto tabulação. Tem dependencia do Javascript
     */

    function autoTab() { // código de erro SI0807
        echo " input onKeyUp='javascript: return auto_tab(this, this.maxLength, event);'";
    }

    /*
     * Metodo para inserir links em http e mailto. Pega a variavel txt e retorna o mesmo txt linkado
     * @param   String $var Variavel a ser examinada
     * @return String   A varivel inserida com os campos para link ja linkados..
     */

    function addLink($var, $css = "") {
        $var = str_replace("http://", "", $var);
        $var = "http://" . $var;
// Link para http
        $var = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a class='$css' href=\"\\0\">\\0</a>", $var);
// Link para mailto
        $var = ereg_replace("[[:alpha:]]+@[^<>[:space:]]+[[:alnum:]/]", "<a class='$css' href=\"mailto: \\0\">\\0</a>", $var);
        return $var;
    }

// fecha addLink

    /*
     * Metodo para retirar tudo que for String de um Inteiro
     * @param String $var Variavel a ser modulada para inteiro
     * @return int Retorna var somente com numeros inteiros
     */

    public static function parseInt($var) {
        return @ereg_replace("[^0-9]", "", $var);
    }

// fecha parseInt

    function isInt($var) {
// APRIMORAR MELHOR ESTE MÉTODO.. EMERGENCIAL
        $t = preg_replace("[^0-9]", "", $var);
        if ($t == $var)
            return true;
        else
            return false;
    }

    function isBoolean($var) {
        if (($var == true) || ($var == false))
            return true;
        else
            return false;
    }

    /*
     * Metodo para formatar um numero qualquer no formato 1124,50. Usado para inserção no BD.
     * @param String $var Variavel a ser modulada
     * @return double Retorna var no formato 1124,50
     */

    function parseDouble($var) {
        $var = RegNegocios::parseInt($var);
        $out = substr($var, 0, strlen($var) - 2) . "." . substr($var, strlen($var) - 2, 2);
        return $out;
    }

    /*
     * Logs de eventos
     * @param String $nomeArquivo Nome do arquivo para gravação do log. Não informar extensão
     * @param String $texto Texto a ser escrito no arquivo
     * @return int Retorna:	  1 para tudo certo, -1 Se arquivo não existe, -2 para erro genérico.
     */

    function gravaLog($nomeArquivo, $texto) {
        /*
          $user = ((isset($_SESSION['idUser']))?$_SESSION['idUser']:1);
          $sql = new RegNegocios();
          $sql->executeQuery("INSERT INTO anz_logs (data, tipo, texto, user_idUser) VALUES (now(), '$nomeArquivo', '$texto', $user)", false);
          return true;
         */
        $arquivo = PATH . "/logs/" . $nomeArquivo . ".txt";
//		die($arquivo);
//if (!file_exists($arquivo))	  return ("-1 ".$arquivo);
        $texto = "[" . date("d-m-Y H:i:s") . "/" . $_SESSION['USER_NOME'] . "]-" . $texto;
        $fp = fopen($arquivo, "a"); // or die("N�o pude criar o arquivo de erro <strong>$arquivo</strong><br>".str_replace("\\n", "<br>", $texto));
        if (fputs($fp, $texto . "\r\n")) {
            fclose($fp);
            return true;
        } else
            return false;
    }

    function deletaArquivo($nomeArquivo, $textoExtra = false) {
        if (file_exists($nomeArquivo)) {
            if (@unlink($nomeArquivo)) {
                @$this->gravaLog("deleta_arquivo", "Deletado arquivo: $nomeArquivo");
                return true;
            } else {
                $headers = "MIME-Version: 1.0\n";
                $headers .="Content-type: text/html; \"MULTIPART/related\"; charset=ISO-8859-1\n";
                $headers .="Content-transfer-encoding: 8BIT\n";
                $headers .="From: Controle Classes<cristofer.batschauer@gmail.com>\n";
                @mail($GLOBALS['DADOS_SITE']['adm_email'], $GLOBALS['DADOS_SITE']['nomeSite'] . " - Erro no Sistema", "Erro na remoção da imagem $nomeArquivo<br>$textoExtra");
            }
        } else
            @$this->gravaLog("deleta_arquivo_naoexiste", "Arquivo: $nomeArquivo");
    }

    function getValueById($table, $cpo, $id) {
        if ((!$id) || ($id == "")) {
            $this->erroPadrao("Variavel id sem valor", "Tabela: $table, Campo: $cpo, ID: $id");
            die();
            return false;
        }
        if (!$this->isInt($id)) {
            $this->erroPadrao("Variavel id diferente de Inteiro", "Tabela: $table, Campo: $cpo, ID: $id");
            die();
            return false;
        }
        $this->executeQuery("SELECT $cpo FROM $table WHERE id= $id");
        return $this->getString(0, $cpo);
    }

    function myHtmlentities($val) {
        return (htmlentities($val));
    }

    function myUcWords($var) {
        return (ucwords(strtolower($var)));
    }

    function escreve($texto, $key, $value) {
        return (str_replace("%$key%", $value, $texto));
    }

    function getDiaSemana($data) {
        $temp = explode("/", $data);
        $diaSemana = date("w", mktime(0, 0, 0, $temp[1], $temp[0], $temp[2]));
        $nomesSemana = array("Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado");
        return $nomesSemana[$diaSemana];
    }

    public function codifica($texto, $iv_len = 16) {
        $chave = TOKEN;
        $texto .= "\x13";
        $n = strlen($texto);
        if ($n % 16)
            $texto .= str_repeat("\0", 16 - ($n % 16));
        $i = 0;
        $Enc_Texto = RegNegocios::Randomizar($iv_len);
        $iv = substr($chave ^ $Enc_Texto, 0, 512);
        while ($i < $n) {
            $Bloco = substr($texto, $i, 16) ^ pack('H*', md5($iv));
            $Enc_Texto .= $Bloco;
            $iv = substr($Bloco . $iv, 0, 512) ^ $chave;
            $i += 16;
        }
        return base64_encode($Enc_Texto);
    }

    public function decodifica($Enc_Texto, $iv_len = 16) {
        $chave = TOKEN;
        $Enc_Texto = base64_decode($Enc_Texto);
        $n = strlen($Enc_Texto);
        $i = $iv_len;
        $texto = '';
        $iv = substr($chave ^ substr($Enc_Texto, 0, $iv_len), 0, 512);
        while ($i < $n) {
            $Bloco = substr($Enc_Texto, $i, 16);
            $texto .= $Bloco ^ pack('H*', md5($iv));
            $iv = substr($Bloco . $iv, 0, 512) ^ $chave;
            $i += 16;
        }
        return preg_replace('/\\x13\\x00*$/', '', $texto);
    }

    private function Randomizar($iv_len) {
        $iv = '';
        while ($iv_len-- > 0) {
            $iv .= chr(mt_rand() & 0xff);
        }
        return $iv;
    }

}

// fecha class
?>