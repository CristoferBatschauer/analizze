<?php
class Logs   {
private $error; // armazena possiveis erros, inclusive, obrigatoriedades.
          private $table = "anz_logs"; // nome da tabela que a entidade representa
          private $cpoId = "data";
              
/** @type datetime*/
private $data;
/** @type varchar(45)*/
private $tipo;
/** @type varchar(600)*/
private $texto;
/** @type int(4) unsigned*/
private $user_idUser;
/** @type varchar(45)*/
private $campo;
/** @type varchar(100)*/
private $old;
/** @type varchar(100)*/
private $new;
public function __construct($data = false, $dados=false)   {$this->error = false;$this->data = $data;if ($data)   {$acc = new MySql($this->table);
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
        $this->setData($dados['data']);
$this->setTipo($dados['tipo']);
$this->setTexto($dados['texto']);
$this->setUser_idUser($dados['user_idUser']);
$this->setCampo($dados['campo']);
$this->setOld($dados['old']);
$this->setNew($dados['new']);;    
        }
/** 
         * Metodo para pegar todos os parametros da entidade em forma de array
        * @return Array
         **/
public function getDados()   {
        $out['data'] = $this->getData();
$out['tipo'] = $this->getTipo();
$out['texto'] = $this->getTexto();
$out['user_idUser'] = $this->getUser_idUser();
$out['campo'] = $this->getCampo();
$out['old'] = $this->getOld();
$out['new'] = $this->getNew();
                return $out;
        }

/* Metodos obrigatório pois EntityManager depende deles */
public function getId()   {
    return $this->data;
}
public function setId($id)   {
if ($this->data == '')   {
   $this->data = (int) $id;
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
/** @type datetime notnull*/
private function setData($data)   {
$this->data = (string)$data;
}
public function getData()   {return $this->data;}
/** @type varchar(45)*/
public function setTipo($tipo)   {
$this->tipo = (string)$tipo;
}
public function getTipo()   {return $this->tipo;}
/** @type varchar(600)*/
public function setTexto($texto)   {
$this->texto = (string)$texto;
}
public function getTexto()   {return $this->texto;}
/** @type int(4) unsigned notnull*/
public function setUser_idUser($user_idUser)   {
if ($user_idUser == "")   {
$this->error['user_idUser'] = "user_idUser é obrigatório";
                } else   {
                   unset($this->error['user_idUser']);
                }
$this->user_idUser = (int)$user_idUser;
}
public function getUser_idUser()   {return $this->user_idUser;}
/** @type varchar(45)*/
public function setCampo($campo)   {
$this->campo = (string)$campo;
}
public function getCampo()   {return $this->campo;}
/** @type varchar(100)*/
public function setOld($old)   {
$this->old = (string)$old;
}
public function getOld()   {return $this->old;}
/** @type varchar(100)*/
public function setNew($new)   {
$this->new = (string)$new;
}
public function getNew()   {return $this->new;}
} // fecha classe