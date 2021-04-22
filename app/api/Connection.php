<?php

namespace Fool\FoolishPlugin\Api;

use Exception;
use Fool\FoolishPlugin\Middleware\LogHandler;

/**
 * Class Connection
 *
 * Handles connection to API, response validation.
 *
 * @property string ticker      The company ticker
 * @property string endpoint    The desired API endpoint
 * @property string base_url    The base URL for the API
 * @property mixed  api_key     The API key
 *
 * @package Fool\FoolishPlugin\Api
 */
class Connection {
	/**
	 * Connection constructor.
	 *
	 * @param string $ticker    The company ticker to pass to the API
	 * @param string $endpoint  The API endpoint
	 */
	public function __construct( string $ticker, string $endpoint ) {
		$this->ticker   = strtoupper( $ticker );
		$this->endpoint = $endpoint;
		$this->base_url = 'https://financialmodelingprep.com/api/v3/';
		$this->api_key  = get_field( 'api_key', 'option' );
	}

	/**
	 * Assembles properties into well-formed URL for which to call the API.
	 *
	 * @return string   The complete URL for the API endpoint.
	 */
	protected function make_request_url() {
		return $this->base_url . $this->endpoint . '/' . $this->ticker . '?apikey=' . $this->api_key;
	}

	/**
	 * Executes cURL request
	 *
	 * @return bool|string
	 *
	 * @throws Exception
	 */
	protected function make_request() {
		$curl = curl_init();

		curl_setopt_array( $curl, array(
			CURLOPT_URL            => $this->make_request_url(),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => '',
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => 'GET',
		) );

		$response       = curl_exec( $curl );
		$response_code  = curl_getinfo( $curl, CURLINFO_RESPONSE_CODE );
		$response_error = curl_error( $curl );

		curl_close( $curl );

		return $this->validate_request( $response, $response_code, $response_error );
	}

	/**
	 * Validates the API response.
	 * Logs errors if encountered.
	 * Handles exceptions if encountered.
	 *
	 * Returns validated response back to calling method.
	 *
	 * @param $response         mixed   cURL response.
	 * @param $response_code    mixed   cURL HTTP response code.
	 * @param $response_error   mixed   cURL HTTP response error.
	 *
	 * @return mixed
	 * @throws Exception
	 */
	protected function validate_request( $response, $response_code, $response_error ) {
		if ( $response_error ) {
			LogHandler::write_to_log( 'cURL Response Error', $response_error, 'error' );

			throw new Exception( 'cURL Error: ' . $response_error );
		} elseif ( $response_code !== 200 ) {
			LogHandler::write_to_log( 'HTTP Response Code Error', $response_code, 'error' );

			throw new Exception( 'Unexpected HTTP Response Code: ' . $response_code );
		} elseif ( $response == "[ ]" ) {
			LogHandler::write_to_log( 'Unexpected API Return Value', $response, 'error' );

			throw new Exception( 'Unexpected API Return Value: ' . $response );
		} elseif ( is_object( json_decode( $response ) ) ) {
			// API throws errors in the form of a single object (rather than the expected array of objects)
			LogHandler::write_to_log( 'Unexpected API Return Data Type (object)', $response, 'error' );

			throw new Exception( 'Unexpected API Return Data Type (object): ' . $response );
		}

		return $response;
	}

	/**
	 * Get the response back from the API
	 *
	 * Return error message if response throws an exception.
	 *
	 * @return string|object
	 */
	public function get_response() {
		try {
			$response = $this->make_request();
		} catch ( Exception $e ) {
			LogHandler::write_to_log( 'Connection Error in getResponse', $e, 'error' );

			return "<div class='alert alert-info'>There's been an error fetching this data. Please try again later.</div>";
		}

		return json_decode( $response )[0];
	}
}
