<?php
/**
 * Class P3NeutralizerTestUrlBanning
 *
 * @package P3_Neutraliser
 */

/**
 * Sample test case.
 */
class P3NeutralizerTestUrlBanning extends WP_UnitTestCase {

	/**
	 * Returns the plugin instance.
	 *
	 * @return P3_Neutraliser_Plugin
	 */
	protected function plugin() {
		return P3_Neutraliser_Plugin::get_instance();
	}

	/**
	 * Data provider for URLs to test
	 *
	 * @return array
	 */
	public function urls() {
		return [
			[
				'https://api.bitbucket.org/2.0/repositories/pipdig/p3',
				403,
			],
			[
				'https://wptagname.space/',
				1,
			],
			[
				'http://wpupdateserver.com/foo',
				403,
			],
			[
				'https://api.bitbucket.org/2.0/repositories/someone-else/p3',
				0,
			],
			[
				'https://pipdigz.co.uk/p3/sneaky_abc123.txt',
				403,
			]
		];
	}

	/**
	 * Test the pseudo-response of URLs.
	 *
	 * @param string $url The URL to test.
	 * @param int    $expected The expected type of response.
	 *
	 * @dataProvider urls
	 */
	public function test url banning( $url, $expected ) {
		$result = $this->plugin()->maybe_intercept_request( 0, [], $url );
		switch ( $expected ) {
			case 0:
				$this->assertThat( $result, $this->equalTo( 0 ) );
				break;
			case 1:
				$this->assertThat( $result['body'], $this->equalTo( '1' ) );
				$this->assertThat( $result['response'], $this->equalTo( 200 ) );
				break;
			case 403:
				$this->assertWPError( $result );
				break;
			default:
				throw new \Exception;
		}
	}
}
