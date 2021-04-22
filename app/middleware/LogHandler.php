<?php

namespace Fool\FoolishPlugin\Middleware;

use Katzgrau\KLogger\Logger;

/**
 * Class LogHandler
 *
 * A collection of helpful methods for error log handling.
 *
 * @package Fool
 */
class LogHandler {
	/**
	 * Handles log writing responsibilities.
	 *
	 * @param string $msg       The message to log.
	 * @param string $error     The error encountered.
	 * @param string $log_level The log level for the event.
	 */
	public static function write_to_log( $msg = '', $error = '', $log_level = 'info' ) {
		$logger = new Logger( WP_PLUGIN_DIR . '/foolish-company-data/logs' );

		if ( $log_level === 'error' ) {
			$logger->error( $msg . ': ' . $error );
		} elseif ( $log_level === 'debug' ) {
			$logger->debug( $msg . ': ' . $error );
		} else {
			$logger->info( $msg . ': ' . $error );
		}
	}
}
