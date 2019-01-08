<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

class LoggerAlreadyExistException extends \Exception {
	/**
	 * LoggerAlreadyExistException constructor.
	 *
	 * @param $key
	 */
	public function __construct( $key ) {
		parent::__construct( "Logger with name '$key' already exists" );
	}

}
