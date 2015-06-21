<?php

class Login extends RegNegocios {

    public function __construct() {
        
    }

    /**
     * M�todo para efetuar o login. Buscar no banco. Caso encontre mais de uma empresa, solicitar escolha da empresa.
     * @see RegNegocios::login()
     */
    function logar($login, $senha, $idEmpresa = false) {
        RegNegocios::gravaLog("login", "User::login($login), senha($senha)");
        //$senha = self::codificaSenha($senha);
        $out['result'] = 0;
        $this->executeQuery("SELECT u.*, e.nome as nomeEmpresa, e.logo FROM anz_user u	
				INNER JOIN anz_empresa e ON e.idEmpresa=u.idEmpresa
				WHERE login= '$login' AND senha= '" . self::codificaSenha($senha) . "'" . (($idEmpresa) ? " AND u.idEmpresa= $idEmpresa" : ""));
        if ($this->numRows > 0) {
            if ($this->numRows > 1) { // tratamento para mais de uma empresa para mesmo login
                $out['result'] = 99;
                $out['empresas'] = '<div data-role="fieldcontain">
                                                <fieldset data-role="controlgroup">
                                                <legend>Escolha qual empresa deseja operar:</legend><br />';
                $i = 0;
                $out['login'] = $login;
                $out['pass'] = $senha;
                while ($this->proxReg()) {
                    $out['empresas'] .= '<input type="radio" name="idEmpresa" id="empresa_' . $i . '" value="' . $this->dd['idEmpresa'] . '" 
                                        onclick="javascript:document.getElementById(\'userLogin\').submit();" />
                                        <label for="empresa_' . $i . '">' . $this->dd['nomeEmpresa'] . '</label>';
                    $i++;
                }
                $out['empresas'] .= '</fieldset></div>';
                return $out;
            }
            $this->proxReg();
            if (self::codificaSenha($senha) != $this->dd['senha']) {
                RegNegocios::gravalog("LoginSenhaErrada", "SenhaBD: " . $this->dd['senha'] . "  ||  Senha enviada: " . RegNegocios::codifica($senha));
                return $out;
            }
        } else {
            return $out;
        }

        // s� segue para um registro valido com senha conferida
        $out['result'] = 1;
        $out['userNome'] = ucwords(strtolower($this->dd['nome']));
        $out['userLogin'] = strtolower($this->dd['login']);
        $out['idUser'] = $this->dd['idUser'];
        $out['userUltAcesso'] = $this->dd['ultAcesso'];
        $out['idEmpresa'] = $this->dd['idEmpresa'];
        $out['nomeEmpresa'] = $this->dd['nomeEmpresa'];
        $out['diaInicioContabil'] = $this->dd['diaInicioContabil'];
        $out['logo'] = (($this->dd['logo'] != '') ? $this->dd['logo'] : 'logo.jpg');
        $this->executeQuery("UPDATE anz_user SET ultAcesso= now() WHERE login= '$login'");
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
        foreach ($var as $key => $val) {
            $_SESSION[$key] = $val;
        }
    }

    /**
     * Metodo que codifica senha com token e criptografia
     * Constante TOKEN definida em _config.php
     * @param unknown_type $senha
     */
    private static function codificaSenha($senha) {
        return md5($senha . TOKEN);
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
        $this->executeQuery("SELECT u.*, e.nome as nomeEmpresa, e.email FROM anz_user u
				INNER JOIN anz_empresa e ON e.idEmpresa=u.idEmpresa
				WHERE login= '$login' ");
        if ($this->numRows == 0)
            return -1;
        $this->proxReg();
        $ddUser = $this->dd;
        $novaSenha = self::geraNovaSenha();
        $this->executeQuery("UPDATE anz_user SET senha='" . self::codificaSenha($novaSenha) . "' WHERE idUser= " . $ddUser['idUser']);
        if (mail($ddUser['email'], 'Analizze - Redefina��o de Senha', "Recebemos solicita��o de nova senha. Utilize para acessar a senha: '$novaSenha'"))
            return true;
        else
            return false;
    }

    public function atualizaSenha($idUser, $novaSenha) {
        $this->executeQuery("UPDATE anz_user SET senha='" . self::codificaSenha($novaSenha) . "' WHERE idUser= " . $idUser);
        return true;
    }

}

?>