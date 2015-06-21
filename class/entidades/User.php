<?php

class User {

    private $idUser;
    private $nome;
    private $login;
    private $senha;
    private $idEmpresa;
    private $ultAcesso;
    private $diaInicioContabil;

    public function User($idUser = false, $dados = false) {
        $this->idUser = $idUser;
        if ($idUser) {
            $acc = new RegNegocios();

            $acc->executeQuery("SELECT * FROM anz_user WHERE idUser= " . $this->idUser);
            if ($acc->numRows == 0) {
                return false;
            }
            $acc->proxReg();
            
            foreach ($acc->dd as $key => $val) {
                $this->$key = $val;//utf8_encode($val);
            }
            
        } else {
            $this->setDados($dados);
        }
    }

    private function setDados($dados) {
        $this->idUser = (int) $dados['idUser'];
        $this->setNome($dados['nome']);
        $this->setLogin($dados['login']);
        $this->setSenha($dados['senha']);
        $this->setIdEmpresa($dados['idEmpresa']);
        $this->setDiaInicioContabil($dados['diaInicioContabil']);
    }

    // metodo para persistencia
    public function save() {
        $acc = new RegNegocios();
        if ($this->idUser) {
            $query = "UPDATE anz_user SET nome='" . $this->getNome() . "', login='" . $this->getLogin() . "', senha='" . $this->getSenha() . "', idEmpresa='" . $this->getIdEmpresa() . "', "
                    . "diaInicioContabil='" . $this->getDiaInicioContabil() . "' WHERE idUser= " . $this->getIdUser();
        } else {
            $query = "INSERT INTO anz_user (nome, login, senha, idEmpresa, diaInicioContabil) VALUES ('" . $this->getNome() . "', '" . $this->getLogin() . "', '" . $this->getSenha() . "', "
                    . "'" . $this->getIdEmpresa() . "', '" . $this->getDiaInicioContabil() . "')";
        }
        $acc->executeQuery($query);
        if ($acc->getLastIdInsert()) {
            $this->idUser = $acc->getLastIdInsert();
        }
    }
    
    public function remove()   {
        $acc = new RegNegocios();
        $acc->executeQuery("DELETE FROM anz_user WHERE idUser= " . $this->getIdUser());
        $this->setDados(array());
    }

    // getters e setters

    public function getIdUser() {
        return $this->idUser;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function getIdEmpresa() {
        return $this->idEmpresa;
    }

    public function getUltAcesso() {
        return $this->ultAcesso;
    }

    public function getDiaInicioContabil() {
        return $this->diaInicioContabil;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setLogin($login) {
        $this->login = $login;
    }

    public function setSenha($senha) {
        $this->senha = md5($senha);
    }

    public function setIdEmpresa($idEmpresa) {
        $this->idEmpresa = $idEmpresa;
    }

    public function setUltAcesso($ultAcesso) {
        $this->ultAcesso = $ultAcesso;
    }

    public function setDiaInicioContabil($diaInicioContabil) {
        $this->diaInicioContabil = $diaInicioContabil;
    }

}
