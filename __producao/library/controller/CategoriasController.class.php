
<?php

/**
 * @date 15-02-13
 * @author Cristofer
 */
class CategoriasController extends AbstractController {

    private $object;
    private $con;
    private $out;

    public function __construct($object) {
        if ($object instanceof Categorias) {
            $this->object = $object;
            $this->con = new MySql("anz_categorias");
            $this->con->setOrder("idCat");
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
            $entities[] = new Categorias($val['idCat']);
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
        if ($this->object->getIdEmpresa() == 2)   { // categoria compartilhada
            $this->setOut("<h3>Categoria compartilhada. Não permitido edição</h3>");
            return false;
        }
        $this->object->save();
        $erro = "Salvo com sucesso!";
        if ($this->object->getError() !== false) {
            $erro = '<h3>Verifique os erros indicados: </h3>';
            $erro .= '<ul>';
            foreach ($this->object->getError() as $val) {
                $erro .= "<li>$val</li>";
            }
            $erro .= '</ul>';
        }
        $this->setOut($erro);
        return $this->getOut();
    }

    /** Criar os métodos do controller daqui pra baixo * */
    public function setInativoParaEmpresa($empresa) {
        if ($empresa instanceof Empresa) {
            $inativo = new Categoriasinativas();
            $inativo->setId($id);
            $inativo->setIdEmpresa($empresa->getId());
            return $inativo->save();
        } else {
            return false;
        }
    }

    public function remove() {
        $this->object->remove();
    }

}
