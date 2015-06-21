
<?php
/**
 * @date 15-02-24
 * @author Cristofer
 */
 
class CategoriasinativasController extends AbstractController  {
    private $object;
    private $con;
    private $out;
    
    public function __construct($object)   {
        if ($object instanceof Categoriasinativas)   {
            $this->object = $object;
            $this->con = new MySql("anz_categoriasinativas");
            $this->con->setOrder("idCat");
            $action = $_GET["action"];
            $dados = $_POST["dados"];
            if (method_exists($this, $action))   {
                $this->$action($dados);
           }
        }
        else   {
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
        foreach($this->con->getList() as $val)   {
            $entities[] = new Categoriasinativas($val['idCat']);
        }
        return $entities;
    }

    /* @overwrite */
    public function getOut() {    
        return $this->out;
    }
    private function setOut($var)   {
        $this->out = $var;
    }
    
    public function save()   {
        $this->object->save();
        $this->setOut(implode("<br>", $this->object->getError()));
    }
    

    /** Criar os métodos do controller daqui pra baixo **/
    

}
