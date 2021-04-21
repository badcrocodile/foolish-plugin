<?php

namespace Fool\FoolishPlugin\Middleware;

use Katzgrau\KLogger\Logger;

/**
 * Class LogHandler
 *
 * @package Fool
 */
class LogHandler {
	public static function writeToLog( $msg = "", $error = "", $log_level = "info" ) {
		$logger = new Logger( WP_PLUGIN_DIR . "/foolish-company-data/logs" );

		if($log_level == 'error') {
			$logger->error( $msg . ": " . $error );
		} elseif($log_level == 'debug') {
			$logger->debug( $msg . ": " . $error );
		} else {
			$logger->info( $msg . ": " . $error );
		}
	}
}
