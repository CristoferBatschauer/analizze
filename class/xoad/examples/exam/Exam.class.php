<?php

require_once('questions.inc.php');

class Exam
{
	var $questions;

	function loadQuestions()
	{
		global $examQuestions;

		if ( ! isset($this->questions)) {

			$this->questions = array();

			foreach ($examQuestions as $question) {

				$this->questions[] = $question[0];
			}

			return true;
		}

		return false;
	}

	function cleanAnswers()
	{
		@session_start();

		$_COOKIE['examData'] = array();
	}

	function getAnswers($id)
	{
		global $examQuestions;

		if ( ! array_key_exists($id, $examQuestions)) {

			return null;
		}

		$answers = array();

		for ($iterator = 1; $iterator < sizeof($examQuestions[$id]) - 1; $iterator ++) {

			$answers[] = $examQuestions[$id][$iterator];
		}

		@session_start();

		$result = array();

		if (array_key_exists($id, $_COOKIE['examData'])) {

			$result['answer'] =& $_COOKIE['examData'][$id];

		} else {

			$result['answer'] = -1;
		}

		$result['data'] =& $answers;

		return $result;
	}

	function submitAnswer($question, $id)
	{
		@session_start();

		if ( ! array_key_exists('examData', $_COOKIE)) {

			$_COOKIE['examData'] = array();
		}

		if ( ! array_key_exists($question, $_COOKIE['examData'])) {

			$_COOKIE['examData'][$question] = $id;

			return true;
		}

		return false;
	}

	function fetchResults()
	{
		global $examQuestions;

		@session_start();

		if (array_key_exists('examData', $_COOKIE)) {

			if (sizeof($_COOKIE['examData']) == sizeof($examQuestions)) {

				$result = array();

				$result['data'] =& $examQuestions;

				$result['answers'] =& $_COOKIE['examData'];

				return $result;
			}
		}

		return null;
	}

	function xoadGetMeta()
	{
		XOAD_Client::privateMethods($this, array('loadQuestions', 'cleanAnswers'));

		XOAD_Client::mapMethods($this, array('getAnswers', 'submitAnswer', 'fetchResults'));
	}
}

?>