<?php

class Analizze  {

    var $dataLimite, $dataLimiteMktime = false;

    /**
      $iconeLeft e $iconeRight: Entra array contendo: Texto=>texto, Icone=>icone
     */
    public function __construct() {
        // data limite
        if (isset($_SESSION['diaInicioContabil'])) {
            $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $this->dataLimite = mktime(0, 0, 0, date('m'), $_SESSION['diaInicioContabil'], date('Y'));
            if (($this->dataLimite - $hoje) <= 0) { // data limite � do pr�ximo m�s
                $this->dataLimite = $dataLimite = mktime(0, 0, 0, date('m') + 1, $_SESSION['diaInicioContabil'], date('Y'));
            }
        } else {
            $this->dataLimite = mktime(0, 0, 0, date('m') + 1, date('d'), date('Y'));
        }
        $this->dataLimiteMktime = $this->dataLimite;
        $this->dataLimite = date('Y-m-d', $this->dataLimite);
    }

    public function geraHeader($iconeLeft, $texto, $iconeRight, $escreve = true) {
        $out = '<div data-role="header" data-theme="a"><div class="ui-grid-b">';
        $out .= '<div class="ui-block-a"><div align="left">' . ((count($iconeLeft) > 0) ? '<a class="' . $iconeLeft['class'] . '" href="' . $iconeLeft['link'] . '" data-role="button" data-icon="' . $iconeLeft['icone'] . '" data-iconpos="left">' . $iconeLeft['texto'] . '</a>' : '') . '</div></div>';
        $out .= '<div class="ui-block-b"><div align="center"><h2>' . $texto . '</h2></div></div>';
        $out .= '<div class="ui-block-c"><div align="right"><span id="loading" style="display:none;">' . Analizze::getLoading() . '</span>' . ((count($iconeRight) > 0) ? '<a class="' . $iconeRight['class'] . '" href="' . $iconeRight['link'] . '" data-role="button" data-icon="' . $iconeRight['icone'] . '" data-iconpos="left">' . $iconeRight['texto'] . '</a>' : '') . '	</div></div>';
        $out .= '</div></div>';

        // user
        $out .= (($_SESSION['userNome'] != '') ? '<div align="right" style="font-size:10px;">Seja bem vindo, ' . $_SESSION['userNome'] . '</div>' : '');
        $out = (($escreve) ? $out : '');
        return $out;
    }

    public function geraFooter($escreve = true, $logoff = false) {
        $out = '
				<div data-role="footer" data-theme="a">
				<h2>' . $_SESSION['nomeEmpresa'] . '</h2>
			    ' . (($logoff) ? '<div align="center" id="logoff"><a href="#"><img src="images/logoff.png" /></a></div>' : '') . '
				<h4>Analizze - By PrC Systems</h4>						
				</div>
				';
        $out = (($escreve) ? $out : '');
        return $out;
    }

    /**
     * Método resposável por gerar uma relação de categorias, conforme escolha do tipo
     */
    public function catGeraRelacao($tipo = 1, $nomeElemento = 'categoria', $onclick = false, $status = 1) {
        $acc = new MySql();
        $query = "SELECT * FROM anz_categorias
				WHERE tipo= $tipo AND idStatus= $status AND idEmpresa= " . $_SESSION['idEmpresa'] . "
				ORDER BY label";
        $acc->executeQuery($query);
        if ($acc->getNumRows() == 0)
            return false;
        $i = 0;
        $out = '';

        $out = '<div data-role="fieldcontain" id="relacao' . $tipo . '" class="relacaoCategorias">
				<fieldset data-role="controlgroup" data-type="horizontal">';
        while ($acc->proxReg()) {
            $dados = $acc->getDD();
            $out .= '<input type="radio" name="' . $nomeElemento . '" ' . $onclick . ' id="' . $nomeElemento . '_' . $i . '" value="' . $dados['idCat'] . '" />
					<label for="' . $nomeElemento . '_' . $i . '" style="font-size: 12px;float:left;margin:2px;width: 140px;height:40px;text-align: center;">' . utf8_encode($dados['label']) . '</label>';
            $i++;
        }
        $out .= '</fieldset></div>';
        return $out;
    }

    /**
     * Metodo responsavel por regitrar os lancamentos. Se n�o receber Id, cria um novo, se receber atualiza
     */
    public function addRegistro($dados, $id = false) {
        $acc = new MySql();
        foreach ($dados as $key => $val)
            $$key = utf8_decode($val);
        $valor = str_replace("R$ ", "", $valor);
        $valor = RegNegocios::parseDouble($valor);
        $vencimento = RegNegocios::arrumaData($vencimento, 'arrumar');
        if ($opcao == "-")
            $valor = $valor * -1.00;
        $descricao = (($descricao == "") ? "Nenhuma nota registrada" : $descricao);
        if ($id) {
            switch ($qualAlterar) {
                case 1:
                    $query = "UPDATE anz_lancamentos SET valor=$valor, vencimento='$vencimento', descricao='$descricao', idCat='$idCat' WHERE  idLancamento=$id";
                    $acc->executeQuery($query);
                    break;
                case 2: // este e futuros
                    $query = "UPDATE anz_lancamentos SET valor=$valor, vencimento='$vencimento', descricao='$descricao', idCat='$idCat' WHERE  idLancamento=$id";
                    $acc->executeQuery($query);
                    $acc->executeQuery("SELECT idLancamento FROM anz_lancamentos
							WHERE vencimento > '$vencimento' AND idLancamento != $id AND idLancPai= (SELECT idLancPai FROM anz_lancamentos WHERE idLancamento= $id)
							ORDER BY idLancamento ASC");
                    while ($acc->proxReg()) {
                        $temp = explode("-", $vencimento);
                        $vencimento = date('Y-m-d', mktime(0, 0, 0, $temp[1] + 1, $temp[2], $temp[0]));
                        unset($temp);
                        $temp = $acc->getDD();
                        $queryUpdate[] = "UPDATE anz_lancamentos SET valor=$valor, vencimento='$vencimento', descricao='$descricao', idCat='$idCat' WHERE  idLancamento=" . $temp['idLancamento'];
                    }
                    foreach ($queryUpdate as $val)
                        $acc->executeQuery($val);
                    break;
                case 3: // todos
                    $acc->executeQuery("SELECT idLancamento FROM anz_lancamentos WHERE idLancPai= (SELECT idLancPai FROM anz_lancamentos WHERE idLancamento= $id)  ORDER BY idLancamento ASC");
                    while ($acc->proxReg()) {
                        $temp = $acc->getDD();
                        $queryUpdate[] = "UPDATE anz_lancamentos SET valor=$valor, vencimento='$vencimento', descricao='$descricao', idCat='$idCat' WHERE  idLancamento=" . $temp['idLancamento'];
                        $temp = explode("-", $vencimento);
                        $vencimento = date('Y-m-d', mktime(0, 0, 0, $temp[1] + 1, $temp[2], $temp[0]));
                    }
                    foreach ($queryUpdate as $val)
                        $acc->executeQuery($val);
                    break;
                default:
            }
            return true;
        } else {
            // lancar primeira ocorr�ncia
            $query = "INSERT INTO anz_lancamentos (dataLancamento, valor, vencimento, descricao, idStatus, idCat, idUser)
			VALUES (NOW(), $valor, '$vencimento', '$descricao', 1, $idCat, " . $_SESSION['idUser'] . ")";
            $acc->executeQuery($query);
            if ($ocorrencias > 1) {
                // pegar id gerado
                $query = "SELECT MAX(idLancamento) as idLancPai FROM anz_lancamentos";
                $acc->executeQuery($query);
                $acc->proxReg();
                $temp = $acc->getDD();
                $idLancPai = $temp['idLancPai'];
                $acc->executeQuery("UPDATE anz_lancamentos SET idLancPai= $idLancPai WHERE idLancamento= $idLancPai");
                // lancar demais ocorrencias
                for ($i = 1; $i <= $ocorrencias; $i++) {
                    $temp = explode("-", $vencimento);
                    $vencimento = date('Y-m-d', mktime(0, 0, 0, $temp[1] + 1, $temp[2], $temp[0]));
                    $query = "INSERT INTO anz_lancamentos (dataLancamento, valor, vencimento, descricao, idStatus, idCat, idUser, idLancPai)
					VALUES (NOW(), $valor, '$vencimento', '$descricao', 1, $idCat, " . $_SESSION['idUser'] . ", $idLancPai)";
                    $acc->executeQuery($query);
                    unset($temp);
                }
            }
        }
        return true;
    }

    public function deleteRegistro($idLancamento, $qualExcluir) {
        $acc = new MySql();
        $acc->executeQuery("SELECT idLancPai, vencimento FROM anz_lancamentos WHERE idLancamento= $idLancamento");
        $acc->proxReg();
        $temp = $acc->getDD();
        $vencimento = $temp['vencimento'];
        $idLancPai = $temp['idLancPai'];
        switch ($qualExcluir) {
            case 1: // somente este
                $acc->executeQuery("DELETE FROM anz_lancamentos WHERE idLancamento= $idLancamento");
                break;
            case 2: // este mais futuros
                $acc->executeQuery("DELETE FROM anz_lancamentos WHERE vencimento >= '$vencimento' AND
				idLancPai= $idLancPai");
                break;
            case 3:  // todos
                // desabilitado por seguranca
                //$acc->executeQuery("DELETE FROM anz_lancamentos WHERE idLancPai= $idLancPai");
                break;
        }
        return true;
    }

    //1: a receber, 2: a pagar
    public function getSaldoLancamentos($dataLimite = false) {
        $acc = new MySql();
        $dataLimite = ((!$dataLimite) ? $this->dataLimite : $dataLimite);
        $acc->executeQuery("SELECT sum(valor) as Saldo FROM anz_lancamentos l
				INNER JOIN anz_user u ON u.idUser = l.idUser
				INNER JOIN anz_empresa e ON e.idEmpresa = u.idEmpresa
				WHERE e.idEmpresa = " . $_SESSION['idEmpresa'] . " AND idStatus=1 AND valor>0 AND vencimento < '$dataLimite'");

        //		SELECT sum(valor) as Saldo FROM anz_lancamentos WHERE idStatus= 1 AND valor > 0 AND vencimento <
        $acc->proxReg();
        $temp = $acc->getDD();
        $dd['entradas'] = $temp['Saldo'];
        $dd['entradasFormat'] = number_format($temp['Saldo'], 2, ',', '.');
        $acc->executeQuery("SELECT sum(valor) as Saldo FROM anz_lancamentos l
				INNER JOIN anz_user u ON u.idUser = l.idUser
				INNER JOIN anz_empresa e ON e.idEmpresa = u.idEmpresa
				WHERE e.idEmpresa = " . $_SESSION['idEmpresa'] . " AND idStatus=1 AND valor<0 AND vencimento < '$dataLimite'");



        //SELECT sum(valor) as Saldo FROM anz_lancamentos WHERE idStatus= 1 AND valor < 0 AND vencimento < '$dataLimite'");
        $acc->proxReg();
        $temp = $acc->getDD();
        $dd['saidas'] = $temp['Saldo'];
        $dd['saidasFormat'] = number_format($temp['Saldo'], 2, ',', '.');

        return $dd;
    }

    //1: a receber, 2: a pagar
    /**
     * @param int $tipo
     * @param String[date] $dataLimite
     * @param int $status
     * @return string
     */
    public function getLancamentos($tipo = 2, $dataLimite = false, $status = 1) {
        $acc = new MySql();
        $dataLimite = ((!$dataLimite) ? $this->dataLimite : $dataLimite);
        $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $class = (($tipo == 2) ? 'negativo' : 'positivo');
        $tipo = (($tipo == 2) ? 'valor < 0' : 'valor >= 0');
        $query = "SELECT l.*, c.label FROM anz_lancamentos l
		INNER JOIN anz_categorias c ON l.idCat = c.idCat
		INNER JOIN anz_user u ON u.idUser = l.idUser
		INNER JOIN anz_empresa e ON e.idEmpresa = u.idEmpresa
		WHERE $tipo AND vencimento < '$dataLimite' AND l.idStatus=$status AND e.idEmpresa= " . $_SESSION['idEmpresa'] . "
				ORDER BY l.vencimento, c.label ASC";
        $acc->executeQuery($query);
        if ($acc->getNumRows() == 0)
            return ("Nenhum registro localizado");
        $out = '<ul data-role="listview" data-split-icon="check">';
        while ($acc->proxReg()) {
            $dd = $acc->getDD();
            if ($dd['vencimento'] < date('Y-m-d', $hoje))
                $vencido = 'style="background:#ff0"';
            else
                $vencido = '';
            $out .= '
				<li ' . $vencido . ' >
					<a class="lancamentos_registros" id="liLancamentos_' . $dd['idLancamento'] . '">					
						<h3 style="width: 100%">' . utf8_encode($dd['label']) . '</h3> <span style="display:none;" id="loadingLancamentos' . $dd['idLancamento'] . '">' . self::getLoading() . ' </span>
						<p>' . utf8_encode($dd['descricao']) . '</p>
						<span class="ui-li-count"><span class="' . $class . '">R$' . number_format($dd['valor'], 2, ',', '.') . '</span></span>
						<p class="ui-li-aside" style="width: 65%;">Venc.: ' . RegNegocios::arrumaData($dd['vencimento'], 'mostrar') . '</p>
					</a>
					<a class="lancamentos_finalizar" id="liLancamentosFinalizar_' . $dd['idLancamento'] . '">	>Padrao</a>
				</li>';
        }
        $out .= '</ul>';
        return ($out);
    }

    public function getDadosRegistro($id) {
        $acc = new MySql();
        $acc->executeQuery("SELECT l.*, c.label, c.idCat as idCat, c.label as catLabel FROM anz_lancamentos l INNER JOIN anz_categorias c ON l.idCat = c.idCat
				WHERE idLancamento= $id");
        $acc->proxReg();
        foreach ($acc->getDD() as $key => $val)
            $dd[$key] = utf8_encode($val);
        $dd['vencimento'] = RegNegocios::arrumaData($dd['vencimento'], 'mostrar');
        $dd['valorFormatado'] = number_format((($dd['valor'] < 0) ? $dd['valor'] * -1 : $dd['valor']), 2, ',', '.');
        return ($dd);
    }

    public function getDadosConta($idConta) {
        $acc = new MySql();
        $acc->executeQuery("SELECT * FROM anz_contas WHERE idConta= $idConta");
        $acc->proxReg();
        $dd = $acc->getDD();
        return ($dd);
    }

    public function getDados($tabela, $idNomeCampo, $idValor) {
        $acc = new MySql();
        $acc->executeQuery("SELECT * FROM $tabela WHERE $idNomeCampo= $idValor");
        $acc->proxReg();
        $dd = $acc->getDD();
        foreach ($dd as $key => $val)
            $dd[$key] = utf8_encode($val);
        return ($dd);
    }

    public function getRelacaoContas() {
        $acc = new MySql();
        $acc->executeQuery("SELECT idConta, nome FROM anz_contas WHERE idEmpresa= " . $_SESSION['idEmpresa']);
        while ($acc->proxReg()) {
            $dd = $acc->getDD();
            $out .= '<option value="' . $dd['idConta'] . '">' . utf8_encode($dd['nome']) . '</option>';
        }
        return $out;
    }

    public function finalizarRegistro($id, $conta) {
        $acc = new MySql();
        $dd = $this->getDadosRegistro($id);
        $conta = $this->getDadosConta($conta);
        if (!is_array($dd))
            return (array(false, utf8_encode("Registro nãoo existe"))); // verifica se registro existe
        if ($dd['idStatus'] != 1)
            return (array(false, utf8_encode("Status não permite finalização"))); // verificar status
        if (!is_array($conta))
            return (array(false, utf8_encode("Conta não existe"))); // verifica se conta existe

            
// Criar uma transa��o
        
        $acc->executeQuery("INSERT INTO anz_contacorrente VALUES ($id, " . $conta['idConta'] . ", NOW(), " . $_SESSION['idEmpresa'] . ")");
        $acc->executeQuery("UPDATE anz_lancamentos SET idStatus= 4 WHERE idLancamento= $id");

        return (array(true, ""));
    }

    public function getSaldoCC($idConta = false, $passivo = false) {
        $acc = new MySql();
        $where = (($idConta) ? " AND cc.idConta= $idConta" : "");
        if ($passivo === false)
            $where .= " AND c.passivo=0";
        else if ($passivo === true)
            $where .= " AND c.passivo=1";

        $acc->executeQuery("SELECT sum(valor) as Saldo FROM anz_lancamentos l
				INNER JOIN anz_contacorrente cc ON cc.idLancamento = l.idLancamento
				INNER JOIN anz_user u ON u.idUser = l.idUser
				INNER JOIN anz_empresa e ON e.idEmpresa = u.idEmpresa
				INNER JOIN anz_contas c ON c.idConta = cc.idConta
				WHERE e.idEmpresa = " . $_SESSION['idEmpresa'] . $where);
        $acc->proxReg();
        $dd = $acc->getDD();
        $dd['saldoFormatado'] = number_format($dd['Saldo'], 2, ',', '.');
        return ($dd);
    }

    public function getExtratoCC($idConta = false, $dtInicial = false, $dtFinal = false, $edit = false) {
        $acc = new MySql();
        if (!$idConta) {
            $acc->executeQuery("SELECT idConta, nome FROM anz_contas WHERE idEmpresa= " . $_SESSION['idEmpresa']);
            while ($acc->proxReg()) {
                $dd = $acc->getDD();
                $sd = self::getExtratoCC($dd['idConta'], $dtInicial, $dtFinal, $edit);
                if (!$sd) {
                    $sd = '<p><b>Conta: </b>' . $dd['nome'] . (($edit) ? ' <a href="#addConta" onclick="javascript:contasSetRegistro(\'' . $dd['idConta'] . '\')"><img src="images/editar.png" width="16" height="16" alt="Editar Conta" /></a>' : '');
                    $saldo = $this->getSaldoCC($dd['idConta'], true);
                    $sd .= '<br><span align="left"><b>Saldo:</b> <span class="' . (($saldo['Saldo'] > 0) ? "positivo" : "negativo") . '">R$ ' . $saldo['saldoFormatado'] . '</span>';
                    $sd .= "</p>Sem movimenta��o.<hr>";
                    $sd = utf8_encode($sd);
                }
                $temp .= '<div id="extratoConta_' . $dd['idConta'] . '">' . $sd . '</div>';
            }
            return $temp;
        }

        $dtInicial = (($dtInicial) ? RegNegocios::arrumaData($dataInicial, 'arrumar') : date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 30, date('Y'))));
        $dtFinal = (($dtFinal) ? RegNegocios::arrumaData($dataFinal, 'arrumar') : date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))));
        $query = "SELECT anz_categorias.label as Categoria, a.*, anz_contas.nome, anz_lancamentos.* FROM anz_contacorrente a
		INNER JOIN anz_contas ON anz_contas.idConta=a.idConta
		INNER JOIN anz_lancamentos ON anz_lancamentos.idLancamento=a.idLancamento
		INNER JOIN anz_categorias ON anz_categorias.idCat=anz_lancamentos.idCat
		INNER JOIN anz_user u ON u.idUser = anz_lancamentos.idUser
		INNER JOIN anz_empresa e ON e.idEmpresa = u.idEmpresa
		WHERE anz_lancamentos.vencimento BETWEEN '$dtInicial 00:00:00' AND '$dtFinal 23:59:59' AND anz_contas.idConta= $idConta AND e.idEmpresa= " . $_SESSION['idEmpresa'] . "
				ORDER BY anz_lancamentos.vencimento ASC
				";
        $acc->executeQuery($query);
        if ($acc->getNumRows() == 0)
            return false;

        while ($acc->proxReg()) {
            $dd = $acc->getDD();
            $conta = $dd['nome'];
            $out .= '
					<div class="ui-block-a">' . RegNegocios::arrumaData($dd['vencimento'], 'mostrar') . '</div>
							<div class="ui-block-b"><div align="right" class="' . (($dd['valor'] > 0) ? "" : "negativo") . '" style="margin-right:10px;">' . number_format($dd['valor'], 2, ',', '.') . '</div></div>
									<div class="ui-block-c">' . $dd['Categoria'] . '</div>
													';
//											<div class="ui-block-d">'.RegNegocios::arrumaData($dd['vencimento'], 'mostrar').'</div>													
//						<div class="ui-block-d">Vencimento</div>
        }
        $saldo = $this->getSaldoCC($idConta, 'nao importa');
        $out = '<p><b>Conta: </b>' . $conta . (($edit) ? ' <a href="#addConta" onclick="javascript:contasSetRegistro(\'' . $idConta . '\')"><img src="images/editar.png" width="16" height="16" alt="Editar Conta" /></a>' : '') . '
				<br><span align="left"><b>Saldo:</b> <span class="' . (($saldo['Saldo'] > 0) ? "positivo" : "negativo") . '">R$ ' . $saldo['saldoFormatado'] . '</span></span>
						<div class="ui-grid-c" style="font-size:14px">
						<div class="ui-block-a">Vencimento</div>
						<div class="ui-block-b"><div align="center">Valor</div></div>
						<div class="ui-block-c">Categoria</div>

						' . $out;
        $out .= '</div>
				</p><hr>';

        return utf8_encode($out);
    }

    public function geraBalanco() {
        $saldoGeral = self::getSaldoCC(false, false);
        $saldoLancamentos = self::getSaldoLancamentos();
        $balanco = ($saldoLancamentos['entradas'] + $saldoLancamentos['saidas'] + $saldoGeral["Saldo"]);

        $div = ' <hr>
				<p><b> Resumos próximos 30 dias:</b></p>
				<p>Entradas pendentes: <span class="' . (($saldoLancamentos['entradas'] >= 0) ? "positivo" : "negativo") . '">R$ ' . $saldoLancamentos['entradasFormat'] . '</span></p>
						<p>Pagamentos pendentes: <span class="' . (($saldoLancamentos['saidas'] >= 0) ? "positivo" : "negativo") . '">R$ ' . $saldoLancamentos['saidasFormat'] . '<span class="' . (($balanco > 0) ? "positivo" : "negativo") . '"></p>
								<p align="left">Sub-total: <span class="' . ((($saldoLancamentos['entradas'] + $saldoLancamentos['saidas']) >= 0) ? "positivo" : "negativo") . '">
										R$' . number_format(($saldoLancamentos['entradas'] + $saldoLancamentos['saidas']), 2, ',', '.') . '</p>
												<p>Saldo em contas: <span class="' . (($saldoGeral['Saldo'] >= 0) ? "positivo" : "negativo") . '">R$' . $saldoGeral['saldoFormatado'] . '</span></p>
														';
        $p = '<p align="center" style="font-size:24px" class="' . (($balanco >= 0) ? "positivo" : "negativo") . '">Balanço até ' . date('d/m/Y', $this->dataLimiteMktime - 1) . ': <br>R$' . number_format($balanco, 2, ',', '.') . '</p>';
        return (array("div" => $div, "p" => $p));
    }

    public function addCat($tipo, $label, $idCat = false) {
        $acc = new MySql();
        if (($tipo == "") || ($label == ""))
            return (array(false, utf8_encode("Dados n�o preenchidos corretamente")));
        $label = utf8_decode($label);
        if (!$idCat) { // novo registro
            $acc->executeQuery("SELECT COUNT(idCat) as qtde
					FROM anz_categorias
					WHERE idEmpresa= " . $_SESSION['idEmpresa'] . " AND tipo= $tipo AND label= '$label'");
            $acc->proxReg();
            $dd = $acc->getDD();
            if ($dd['qtde'] > 0)
                return (array(false, utf8_encode("J� existe categoria com estas caracteristicas")));
            $acc->executeQuery("INSERT INTO anz_categorias (idEmpresa, tipo, label) VALUES (" . $_SESSION['idEmpresa'] . ", $tipo, '$label')");
            return (array(true, utf8_encode("Categoria incluida com sucesso!")));
        }
        else {
            $acc->executeQuery("SELECT tipo FROM anz_categorias WHERE idEmpresa= " . $_SESSION['idEmpresa'] . " AND  idCat= $idCat");
            if ($acc->getNumRows() == 0)
                return (array(false, utf8_encode("Categoria '$idCat' n�o localizada.")));
            $acc->proxReg();
            $dd = $acc->getDD();
            if ($dd['tipo'] != $tipo)
                $txtAdicional = utf8_encode("\n\nObs: Não é possível mudar o tipo de uma categoria. Apenas o nome foi alterado.");
            $acc->executeQuery("UPDATE anz_categorias SET label= '$label' WHERE idCat= $idCat");
            return (array(true, "Categoria atualizada com sucesso!" . $txtAdicional));
        }
    }

    public function atualizaStatus($tabela, $novoStatus, $idNomeCampo, $idValor, $nomeCampoStatus = 'idStatus') {
        $acc = new MySql();
        $acc->executeQuery("UPDATE $tabela SET $nomeCampoStatus= $novoStatus WHERE $idNomeCampo= $idValor");
        return true;
    }

    public function addConta($dados, $id) {
        $acc = new MySql();
        if ($dados['nome'] == '')
            return (array(false, utf8_encode('Nome da conta n�o informado')));
        foreach ($dados as $key => $val)
            $$key = utf8_decode($val);
        if ($id != '') { // update
            $acc->executeQuery("UPDATE anz_contas SET passivo= $passivo, nome='$nome', banco='$banco', agencia='$agencia', conta='$conta', titular='$titular'
					WHERE idConta= $id");
        } else { // novo
            $acc->executeQuery("INSERT INTO anz_contas (nome, banco, agencia, conta, titular, idEmpresa, passivo) VALUES
					('$nome', '$banco', '$agencia', '$conta', '$titular', " . $_SESSION['idEmpresa'] . ", $passivo)");
        }
        return true;
    }

    /* Responsavel por registrar valores entre paginas. Evitar SQL injection */

    public function set($dados) {
        foreach ($dados as $key => $val)
            $_SESSION[$key] = utf8_decode($val);
    }

    public function numberFormat($numero) {
        return (number_format($numero, 2, ',', '.'));
    }

    public function getLoading($tamanho = 33) {
        return '<img src="images/loading.gif" width="' . $tamanho . '" height="' . $tamanho . '">';
    }

    /**
     * Metodo para atualizar cadastro do usuario
     * $dados Array
     * */
    public function cadastroUserAtualizaDiaCorte($novoDia) {
        $acc = new MySql();
        $acc->executeQuery("UPDATE anz_user SET diaInicioContabil= $novoDia WHERE idUser= " . $_SESSION['idUser']);
        return true;
    }

}

?>