<?php

$sessaoLivre = true;
require ('../AnalizzeLibrary.php');


/* * *************************************************************************************** */
/**
 * Nao alterar daqui pra baixo
 */
$queryTables = "SHOW TABLE STATUS FROM `analizze` WHERE Name like 'anz%';";
//$queryTables = "SHOW TABLE STATUS FROM `livreemjesus` WHERE Name='anz_categoriasInativas';";
$acc = new MySql($table);
$acc->executeQuery($queryTables);
while ($acc->proxReg()) {
    $tabelas[] = $acc->getDD();
}

foreach ($tabelas as $tabela) {
    $table = $tabela['Name'];
    $nomeEntidade = ucWords(str_replace("anz_", "", $table));
    $acc = new MySql($table);
    $queryUpdate = array();
    $queryInsertCampos = array();
    $queryInsertValues = array();
    $setDados = array();
    $getDados = array();
    $out = array();
    $controller = array();


    $acc->executeQuery("SELECT COLUMN_NAME as campoId FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'analizze' AND TABLE_NAME = '" . $table . "' LIMIT 0,1");
    $acc->proxReg();
    $dd = $acc->getDD();
    $CAMPOID = $dd['campoId'];

    $acc->getTableStruct();
    $out[] = '<?php';
    $out[] = 'class ' . $nomeEntidade . '   {';
    $out[] = 'private $error; // armazena possiveis erros, inclusive, obrigatoriedades.
          private $table = "' . $table . '"; // nome da tabela que a entidade representa
          private $cpoId = "' . $CAMPOID . '";
              ';

    foreach ($acc->getAtributos() as $nomeCampo => $detalhes) {
        $out[] = '/**'
                . ' @type ' . $detalhes['tipoAtributo'] . ''
                . '*/';
        $out[] = 'private $' . $nomeCampo . ';';
        if ($nomeCampo != $CAMPOID) {
            $queryUpdate[] = $nomeCampo . '= \'".$this->get' . ucWords($nomeCampo) . '()."\'';
            $queryInsertCampos[] = $nomeCampo;
            $queryInsertValues[] = '\'".$this->get' . ucWords($nomeCampo) . '()."\'';
        }

        $setDados[] = '$this->set' . ucwords($nomeCampo) . '($dados[\'' . $nomeCampo . '\']);';
        $getDados[] = '$out[\'' . $nomeCampo . '\'] = $this->get' . ucwords($nomeCampo) . '();';
    }

    $queryUpdate = "UPDATE $table SET " . implode(", ", $queryUpdate) . " WHERE $CAMPOID= \$this->get" . ucwords($CAMPOID) . "()";
    $queryInsert = "INSERT INTO $table (" . implode(', ', $queryInsertCampos) . ") VALUES (" . implode(", ", $queryInsertValues) . ")";

    $out[] = 'public function __construct($' . $CAMPOID . ' = false, $dados=false)   {'
            . '$this->error = false;'
            . '$this->' . $CAMPOID . ' = $' . $CAMPOID . ';
                $dados["idEmpresa"] = $_SESSION["idEmpresa"];'
            . 'if ($' . $CAMPOID . ')   {'
            . '$acc = new MySql($this->table);
           $dados = $acc->read($this->getId(), $this->getCpoId());
            if ($acc->getNumRows() == 0) {
                return false;
            }
            }
            $this->setDados($dados);
       }';


    $out[] = '
    /** Persistencia do objeto **/
    public function save() {
        if ($this->getError()) {
            return false;
        }
        $em = new EntityManager($this);
        $em->save();
        if ($em->getError() !== false )   {
            // tratar retorno false
            echo $em->getMessage(); // mostra mensagem de erro
            if (is_array($em->getError()))   {
            foreach ($em->getError() as $val)   {
                echo $val ."<br />";
            }
            }
        }
    }

    /*** Metodo para remover objeto    */
    public function remove() {
        $em = new EntityManager($this);
        $em->remove();
        if ($em->getError() !== false)   {
            // tratar retorno false
            echo $em->getMessage(); // mostra mensagem de erro
        }
        else   {
            // zerar dados do objeto;
            $this->setDados(array());
        }
        
    }';

    $out[] = '/** 
         * Metodo para setar todos os parametros da entidades 
        * @param Array $dados
         **/';
    $out[] = 'private function setDados($dados)   {
        ' . implode("\n", $setDados) . ';    
        }';

    $out[] = '/** 
         * Metodo para pegar todos os parametros da entidade em forma de array
        * @return Array
         **/';
    $out[] = 'public function getDados()   {
        ' . implode("\n", $getDados) . '
                return $out;
        }';

    $out[] = '
/* Metodos obrigatório pois EntityManager depende deles */
public function getId()   {
    return $this->' . $CAMPOID . ';
}
public function setId($id)   {
if ($this->' . $CAMPOID . ' == \'\')   {
   $this->' . $CAMPOID . ' = (int) $id;
       }
}
  public function setError($error)   {
  $this->error = $error;
  }
  public function getError()  {
  return $this->error;
  }
';

    $out[] = '
    
    public function getTable()   {
    return $this->table;
    }
    
    public function getCpoId()   {
    return $this->cpoId;
    }
';


    $out[] = '/**Getters And Setters**/';
    foreach ($acc->getAtributos() as $nomeCampo => $detalhes) {
        $detalhes['tipo'] = str_replace('small', '', $detalhes['tipo']);
        $detalhes['tipo'] = str_replace('tiny', '', $detalhes['tipo']);
        $detalhes['tipo'] = str_replace('double', 'string', $detalhes['tipo']);
        $detalhes['tipo'] = str_replace('varchar', 'string', $detalhes['tipo']);
        $detalhes['tipo'] = str_replace('datetime', 'string', $detalhes['tipo']);
        $detalhes['tipo'] = str_replace('date', 'string', $detalhes['tipo']);
        $detalhes['tipo'] = str_replace('enum', 'string', $detalhes['tipo']);

        // set
        $out[] = '/**'
                . ' @type ' . $detalhes['tipoAtributo'] . (($detalhes['notnull']) ? ' notnull' : '') . ''
                . '*/';
        $out[] = (($nomeCampo == $CAMPOID) ? 'private' : 'public') . ' function set' . ucwords($nomeCampo) . '($' . $nomeCampo . ')   {';
        if (($detalhes['notnull']) && ($nomeCampo != $CAMPOID)) {
            $out[] = 'if ($' . $nomeCampo . ' == "")   {';
            $out[] = '$this->error[\'' . $nomeCampo . '\'] = "' . $nomeCampo . ' é obrigatório";
                } else   {
                   unset($this->error[\'' . $nomeCampo . '\']);
                }';
        }
        $out[] = '$this->' . $nomeCampo . ' = (' . $detalhes['tipo'] . ')$' . $nomeCampo . ';';
        $out[] = '}';

        //get
        $out[] = 'public function get' . ucwords($nomeCampo) . '()   {'
                . 'return $this->' . $nomeCampo . ';'
                . '}';
    }



    $out[] = '} // fecha classe';

    $fp = fopen("new/".ucwords($nomeEntidade) . '.class.php', "w+");
    fputs($fp, implode("\n", $out));
    fclose($fp);

    echo "Entidade '$nomeEntidade' Criada<br>";

    /*     * **
      ####################################################################################################################
      CRIACAO DO CONTROLLER
      ####################################################################################################################
     * * */
    $controller[] = '
<?php
/**
 * @date ' . date('y-m-d') . '
 * @author Cristofer
 */
 
class ' . $nomeEntidade . 'Controller extends AbstractController  {
    private $object;
    private $con;
    private $out;
    
    public function __construct($object)   {
        if ($object instanceof ' . $nomeEntidade . ')   {
            $this->object = $object;
            $this->con = new MySql("' . $table . '");
            $this->con->setOrder("' . $CAMPOID . '");
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
            $entities[] = new ' . $nomeEntidade . '($val[\'' . $CAMPOID . '\']);
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
        $erro = "<h3>Salvo com sucesso!</h3>";
        if ($this->object->getError() !== false) {
            $erro = "<h3>Verifique os erros indicados: </h3><ul>";
            foreach ($this->object->getError() as $val)  
                $erro .= "<li>$val</li>";
            $erro .= "</ul>";
        }
        $this->setOut($erro);
        return $this->getOut();        
    }
    
    public function remove()   {
        $this->object->remove();
    }
    

    /** Criar os métodos do controller daqui pra baixo **/
    

}
';

    $fp = fopen('../controller/new/' . ucwords($nomeEntidade) . 'Controller.class.php', "w+");
    fputs($fp, implode("\n", $controller));
    fclose($fp);


    /*     * **
      ####################################################################################################################
      CRIACAO DO SERVLET
      ####################################################################################################################
     * * */
    $servlet = array();
    $servlet[] = '
<?php
require ("../AnalizzeLibrary.php");
$id = $_GET["id"];
$dados = $_POST["dados"];

$servlet = new EmpresaController(new Empresa($id, $dados));
echo $servlet->getOut();     
        ';


    $fp = fopen('../servlets/new/' . ucwords($nomeEntidade) . 'Servlet.php', "w+");
    fputs($fp, implode("\n", $servlet));
    fclose($fp);
} // FECHA FOREACH TABELAS


