<?php

namespace Fool\FoolishPlugin\Api;

use Exception;
use Fool\FoolishPlugin\Middleware\LogHandler;

class Connection {
	/**
	 * @var string
	 */
	public string $ticker;

	/**
	 * @var string
	 */
	public string $base_url;

	/**
	 * @var string
	 */
	public string $endpoint;

	/**
	 * @var string
	 */
	private string $api_key;

	/**
	 * Connection constructor.
	 *
	 * @param string $ticker
	 * @param string $endpoint
	 */
	public function __construct( string $ticker, string $endpoint ) {
		$this->ticker   = strtoupper( $ticker );
		$this->endpoint = $endpoint;
		$this->base_url = 'https://financialmodelingprep.com/api/v3/';
		$this->api_key  = get_field( 'api_key', 'option' );
	}

	/**
	 * Assembles properties into well-formed URL for which to call the API
	 *
	 * @return string
	 */
	protected function makeRequestUrl() {
		return $this->base_url . $this->endpoint . "/" . $this->ticker . "?apikey=" . $this->api_key;
	}

	/**
	 * Executes cURL request
	 *
	 * @return bool|string
	 * @throws Exception
	 */
	protected function makeRequest() {
		$curl = curl_init();

		curl_setopt_array( $curl, array(
			CURLOPT_URL            => $this->makeRequestUrl(),
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

		return $this->validateRequest( $response, $response_code, $response_error );
	}

	/**
	 * Validates the API response.
	 * Logs errors if encountered.
	 * Handles exceptions if encountered.
	 *
	 * Returns validated response back to calling method.
	 *
	 * @param $response
	 * @param $response_code
	 * @param $response_error
	 *
	 * @return mixed
	 * @throws Exception
	 */
	protected function validateRequest( $response, $response_code, $response_error ): mixed {
		if ( $response_error ) {
			LogHandler::writeToLog( 'cURL Response Error', $response_error, 'error' );

			throw new Exception( 'cURL Error: ' . $response_error );
		} elseif ( $response_code !== 200 ) {
			LogHandler::writeToLog( 'HTTP Response Code Error', $response_code, 'error' );

			throw new Exception( 'Unexpected HTTP Response Code: ' . $response_code );
		} elseif ( $response == "[ ]" ) {
			LogHandler::writeToLog( 'Unexpected API Return Value', $response, 'error' );

			throw new Exception( 'Unexpected API Return Value: ' . $response );
		} elseif ( is_object( json_decode( $response ) ) ) {
			// API throws errors in the form of a single object (rather than the expected array of objects)
			LogHandler::writeToLog( 'Unexpected API Return Data Type (object)', $response, 'error' );

			throw new Exception( 'Unexpected API Return Data Type (object): ' . $response );
		}

		return $response;
	}

	/**
	 * @return string|object
	 */
	public function getResponse() {
		try {
			$response = $this->makeRequest();
		} catch( Exception $e ) {
			LogHandler::writeToLog( 'Connection Error in getResponse', $e, 'error' );

			return "<div class='alert alert-info'>There's been an error fetching this data. Please try again later.</div>";
		}

		return json_decode( $response )[0];
	}
}
