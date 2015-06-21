
<?php

/**
 * @date 15-02-13
 * @author Cristofer
 */
class LancamentosController extends AbstractController {

    private $object;
    private $con;
    private $out;
    private $dataLimite;
    private $dataLimiteMktime;

    public function __construct($object) {
        if ($object instanceof Lancamentos) {
            $this->object = $object;
            $this->con = new MySql("anz_lancamentos");
            $this->con->setOrder("idLancamento");
            $action = $_GET["action"];
            $dados = $_POST["dados"];
            if (method_exists($this, $action)) {
                $this->$action($dados);
            }
            // data limite
            if (isset($_SESSION['diaInicioContabil'])) {
                $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $this->dataLimite = mktime(0, 0, 0, date('m'), $_SESSION['diaInicioContabil'], date('Y'));
                if (($this->dataLimite - $hoje) <= 0) { // data limite � do pr�ximo m�s
                    $this->dataLimite = $dataLimite = mktime(0, 0, 0, date('m') + 1, $_SESSION['diaInicioContabil'], date('Y'));
                }
            } else {
                $this->dataLimite = mktime(0, 0, 0, date('m') + 1, date('d'), date('Y'));
            }
            $this->dataLimiteMktime = $this->dataLimite;
            $this->dataLimite = date('Y-m-d', $this->dataLimite);
            
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

    public function getList($tipo = 2, $status=1) {
        $acc = $this->con;
        $dataLimite = ((!$dataLimite) ? $this->dataLimite : $dataLimite);
        $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $class = (($tipo == 2) ? 'negativo' : 'positivo');
        $tipo = (($tipo == 2) ? 'valor < 0' : 'valor >= 0');
        $query = "SELECT l.*, c.label FROM anz_lancamentos l
		INNER JOIN anz_categorias c ON l.idCat = c.idCat
		INNER JOIN anz_user u ON u.idUser = l.idUser
		INNER JOIN anz_empresa e ON e.idEmpresa = u.idEmpresa
		WHERE $tipo AND vencimento < '".$this->dataLimite."' AND l.idStatus=$status AND e.idEmpresa= " . $_SESSION['idEmpresa'] . "
                ORDER BY l.vencimento, c.label ASC";
        $acc->executeQuery($query);
        if ($acc->getNumRows() == 0) {
            return ("Nenhum registro localizado");
        }
        while ($acc->proxReg())   {
            $dd = $acc->getDD();
            $entities[] = new Lancamentos($dd['idLancamento']);
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

    public function remove() {
        
    }

    /** Criar os métodos do controller daqui pra baixo * */
}
