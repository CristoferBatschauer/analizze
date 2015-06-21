<?php
require_once 'Conexao.php';

class Teste {

    public $con;

    public function __construct() {
        $this->con = new Conexao();
    }

    public function qquma() {
        $this->con->executeQuery("SELECT * FROM anz_contas");
        while ($dd = $this->con->next())   {
            var_dump($dd);
        }
        //var_dump($this->con);
    }

}
/*

$var = new Teste();

$var->qquma();

*/