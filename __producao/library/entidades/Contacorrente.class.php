<?php
class Contacorrente   {
private $error; // armazena possiveis erros, inclusive, obrigatoriedades.
          private $table = "anz_contacorrente"; // nome da tabela que a entidade representa
          private $cpoId = "idLancamento";
              
/** @type int(10) unsigned*/
private $idLancamento;
/** @type smallint(2)*/
private $idConta;
/** @type datetime*/
private $data;
/** @type int(4) unsigned*/
private $idUser;
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
        $this->setIdLancamento($dados['idLancamento']);
$this->setIdConta($dados['idConta']);
$this->setData($dados['data']);
$this->setIdUser($dados['idUser']);;    
        }
/** 
         * Metodo para pegar todos os parametros da entidade em forma de array
        * @return Array
         **/
public function getDados()   {
        $out['idLancamento'] = $this->getIdLancamento();
$out['idConta'] = $this->getIdConta();
$out['data'] = $this->getData();
$out['idUser'] = $this->getIdUser();
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
/** @type int(10) unsigned notnull*/
private function setIdLancamento($idLancamento)   {
$this->idLancamento = (int)$idLancamento;
}
public function getIdLancamento()   {return $this->idLancamento;}
/** @type smallint(2) notnull*/
public function setIdConta($idConta)   {
if ($idConta == "")   {
$this->error['idConta'] = "idConta é obrigatório";
                } else   {
                   unset($this->error['idConta']);
                }
$this->idConta = (int)$idConta;
}
public function getIdConta()   {return $this->idConta;}
/** @type datetime notnull*/
public function setData($data)   {
if ($data == "")   {
$this->error['data'] = "data é obrigatório";
                } else   {
                   unset($this->error['data']);
                }
$this->data = (string)$data;
}
public function getData()   {return $this->data;}
/** @type int(4) unsigned notnull*/
public function setIdUser($idUser)   {
if ($idUser == "")   {
$this->error['idUser'] = "idUser é obrigatório";
                } else   {
                   unset($this->error['idUser']);
                }
$this->idUser = (int)$idUser;
}
public function getIdUser()   {return $this->idUser;}
} // fecha classe