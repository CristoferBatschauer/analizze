<?php

define('XOAD_CHAT_USERS_TABLE_NAME', 'xoad_chat_users');

class ChatClient
{
	var $nick;

	var $color;

	function ChatClient()
	{
		$this->color = '#000';
	}

	function getConnection()
	{
		$connection = pg_connect('server', 'user', 'password');

		pg_select_db('database', $connection);

		return $connection;
	}

	function closeConnection($connection)
	{
		pg_close($connection);
	}

	function getUsers($nick)
	{
		$connection = $this->getConnection();

		$now = time();

		$sqlQuery = '
			UPDATE
				' . XOAD_CHAT_USERS_TABLE_NAME . '
			SET
				time = ' . $now . '
			WHERE
				nick = \'' . $this->escapeString($nick, $connection) . '\'
		';

		pg_query($sqlQuery, $connection);

		$sqlQuery = '
			SELECT
				nick
			FROM
				' . XOAD_CHAT_USERS_TABLE_NAME . '
			WHERE
				time < ' . ($now - 30) . '
			ORDER BY
				time ASC,
				nick ASC
		';

		$sqlResult = pg_query($sqlQuery);

		$oldUsers = array();

		while ($row = pg_fetch_assoc($sqlResult)) {

			$oldUsers[] = $row['nick'];
		}

		pg_free_result($sqlResult);

		$sqlQuery = '
			DELETE FROM
				' . XOAD_CHAT_USERS_TABLE_NAME . '
			WHERE
				time < ' . ($now - 30) . '
		';

		pg_query($sqlQuery);

		$sqlQuery = '
			SELECT
				nick
			FROM
				' . XOAD_CHAT_USERS_TABLE_NAME . '
			ORDER BY
				nick ASC
		';

		$sqlResult = pg_query($sqlQuery, $connection);

		$users = array();

		while ($row = pg_fetch_assoc($sqlResult)) {

			$users[] = $row['nick'];
		}

		pg_free_result($sqlResult);

		$storage =& XOAD_Events_Storage::getStorage();

		foreach ($oldUsers as $nick) {

			$storage->postEvent('onUserLeave', 'ChatClient', null, $nick);
		}

		$this->closeConnection($connection);

		return $users;
	}

	function renameUser($oldNick, $newNick)
	{
		$connection = $this->getConnection();

		$now = time();

		$sqlQuery = '
			UPDATE
				' . XOAD_CHAT_USERS_TABLE_NAME . '
			SET
				time = ' . $now . ',
				nick = \'' . $this->escapeString($newNick, $connection) . '\'
			WHERE
				nick = \'' . $this->escapeString($oldNick, $connection) . '\'
		';

		pg_query($sqlQuery, $connection);

		$this->closeConnection($connection);
	}

	function addUser($nick)
	{
		$connection = $this->getConnection();

		$now = time();

		$sqlQuery = '
			INSERT INTO
				' . XOAD_CHAT_USERS_TABLE_NAME . '
			(
				nick,
				time
			)
			VALUES
			(
				\'' . $this->escapeString($nick, $connection) . '\',
				' . $now . '
			)
		';

		pg_query($sqlQuery, $connection);

		$this->closeConnection($connection);
	}

	function escapeString($unescapedString, $connection)
	{
		if (function_exists('pg_real_escape_string')) {

			return pg_real_escape_string($unescapedString, $connection);
		}

		return pg_escape_string($unescapedString);
	}

	function xoadGetMeta()
	{
		XOAD_Client::privateMethods($this, array('getConnection', 'closeConnection', 'escapeString'));

		XOAD_Client::mapMethods($this, array('getUsers', 'renameUser', 'addUser'));
	}
}

?>