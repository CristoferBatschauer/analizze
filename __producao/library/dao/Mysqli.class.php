<?php

class Mysqli implements InterfaceDatabase {

    private $con;
    public $query;
    public $result;
    public $numRows;
    public $error;
    public $dd;
    public $lastInsertId;

    public function __construct() {
        $this->open();
    }

    private function open() {
        $this->con = new mysqli(Config::getData('mysql', 'host'), Config::getData('mysql', 'user'), Config::getData('mysql', 'pwd'), Config::getData('mysql', 'database'));
        $this->autocommit(true);
    }

    public function close() {
        $this->con->close();
    }

    public function autocommit($var) {
        $this->con->autocommit($var);
    }

    public function commit() {
        $this->con->commit();
    }

    public function rollback() {
        $this->con->rollback();
    }

    public function executeQuery($query, $gravarLog = true) {
        $this->open();
        $this->numRows = 0;
        $this->result = false;
        $tipo = explode(" ", $query);
        RegNegocios::gravaLog($tipo[0], addslashes($query));
        try {
            $this->result = $this->con->query($query);
            if ($tipo[0] == "SELECT") {
                $this->numRows = $this->result->num_rows;
            } else {
                $this->numRows = $this->result->affected_rows;
                $this->lastInsertId = $this->con->insert_id;
            }
            $this->error = $this->con->error;
        } catch (Exception $exc) {
            
        }

        return $this->result;
    }

    public function next() {
        try {
            $dados = $this->result->fetch_array(MYSQLI_ASSOC);
            if (is_array($dados)) {
                return $dados;
            } else {
                $this->result->close();
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}

/*
 * Teste:
$sql = new Conexao();
$sql->executeQuery("SELECT * FROM anz_lancamentos");
while ($dados = $sql->next())   {
    print_r($dados);
}
 * 
 */