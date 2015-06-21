<?php
/**
 * XOAD Events MySQL Provider file.
 *
 * <p>This file defines the {@link XOAD_Events_Storage_MySQL} Class.</p>
 * <p>Example:</p>
 * <code>
 * <?php
 *
 * require_once('xoad.php');
 *
 * require_once(XOAD_BASE . '/classes/events/storage/MySQL.class.php');
 *
 * $storage = new XOAD_Events_Storage_MySQL('server=?;user=?;password=?;database=?;[port=?]');
 *
 * $storage->postEvent('event', 'class');
 *
 * ?>
 * </code>
 *
 * @author		Stanimir Angeloff
 *
 * @package		XOAD
 *
 * @subpackage	XOAD_Events
 *
 * @version		0.6.0.0
 *
 */

/**
 * Defines the table name in the MySQL database that is used to save the
 * events information (default value: xoad_events).
 *
 * @ignore
 *
 */
define('XOAD_EVENTS_TABLE_NAME', 'xoad_events');

/**
 * XOAD Events Storage MySQL Class.
 *
 * <p>This class is a {@link XOAD_Events_Storage} successor.</p>
 * <p>The class allows you to save events information in
 * MySQL database.</p>
 * <p>Example:</p>
 * <code>
 * <?php
 *
 * require_once('xoad.php');
 *
 * require_once(XOAD_BASE . '/classes/events/storage/MySQL.class.php');
 *
 * $storage = new XOAD_Events_Storage_MySQL('server=?;user=?;password=?;database=?;[port=?]');
 *
 * $storage->postEvent('event', 'class');
 *
 * ?>
 * </code>
 *
 * @author		Stanimir Angeloff
 *
 * @package		XOAD
 *
 * @subpackage	XOAD_Events
 *
 * @version		0.6.0.0
 *
 */
class XOAD_Events_Storage_MySQL extends XOAD_Events_Storage
{
	/**
	 * Holds the MySQL server used in the connection string.
	 *
	 * @access	protected
	 *
	 * @var		string
	 *
	 */
	var $server;

	/**
	 * Holds the MySQL user used in the connection string.
	 *
	 * @access	protected
	 *
	 * @var		string
	 *
	 */
	var $user;

	/**
	 * Holds the MySQL password used in the connection string.
	 *
	 * @access	protected
	 *
	 * @var		string
	 *
	 */
	var $password;

	/**
	 * Holds the MySQL database used in the connection string.
	 *
	 * @access	protected
	 *
	 * @var		string
	 *
	 */
	var $database;

	/**
	 * Holds the MySQL port used in the connection string.
	 *
	 * @access	protected
	 *
	 * @var		string
	 *
	 */
	var $port = 3306;

	/**
	 * Indicates whether to open a new connection to the MySQL server
	 * if an old one already exists.
	 *
	 * @access	protected
	 *
	 * @var		bool
	 *
	 */
	var $openNew;

	/**
	 * Creates a new instance of the {@link XOAD_Events_Storage_MySQL} class.
	 *
	 * @access	public
	 *
	 * @param	string	$dsn	The data source name and parameters to use
	 *							when connecting to MySQL.
	 *
 	 */
	function XOAD_Events_Storage_MySQL($dsn)
	{
		parent::XOAD_Events_Storage($dsn);
	}

	/**
	 * Retrieves a static instance of the {@link XOAD_Events_Storage_MySQL} class.
	 *
	 * <p>This method overrides {@link XOAD_Events_Storage::getStorage}.</p>
	 *
	 * @access	public
	 *
	 * @param	string	$dsn	The data source name and parameters to use
	 *							when connecting to MySQL.
	 *
	 * @return	object	A static instance to the
	 *					{@link XOAD_Events_Storage_MySQL} class.
	 *
	 * @static
	 *
 	 */
	function &getStorage($dsn)
	{
		static $instance;

		if ( ! isset($instance)) {

			$instance = new XOAD_Events_Storage_MySQL($dsn);
		}

		return $instance;
	}

	/**
	 * Creates a MySQL connection link.
	 *
	 * @access	private
	 *
	 * @return	resource
	 *
	 */
	function &getConnection()
	{
		$server = $this->server;

		if ($this->port != 3306) {

			$server .= ':' . $this->port;
		}

		$connection = pg_connect($server, $this->user, $this->password, $this->openNew);

		pg_select_db($this->database, $connection);

		return $connection;
	}

	/**
	 * Closes a MySQL connection link.
	 *
	 * @access	private
	 *
	 * @return	void
	 *
	 */
	function closeConnection(&$connection)
	{
		pg_close($connection);

		$connection = null;
	}

	/**
	 * Escapes special characters in the {@link $unescapedString},
	 * taking into account the current charset of the connection.
	 *
	 * @access	private
	 *
	 * @return	string
	 *
	 */
	function escapeString($unescapedString, $connection)
	{
		if (function_exists('pg_real_escape_string')) {

			return pg_real_escape_string($unescapedString, $connection);
		}

		return pg_escape_string($unescapedString);
	}

	/**
 	 * Posts multiple events to the database.
	 *
	 * <p>Valid keys for each event are:</p>
	 * - event		- the event name (case-sensitive);
	 * - className	- the class that is the source of the event;
	 * - sender		- the sender object of the event;
	 * - data		- the data associated with the event;
	 * - filter		- the event filter data (case-insensitive);
	 *				  using this key you can post events with
	 *				  the same name but with different filter data;
	 *				  the client will respond to them individually;
	 * - time		- the event start time (seconds since the Unix
	 *				  Epoch (January 1 1970 00:00:00 GMT);
	 * - lifetime	- the event lifetime (in seconds);
	 *
	 * @access	public
	 *
	 * @param	array	$eventsData		Array containing associative arrays
	 *									with information for each event.
	 *
	 * @return	bool	true on success, false otherwise.
	 *
	 */
	function postMultipleEvents($eventsData)
	{
		$connection =& $this->getConnection();

		foreach ($eventsData as $event) {

			if ( ! parent::postMultipleEvents($event)) {

				continue;
			}

			$sqlQuery = '
				INSERT INTO
					' . XOAD_EVENTS_TABLE_NAME . '
				(
					event,
					className,
					filter,
					sender,
					data,
					time,
					endTime
				)
				VALUES
				(
					\'' . $this->escapeString($event['event'], $connection) . '\',
					\'' . $this->escapeString($event['className'], $connection) . '\',
			';

			if (isset($event['filter'])) {

				$sqlQuery .= '\'' . $this->escapeString($event['filter'], $connection) . '\',';

			} else {

				$sqlQuery .= 'NULL,';
			}

			if (isset($event['sender'])) {

				$sqlQuery .= '\'' . $this->escapeString(serialize($event['sender']), $connection) . '\',';

			} else {

				$sqlQuery .= 'NULL,';
			}

			if (isset($event['data'])) {

				$sqlQuery .= '\'' . $this->escapeString(serialize($event['data']), $connection) . '\',';

			} else {

				$sqlQuery .= 'NULL,';
			}

			$sqlQuery .= '
					' . $this->escapeString($event['time'], $connection) . ',
					' . $this->escapeString($event['time'] + $event['lifetime'], $connection) . '
				)';

			pg_query($sqlQuery, $connection);
		}

		$this->closeConnection($connection);

		return true;
	}

	/**
 	 * Deletes old events from the database.
	 *
	 * <p>This method is called before calling {@link filterEvents}
	 * or {@link filterMultipleEvents} to delete all expired events
	 * from the database.</p>
	 *
	 * @access	public
	 *
	 * @return	bool	true on success, false otherwise.
	 *
	 */
	function cleanEvents()
	{
		$connection =& $this->getConnection();

		$time = parent::cleanEvents();

		$sqlQuery = '
			DELETE FROM
				' . XOAD_EVENTS_TABLE_NAME . '
			WHERE
				endTime < ' . $this->escapeString($time, $connection) . '
		';

		pg_query($sqlQuery, $connection);

		$this->closeConnection($connection);

		return true;
	}

	/**
 	 * Filters the events in the database using multiple criterias.
	 *
	 * <p>Valid keys for each event are:</p>
	 * - event		- the event name (case-sensitive);
	 * - className	- the class that is the source of the event;
	 * - filter		- the event filter data (case-insensitive);
	 * 				  using this argument you can filter events with
	 *				  the same name but with different filter data;
	 * - time		- the event start time (seconds since the Unix
	 *				  Epoch (January 1 1970 00:00:00 GMT).
	 *
	 * @access	public
	 *
	 * @param	array	$eventsData		Array containing associative arrays
	 *									with information for each event.
	 *
	 * @return	array	Sequental array that contains all events that match the
	 *					supplied criterias, ordered by time (ascending).
	 *
	 */
	function filterMultipleEvents($eventsData)
	{
		$connection =& $this->getConnection();

		$sqlQuery = '
			SELECT
				event,
				className,
				filter,
				sender,
				data,
				time,
				endTime
			FROM
				' . XOAD_EVENTS_TABLE_NAME . '
			WHERE
		';

		$index = 0;

		$length = sizeof($eventsData);

		foreach ($eventsData as $event) {

			if ( ! parent::filterMultipleEvents($event)) {

				continue;
			}

			$sqlQuery .= '(
				time > ' . $this->escapeString($event['time'], $connection) . '
				AND
				event = \'' . $this->escapeString($event['event'], $connection) . '\'
				AND
				className = \'' . $this->escapeString($event['className'], $connection) . '\'
			';

			if (isset($event['filter'])) {

				$sqlQuery .= 'AND filter = \'' . $this->escapeString($event['filter'], $connection) . '\'';
			}

			if ($index < $length - 1) {

				$sqlQuery .= ') OR ';

			} else {

				$sqlQuery .= ')';
			}

			$index ++;
		}

		$sqlQuery .= '
			ORDER BY
				time ASC
		';

		$events = array();

		$sqlResult = pg_query($sqlQuery, $connection);

		while (($row = pg_fetch_assoc($sqlResult)) !== false) {

			$events[] = array(
			'event'		=>	$row['event'],
			'className'	=>	$row['className'],
			'filter'	=>	$row['filter'],
			'time'		=>	(float) $row['time'],
			'endTime'	=>	(float) $row['endTime'],
			'eventData'	=>	array(
			'sender'	=>	($row['sender'] === null ? null : unserialize($row['sender'])),
			'data'		=>	($row['data'] === null ? null : unserialize($row['data']))
			));
		}

		pg_free_result($sqlResult);

		$this->closeConnection($connection);

		return $events;
	}
}
?>