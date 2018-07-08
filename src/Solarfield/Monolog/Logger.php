<?php
namespace Solarfield\Monolog;

use Solarfield\Ok\LoggerInterface;

/**
 * A drop-in replacement for \Monolog\Logger, which also implements \Solarfield\Ok\LoggerInterface.
 * @package Solarfield\Monolog
 */
class Logger extends \Monolog\Logger implements LoggerInterface {
	function name(): string {
		return $this->getName();
	}
	
	function cloneWithName(string $aName): LoggerInterface {
		return $this->withName($aName);
	}
}
