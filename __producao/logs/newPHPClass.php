<?php

class anz_lancamentos {

    private $error;

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

    public function __construct() {
        $this->error = false;
    }

    /*     * Getters And Setters* */

    /** @type int(10) unsigned notnull */
    public function setIdLancamento($idLancamento) {
        if ($idLancamento == "") {
            $this->error[] = "idLancamento é obrigatório";
        }
        $this->idLancamento = $idLancamento;
    }

    public function getIdLancamento() {
        return $this->idLancamento;
    }

    /** @type datetime notnull */
    public function setDataLancamento($dataLancamento) {
        if ($dataLancamento == "") {
            $this->error[] = "dataLancamento é obrigatório";
        }
        $this->dataLancamento = $dataLancamento;
    }

    public function getDataLancamento() {
        return $this->dataLancamento;
    }

    /** @type double(12,2) */
    public function setValor($valor) {
        $this->valor = $valor;
    }

    public function getValor() {
        return $this->valor;
    }

    /** @type date */
    public function setVencimento($vencimento) {
        $this->vencimento = $vencimento;
    }

    public function getVencimento() {
        return $this->vencimento;
    }

    /** @type varchar(100) */
    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    /** @type smallint(3) unsigned notnull */
    public function setIdStatus($idStatus) {
        if ($idStatus == "") {
            $this->error[] = "idStatus é obrigatório";
        }
        $this->idStatus = $idStatus;
    }

    public function getIdStatus() {
        return $this->idStatus;
    }

    /** @type smallint(4) unsigned notnull */
    public function setIdCat($idCat) {
        if ($idCat == "") {
            $this->error[] = "idCat é obrigatório";
        }
        $this->idCat = $idCat;
    }

    public function getIdCat() {
        return $this->idCat;
    }

    /** @type int(10) unsigned */
    public function setIdLancPai($idLancPai) {
        $this->idLancPai = $idLancPai;
    }

    public function getIdLancPai() {
        return $this->idLancPai;
    }

    /** @type int(4) unsigned notnull */
    public function setIdUser($idUser) {
        if ($idUser == "") {
            $this->error[] = "idUser é obrigatório";
        }
        $this->idUser = $idUser;
    }

    public function getIdUser() {
        return $this->idUser;
    }

}

// fecha classe