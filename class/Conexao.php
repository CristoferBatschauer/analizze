<?php

require_once 'RegNegocios.php';

class Conexao {

    private $mysqli;
    public $query;
    public $result;
    public $numRows;
    public $error;
    public $dd;
    public $lastInsertId;

    public function Conexao() {
        $this->open();
    }


    private function open() {
        $this->mysqli = new mysqli($GLOBALS["HOST"], $GLOBALS["USER"], $GLOBALS["PW"], $GLOBALS["DBNOME"]);
        $this->autocommit(true);
    }

    public function close() {
        $this->mysqli->close();
    }

    public function autocommit($var) {
        $this->mysqli->autocommit($var);
    }
    
    public function commit()   {
        $this->mysqli->commit();
    }
    
    public function rollback()   {
        $this->mysqli->rollback();
    }

    public function executeQuery($query, $gravarLog = true) {
        $this->open();
        $this->numRows = 0;
        $this->result = false;
        $tipo = explode(" ", $query);
        RegNegocios::gravaLog($tipo[0], addslashes($query));
        try {
            $this->result = $this->mysqli->query($query);
            RegNegocios::gravaLog($tipo[0], "RODOU");
            if ($tipo[0] == "SELECT") {
                $this->numRows = $this->result->num_rows;
            } else {
                $this->numRows = $this->result->affected_rows;
                $this->lastInsertId = $this->mysqli->insert_id;
            }
            $this->error = $this->mysqli->error;
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