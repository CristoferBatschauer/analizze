<?php

/**
 * TODO Auto-generated comment.
 */
class MySql implements InterfaceDatabase{

    private $idRegistro = false;
    private $table = '';
    private $tableStruct;
    private $idtLink = false;
    private $idtQuery = 0;
    private $dd = array();
    private $order = "id ASC";
    private $numRows = 0;
    private $query = "";
    private $atributos;
    private $cposDouble = array();
    private $cposDate = array();
    private $cposDateTime = array();
    private $cposNotNull = array();
    private $lastId = false;

    // construtor
    public function __construct($tableName = '') {
        $this->conecta();
        $this->setTable($tableName);
        Config::init();
        Log::init();
    }

    // conexão
    public function conecta() {
        try {
            if ($this->idtLink == 0) {
                $this->idtLink = mysql_connect(Config::getData('mysql', 'host'), Config::getData('mysql', 'user'), Config::getData('mysql', 'pwd'));
                if (!$this->idtLink)
                    Log::error(__CLASS__ . __FUNCTION__ . 'Erro de conexão');
                //throw new AnalizzeException("Mysql: Erro na conexão com banco de dados", 5);

                if (!mysql_query(sprintf("use %s", Config::getData('mysql', 'database')), $this->idtLink))
                    Log::error(__CLASS__ . __FUNCTION__ . 'Erro de conexão');
                //throw new AnalizzeException("Mysql: Erro na sele��o do banco de dados", 5);
            }
        } catch (AnalizzeException $e) {
            Log::error(__CLASS__ . __FUNCTION__ . $e->getMessage());
        } catch (Exception $e) {
            Log::error(__CLASS__ . __FUNCTION__ . $e->getMessage());
        }
    }

    /**
     * Metodo para inserir dados em determinanda tabela
     */
    public function add($dados) {
        
    }

    /**
     * Metodo responsavel pela leitura de uma tabela toda ou de um registro unico.
     */
    public function read($table, $id = false) {
        $this->conecta();
        if (!isset($table)) {
            Log::error(__METHOD__ . ':' . __LINE__);
            //throw new AnalizzeException("MySql::Read - Table não definido", 5);
        }
        $this->setTable($table);
        if ($id !== false) {
            $this->setQuery("SELECT * FROM " . $table . " WHERE id= $id");
        } else {
            $this->setQuery("SELECT * FROM " . $table . " ORDER BY " . $this->order);
        }
        $this->executeQuery();
    }

    /**
     * Método responsável pela exclusão de um registro.
     */
    public function delete($id, $nomeCampo = "id") {
        if ($this->getTable() == "") {
            Log::error("MySql:Delete - Table não definido");
        }
        $this->setQuery("DELETE FROM " . $this->getTable() . " WHERE $nomeCampo= $id");
        return ($this->executeQuery());
    }

    /**
     * TODO Auto-generated comment.
     */
    public function executeQuery($query = false) {
        $this->conecta();
        if (($this->query == "") && (!$query)) {
            Log::error(__METHOD__ . ':{linha ' . __LINE__ .  '} Query não definida ' . $this->query);
            throw new AnalizzeException("MySql: String Query não definida", 5);
        }
        if ($query) {
            $this->query = $query;
        }

        $this->idtQuery = mysql_query($this->query, $this->idtLink);

        if (!$this->idtQuery) {
            $this->conecta();
            Log::error(__METHOD__ . ':{linha ' . __LINE__ . '} Query não executada: ' . $this->query);
            return 'herrr .  mysql_query(' . $this->query . ', ' . $this->idtLink . ');';
        }

// last id
        $t = explode(" ", $this->query);
        if (strtoupper($t[0]) == "INSERT") {
            $this->lastId = mysql_insert_id($this->idtLink);
            Log::debug('Query:' . $this->query);
        } else if (strtoupper($t[0] == "SELECT")) {
            $this->numRows = mysql_num_rows($this->idtQuery);
            //Log::debug('Query:' . $this->query);
        }
        return true;
    }

    /**
     * TODO Auto-generated comment.
     */
    public function proxReg() {
        if ($this->idtQuery == false) {
            throw new AnalizzeException("Mysql: idtQuery não definido", 5);
        }
        $this->dd = mysql_fetch_assoc($this->idtQuery);
        $FimDados = is_array($this->dd);
        if (!$FimDados) {
            mysql_free_result($this->idtQuery);
            $this->idtQuery = 0;
        }
        return $FimDados;
    }

    /**
     * TODO Auto-generated comment.
     */
    public function getTableStruct() {
        $this->conecta();
        $this->cposDouble = array();
        $this->cposDate = array();
        $this->cposDateTime = array();
        $this->cposNotNull = array();
        $this->dd = array();
        $this->atributos = array();
        $result = mysql_list_fields(Config::getData('mysql', 'banco'), $this->getTable(), $this->idtLink);

        for ($i = 0; $i < mysql_num_fields($result); $i ++) {
            $nomeCpo = $this->dd[] = mysql_field_name($result, $i);
            $notnull = false;
            // notnull
            $flags = mysql_field_flags($result, $i);
            $temp = explode(" ", $flags);
            foreach ($temp as $key => $val) {
                if ($val == 'not_null') {
                    $this->cposNotNull [] = $nomeCpo;
                    $notnull = true;
                }
            }
            // tipo do campo
            $tipo = mysql_field_type($result, $i);
            if ($tipo == 'real')
                $this->cposDouble [] = $nomeCpo;
            if ($tipo == 'date')
                $this->cposDate [] = $nomeCpo;
            if ($tipo == 'datetime')
                $this->cposDateTime [] = $nomeCpo;

            $tamanho = mysql_field_len($result, $i);
            $this->atributos[$nomeCpo] = array("tamanho" => $tamanho, "tipo" => $tipo, "notnull" => $notnull);
        }
    }

    /**
     * TODO Auto-generated comment.
     */
    public function getAtributos() {
        return $this->atributos;
    }

    /**
     * 
     * @return type String
     * Retorna o nome da tabela setada
     */
    public function getTable() {
        return $this->tableName;
    }

    public function setTable($tableName) {
        $this->tableName = $tableName;
    }

    /**
     * TODO Auto-generated comment.
     */
    public function setQuery($query) {
        Log::debug(__METHOD__ . ' Query: ' . $query);
        $this->query = $query;
    }

    /**
     * TODO Auto-generated comment.
     */
    public function getLastId() {
        return $this->lastId;
    }

    /**
     * TODO Auto-generated comment.
     */
    public function getCposDouble() {
        return $this->cposDouble;
    }

    /**
     * TODO Auto-generated comment.
     */
    public function getCposDate() {
        return $this->cposDate;
    }

    /**
     * TODO Auto-generated comment.
     */
    public function getCposDatetime() {
        return $this->cposDateTime;
    }

    /**
     * TODO Auto-generated comment.
     */
    public function getCposNotNull() {
        return $this->cposNotNull;
    }

    public function getDD() {
        return $this->dd;
    }

    public function getNumRows() {
        return $this->numRows;
    }

    public function setNumRows($numRows) {
        $this->numRows = $numRows;
    }

    public function setOrder($order) {
        $this->order = $order;
    }

    public function teste() {
        return __METHOD__;
    }

    public function autocommit($var) {
        
    }

    public function close() {
        
    }

    public function commit() {
        
    }

    public function next() {
        
    }

    public function open() {
        
    }

    public function rollback() {
        
    }

}
