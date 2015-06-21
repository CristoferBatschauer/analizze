<?php

require_once 'RegNegocios.php';
require_once 'Conexao.php';

class User {

    public $con;
    public $nome;

    public function __construct() {
        $this->con = new Conexao();
    }

    /**
     * M�todo para efetuar o login. Buscar no banco. Caso encontre mais de uma empresa, solicitar escolha da empresa.
     * @see RegNegocios::login()
     */
    function login($login, $senha) {
        $this->con = new Conexao();
        RegNegocios::gravaLog("login", "User::login($login), senha($senha)");
        //$senha = self::codificaSenha($senha);
        $out['result'] = 0;
        $this->con->executeQuery("SELECT u.*, e.nome as nomeEmpresa, e.logo FROM anz_user u INNER JOIN anz_empresa e ON e.idEmpresa=u.idEmpresa	WHERE login= '$login' ");
        if ($this->con->numRows > 0) {
            RegNegocios::gravaLog("login", "Correto");
            $dd = $this->con->next();
            if ($this->con->numRows > 1) { // tratamento para mais de uma empresa para mesmo login
                $out['result'] = 99;
                return $out;
            }
            if ($dd['senha'] != $this->codificaSenha($senha)) {
                RegNegocios::gravalog("LoginSenhaErrada", "SenhaBD: " . $dd['senha'] . "  ||  Senha enviada: $senha");
                $out['result'] = -1;
                return $out;
            }
        } else {
            return $out;
        }

        // s� segue para um registro valido com senha conferida
        $out['result'] = 1;
        $out['userNome'] = ucwords(strtolower($dd['nome']));
        $out['userLogin'] = strtolower($dd['login']);
        $out['idUser'] = $dd['idUser'];
        $out['userUltAcesso'] = $dd['ultAcesso'];
        $out['idEmpresa'] = $dd['idEmpresa'];
        $out['nomeEmpresa'] = $dd['nomeEmpresa'];
        $out['diaInicioContabil'] = $dd['diaInicioContabil'];
        $out['mesesFuturos'] = 0; // Zero significa apenas um mês, ou padrão.
        $out['logo'] = (($dd['logo'] != '') ? $dd['logo'] : 'logo.jpg');
        $this->con->executeQuery("UPDATE anz_user SET ultAcesso= now() WHERE login= '$login'");
        $this->loginSetaCookies($out);
        return $out;
    }

    function logoff() {
        RegNegocios::gravaLog("logoff", "Login: " . $_SESSION['userLogin']);
        foreach ($_SESSION as $key => $val) {
            unset($_SESSION[$key]);
        }
        //$this->loginSetaCookies();
    }

    function loginSetaCookies($var = false) {
        RegNegocios::gravaLog("LoginCorreto", var_dump($var, true));
        foreach ($var as $key => $val) {
            $_SESSION[$key] = $val;
        }
    }

    /**
     * Metodo que codifica senha com token e criptografia
     * Constante TOKEN definida em _config.php
     * @param unknown_type $senha
     */
    public function codificaSenha($senha) {
        return md5($senha);
    }

    private function geraNovaSenha() {
        return ( substr(md5(microtime()), 0, 8) );
    }

    /**
     * M�todo para reenvio de senha. Esqueci minha senha.
     * @param string $login
     * @param int $idEmpresa
     */
    public function reenviaPw($login, $idEmpresa = false) {
        // buscar empresa referente login
        // se encontrar mais de um login
        $this->con->executeQuery("SELECT u.*, e.nome as nomeEmpresa, e.email FROM anz_user u
				INNER JOIN anz_empresa e ON e.idEmpresa=u.idEmpresa
				WHERE login= '$login' ");
        if ($this->con->numRows == 0) {
            return -1;
        }
        $dd = $this->con->next();
        $ddUser = $dd;
        $novaSenha = self::geraNovaSenha();
        $this->con->executeQuery("UPDATE anz_user SET senha='" . self::codificaSenha($novaSenha) . "' WHERE idUser= " . $ddUser['idUser']);
        if (mail($ddUser['email'], 'Analizze - Redefina��o de Senha', "Recebemos solicita��o de nova senha. Utilize para acessar a senha: '$novaSenha'")) {
            return true;
        } else {
            return false;
        }
    }

    public function atualizaSenha($idUser, $novaSenha) {
        $this->con->executeQuery("UPDATE anz_user SET senha='" . self::codificaSenha($novaSenha) . "' WHERE idUser= " . $idUser);
        return true;
    }

}
