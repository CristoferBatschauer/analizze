<?php

class Categorias {

    private $error; // armazena possiveis erros, inclusive, obrigatoriedades.
    private $table = "anz_categorias"; // nome da tabela que a entidade representa
    private $cpoId = "idCat";

    /** @type smallint(4) unsigned */
    private $idCat;

    /** @type enum('1','2') */
    private $tipo;

    /** @type varchar(8) */
    private $icone;

    /** @type varchar(45) */
    private $label;

    /** @type smallint(3) unsigned */
    private $idStatus;

    /** @type smallint(2) unsigned */
    private $idEmpresa;

    public function __construct($idCat = false, $dados = false) {
        $this->error = false;
        $this->idCat = $idCat;
        $dados['idEmpresa'] = $_SESSION['idEmpresa'];
        if ($idCat) {
            $acc = new MySql($this->table);
            $dados = $acc->read($this->getId(), $this->getCpoId());
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
        $this->setIdCat($dados['idCat']);
        $this->setTipo($dados['tipo']);
        $this->setIcone($dados['icone']);
        $this->setLabel($dados['label']);
        $this->setIdStatus($dados['idStatus']);
        $this->setIdEmpresa($dados['idEmpresa']);
        ;
    }

    /**
     * Metodo para pegar todos os parametros da entidade em forma de array
     * @return Array
     * */
    public function getDados() {
        $out['idCat'] = $this->getIdCat();
        $out['tipo'] = $this->getTipo();
        $out['icone'] = $this->getIcone();
        $out['label'] = $this->getLabel();
        $out['idStatus'] = $this->getIdStatus();
        $out['idEmpresa'] = $this->getIdEmpresa();
        return $out;
    }

    /* Metodos obrigatório pois EntityManager depende deles */

    public function getId() {
        return $this->idCat;
    }

    public function setId($id) {
        if ($this->idCat == '') {
            $this->idCat = (int) $id;
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

    /** @type smallint(4) unsigned notnull */
    private function setIdCat($idCat) {
        $this->idCat = (int) $idCat;
    }

    public function getIdCat() {
        return $this->idCat;
    }

    /** @type enum('1','2') notnull */
    public function setTipo($tipo) {
        if ($tipo == "") {
            $this->error['tipo'] = "Tipo é obrigatório";
        } else {
            unset($this->error['tipo']);
        }
        $this->tipo = (string) $tipo;
    }

    public function getTipo() {
        return $this->tipo;
    }

    /** @type varchar(8) */
    public function setIcone($icone) {
        $this->icone = (string) $icone;
    }

    public function getIcone() {
        return $this->icone;
    }

    /** @type varchar(45) notnull */
    public function setLabel($label) {
        if ($label == "") {
            $this->error['label'] = "Nome é obrigatório";
        } else {
            unset($this->error['label']);
        }
        $this->label = (string) $label;
    }

    public function getLabel() {
        return $this->label;
    }

    /** @type smallint(3) unsigned notnull */
    public function setIdStatus($idStatus) {
        if ($idStatus == "") {
            $this->error['idStatus'] = "Status é obrigatório";
        } else {
            unset($this->error['idStatus']);
        }
        $this->idStatus = (int) $idStatus;
    }

    public function getIdStatus() {
        return $this->idStatus;
    }

    /** @type smallint(2) unsigned notnull */
    public function setIdEmpresa($idEmpresa) {
        if ($idEmpresa == "") {
            $this->error['idEmpresa'] = "idEmpresa é obrigatório";
        } else {
            unset($this->error['idEmpresa']);
        }
        $this->idEmpresa = (int) $idEmpresa;
    }

    public function getIdEmpresa() {
        return $this->idEmpresa;
    }

}

// fecha classe