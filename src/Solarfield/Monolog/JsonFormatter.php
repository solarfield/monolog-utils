<?php
namespace Solarfield\Monolog;

use Solarfield\Ok\JsonUtils;
use Solarfield\Ok\MiscUtils;

/**
 * Formats the record as a JSON blob, with a trailing newline.
 * Dates are formatted as ISO 8601 (PHP date() format 'c').
 * Context and extra are processed by Solarfield\Ok\MiscUtils::varData().
 * Record is serialized to JSON via Solarfield\Ok\JsonUtils::toJson().
 */
class JsonFormatter implements \Monolog\Formatter\FormatterInterface {
	public function format(array $record) {
		return JsonUtils::toJson([
			'datetime' => $record['datetime']->format('c'),
			'channel' => $record['channel'],
			'level' => $record['level'],
			'message' => $record['message'],
			'context' => MiscUtils::varData($record['context']),
			'extra' => MiscUtils::varData($record['extra']),
		]) . "\n";
	}

	public function formatBatch(array $records) {
		$formatted = '';

		foreach ($records as $record) {
			$formatted .= $this->format($record);
		}

		return $formatted;
	}
}