<?php

interface InterfaceDatabase {   
    public function __construct();
    public function open();
    public function close();
    public function autocommit($var);
    public function commit();
    public function rollback();
    public function executeQuery($query, $gravarLog=true);
    public function next();
}