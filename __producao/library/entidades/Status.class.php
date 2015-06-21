<?php
class Status   {
private $error; // armazena possiveis erros, inclusive, obrigatoriedades.
          private $table = "anz_status"; // nome da tabela que a entidade representa
          private $cpoId = "idStatus";
              
/** @type smallint(3) unsigned*/
private $idStatus;
/** @type varchar(45)*/
private $statusLabel;
/** @type varchar(10)*/
private $statusValor;
public function __construct($idStatus = false, $dados=false)   {$this->error = false;$this->idStatus = $idStatus;if ($idStatus)   {$acc = new MySql($this->table);
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
        $this->setIdStatus($dados['idStatus']);
$this->setStatusLabel($dados['statusLabel']);
$this->setStatusValor($dados['statusValor']);;    
        }
/** 
         * Metodo para pegar todos os parametros da entidade em forma de array
        * @return Array
         **/
public function getDados()   {
        $out['idStatus'] = $this->getIdStatus();
$out['statusLabel'] = $this->getStatusLabel();
$out['statusValor'] = $this->getStatusValor();
                return $out;
        }

/* Metodos obrigatório pois EntityManager depende deles */
public function getId()   {
    return $this->idStatus;
}
public function setId($id)   {
if ($this->idStatus == '')   {
   $this->idStatus = (int) $id;
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
/** @type smallint(3) unsigned notnull*/
private function setIdStatus($idStatus)   {
$this->idStatus = (int)$idStatus;
}
public function getIdStatus()   {return $this->idStatus;}
/** @type varchar(45) notnull*/
public function setStatusLabel($statusLabel)   {
if ($statusLabel == "")   {
$this->error['statusLabel'] = "statusLabel é obrigatório";
                } else   {
                   unset($this->error['statusLabel']);
                }
$this->statusLabel = (string)$statusLabel;
}
public function getStatusLabel()   {return $this->statusLabel;}
/** @type varchar(10) notnull*/
public function setStatusValor($statusValor)   {
if ($statusValor == "")   {
$this->error['statusValor'] = "statusValor é obrigatório";
                } else   {
                   unset($this->error['statusValor']);
                }
$this->statusValor = (string)$statusValor;
}
public function getStatusValor()   {return $this->statusValor;}
} // fecha classe