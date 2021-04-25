<?php
/**
 * Class TestApi
 *
 * @package Fool
 */

use Fool\FoolishPlugin\Api\Connection;

/**
 * Sample test case.
 */
class TestApi extends WP_UnitTestCase {


	public object $profile;
	public string $ticker;

	public function setUp() {
		parent::setUp();

		$api_key      = '34392a98d7e775439dddd64023137556';
		$this->ticker = 'SBUX';

		$this->profile = ( new Connection( $this->ticker, 'profile', $api_key ) )->get_response();
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_api_connection() {
		$this->assertTrue( $this->profile->symbol === $this->ticker );
	}
}
