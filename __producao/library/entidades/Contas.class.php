<?php
class Contas   {
private $error; // armazena possiveis erros, inclusive, obrigatoriedades.
          private $table = "anz_contas"; // nome da tabela que a entidade representa
          private $cpoId = "idConta";
              
/** @type smallint(2)*/
private $idConta;
/** @type varchar(45)*/
private $nome;
/** @type varchar(3)*/
private $banco;
/** @type varchar(6)*/
private $agencia;
/** @type varchar(45)*/
private $conta;
/** @type varchar(150)*/
private $titular;
/** @type smallint(2) unsigned*/
private $idEmpresa;
/** @type tinyint(1)*/
private $passivo;
public function __construct($idConta = false, $dados=false)   {$this->error = false;$this->idConta = $idConta;if ($idConta)   {$acc = new MySql($this->table);
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
        $this->setIdConta($dados['idConta']);
$this->setNome($dados['nome']);
$this->setBanco($dados['banco']);
$this->setAgencia($dados['agencia']);
$this->setConta($dados['conta']);
$this->setTitular($dados['titular']);
$this->setIdEmpresa($dados['idEmpresa']);
$this->setPassivo($dados['passivo']);;    
        }
/** 
         * Metodo para pegar todos os parametros da entidade em forma de array
        * @return Array
         **/
public function getDados()   {
        $out['idConta'] = $this->getIdConta();
$out['nome'] = $this->getNome();
$out['banco'] = $this->getBanco();
$out['agencia'] = $this->getAgencia();
$out['conta'] = $this->getConta();
$out['titular'] = $this->getTitular();
$out['idEmpresa'] = $this->getIdEmpresa();
$out['passivo'] = $this->getPassivo();
                return $out;
        }

/* Metodos obrigatório pois EntityManager depende deles */
public function getId()   {
    return $this->idConta;
}
public function setId($id)   {
if ($this->idConta == '')   {
   $this->idConta = (int) $id;
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
/** @type smallint(2) notnull*/
private function setIdConta($idConta)   {
$this->idConta = (int)$idConta;
}
public function getIdConta()   {return $this->idConta;}
/** @type varchar(45)*/
public function setNome($nome)   {
$this->nome = (string)$nome;
}
public function getNome()   {return $this->nome;}
/** @type varchar(3)*/
public function setBanco($banco)   {
$this->banco = (string)$banco;
}
public function getBanco()   {return $this->banco;}
/** @type varchar(6)*/
public function setAgencia($agencia)   {
$this->agencia = (string)$agencia;
}
public function getAgencia()   {return $this->agencia;}
/** @type varchar(45)*/
public function setConta($conta)   {
$this->conta = (string)$conta;
}
public function getConta()   {return $this->conta;}
/** @type varchar(150)*/
public function setTitular($titular)   {
$this->titular = (string)$titular;
}
public function getTitular()   {return $this->titular;}
/** @type smallint(2) unsigned notnull*/
public function setIdEmpresa($idEmpresa)   {
if ($idEmpresa == "")   {
$this->error['idEmpresa'] = "idEmpresa é obrigatório";
                } else   {
                   unset($this->error['idEmpresa']);
                }
$this->idEmpresa = (int)$idEmpresa;
}
public function getIdEmpresa()   {return $this->idEmpresa;}
/** @type tinyint(1) notnull*/
public function setPassivo($passivo)   {
if ($passivo == "")   {
$this->error['passivo'] = "passivo é obrigatório";
                } else   {
                   unset($this->error['passivo']);
                }
$this->passivo = (int)$passivo;
}
public function getPassivo()   {return $this->passivo;}
} // fecha classe