<?php
namespace Solarfield\Monolog;

use Monolog\Logger;
use PDO;
use Solarfield\Ok\JsonUtils;
use Solarfield\Ok\MiscUtils;

/**
 * Inserts the record to an SQL database table using PDO.
 */
class SqlTableHandler extends \Monolog\Handler\AbstractHandler {
	private $connectionString;
	private $tableName;

	public function handle(array $record) {
		$db = new PDO($this->connectionString);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$params = [
			'entryDatetime' => $record['datetime']->format('c'),
			'channel' => $record['channel'],
			'level' => $record['level'],
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
	 * @param string $connectionString PDO connection string, including username & password.
	 * @param mixed $level See monolog documentation.
	 * @param bool $bubble See monolog documentation.
	 * @param string $tableName Name of the table the record will be inserted into.
	 * @throws \Exception
	 */
	public function __construct($connectionString, $level = Logger::DEBUG, $bubble = true, $tableName = 'log_entry') {
		parent::__construct($level, $bubble);

		$this->connectionString = $connectionString;

		if (preg_match('/[^a-z0-9_]/i', $tableName)) throw new \Exception(
			"Table name must contain only characters a-z, 0-9, or underscore."
		);
		$this->tableName = $tableName;
	}
}