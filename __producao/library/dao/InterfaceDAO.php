<?php
interface InterfaceDAO   {
    public function __construct($tableName=false);
    public function conecta();
    public function read($id, $campoId = 'id');
    public function remove($id, $nomeCampo = "id");
    public function getList();
    public function executeQuery($query = false);
    public function proxReg();
    public function getTableStruct();
    public function getAtributos();
    public function getTable();
    public function setTable($tableName);
    public function setQuery($query);
    public function getLastId();
    public function getCposDouble();
    public function getCposDate();
    public function getCposDatetime();
    public function getCposNotNull();
    public function getDD();
    public function getNumRows();
    public function setNumRows($numRows);
    public function setOrder($order);
    public function toString();
    public function teste();

}