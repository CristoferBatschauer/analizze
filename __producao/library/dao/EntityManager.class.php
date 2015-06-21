<?php

/**
 * Description of EntityManager
 *
 * @author Cristofer
 */
class EntityManager {

    private $object;
    private $error;
    private $message;

    public function EntityManager($object) {
        $this->object = $object;
        $this->object->setError(false);
        $this->message = '';
    }

    /**
     * Usado para registrar inserts e updates
     * Retorna Array:
     * 0 - True or False
     * 1 - Mensagem de confirma��o
     * 2 - Array com campos obrigat�rios
     * 3 - ID da opera��o
     * 4 - Link para retorno completo
     */
    public function save() {
        $tabela = $this->object->getTable();
        $dd = $this->object->getDados();
        try {
            $sql = new MySql($tabela);
            $sql->getTableStruct();

            $dados = array();
            $dd ['id_user'] = IDUSER;

            $id = (($dd[$this->object->getCpoId()]) ? $dd[$this->object->getCpoId()] : false);

            if ((isset($dd ['senha'])) && (strlen($dd ['senha']) != 32)) {
                $dd ['senha'] = md5($dd ['senha']);
            }

            $ret [0] = true;
            $ret [1] = "Registro atualizado com sucesso!";
            $ret [4] = (($dd ['rt']) ? $dd ['rt'] : false);

            // validação de campos específicos
            if (isset($dd['email']) && $dd['email'] != '' && (!RegNegocios::validaEmail($dd['email']))) {
                return (array(false, "Email informado é inválido", array('email')));
            }

            foreach ($sql->getAtributos() as $nomeCampo=>$detalhes) {
                foreach ($dd as $key => $val) {
                    if (($nomeCampo == $key) && ($nomeCampo != $this->object->getCpoId())) {
                        $dados [$nomeCampo] = $val; //dd [$nomeCampo];
                    }
                }
            }
            // formatação de valores
            if (is_array($sql->getCposDouble())) {
                foreach ($sql->getCposDouble() as $key => $val) {
                    if (isset($dados [$val])) {
                        $dados [$val] = RegNegocios::parseDouble($dados [$val]);
                    }
                }
            }
            if (is_array($sql->getCposDate())) {
                foreach ($sql->getCposDate() as $key => $val) {
                    if ((isset($dados [$val])) && $dados[$val] != "") {
                        $dados [$val] = RegNegocios::formataString('date', $dados [$val], 'arrumar');
                    }
                }
            }
            if (is_array($sql->getCposDateTime())) {
                foreach ($sql->getCposDateTime() as $key => $val) {
                    if (isset($dados [$val])) {
                        $dados [$val] = RegNegocios::formataString('date', $dados [$val], 'arrumar');
                    }
                }
            }

            // campos obrigatórios
            foreach ($sql->getCposNotNull() as $key => $val) {
                if (
                        (isset($dados[$val])) &&
                        ($dados [$val] == '') &&
                        ($key != $this->object->getCpoId())
                ) {
                    $erro [] = $val; // campos obrigatórios
                    $ret [0] = false;
                    $ret [1] = "Verifique os campos não preenchidos corretamente.";
                }
            }

            $ret[2] = $erro;
            unset($erro);

            if (!$ret [0]) {
                $this->object->setError($ret[1]);
                return false;
            }

            // configurar query: update ou insert
            if (count($dados) == 0) {
                $this->object->setError(array("Erro 5847"));
                return false;
            }

            foreach ($dados as $key => $val) {
                $val = utf8_decode($val);
                $queryUpdate [] = "$key= '$val'";
                $queryInsertKeys [] = $key;
                $queryInsertVals [] = "'$val'";
            }
            $queryUpdate = "UPDATE $tabela SET " . implode(", ", $queryUpdate) . " WHERE " . $this->object->getCpoId() . "= $id";
            $queryInsert = "INSERT INTO $tabela (" . implode(", ", $queryInsertKeys) . ") VALUES (" . implode(", ", $queryInsertVals) . ")";
            //$tipo = "Update";

            $insert = (($id == false) ? true : false);
            $query = (($insert) ? $queryInsert : $queryUpdate);
        } catch (Exception $e) {
                $this->object->setError(array($e->getMessage()));
                return false;
        }

        $sql->setQuery($query);
        if (!$sql->executeQuery()) {
                $this->object->setError(array("Erro 5869"));
                return false;
        }

        // saida
        if ($insert) {
            $id = $sql->getLastId();
            $ret [1] = "Registro inserido com sucesso!";
        }
        $ret [3] = $id;

        // Gera log da atualização
        Log::debug(__METHOD__ . ":" . __LINE__ . ": Tabela: $tabela, idRegistro: $id, Query: $query");
        if ($ret[0] === true) {
            $this->object->setError(false);
        } else {
            $this->object->setError($ret[2]);
        }
        $this->message = $ret[1];
        if (!$this->object->getId()) {
            $this->object->setId($ret[3]);
        }
    }

    public function remove() {
        $sql = new MySql($this->object->getTable());
        $ret = $sql->remove($this->object->getId(), $this->object->getCpoId());
        if ($ret === true)  $this->object->setError(false);
        else   $this->object->setError(array($ret));

    }

    public function getMessage() {
        return $this->message;
    }

    public function getError() {
        return $this->error;
    }

    public function getObject() {
        return $this->object;
    }

}
