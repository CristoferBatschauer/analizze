<?php

class TestEvents
{
	var $myVar = 0;

	function Test() {}
}

require_once('../../xoad.php');

$serverEvents = array();

class ServerObserver extends XOAD_Observer
{
	function updateObserver($event, $arg)
	{
		global $serverEvents;

		if ($arg == null) {

			$arg = "null";
		}

		$serverEvents[] = $event . ' => ' . str_replace("\n", "\n" . str_repeat(' ', strlen($event) + 4), var_export($arg, true));

		if ($event == 'dispatchLeave') {

			$arg['response']['output'] = "<strong>Server Events:</strong>\n\n" . join('<hr />', $serverEvents);
		}
	}
}

XOAD_Server::addObserver(new ServerObserver());

if (XOAD_Server::runServer()) {

	exit;
}

?>
<?= XOAD_Utilities::header('../..') ?>

<script type="text/javascript">

var obj = <?= XOAD_Client::register(new TestEvents()) ?>;

obj.test();

document.write('<pre>');
document.write(obj.fetchOutput());
document.write('</pre>');

</script>