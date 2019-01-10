<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

class ArrayKeyAlreadyExistException extends \Exception {

	/**
	 * ArrayKeyAlreadyExistException constructor.
	 *
	 * @param string $key
	 */
	public function __construct( string $key ) {
		parent::__construct( "Array key $key already exists" );
	}

}
