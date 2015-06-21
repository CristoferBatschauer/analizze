<?php

class User {

    private $idUser;
    private $nome;
    private $login;
    private $senha;
    private $idEmpresa;
    private $ultAcesso;
    private $diaInicioContabil;
    private $erro;
    private $senhaBancoDados;

    public function User($idUser = false, $dados = array()) {
        $this->idUser = $idUser;
        $this->erro = array();
        if ($idUser) {
            $acc = new MySql();
            $acc->executeQuery("SELECT * FROM anz_user WHERE idUser= " . $this->idUser);
            if ($acc->getNumRows() == 0) {
                return false;
            }
            $acc->proxReg();
            $dados = $acc->getDD();
            //$dados['senha'] = RegNegocios::decodifica($dados['senha']);
            $dados['senhaBancoDados'] = $dados['senha'];
        }
        $this->setDados($dados);
    }

    private function setDados($dados) {
        $this->idUser = (int) $dados['idUser'];
        $this->setNome($dados['nome']);
        $this->setLogin($dados['login']);
        $this->setIdEmpresa($dados['idEmpresa']);
        $this->setDiaInicioContabil($dados['diaInicioContabil']);
        $this->setSenhaBancoDados($dados['senhaBancoDados']);
        $this->setSenha($dados['senha']);
    }

    // metodo para persistencia
    public function save() {
        $acc = new MySql();
        if ($this->getErro()) {
            return false;
        }
        if ($this->idUser) {
            if ($this->getSenha() != '') {
                $senha = "senha='" . $this->getSenha() . "', ";
            }
            $query = "UPDATE anz_user SET " . $senha . " nome='" . $this->getNome() . "', login='" . $this->getLogin() . "', idEmpresa='" . $this->getIdEmpresa() . "', "
                    . "diaInicioContabil='" . $this->getDiaInicioContabil() . "' WHERE idUser= " . $this->getIdUser();
        } else {
            $query = "INSERT INTO anz_user (nome, login, senha, idEmpresa, diaInicioContabil) VALUES ('" . $this->getNome() . "', '" . $this->getLogin() . "', '" . $this->getSenha() . "', "
                    . "'" . $this->getIdEmpresa() . "', '" . $this->getDiaInicioContabil() . "')";
        }
        $acc->executeQuery($query);
        if ($acc->getLastId()) {
            $this->idUser = $acc->getLastId();
        }
        return true;
    }

    public function remove() {
        $acc = new MySql();
        $acc->executeQuery("DELETE FROM anz_user WHERE idUser= " . $this->getIdUser());
        $this->setDados(array());
    }
    
        /**
     * Metodo que codifica senha com token e criptografia
     * Constante TOKEN definida em _config.php
     * @param String $senha
     */
    public static function codificaSenha($senha) {
        return md5($senha.  Config::getData('credenciais', 'token'));
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

    public function getErro() {
        return $this->erro;
    }

    public function setNome($nome) {
        if ($nome == '')   $this->erro[] = "Nome é obrigatório";
        $this->nome = $nome;
    }

    public function setLogin($login) {
        if ($login == ''){
            $this->erro[] = 'Email é obrigatório';
        }
        if (!RegNegocios::validaEmail($login))  {
            $this->erro[] = 'Email inválido';
        }
        $this->login = $login;
    }

    public function setSenha($senha) {
        if ($this->getSenhaBancoDados() == $senha)   { // não houve alteração
            $this->senha = $senha;
        }
        $forcaSenha = RegNegocios::forcaSenha($senha);
        if ($forcaSenha < 3) {
            $this->senha = false;
            $this->erro[] = "Senha inválida. Necessário utilizar números, letras e caratcteres especiais (!@#)";
        } else {
            $this->senha = self::codificaSenha($senha);
        }
    }

    public function setIdEmpresa($idEmpresa) {
        $this->idEmpresa = $idEmpresa;
    }

    public function setUltAcesso($ultAcesso) {
        $this->ultAcesso = $ultAcesso;
    }

    public function setDiaInicioContabil($diaInicioContabil) {
        if ($diaInicioContabil == '')   $diaInicioContabil = 1;
        $this->diaInicioContabil = $diaInicioContabil;
    }

    public function getSenhaBancoDados() {
        return $this->senhaBancoDados;
    }

    public function setSenhaBancoDados($senhaBancoDados) {
        $this->senhaBancoDados = $senhaBancoDados;
    }

}
