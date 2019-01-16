<?php

namespace Oforge\Engine\Modules\Core\Manager\Logger;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Oforge\Engine\Modules\Core\Exceptions\LoggerAlreadyExistException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\Statics;

class LoggerManager {
	private const FILE_EXTENSION = '.log';
	private $logger = [];
	private $defaultLoggerName = null;

	/**
	 * LoggerManager constructor.
	 *
	 * @param array $settings
	 *
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	public function __construct( array $settings ) {
		foreach ( $settings as $setting ) {

			if ( isset( $setting['name'] ) ) {
				$name = $setting['name'];
				if ( is_null( $this->defaultLoggerName ) ) {
					$this->defaultLoggerName = $name;
				}
				$this->initLogger( $setting['name'], $setting );
			}
		}
	}

	/**
	 * Deletes old Logfiles older x days (default: 28).
	 *
	 * @param int $days
	 */
	public function cleanupLogfiles( $days = 28 ) {//TODO noch ungetestet
		$logDir = Statics::LOGS_DIR;
		if ( file_exists( $logDir ) ) {
			$now           = time();
			$daysInSeconds = $days * 24 * 60 * 60;
			foreach ( new \DirectoryIterator( $logDir ) as $fileInfo ) {
				if ( $fileInfo->isDot() ) {
					continue;
				}
				if ( $fileInfo->isFile() && ( $now - $fileInfo->getMTime() ) >= $daysInSeconds && $fileInfo->isWritable() ) {
					unlink( $fileInfo->getRealPath() );
				}
			}
		}
	}

	/**
	 * Returns logger with name or default (first) logger.
	 *
	 * @param string|null $name
	 *
	 * @return Logger
	 */
	public function get( ?string $name = null ) : Logger {
		if ( !isset( $this->logger[ $name ] ) ) {
			$name = $this->defaultLoggerName;
		}

		return $this->logger[ $name ];
	}

	/**
	 * Initialize logger.
	 *
	 * @param string $name
	 * @param array $config
	 *
	 * @throws LoggerAlreadyExistException If Logger with name already exist.
	 * @throws \Exception
	 */
	public function initLogger( string $name, array $config = [] ) {
		if ( isset( $this->logger[ $name ] ) ) {
			throw new LoggerAlreadyExistException( $name );
		}
		// Create the logger
		$logger = new Logger( $name );
		$level  = ArrayHelper::get( $config, 'level', Logger::DEBUG );
		$type   = ArrayHelper::get( $config, 'type', 'default' );
		switch ( $type ) {
			case 'StreamHandler':
				$path = ArrayHelper::get( $config, 'path', Statics::LOGS_DIR . DIRECTORY_SEPARATOR . $name . self::FILE_EXTENSION );
				$logger->pushHandler( new StreamHandler( $path, $level ) );
				break;
			case 'RotatingFileHandler':
			default:
				$path     = ArrayHelper::get( $config, 'path', Statics::LOGS_DIR . DIRECTORY_SEPARATOR . $name . self::FILE_EXTENSION );
				$maxFiles = ArrayHelper::get( $config, 'max_files', 28 );
				$logger->pushHandler( new RotatingFileHandler( $path, $maxFiles, $level ) );
				break;
		}
		$this->logger[ $name ] = $logger;
	}

	/**
	 * Register logger with name.
	 *
	 * @param string $name
	 * @param Logger $logger
	 *
	 * @throws LoggerAlreadyExistException
	 */
	public function registerLogger( string $name, Logger $logger ) {
		if ( isset( $this->logger[ $name ] ) ) {
			throw new LoggerAlreadyExistException( $name );
		}
		$this->logger[ $name ] = $logger;
	}

}
