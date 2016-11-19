<?php
namespace Solarfield\Monolog;
use Throwable;

/**
 * Falls back to each successive inner handler, if the previous handler did not handle the record, if an exception was
 * thrown during handling, or if the inner handler is set to bubble.
 * Exceptions thrown by internal handlers are caught and passed to error_log() directly.
 */
class FallbackHandler extends \Monolog\Handler\GroupHandler {
	public function handle(array $record) {
		if ($this->processors) {
			foreach ($this->processors as $processor) {
				$record = call_user_func($processor, $record);
			}
		}

		/**
		 * @var $handler \Monolog\Handler\HandlerInterface
		 */
		foreach ($this->handlers as $handler) {
			try {
				if ($handler->handle($record)) {
					break;
				}
			}
			catch (Throwable $e) {
				error_log($e);
			}
		}

		return false === $this->bubble;
	}

	public function handleBatch(array $records) {
		/**
		 * @var $handler \Monolog\Handler\HandlerInterface
		 */
		foreach ($this->handlers as $handler) {
			$errorOccurred = false;

			foreach ($records as $record) {
				try {
					if (!$handler->handle($record)) {
						$errorOccurred = true;
					}
				}
				catch (Throwable $e) {
					$errorOccurred = true;
					error_log($e);
				}
			}

			if (!$errorOccurred) {
				break;
			}
		}
	}
}