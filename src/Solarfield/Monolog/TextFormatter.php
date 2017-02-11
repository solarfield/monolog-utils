<?php
namespace Solarfield\Monolog;

use Solarfield\Ok\MiscUtils;

/**
 * Formats a human-readable dump of debug info.
 */
class TextFormatter implements \Monolog\Formatter\FormatterInterface {
	public function format(array $record) {
		$formatted = "{$record['channel']}.{$record['level_name']} {$record['message']}";

		if ($record['context']) {
			$formatted .= "\n\n[context] " . var_export(MiscUtils::varData($record['context']), true);
		}

		if ($record['extra']) {
			$formatted .= "\n\n[extra] " . var_export(MiscUtils::varData($record['extra']), true);
		}

		return $formatted . "\n";
	}

	public function formatBatch(array $records) {
		$formatted = '';

		foreach ($records as $record) {
			$formatted .= $this->format($record);
		}

		return $formatted;
	}
}