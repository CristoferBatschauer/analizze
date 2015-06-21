<?php

class Relatorios extends Analizze {

    var $header, $status, $statusLabel, $dataInicio, $dataFim, $tipo, $nomeRelatorio, $idCat;

    public function Relatorios() {
        $this->removeRelatoriosAntigos();
    }

    /**
     * Método para remover os arquivos armazenados ha mais de 15 dias. 
     * Coloquei 20 dias para evitar problemas.
     */
    private function removeRelatoriosAntigos() {
        $zips = glob(ROOT_SISTEMA . "/relatorios/{*.pdf}", GLOB_BRACE);
        $dataLimite = mktime(0, 0, 0, date('m'), date('d') - 20, date('Y'));
        foreach ($zips as $key => $arquivo) {
            if (filectime($arquivo) < $dataLimite) {
                RegNegocios::delete_dir($arquivo);
            }
        }
    }

    public function getLancamentos() {
        $acc = new MySql();
        $idCat = false;
        switch ($this->tipo) {
            case 1: // Contas a Receber
                $query[] = " valor > 0";
                if ($_SESSION['idCatCredito'] != '')
                    $idCat = $_SESSION['idCatCredito'];
                $this->setNomeRelatorio("Contas a Receber");
                $txt = "Recebimento ";
                break;
            case 2: // Contas a Pagar
                $query[] = " valor < 0";
                if ($_SESSION['idCatDebito'] != '')
                    $idCat = $_SESSION['idCatDebito'];
                $this->setNomeRelatorio("Contas a Pagar");
                $txt = "Pagamento";
                break;
            case 3:
                $query[] = " valor != 0";
                $this->setNomeRelatorio("Contas a Pagar e Receber");
                $txt = "Pagamento/recebimento";
                break;
            default:
                return false;
        }
        switch ($this->status) {
            case 1:
                $this->setStatusLabel("Aguardando " . $txt);
                break;
            case 2:
                $this->setStatusLabel(utf8_decode("Status: Lançamento recusado"));
                break;
            case 3:
                $this->setStatusLabel(utf8_decode("Status: Aguardando autorização"));
                break;
            case 4:
                $this->setStatusLabel(utf8_decode("Status: Finalizado"));
                break;
            case 99:
                $this->setStatusLabel(utf8_decode("Status: Todos"));
                $this->status = "1 OR l.idStatus>0 ";
            default:
        }

        if ($idCat) {
            $temp = $this->getDados('anz_categorias', 'idCat', $idCat);
            $query[] = 'l.idCat = ' . $idCat;
            $this->setStatusLabel('Categoria: ' . utf8_decode($temp['label']) . ' - ' . $this->getStatusLabel());
        }


        if ($this->getIdCat() != '')
            $query[] = 'l.idCat = ' . $this->getIdCat();


        $query = "SELECT l.*, c.label, u.nome as userNome FROM anz_lancamentos l
		INNER JOIN anz_categorias c ON l.idCat = c.idCat
		INNER JOIN anz_user u ON u.idUser = l.idUser
		INNER JOIN anz_empresa e ON e.idEmpresa = u.idEmpresa
		WHERE " . implode(" AND ", $query) . " AND vencimento >= '" . $this->getDataInicio() . "' AND vencimento <= '" . $this->getDataFim() . "' AND (l.idStatus=" . $this->getStatus() . ") AND e.idEmpresa= " . $_SESSION['idEmpresa'] . "
		ORDER BY vencimento ASC";

        $acc->executeQuery($query);
        if ($acc->getNumRows() == 0)
            return false;

        $this->setHeader(
                array("Vencimento|28|C", "Valor (R$)|25|R", "Categoria|41|C", "Notas|98|L"));
        while ($acc->proxReg()) {
            $dd = $acc->getDD();
            $out[] = array(
                RegNegocios::arrumaData($dd['vencimento'], 'mostrar'),
                number_format($dd['valor'], 2, ',', '.'),
                    $dd['label'],
                $dd['descricao'],
                $dd['valor']
            );
        }
        return $out;
    }

    public function getDataInicio() {
        return $this->dataInicio;
    }

    public function setDataInicio($dataInicio = false) {
        $this->dataInicio = (($dataInicio !== false) ? $dataInicio : date('Y-m-d', mktime(0, 0, 0, date('m') - 12, date('d'), date('Y')))); // 1 ano atras
    }

    public function getDataFim() {
        return $this->dataFim;
    }

    public function setDataFim($dataFim = false) {
        $this->dataFim = (($dataFim !== false) ? $dataFim : date('Y-m-d', mktime(0, 0, 0, date('m') + 1, date('d'), date('Y')))); // 30 dias
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function getNomeRelatorio() {
        return $this->nomeRelatorio;
    }

    private function setNomeRelatorio($nomeRelatorio) {
        $this->nomeRelatorio = $nomeRelatorio;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getHeader() {
        return $this->header;
    }

    private function setHeader($header) {
        $this->header = $header;
    }

    public function getStatusLabel() {
        return $this->statusLabel;
    }

    public function setStatusLabel($statusLabel) {
        $this->statusLabel = $statusLabel;
    }

    public function setIdCat($var) {
        $this->idCat = $var;
    }

    public function getIdCat() {
        return $this->idCat;
    }

}

?>