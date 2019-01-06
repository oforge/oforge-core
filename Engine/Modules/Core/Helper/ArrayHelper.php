<?php

namespace Oforge\Engine\Modules\Core\Helper;

class ArrayHelper {
	private function __construct() {
	}

	public static function isAssoc( $array ) {
		return ( $array !== array_values( $array ) );
	}

	public static function get( $array, $key, $defaultValue = null ) {
		return isset( $array[ $key ] ) ? $array[ $key ] : $defaultValue;
	}

	public static function getNullable( $array, $key, $defaultValue = null ) {
		return array_key_exists( $key, $array ) ? $array[ $key ] : $defaultValue;
	}

	public static function createDataArray( $keys, $inputArray, $defaultValue = '' ) {
		$tmp = array_fill_keys( $keys, $defaultValue );

		return array_replace( $tmp, array_intersect_key( $inputArray, $tmp ) );
	}
}
