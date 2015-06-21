<?php
class    {
private $error; // armazena possiveis erros, inclusive, obrigatoriedades.
          private $table = ""; // nome da tabela que a entidade representa
          private $cpoId = "idLancamento";
              
public function __construct($idLancamento = false, $dados=false)   {$this->error = false;$this->idLancamento = $idLancamento;if ($idLancamento)   {$acc = new MySql($this->table);
           $dados = $acc->read($this->getId(), $this->getCpoId());
            if ($acc->getNumRows() == 0) {
                return false;
            }
            }
            $this->setDados($dados);
       }

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
        
    }
/** 
         * Metodo para setar todos os parametros da entidades 
        * @param Array $dados
         **/
private function setDados($dados)   {
        ;    
        }
/** 
         * Metodo para pegar todos os parametros da entidade em forma de array
        * @return Array
         **/
public function getDados()   {
        
                return $out;
        }

/* Metodos obrigatório pois EntityManager depende deles */
public function getId()   {
    return $this->idLancamento;
}
public function setId($id)   {
if ($this->idLancamento == '')   {
   $this->idLancamento = (int) $id;
       }
}
  public function setError($error)   {
  $this->error = $error;
  }
  public function getError()  {
  return $this->error;
  }


    
    public function getTable()   {
    return $this->table;
    }
    
    public function getCpoId()   {
    return $this->cpoId;
    }

/**Getters And Setters**/
} // fecha classe