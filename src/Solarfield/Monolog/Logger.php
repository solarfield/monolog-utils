<?php
namespace Solarfield\Monolog;

use Solarfield\Ok\LoggerInterface;

/**
 * A drop-in replacement for \Monolog\Logger, which also implements \Solarfield\Ok\LoggerInterface.
 * @package Solarfield\Monolog
 */
class Logger implements LoggerInterface {
	private $monolog;
	
	function getName(): string {
		return $this->monolog->getName();
	}
	
	function withName(string $aName): LoggerInterface {
		return new static($aName, $this->monolog->getHandlers(), $this->monolog->getProcessors());
	}
	
	public function emergency($message, array $context = []) {
		$this->monolog->emergency($message, $context);
	}
	
	public function alert($message, array $context = []) {
		$this->monolog->alert($message, $context);
	}
	
	public function critical($message, array $context = []) {
		$this->monolog->critical($message, $context);
	}
	
	public function error($message, array $context = []) {
		$this->monolog->error($message, $context);
	}
	
	public function warning($message, array $context = []) {
		$this->monolog->warning($message, $context);
	}
	
	public function notice($message, array $context = []) {
		$this->monolog->notice($message, $context);
	}
	
	public function info($message, array $context = []) {
		$this->monolog->info($message, $context);
	}
	
	public function debug($message, array $context = []) {
		$this->monolog->debug($message, $context);
	}

	public function log($level, $message, array $context = []) {
		$this->monolog->log($level, $message, $context);
	}
	
	public function __call($name, $arguments) {
		return $this->monolog->$name(...$arguments);
	}
	
	public function __construct(string $name, $handlers = [], $processors = []) {
		$this->monolog = new \Monolog\Logger($name, $handlers, $processors);
	}
}
