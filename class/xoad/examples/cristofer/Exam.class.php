<?php
class Exam extends RegNegocios   {
	function listaTodos()   {
		$this->executeQuery("SELECT * FROM noticias_news ORDER BY codNoticia DESC limit 0, 10");
		while ($this->proxReg())   $out .= $this->dd['chamada'].'<br>';
		
		return urlencode($out);
	}
	function none ()   {
	}
	function xoadGetMeta()   	{
		XOAD_Client::privateMethods($this, array('none'));
		XOAD_Client::mapMethods($this, array('listaTodos'));
	}
}

?>