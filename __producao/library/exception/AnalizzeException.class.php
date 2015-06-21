<?php
if (! defined ( 'ANALIZZE_LIBRARY' )) {	die ( 'Acesso direto n�o permitido' ); }


class AnalizzeException extends Exception{
	public function __construct($message, $code=0)   {
		parent::__construct($message, $code);
		Log::error($message);
	}
	
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}
?>