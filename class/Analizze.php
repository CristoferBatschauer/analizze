<?php

require_once ('RegNegocios.php');
require_once ('Conexao.php');

class Analizze {

    var $dataLimite, $dataLimiteMktime = false;
    public $con;

    /**
      $iconeLeft e $iconeRight: Entra array contendo: Texto=>texto, Icone=>icone
     */
    public function Analizze() {
        $this->con = new Conexao();
// data limite
        if (isset($_SESSION['diaInicioContabil'])) {

            $hoje = mktime(-3, 0, 0, date('m'), date('d'), date('Y'));
            $this->dataLimite = mktime(-3, 0, 0, date('m') + $_SESSION['mesesFuturos'], $_SESSION['diaInicioContabil'], date('Y'));
            if (($this->dataLimite - $hoje) <= 0) { // data limite do pr�ximo mês
                $this->dataLimite = $dataLimite = mktime(-3, 0, 0, date('m') + 1, $_SESSION['diaInicioContabil'], date('Y'));
            }
        } else {
            $this->dataLimite = mktime(-3, 0, 0, date('m') + 1, date('d'), date('Y'));
        }
        $this->dataLimiteMktime = $this->dataLimite;
        $this->dataLimite = date('Y-m-d', $this->dataLimite);
    }

    public function geraHeader($iconeLeft, $texto, $iconeRight, $escreve = true) {
        $out = '<div style="margin-bottom:50px;max-width: 768px; position: fixed;text-align: center;top: 0;z-index: 10;" data-role="header" data-theme="a"><div class="ui-grid-b">';
        $out .= '<div class="ui-block-a"><div align="left">' . ((count($iconeLeft) > 0) ? '<a class="' . $iconeLeft['class'] . '" href="' . $iconeLeft['link'] . '" data-role="button" data-icon="' . $iconeLeft['icone'] . '" data-iconpos="left">' . $iconeLeft['texto'] . '</a>' : '') . '</div></div>';
        $out .= '<div class="ui-block-b"><div align="center"><h2>' . $texto . '</h2></div></div>';
        $out .= '<div class="ui-block-c"><div align="right"><span id="loading" style="display:none;">' . Analizze::getLoading() . '</span>' . ((count($iconeRight) > 0) ? '<a class="' . $iconeRight['class'] . '" href="' . $iconeRight['link'] . '" data-role="button" data-icon="' . $iconeRight['icone'] . '" data-iconpos="left">' . $iconeRight['texto'] . '</a>' : '') . '	</div></div>';
        $out .= '</div></div>'
                . '<div style="height:50px;">fim</div>';


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
        $this->con->executeQuery("SELECT * FROM anz_categorias
				WHERE tipo= $tipo AND idStatus= $status AND (idEmpresa= 2 OR idEmpresa= " . $_SESSION['idEmpresa'] . ")
				ORDER BY label");

        if ($this->con->numRows == 0) {
            return false;
        }

        $i = 0;
        $out = '';

        $out = '<div data-role="fieldcontain" id="relacao' . $tipo . '" class="relacaoCategorias">
				<fieldset data-role="controlgroup" data-type="horizontal">';
        while ($dd = $this->con->next()) {
            $out .= '<input type="radio" name="' . $nomeElemento . '" ' . $onclick . ' id="' . $nomeElemento . '_' . $i . '" value="' . $dd['idCat'] . '" />
		<label for="' . $nomeElemento . '_' . $i . '" style="font-size: 12px;float:left;margin:2px;width: 140px;height:40px;text-align: center;">' . utf8_encode($dd['label']) . '</label>';
            $i++;
        }
        $out .= '</fieldset></div>';
        return $out;
    }

    /**
     * Metodo responsavel por regitrar os lancamentos. Se n�o receber Id, cria um novo, se receber atualiza
     */
    public function addRegistro($dados, $id = false) {
        $this->con = new Conexao();
        $execute = true;
        $this->con->autocommit(false);

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
                    if (!$this->con->executeQuery($query)) {
                        RegNegocios::gravaLog("ERROR", __METHOD__ . ":" . __LINE__);
                        $execute = false;
                    }
                    break;
                case 2: // este e futuros
                    $query = "UPDATE anz_lancamentos SET valor=$valor, vencimento='$vencimento', descricao='$descricao', idCat='$idCat' WHERE  idLancamento=$id";
                    $this->con->executeQuery($query);
                    $this->con->executeQuery("SELECT idLancamento FROM anz_lancamentos
							WHERE vencimento > '$vencimento' AND idLancamento != $id AND idLancPai= (SELECT idLancPai FROM anz_lancamentos WHERE idLancamento= $id)
							ORDER BY idLancamento ASC");
                    while ($dd = $this->con->next()) {
                        $temp = explode("-", $vencimento);
                        $vencimento = date('Y-m-d', mktime(0, 0, 0, $temp[1] + 1, $temp[2], $temp[0]));
                        unset($temp);
                        $queryUpdate[] = "UPDATE anz_lancamentos SET valor=$valor, vencimento='$vencimento', descricao='$descricao', idCat='$idCat' WHERE  idLancamento=" . $dd['idLancamento'];
                    }
                    foreach ($queryUpdate as $query)
                        if (!$this->con->executeQuery($query)) {
                            RegNegocios::gravaLog("ERROR", __METHOD__ . ":" . __LINE__);
                            $execute = false;
                        }
                    break;
                default:
            }
        } else {
// lancar primeira ocorrencia
            $query = "INSERT INTO anz_lancamentos (dataLancamento, valor, vencimento, descricao, parcelas, idStatus, idCat, idUser)
			VALUES (NOW(), $valor, '$vencimento', '$descricao', '1/" . $ocorrencias . "', 1, $idCat, " . $_SESSION['idUser'] . ")";
            RegNegocios::gravaLog("ERROR", __METHOD__ . ":" . __LINE__ . " - Query: " . $query);
            if (!$this->con->executeQuery($query)) {
                RegNegocios::gravaLog("ERROR", __METHOD__ . ":" . __LINE__ . " - Query: " . $query);
                $execute = false;
            }
            if ($ocorrencias > 1) {
// pegar id gerado
                /*
                  $query = "SELECT MAX(idLancamento) as idLancPai FROM anz_lancamentos";
                  RegNegocios::gravaLog("ERROR", __METHOD__ . ":" . __LINE__." - Query: " . $query);
                  if (!$this->con->executeQuery($query)) {
                  RegNegocios::gravaLog("ERROR", __METHOD__ . ":" . __LINE__);
                  $execute = false;
                  }
                  $this->con->next();
                 */
                $idLancPai = $this->con->lastInsertId; //$dd['idLancPai'];
                if (!$this->con->executeQuery("UPDATE anz_lancamentos SET idLancPai= $idLancPai WHERE idLancamento= $idLancPai")) {
                    RegNegocios::gravaLog("ERROR", __METHOD__ . ":" . __LINE__ . " - Query: " . $query);
                    $execute = false;
                }
// lancar demais ocorrencias
                for ($i = 1; $i < $ocorrencias; $i++) {
                    $temp = explode("-", $vencimento);
                    $vencimento = date('Y-m-d', mktime(0, 0, 0, $temp[1] + 1, $temp[2], $temp[0]));
                    $query = "INSERT INTO anz_lancamentos (dataLancamento, valor, vencimento, descricao, parcelas, idStatus, idCat, idUser, idLancPai)
					VALUES (NOW(), $valor, '$vencimento', '$descricao', '" . (($i + 1) . '/' . $ocorrencias) . "', 1, $idCat, " . $_SESSION['idUser'] . ", $idLancPai)";
                    if (!$this->con->executeQuery($query)) {
                        RegNegocios::gravaLog("ERROR", __METHOD__ . ":" . __LINE__ . " - Query: " . $query);
                        $execute = false;
                        break;
                    }
                    unset($temp);
                }
//return $idLancPai;
            }
        }
// executar commit ou rollback
        if ((!$execute)) {
            $this->con->rollback();
            $this->con->autocommit(true);
            return false;
        } else {
            $this->con->commit();
            $this->con->autocommit(true);
            return true;
        }
    }

    public function deleteRegistro($idLancamento, $qualExcluir) {
        $this->con->executeQuery("SELECT idLancPai, vencimento FROM anz_lancamentos WHERE idLancamento= $idLancamento");
        $dd = $this->con->next();
        var_dump($dd);
        $vencimento = $dd['vencimento'];
        $idLancPai = $dd['idLancPai'];
        switch ($qualExcluir) {
            case 1: // somente este
                $this->con->executeQuery("DELETE FROM anz_lancamentos WHERE idLancamento= $idLancamento");
                break;
            case 2: // este mais futuros
                $this->con->executeQuery("DELETE FROM anz_lancamentos WHERE vencimento >= '$vencimento' AND
				idLancPai= $idLancPai");
                break;
            case 3:  // todos
                $this->con->executeQuery("DELETE FROM anz_lancamentos WHERE idLancPai= $idLancPai");
                break;
        }
        if ($this->con->numRows > 0)
            return true;
        else
            return false;
    }

//1: a receber, 2: a pagar
    public function getSaldoLancamentos($dataLimite = false) {
        $dataLimite = ((!$dataLimite) ? $this->dataLimite : $dataLimite);
        $this->con->executeQuery("SELECT sum(valor) as Saldo FROM anz_lancamentos l
				INNER JOIN anz_user u ON u.idUser = l.idUser
				INNER JOIN anz_empresa e ON e.idEmpresa = u.idEmpresa
				WHERE e.idEmpresa = " . $_SESSION['idEmpresa'] . " AND idStatus=1 AND valor>0 AND vencimento < '$dataLimite'");

//		SELECT sum(valor) as Saldo FROM anz_lancamentos WHERE idStatus= 1 AND valor > 0 AND vencimento <
        $dd = $this->con->next();
        $out['entradas'] = $dd['Saldo'];
        $out['entradasFormat'] = number_format($dd['Saldo'], 2, ',', '.');
        $this->con->executeQuery("SELECT sum(valor) as Saldo FROM anz_lancamentos l
				INNER JOIN anz_user u ON u.idUser = l.idUser
				INNER JOIN anz_empresa e ON e.idEmpresa = u.idEmpresa
				WHERE e.idEmpresa = " . $_SESSION['idEmpresa'] . " AND idStatus=1 AND valor<0 AND vencimento < '$dataLimite'");



//SELECT sum(valor) as Saldo FROM anz_lancamentos WHERE idStatus= 1 AND valor < 0 AND vencimento < '$dataLimite'");
        $dd = $this->con->next();
        $out['saidas'] = $dd['Saldo'];
        $out['saidasFormat'] = number_format($dd['Saldo'], 2, ',', '.');

        return $out;
    }

//1: a receber, 2: a pagar
    /**
     * @param int $tipo
     * @param String[date] $dataLimite
     * @param int $status
     * @return string
     */
    public function getLancamentos($tipo = 2, $dataLimite = false, $status = 1) {
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
        $this->con->executeQuery($query);
        if ($this->con->numRows == 0) {
            return ("Nenhum registro localizado");
        }
        $out = '<ul data-role="listview" data-split-icon="check">';
        while ($dd = $this->con->next()) {
            if ($dd['vencimento'] < date('Y-m-d', $hoje)) {
                $vencido = 'style="background:#ff0"';
            } else {
                $vencido = '';
            }
            $out .= '
				<li ' . $vencido . ' >
					<a class="lancamentos_registros" id="liLancamentos_' . $dd['idLancamento'] . '">					
						<h3 style="width: 100%">' . $dd['label'] . '</h3> <span style="display:none;" id="loadingLancamentos' . $dd['idLancamento'] . '">' . self::getLoading() . ' </span>
						<p>' . $dd['descricao'] . '</p>
						<span class="ui-li-count"><span class="' . $class . '">R$' . number_format($dd['valor'], 2, ',', '.') . '</span></span>
						<p class="ui-li-aside" style="width: 65%;">Venc.: ' . RegNegocios::arrumaData($dd['vencimento'], 'mostrar') . '</p>
					</a>
					<a class="lancamentos_finalizar" id="liLancamentosFinalizar_' . $dd['idLancamento'] . '">	>Padrao</a>
				</li>';
        }
        $out .= '</ul>';
        return $out;
    }

    public function getDadosRegistro($id) {
        $this->con->executeQuery("SELECT l.*, c.label, c.idCat as idCat, c.label as catLabel FROM anz_lancamentos l INNER JOIN anz_categorias c ON l.idCat = c.idCat
				WHERE idLancamento= $id");
        $dd = $this->con->next();
        foreach ($dd as $key => $val) {
            $dd[$key] = utf8_encode($val);
        }
        $dd['vencimento'] = RegNegocios::arrumaData($dd['vencimento'], 'mostrar');
        $dd['valorFormatado'] = number_format((($dd['valor'] < 0) ? $dd['valor'] * -1 : $dd['valor']), 2, ',', '.');
        return ($dd);
    }

    public function getDadosConta($idConta) {
        $this->con->executeQuery("SELECT * FROM anz_contas WHERE idConta= $idConta");
        $dd = $this->con->next();
        return ($dd);
    }

    public function getDados($tabela, $idNomeCampo, $idValor) {
        $this->con->executeQuery("SELECT * FROM $tabela WHERE $idNomeCampo= $idValor");
        $dd = $this->con->next();
        foreach ($dd as $key => $val)
            $dd[$key] = utf8_encode($val);
        return ($dd);
    }

    public function getRelacaoContas() {
        $this->con->executeQuery("SELECT idConta, nome FROM anz_contas WHERE idEmpresa= " . $_SESSION['idEmpresa']);
        while ($dd = $this->con->next()) {
            $out .= '<option value="' . $dd['idConta'] . '">' . utf8_encode($dd['nome']) . '</option>';
        }
        return $out;
    }

    public function finalizarRegistro($id, $conta, $transacao = true) {
        $dd = $this->getDadosRegistro($id);
        $conta = $this->getDadosConta($conta);
        if (!is_array($dd)) {
            return (array(false, utf8_encode("Registro não existe")));
        } // verifica se registro existe
        if ($dd['idStatus'] != 1) {
            return (array(false, utf8_encode("Status não permite finalização")));
        } // verificar status
        if (!is_array($conta)) {
            return (array(false, utf8_encode("Conta não existe")));
        } // verifica se conta existe
// Criar uma transacao
        $erro = 0;
        $this->con->autocommit(!$transacao);
        if (!$this->con->executeQuery("INSERT INTO anz_contacorrente VALUES ($id, " . $conta['idConta'] . ", NOW(), " . $_SESSION['idEmpresa'] . ")")) {
            $erro++;
            RegNegocios::gravaLog("ERROR", __METHOD__ . ":" . __LINE__ . " - " . $this->con->error);
        }
        if (!$this->con->executeQuery("UPDATE anz_lancamentos SET idStatus= 4 WHERE idLancamento= $id")) {
            RegNegocios::gravaLog("ERROR", __METHOD__ . ":" . __LINE__);
            $erro++;
        }

        if ($erro !== 0) {
            $this->con->rollback();
            $this->con->autocommit(true);
            return (array(false, "Erro ao completar transação - W54TR"));
        } else {
            $this->con->commit();
            $this->con->autocommit(true);
            return (array(true, ""));
        }
        return (array(true, ""));
    }

    /**
     * Método resposável por gerar uma relação de contas e seus saldos. 
     */
    public function homeSaldoContas() {
        $acc = new Conexao;
        $acc->executeQuery("SELECT idConta, nome, passivo FROM anz_contas WHERE idEmpresa= " . $_SESSION['idEmpresa'] . " ORDER BY passivo ASC");
        $i = 0;
        $out = '<div data-role="fieldcontain" id="homeSaldoContas" class="homeSaldoContas">
				<fieldset data-role="controlgroup" data-type="horizontal">';
        while ($dd = $acc->next()) {
            $saldo = $this->getSaldoCC($dd['idConta'], "none");
            $out .= '<input type="radio" name="homeSaldoConta" class="homeSaldoContas" id="homeSaldoConta_' . $dd['idConta'] . '" value="' . $dd['idConta'] . '" onclick="extratoShow(\'' . $dd['idConta'] . '\')" />
		<label for="homeSaldoConta_' . $dd['idConta'] . '" style="font-size: 12px;float:left;padding: 1px;margin:2px;width: 135px;height:70px;text-align: center;">'
                    . $dd['nome'] . '<br /> <span class="' . (($saldo['Saldo'] > 0) ? "positivo" : "negativo") . '">R$ ' . $saldo['saldoFormatado'] . '</span> </label>';
            $i++;
        }
        $out .= '</fieldset></div>';
        $out = "<h3>Saldo contas: </h3> " . $out;
        $out .= "<p id='extratoShow'></p>";
        return $out;
    }

    public function getSaldoCC($idConta = false, $passivo = false) {
        $where = (($idConta) ? " AND cc.idConta= $idConta" : "");
        if ($passivo === false) {
            $where .= " AND c.passivo=0";
        } else if ($passivo === true) {
            $where .= " AND c.passivo=1";
        }

        $this->con->executeQuery("SELECT sum(valor) as Saldo FROM anz_lancamentos l
				INNER JOIN anz_contacorrente cc ON cc.idLancamento = l.idLancamento
				INNER JOIN anz_user u ON u.idUser = l.idUser
				INNER JOIN anz_empresa e ON e.idEmpresa = u.idEmpresa
				INNER JOIN anz_contas c ON c.idConta = cc.idConta
				WHERE e.idEmpresa = " . $_SESSION['idEmpresa'] . $where);
        $dd = $this->con->next();
        $dd['saldoFormatado'] = number_format($dd['Saldo'], 2, ',', '.');
        return ($dd);
    }

    public function getExtratoCC($idConta = false, $dtInicial = false, $dtFinal = false, $edit = false) {
        if (!$idConta) {
            $acc = new Conexao;
            $acc->executeQuery("SELECT idConta, nome FROM anz_contas WHERE idEmpresa= " . $_SESSION['idEmpresa']);
            while ($dd = $acc->next()) {
                $sd = $this->getExtratoCC($dd['idConta'], $dtInicial, $dtFinal, $edit);
                if (!$sd) {
                    $sd = '<p><b>Conta: </b>' . utf8_encode($dd['nome']) . (($edit) ? ' <a href="#addConta" onclick="javascript:contasSetRegistro(\'' . $dd['idConta'] . '\')"><img src="images/editar.png" width="16" height="16" alt="Editar Conta" /></a>' : '');
                    $saldo = $this->getSaldoCC($dd['idConta'], true);
                    $sd .= '<br><span align="left"><b>Saldo:</b> <span class="' . (($saldo['Saldo'] > 0) ? "positivo" : "negativo") . '">R$ ' . $saldo['saldoFormatado'] . '</span>';
                    $sd .= "</p>Sem Movimentação no periodo.<hr>";
                }
                $temp .= '<div id="extratoConta_' . $dd['idConta'] . '">' . $sd . '</div>';
            }
            return $temp;
        }

        $dtInicial = (($dtInicial) ? RegNegocios::arrumaData($dataInicial, 'arrumar') : date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 60, date('Y'))));
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
        $this->con->executeQuery($query);
        if ($this->con->numRows == 0)
            return false;

        while ($dd = $this->con->next()) {
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
        $saldoGeral = $this->getSaldoCC(false, false);
        $saldoLancamentos = $this->getSaldoLancamentos();
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
        if (($tipo == "") || ($label == "")) {
            return (array(false, utf8_encode("Dados n�o preenchidos corretamente")));
        }
        $label = utf8_decode($label);
        if (!$idCat) { // novo registro
            $this->con->executeQuery("SELECT COUNT(idCat) as qtde
					FROM anz_categorias
					WHERE idEmpresa= " . $_SESSION['idEmpresa'] . " AND tipo= $tipo AND label= '$label'");
            $dd = $this->con->next();
            if ($dd['qtde'] > 0) {
                return (array(false, utf8_encode("J� existe categoria com estas caracteristicas")));
            }
            $this->con->executeQuery("INSERT INTO anz_categorias (idEmpresa, tipo, label) VALUES (" . $_SESSION['idEmpresa'] . ", $tipo, '$label')");
            return (array(true, utf8_encode("Categoria incluida com sucesso!")));
        } else {
            $this->con->executeQuery("SELECT tipo FROM anz_categorias WHERE idEmpresa= " . $_SESSION['idEmpresa'] . " AND  idCat= $idCat");
            if ($this->con->numRows == 0) {
                return (array(false, utf8_encode("Categoria '$idCat' n�o localizada.")));
            }
            $dd = $this->con->next();
            if ($dd['tipo'] != $tipo) {
                $txtAdicional = utf8_encode("\n\nObs: Não é possível mudar o tipo de uma categoria. Apenas o nome foi alterado.");
            }
            $this->con->executeQuery("UPDATE anz_categorias SET label= '$label' WHERE idCat= $idCat");
            return (array(true, "Categoria atualizada com sucesso!" . $txtAdicional));
        }
    }

    public function atualizaStatus($tabela, $novoStatus, $idNomeCampo, $idValor, $nomeCampoStatus = 'idStatus') {
        $this->con->executeQuery("UPDATE $tabela SET $nomeCampoStatus= $novoStatus WHERE $idNomeCampo= $idValor");
        return true;
    }

    public function addConta($dados, $id) {
        if ($dados['nome'] == '') {
            return (array(false, utf8_encode('Nome da conta n�o informado')));
        }
        foreach ($dados as $key => $val) {
            $$key = utf8_decode($val);
        }
        if ($id != '') { // update
            $this->con->executeQuery("UPDATE anz_contas SET passivo= $passivo, nome='$nome', banco='$banco', agencia='$agencia', conta='$conta', titular='$titular'
					WHERE idConta= $id");
        } else { // novo
            $this->con->executeQuery("INSERT INTO anz_contas (nome, banco, agencia, conta, titular, idEmpresa, passivo) VALUES
					('$nome', '$banco', '$agencia', '$conta', '$titular', " . $_SESSION['idEmpresa'] . ", $passivo)");
        }
        return true;
    }

    /* Responsavel por registrar valores entre paginas. Evitar SQL injection */

    public function set($dados) {
        foreach ($dados as $key => $val) {
            $_SESSION[$key] = utf8_decode($val);
        }
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
        $this->con->executeQuery("UPDATE anz_user SET diaInicioContabil= $novoDia WHERE idUser= " . $_SESSION['idUser']);
        return true;
    }

    /**
      Metodo para realizar transferência entre contas obrigatoriamente
      @$dados recebe array com dados de valor, vencimento e outros...
     * */
    public function transferenciaEntreContas($ctaOrigem, $ctaDestino, $valor) {
        if (!$ctaOrigem || !$ctaDestino || !$valor) {
            return "$ctaOrigem, $ctaDestino, $valor Preencha todos os campos corretamente";
        }
        if ($ctaDestino == $ctaOrigem) {
            return "Conta destino e origem iguais.";
        }
        $this->con = new Conexao();
        $execute = false;
        $this->con->autocommit(false);
        $dados = array();
        $dados['valor'] = $valor;
        $dados['vencimento'] = date('d/m/Y');

// inserir lançamento crédito
        $dados['idCat'] = 1;
        $execute = $this->addRegistro($dados);
        $idCredito = $this->con->lastInsertId;

// inserir lançamento débito
        $dados['idCat'] = 2;
        $dados['opcao'] = "-";
        $execute = $this->addRegistro($dados);
        $idDebito = $this->con->lastInsertId;

// finalizar lançamento crédito
        $finCredito = $this->finalizarRegistro($idCredito, $ctaDestino, false);
// finalizar lancamento débito
        $finDebito = $this->finalizarRegistro($idDebito, $ctaOrigem, false);

// executar commit ou rollback
        if ((!$execute)) { // || (!$finDebito[0]) || (!$finCredito[0])) {
            $this->con->rollback();
            $this->con->autocommit(true);
            return false;
        } else {
            $this->con->commit();
            $this->con->autocommit(true);
            return true;
        }
    }

    public function getPizza($qtde = 7) {
        $dtInicial = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 60, date('Y')));

        $queryTotalSistemaDebito = "SELECT a.idCat as Categoria, SUM(valor) as total, AVG(valor) as media , b.label FROM anz_lancamentos a
INNER JOIN anz_categorias b ON a.idCat = b.idCat
where a.idCat > 2 AND idEmpresa= " . $_SESSION['idEmpresa'] . " AND a.idStatus= 4 AND valor < 0 AND vencimento < '" . $this->dataLimite . " 23:59:59'
GROUP BY a.idCat
ORDER BY media ASC
LIMIT $qtde";

        $this->con->executeQuery($queryTotalSistemaDebito);
        while ($dd = $this->con->next()) {
            // Data
            $chd[] = number_format( ( ($dd['media']*-1) *10/100), '0', '', '');
            // Labels
            $chl[] = ' R$'.number_format(($dd['media']*-1), '2', ',', '.');
            // Legend
            $chdl[] = utf8_encode($dd['label']). ' - R$'.number_format(($dd['media']*-1), '2', ',', '.');
        }
        $chl = array();
        //$chdl = array();
        $out = '<img src = "http://chart.apis.google.com/chart?cht=p&chd=t:'.implode(',',$chd).'&chs=320x250&chl='.implode('|',$chl).'&chdl='.  implode('|', $chdl).'" alt = "" />';
        return $out;
    }

}
