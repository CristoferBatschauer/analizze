<?php
$sessaoLivre = true;
require ('./library/AnalizzeLibrary.php');
/*
session_start();
require ('_config.php');
require ('class/RegNegocios.php');
require ('class/Analizze.php');
require ('class/Relatorios.php');
require ('class/fpdf/fpdf.php');
require ('class/PDF.php');
 * 
 */
$dirSave = './relatorios/';
$vlrTotalDebito = 0;
$vlrTotalCredito = 0;

$rel = new Relatorios();
$rel->setDataInicio($rel->arrumaData($_SESSION['dataInicio'], 'arrumar'));
$rel->setDataFim($rel->arrumaData($_SESSION['dataFim'], 'arrumar'));
$rel->setStatus($_SESSION['status']);
$rel->setTipo($_SESSION['tipo']);
$data = $rel->getLancamentos();

$pdf = new PDF($rel->getNomeRelatorio(), './images/logos/'.$_SESSION['logo'], $rel->getStatusLabel());
$pdf->AddPage();

// informações dos dados utilizados para pesquisa
$pdf->Cell(0, 10, "Data inicio: ".$rel->arrumaData($rel->getDataInicio(), 'mostrar') . "    Data final: ".$rel->arrumaData($rel->getDataFim(), 'mostrar'), 0, 0, "C" );
$pdf->ln();

if ($data==false)   {
	$pdf->SetFont('Arial','B',20);
	$pdf->cell(0, 150, "Nenhum registro localizado.", 0, 0, "C");
}
else  {
	// header
	$pdf->SetFont('Arial','B',10);
	$pdf->SetFillColor(216,216,191);
	$pdf->SetTextColor(0);
	foreach($rel->getHeader() as $key=>$val)   {
		$temp = explode("|", $val);
		$w[] = $temp[1];
		$a[] = $temp[2];
		$pdf->Cell($temp[1], 6, $temp[0], 1, 0, $temp[2], true);
	}
	$pdf->ln(6);

	$pdf->SetFont('Arial','',10);
	$pdf->SetFillColor(224,235,255);
	$pdf->SetTextColor(0);

	$fill = false;
	$controleHeader = 1;
	foreach($data as $val)   {
		for($i=0; $i<count($w); $i++)   {
			$align = (($i==1)?"R":"C");
			$align = (($i==4)?"L":$align);
			$pdf->Cell($w[$i], 6, $val[$i], 1, 0, $a[$i], $fill);
		}
		$vlrTotalDebito += (($val[4]<0)?$val[4]:0);
		$vlrTotalCredito += (($val[4]>0)?$val[4]:0);
		$saldo += $val[4];
		$fill = !$fill;
		$pdf->ln(6);
		if ($controleHeader == 38)   {
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',10);
			$pdf->SetFillColor(216,216,191);
			$pdf->SetTextColor(0);
			foreach($rel->getHeader() as $key=>$val)   {
				$temp = explode("|", $val);
				$pdf->Cell($temp[1], 6, $temp[0], 1, 0, $temp[2], true);
			}
			$pdf->ln(6);
			$controleHeader = 1;
		}
		$pdf->SetFont('Arial','',10);
		$pdf->SetFillColor(224,235,255);
		$pdf->SetTextColor(0);
		$controleHeader++;
	}

	$pdf->ln();
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(190, 4, "Total de registros: ".count($data), 0, 0, "R");

	// cabecalho a direita primeira pagina.

	$pdf->SetFont('Arial','',10);
	$pdf->ln(10);

	if ($rel->getTipo() == 2 || $rel->getTipo()==3)   {
		$pdf->Cell(155, 5, "Total a pagar:", 0, 0, "R");
		$pdf->Cell(35, 5, "R$".number_format($vlrTotalDebito, 2, ',', '.'), 0, 0, "R");
		$pdf->ln();
	}
	if ($rel->getTipo() == 1 || $rel->getTipo()==3)   {
		$pdf->Cell(155, 5, "Total a receber:", 0, 0, "R");
		$pdf->Cell(35, 5, "R$".number_format($vlrTotalCredito, 2, ',', '.'), 0, 0, "R");
		$pdf->ln();
	}
	/*
	if ($rel->getTipo() == 3 && $rel->getStatus() == 1)   {
		$saldoCC = $rel->getSaldoCC();
		$pdf->Cell(155, 5, "Saldo em contas: ", 0, 0, "R");
		$pdf->Cell(35, 5, "R$".$saldoCC['saldoFormatado'], 0, 0, "R");
		$vlrTotalCredito += $saldoCC['Saldo'];
		$pdf->ln();
	}
	*/	
	if ($rel->getTipo() == 3)   {
		$saldo = ($vlrTotalDebito+$vlrTotalCredito);
		if ($saldo < 0)   $pdf->SetTextColor(255, 0, 0);
		$pdf->Cell(155, 5, "Resultado:", 0, 0, "R");
		$pdf->Cell(35, 5, "R$".number_format(($vlrTotalDebito+$vlrTotalCredito), 2, ',', '.'), 0, 0, "R");
	//  $pdf->Cell(35, 5, "R$".$saldo, 0, 0, "R");
		if ($saldo < 0)   $pdf->SetTextColor(0);
	}
}
$nomeRelatorio = "Analizze_" . date('dmY_His').'.pdf';
ob_clean();
$pdf->Output($dirSave . $nomeRelatorio, 'F');

//header("Location:".$dirSave."/".$nomeRelatorio);
?>

<script>
window.location = "<?= $dirSave.$nomeRelatorio ?>";
</script>