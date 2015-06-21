<?php

require_once 'User.php';

class Empresa {

    private $idEmpresa;
    private $data;
    private $nome;
    private $responsavel;
    private $email;
    private $logo;

    public function Empresa($idEmpresa = false, $dados = false) {
        $this->idEmpresa = $idEmpresa;
        if ($idEmpresa) {
            $acc = new RegNegocios();
            $acc->executeQuery("SELECT * FROM anz_empresa WHERE idEmpresa= " . $this->idEmpresa);
            if ($acc->numRows == 0) {
                return false;
            }
            $acc->proxReg();
            foreach ($acc->dd as $key => $val) {
                $this->$key = utf8_encode($val);
            }
        } else {
            $this->setDados($dados);
        }
    }

    private function setDados($dados) {
        $this->idEmpresa = (int) $dados['idEmpresa'];
        $this->setNome($dados['nome']);
        $this->setResponsavel($dados['responsavel']);
        $this->setEmail($dados['email']);
        $this->setLogo($dados['logo']);
    }

    // metodo para persistencia
    public function save() {
        $acc = new RegNegocios();
        if ($this->idEmpresa) { // registro já existe, atualizar.
            $query = "UPDATE anz_empresa SET nome='" . $this->getNome() . "', responsavel='" . $this->getResponsavel() . "', "
                    . "email='" . $this->getEmail() . "', logo='" . $this->getLogo() . "' WHERE  idEmpresa= " . $this->getIdEmpresa();
        } else { // registro não existe, criar
            $query = "INSERT INTO anz_empresa (data, nome, responsavel, email, logo) VALUES (now(), '" . $this->getNome() . "', '" . $this->getResponsavel() . "', "
                    . "'" . $this->getEmail() . "', '" . $this->getLogo() . "')";
        }
        $acc->executeQuery($query);
        if ($acc->getLastIdInsert()) {
            $this->idEmpresa = $acc->getLastIdInsert();
        }
    }

    public function remove() {
        $acc = new RegNegocios();
        $acc->executeQuery("DELETE FROM anz_empresa WHERE idEmpresa= " . $this->getIdEmpresa());
        $acc->executeQuery("DELETE FROM anz_empresa WHERE idEmpresa= " . $this->getIdEmpresa());
        $this->setDados(array());
    }

    // metodo para listar os usuarios desta entidade
    public function getUsers() {
        $acc = new RegNegocios();
        $acc->executeQuery("SELECT idUser FROM anz_user WHERE idEmpresa= " . $this->idEmpresa);
        $out = array();
        while ($acc->proxReg()) {
            $out[] = new User($acc->dd['idUser']);
        }
        return $out;
    }

    public static function getRelacaoEmpresas() {
        $acc = new RegNegocios();
        $acc->executeQuery("SELECT idEmpresa FROM anz_empresa ORDER BY nome ASC");
        $empresas = array();
        while ($acc->proxReg()) {
            $empresas[] = new Empresa($acc->dd['idEmpresa']);
        }
        return $empresas;
    }

    public function getIdEmpresa() {
        return $this->idEmpresa;
    }

    public function getData() {
        return $this->data;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getResponsavel() {
        return $this->responsavel;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getLogo() {
        return $this->logo;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setResponsavel($responsavel) {
        $this->responsavel = $responsavel;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setLogo($logo) {
        $this->logo = $logo;
    }

}
