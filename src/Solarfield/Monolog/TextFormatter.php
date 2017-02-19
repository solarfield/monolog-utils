<?php
namespace Solarfield\Monolog;

use Solarfield\Ok\MiscUtils;

/**
 * Formats a human-readable dump of debug info.
 */
class TextFormatter implements \Monolog\Formatter\FormatterInterface {
	/**
	 * Throwable objects stored at $record.context.exception, will be cast to string, and moved to
	 * $record.context.exceptionAsString.
	 * If $record.context.exceptionAsString matches $record.message, it will be excluded.
	 * @param array $record
	 * @return string
	 */
	public function format(array $record) {
		$formatted = "[" . date('Y-m-d H:i:s') . "] {$record['channel']}.{$record['level_name']} {$record['message']}";

		if ($record['context']) {
			if (
				is_array($record['context'])
				&& array_key_exists('exception', $record['context'])
				&& $record['context']['exception'] instanceof \Throwable
			) {
				$exception = $record['context']['exception'];
				unset($record['context']['exception']);
				
				$exceptionAsString = (string)$exception;
				
				if ($record['message'] !== $exceptionAsString) {
					$record['context']['exceptionAsString'] = $exceptionAsString;
				}
			}
			
			$formatted .= "\n\n[context] " . var_export(MiscUtils::varData($record['context']), true);
		}

		if ($record['extra']) {
			$formatted .= "\n\n[extra] " . var_export(MiscUtils::varData($record['extra']), true);
		}

		return $formatted . "\n\n\n";
	}

	public function formatBatch(array $records) {
		$formatted = '';

		foreach ($records as $record) {
			$formatted .= $this->format($record);
		}

		return $formatted;
	}
}