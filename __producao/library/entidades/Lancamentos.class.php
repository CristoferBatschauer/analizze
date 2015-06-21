<?php

class Lancamentos {

    private $error; // armazena possiveis erros, inclusive, obrigatoriedades.
    private $table = "anz_lancamentos"; // nome da tabela que a entidade representa
    private $cpoId = "idLancamento";

    /** @type int(10) unsigned */
    private $idLancamento;

    /** @type datetime */
    private $dataLancamento;

    /** @type double(12,2) */
    private $valor;

    /** @type date */
    private $vencimento;

    /** @type varchar(100) */
    private $descricao;

    /** @type smallint(3) unsigned */
    private $idStatus;

    /** @type smallint(4) unsigned */
    private $idCat;

    /** @type int(10) unsigned */
    private $idLancPai;

    /** @type int(4) unsigned */
    private $idUser;
    private $nomeCategoria;

    public function __construct($idLancamento = false, $dados = false) {
        $this->error = false;
        $this->idLancamento = $idLancamento;
        if ($idLancamento) {
            $acc = new MySql($this->table);
            $acc->executeQuery("SELECT a.*, b.label as nomeCategoria FROM anz_lancamentos a "
                    . "INNER JOIN anz_categorias b ON a.idCat = b.idCat WHERE idLancamento= $idLancamento");
            $acc->proxReg();
            //$dados = $acc->read($this->getId(), $this->getCpoId());
            $dados = $acc->getDD();
            if ($acc->getNumRows() == 0) {
                return false;
            }
        }
        $this->setDados($dados);
    }

    /** Persistencia do objeto * */
    public function save() {
        if ($this->getError()) {
            return false;
        }
        $em = new EntityManager($this);
        $em->save();
        if ($em->getError() !== false) {
            // tratar retorno false
            echo $em->getMessage(); // mostra mensagem de erro
            if (is_array($em->getError())) {
                foreach ($em->getError() as $val) {
                    echo $val . "<br />";
                }
            }
        }
    }

    /*     * * Metodo para remover objeto    */

    public function remove() {
        $em = new EntityManager($this);
        $em->remove();
        if ($em->getError() !== false) {
            // tratar retorno false
            echo $em->getMessage(); // mostra mensagem de erro
        } else {
            // zerar dados do objeto;
            $this->setDados(array());
        }
    }

    /**
     * Metodo para setar todos os parametros da entidades 
     * @param Array $dados
     * */
    private function setDados($dados) {
        if (is_array($dados))
            foreach ($dados as $key => $data)
                $dados[$key] = utf8_encode($data);
        $this->setIdLancamento($dados['idLancamento']);
        $this->setDataLancamento($dados['dataLancamento']);
        $this->setValor($dados['valor']);
        $this->setVencimento($dados['vencimento']);
        $this->setDescricao($dados['descricao']);
        $this->setIdStatus($dados['idStatus']);
        $this->setIdCat($dados['idCat']);
        $this->setIdLancPai($dados['idLancPai']);
        $this->setIdUser($dados['idUser']);
        $this->setNomeCategoria($dados['nomeCategoria']);
    }

    /**
     * Metodo para pegar todos os parametros da entidade em forma de array
     * @return Array
     * */
    public function getDados() {
        $out['idLancamento'] = $this->getIdLancamento();
        $out['dataLancamento'] = $this->getDataLancamento();
        $out['valor'] = $this->getValor();
        $out['vencimento'] = $this->getVencimento();
        $out['descricao'] = $this->getDescricao();
        $out['idStatus'] = $this->getIdStatus();
        $out['idCat'] = $this->getIdCat();
        $out['idLancPai'] = $this->getIdLancPai();
        $out['idUser'] = $this->getIdUser();
        return $out;
    }

    /* Metodos obrigatório pois EntityManager depende deles */

    public function getId() {
        return $this->idLancamento;
    }

    public function setId($id) {
        if ($this->idLancamento == '') {
            $this->idLancamento = (int) $id;
        }
    }

    public function setError($error) {
        $this->error = $error;
    }

    public function getError() {
        return $this->error;
    }

    public function getTable() {
        return $this->table;
    }

    public function getCpoId() {
        return $this->cpoId;
    }

    /*     * Getters And Setters* */

    /** @type int(10) unsigned notnull */
    private function setIdLancamento($idLancamento) {
        $this->idLancamento = (int) $idLancamento;
    }

    public function getIdLancamento() {
        return $this->idLancamento;
    }

    /** @type datetime notnull */
    public function setDataLancamento($dataLancamento) {
        if ($dataLancamento == "") {
            $this->error['dataLancamento'] = "dataLancamento é obrigatório";
        } else {
            unset($this->error['dataLancamento']);
        }
        $this->dataLancamento = (string) $dataLancamento;
    }

    public function getDataLancamento() {
        return $this->dataLancamento;
    }

    /** @type double(12,2) */
    public function setValor($valor) {
        $this->valor = (string) $valor;
    }

    public function getValor() {
        return $this->valor;
    }

    /** @type date */
    public function setVencimento($vencimento) {
        $this->vencimento = (string) $vencimento;
    }

    public function getVencimento() {
        return $this->vencimento;
    }

    /** @type varchar(100) */
    public function setDescricao($descricao) {
        $this->descricao = (string) $descricao;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    /** @type smallint(3) unsigned notnull */
    public function setIdStatus($idStatus) {
        if ($idStatus == "") {
            $this->error['idStatus'] = "idStatus é obrigatório";
        } else {
            unset($this->error['idStatus']);
        }
        $this->idStatus = (int) $idStatus;
    }

    public function getIdStatus() {
        return $this->idStatus;
    }

    /** @type smallint(4) unsigned notnull */
    public function setIdCat($idCat) {
        if ($idCat == "") {
            $this->error['idCat'] = "idCat é obrigatório";
        } else {
            unset($this->error['idCat']);
        }
        $this->idCat = (int) $idCat;
    }

    public function getIdCat() {
        return $this->idCat;
    }

    /** @type int(10) unsigned */
    public function setIdLancPai($idLancPai) {
        $this->idLancPai = (int) $idLancPai;
    }

    public function getIdLancPai() {
        return $this->idLancPai;
    }

    /** @type int(4) unsigned notnull */
    public function setIdUser($idUser) {
        if ($idUser == "") {
            $this->error['idUser'] = "idUser é obrigatório";
        } else {
            unset($this->error['idUser']);
        }
        $this->idUser = (int) $idUser;
    }

    public function getIdUser() {
        return $this->idUser;
    }

    public function getNomeCategoria() {
        return $this->nomeCategoria;
    }

    public function setNomeCategoria($nomeCategoria) {
        $this->nomeCategoria = $nomeCategoria;
    }

}

// fecha classe