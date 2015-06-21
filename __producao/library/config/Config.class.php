<?php
if (! defined ( 'ANALIZZE_LIBRARY' )) {	die ( 'Acesso direto não permitido' ); }

class Config{
	
	private static $config;
	private static $data;
	const varName = 'AnalizzeConfig';
	
	private function __construct() {
		require_once AnalizzeLibrary::getPath().DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."Config.php";
		$varName = self::varName;
		if (isset($$varName)) {
			self::$data = $$varName;
			unset($$varName);
		} else {
			throw new Exception("Config is undefined.");
		}
	}

	public static function init() {
		if (self::$config == null) {
			self::$config = new Config();
		}
		return self::$config;
	}
	
	public static function getData($key1, $key2 = null) {
		if ($key2 != null) {
			if (isset(self::$data[$key1][$key2])) {
				return self::$data[$key1][$key2];
			} else {
				throw new Exception("Config keys {$key1}, {$key2} not found.");
			}
		} else {
			if (isset(self::$data[$key1])) {
				return self::$data[$key1];
			} else {
				throw new Exception("Config key {$key1} not found.");
			}
		}
	}
	
	public static function setData($key1, $key2, $value) {
		if (isset(self::$data[$key1][$key2])) {
			self::$data[$key1][$key2] = $value;
		} else {
			throw new Exception("Config keys {$key1}, {$key2} not found.");
		}
	}	
	
	public static function logIsActive() {
		if (isset(self::$data['log']) && isset(self::$data['log']['active'])) {
			return (bool) self::$data['log']['active'];
		} else {
			throw new Exception("Log activation flag not set.");
		}
	}
	
	public static function activeLog($fileName = null) {
		self::setData('log', 'active', true);
		self::setData('log', 'fileLocation', $fileName ? $fileName : '');
		LogPagSeguro::reLoad();
	}
	
	public static function getLogFileLocation() {
		if (isset(self::$data['log']) && isset(self::$data['log']['fileLocation'])) {
			return self::$data['log']['fileLocation'];
		} else {
			throw new Exception("Log file location not set.");
		}
	}	
	
}
?>
