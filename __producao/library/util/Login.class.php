<?php

class Login  {

    public function __construct() {
        Log::debug("Criado Objeto Login");
    }
    
    /**
     * Método para efetuar o login. Buscar no banco. Caso encontre mais de uma empresa, solicitar escolha da empresa.
     * @see RegNegocios::login()
     */
    public function logar($login, $senha, $idEmpresa = false) {
        RegNegocios::gravaLog("login", "User::login($login)");
        //$senha = self::codificaSenha($senha);
        $acc = new MySql();
        $acc->executeQuery("SELECT u.*, e.nome as nomeEmpresa, e.logo FROM anz_user u	
				INNER JOIN anz_empresa e ON e.idEmpresa=u.idEmpresa
				WHERE login= '$login' AND senha= '" . self::codificaSenha($senha) . "'" . (($idEmpresa) ? " AND u.idEmpresa= $idEmpresa" : ""));
        $out['result'] = $acc->getNumRows();
        if ($acc->getNumRows() > 0) {
            if ($acc->getNumRows() > 1) { // tratamento para mais de uma empresa para mesmo login
                $out['result'] = 99;
                $out['empresas'] = '<div data-role="fieldcontain">
                                                <fieldset data-role="controlgroup">
                                                <legend>Escolha qual empresa deseja operar:</legend><br />';
                $i = 0;
                $out['login'] = $login;
                $out['pass'] = $senha;
                while ($acc->proxReg()) {
                    $empresa = $acc->getDD();
                    $out['empresas'] .= '<input type="radio" name="idEmpresa" id="empresa_' . $i . '" value="' . $empresa['idEmpresa'] . '" 
                                        onclick="javascript:document.getElementById(\'userLogin\').submit();" />
                                        <label for="empresa_' . $i . '">' . $empresa['nomeEmpresa'] . '</label>';
                    $i++;
                }
                $out['empresas'] .= '</fieldset></div>';
                return $out;
            }
            $acc->proxReg();
            $dados = $acc->getDD();
            $user = new User($dados['idUser']);
            if (self::codificaSenha($senha) != $dados['senha']) {
                RegNegocios::gravalog("LoginSenhaErrada", "SenhaBD: " . $this->dd['senha'] . "  ||  Senha enviada: " . RegNegocios::codifica($senha));
                return $out;
            }
        } else {
            return $out;
        }

        // s� segue para um registro valido com senha conferida
        $out['result'] = 1;
        $out['user'] = $user;
        $out['userNome'] = ucwords(strtolower($user->getNome()));
        $out['userLogin'] = strtolower($user->getLogin());
        $out['idUser'] = $user->getIdUser();
        $out['userUltAcesso'] = $user->getUltAcesso();
        $out['idEmpresa'] = $user->getIdEmpresa();
        $out['nomeEmpresa'] = $dados['nomeEmpresa'];
        $out['diaInicioContabil'] = $user->getDiaInicioContabil();
        $out['logo'] = (($dados['logo'] != '') ? $dados['logo'] : 'logo.jpg');
        $acc->executeQuery("UPDATE anz_user SET ultAcesso= now() WHERE login= '$login'");
        self::loginSetaCookies($out);
        return $out;
    }

    public function logoff() {
        RegNegocios::gravaLog("logoff", "Login: " . $_SESSION['userLogin']);
        foreach ($_SESSION as $key => $val) {
            unset($_SESSION[$key]);
        }
        //$this->loginSetaCookies();
    }

    public function loginSetaCookies($var = false) {
        foreach ($var as $key => $val) {
            $_SESSION[$key] = $val;
        }
    }

    /**
     * Metodo que codifica senha com token e criptografia
     * Constante TOKEN definida em _config.php
     * @param String $senha
     */
    private static function codificaSenha($senha) {
        return User::codificaSenha($senha);
    }

    private function geraNovaSenha() {
        return ( substr(md5(microtime()), 0, 8) );
    }

    /**
     * M�todo para reenvio de senha. Esqueci minha senha.
     * @param string $login
     * @param int $idEmpresa
     */
    public function reenviaPw($login, $idEmpresa) {
        $acc = new MySql();
        $acc->executeQuery("SELECT u.*, e.nome as nomeEmpresa, e.email FROM anz_user u
				INNER JOIN anz_empresa e ON e.idEmpresa=u.idEmpresa
				WHERE login= '$login' AND idEmpresa= '$idEmpresa' ");
        if ($acc->getNumRows() == 0) {
            return -1;
        }
        $acc->proxReg();
        $ddUser = $acc->getDD();
        $novaSenha = self::geraNovaSenha();
        $acc->executeQuery("UPDATE anz_user SET senha='" . self::codificaSenha($novaSenha) . "' WHERE idUser= " . $ddUser['idUser']);
        if (mail($ddUser['email'], 'Analizze - Redefinação de Senha', "Recebemos solicitação de nova senha. Utilize para acessar a senha: '$novaSenha'")) {
            return true;
        } else {
            return false;
        }
    }

    public function atualizaSenha($idUser, $novaSenha) {
        $user = new User($idUser);
        $user->setSenha($novaSenha);
        $user->save();
        if (!$user->getErro()) {
            return true;
        } else {
            return $user->getErro();
        }
    }

}