<?php
require_once('RegNegocios.php');
require_once('Exam.class.php');
define('XOAD_AUTOHANDLE', true);
require_once('../../xoad.php');
$exam = new Exam();
//$exam->loadQuestions();
//$exam->cleanAnswers();

?>
<?= '<?' ?>xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-BR">
	<head>
		<title>Cristofer testando XOAD</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?= XOAD_Utilities::header('../..') ?>

		<style type="text/css" media="screen">

			body
			{
				background-color: #fff;
				color: #000;
				font: normal 0.8em tahoma, verdana, arial, serif;
				margin: 0;
				padding: 1em;
			}

			a
			{
				color: #04c;
				text-decoration: underline;
			}

			a:hover
			{
				color: #08f;
				text-decoration: underline;
			}

			a.active,
			a.active:hover
			{
				color: #c00;
				cursor: default;
				text-decoration: none;
			}

			a.answered,
			a.answered:hover
			{
				color: #4a4;
			}

			#loading
			{
				background-color: #c00;
				border: 0.1em solid #800;
				color: #fff;
				display: none;
				margin: 0;
				right: 1em;
				padding: 0.25em 0.5em 0.25em 0.5em;
				position: absolute;
				top: 1em;
				width: 6em;
			}

			#answers ol
			{
				margin: 0;
				padding: 0 0 1em 2em;
			}

			#answers ol li
			{
				margin: 0;
				padding: 0 0 0.25em 0;
			}

			#answers ol .correct
			{
				color: #4a4;
				font-weight: bold;
			}

			#answers ol .incorrect
			{
				color: #c00;
				font-weight: bold;
				text-decoration: line-through;
			}

			h1
			{
				border-bottom: 0.1em solid #ccc;
				font-size: 1.2em;
				padding: 0;
				margin: 0 0 1em 0;
			}

			h2
			{
				font-size: 0.9em;
				margin: 0 0 0.5em 0;
				padding: 0;
			}

			.answer
			{
				font-weight: bold;
			}

		</style>


	</head>
	<body>

		<div id="loading">Loading...</div>
		<script type="text/javascript">
		var exam = <?= XOAD_Client::register($exam) ?>;
		function showLoading()   {
			document.body.style.cursor = 'wait';
			document.getElementById('loading').style.display = 'block';
		};

		function hideLoading()   {
			document.body.style.cursor = 'default';
			document.getElementById('loading').style.display = 'none';
		};

		function busca()   {
			showLoading();
			var resultado = exam.listaTodos();
			resultado = resultado.replace(/\+/g, " "); // substitui os + por espaços
			resultado = unescape(resultado); // Desfaz o que a função urlencode(); fez.
			document.getElementById('escreve').innerHTML = resultado;
			hideLoading();
		};
		</script>		
<h1 id="question">Welcome!
  <select name="select">
    <option value="treste">teste</option>
    <option value="testetsets">testetet</option>
    <option value="testestes">'testestets</option>
  </select>
</h1>
		<a href="#" onClick="javascript: showLoading(); busca();">Clica aqui!</a>  ---   <a href="index.php">Limpar</a>
		<div id="escreve">Isso vai trocar pelos emails....</div>
	</body>
</html>