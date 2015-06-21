
<?php

/**
 * @date 15-02-13
 * @author Cristofer
 */
class EmpresaController extends AbstractController {

    private $object;
    private $con;
    private $out;

    public function __construct($object) {
        if ($object instanceof Empresa) {
            $this->object = $object;
            $this->con = new MySql("anz_empresa");
            $this->con->setOrder("idEmpresa");
            $action = $_GET["action"];
            $dados = $_POST["dados"];
            if (method_exists($this, $action)) {
                $this->$action($dados);
            }
        } else {
            Log::error(__METHOD__ . "Objeto enviado não é do tipo permitido para esta classe");
            die("Tipo incorreto");
        }
    }

    /* @overwrite */

    public function getForm() {
        $this->out = __METHOD__;
    }

    /* @overwrite */

    public function getList() {
        foreach ($this->con->getList() as $val) {
            $entities[] = new Empresa($val['idEmpresa']);
        }
        return $entities;
    }

    /* @overwrite */

    public function getOut() {
        return $this->out;
    }

    private function setOut($var) {
        $this->out = $var;
    }

    public function save() {
        $this->object->save();
        $this->setOut(implode("<br>", $this->object->getError()));
    }

    /** Criar os métodos do controller daqui pra baixo * */
    public function getUsers() {
        $acc = $this->con;
        $acc->executeQuery("SELECT idUser FROM anz_user WHERE idEmpresa= " . $this->object->getIdEmpresa());
        $out = array();
        while ($acc->proxReg()) {
            $dd = $acc->getDD();
            $out[] = new User($dd['idUser']);
        }
        return $out;
    }

}
