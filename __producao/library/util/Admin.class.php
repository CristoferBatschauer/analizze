<?php

class Admin  extends RegNegocios {
	private $idEmpresa;
	
	public function Admin($idEmpresa=false)   {
		if ($idEmpresa)   $this->idEmpresa = $idEmpresa;
	}
	

	public static function geraHeader($iconeLeft, $texto, $iconeRight, $escreve=true)   {
		$out = '<div data-role="header" data-theme="e"><div class="ui-grid-b">';
		$out .= '<div class="ui-block-a"><div align="left">'.((count($iconeLeft) > 0)?'<a class="'.$iconeLeft['class'].'" href="'.$iconeLeft['link'].'"  data-role="button" data-icon="'.$iconeLeft['icone'].'" data-iconpos="left">'.$iconeLeft['texto'].'</a>':'').'</div></div>';
		$out .= '<div class="ui-block-b"><div align="center"><h2>'.$texto.'</h2></div></div>';
		$out .= '<div class="ui-block-c"><div align="right">'.((count($iconeRight) > 0)?'<a class="'.$iconeRight['class'].'" href="'.$iconeRight['link'].'"  data-role="button" data-icon="'.$iconeRight['icone'].'" data-iconpos="left">'.$iconeRight['texto'].'</a>':'').'	</div></div>';
		$out .= '</div></div>';
		
		// user
		$out .= (($_SESSION['userNome']!='')?'<div align="right" style="font-size:10px;">Seja bem vindo, '.$_SESSION['userNome'].'</div>':'');
		$out = (($escreve)?$out:'');
		return $out;
	}

	public static function geraFooter($escreve=true, $logoff=false)   {
		$out = '
				<div data-role="footer" data-theme="e">
					<div align="center" id="logoff">
						<a href="../index.php" data-ajax="false">
							<img src="../images/logoff.gif" />
						</a></div>
				</div>
				';
		$out = (($escreve)?$out:'');
		return $out;
	}
	

	
	public function getDadosEmpresa()   {
		if ($this->idEmpresa)   {
			$this->executeQuery("SELECT * FROM anz_empresa WHERE idEmpresa= " . $this->idEmpresa);
			if ($this->numRows == 0)   return false;
			$this->proxReg();
			foreach ($this->dd as $key=>$val)   $this->dd[$key] = utf8_encode($val);
			return $this->dd;
		}
	}
}

?>