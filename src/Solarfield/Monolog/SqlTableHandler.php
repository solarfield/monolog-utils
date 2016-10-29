<?php
namespace Solarfield\Monolog;

use Monolog\Logger;
use PDO;
use Solarfield\Ok\JsonUtils;
use Solarfield\Ok\MiscUtils;

/**
 * Inserts the record to an SQL database table using PDO.
 * The log-level is stored as the level name (e.g. 'ERROR').
 * The context and extra objects are serialized using \Solarfield\Ok\MiscUtils::varData().
 */
class SqlTableHandler extends \Monolog\Handler\AbstractHandler {
	private $dsn;
	private $username;
	private $password;
	private $driverOptions;
	private $tableName;
	private $datetimeFormat;

	public function handle(array $record) {
		$db = new PDO($this->dsn, $this->username, $this->password, $this->driverOptions);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$params = [
			'entryDatetime' => $record['datetime']->format($this->datetimeFormat),
			'channel' => $record['channel'],
			'level' => $record['level_name'],
			'message' => $record['message'],
			'context' => JsonUtils::toJson(MiscUtils::varData($record['context'])),
			'extra' => JsonUtils::toJson(MiscUtils::varData($record['extra'])),
		];

		$sql = "
			INSERT INTO {$this->tableName}
			(entry_datetime, channel, level, message, context, extra)
			VALUES(:entryDatetime, :channel, :level, :message, :context, :extra)
		";

		$db->prepare($sql)->execute($params);

		return $this->bubble === false;
	}

	/**
	 * SqlTableHandler constructor.
	 * @param string|array $connection PDO connection string.
	 *  Can also be specified as an array with keys 'dsn', 'username', 'password', 'options', which are mapped
	 *  to the parameters of PDO::__construct().
	 * @param mixed $level See monolog documentation.
	 * @param bool $bubble See monolog documentation.
	 * @param array $options Runtime options.
	 *  table_name: Name of the table the record will be inserted into.
	 *  datetime_format: PHP date() format used to format datetime values for SQL.
	 * @throws \Exception
	 */
	public function __construct($connection, $level = Logger::DEBUG, $bubble = true, array $options = null) {
		parent::__construct($level, $bubble);

		if (is_array($connection)) {
			$this->dsn = $connection['dsn']??null;
			$this->username = $connection['username']??null;
			$this->password = $connection['password']??null;
			$this->driverOptions = $connection['options']??[];
		}
		else {
			$this->dsn = $connection;
		}

		$t = (string)$options['table_name']??'log_entry';
		if (preg_match('/[^a-z0-9_]/i', $t)) throw new \Exception(
			"Table name must contain only characters a-z, 0-9, or underscore."
		);
		$this->tableName = $t;
		
		$this->datetimeFormat = (string)($options['datetime_format']??'Y-m-d H:i:s');
	}
}