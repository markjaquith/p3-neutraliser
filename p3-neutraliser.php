<?php
/**
 * Plugin Name: P3 Neutraliser
 * Description: Prevents the Pipdig P3 plugin from updating or making remote calls to Pipdig servers.
 * Author: Mark Jaquith
 * Author URI: http://coveredweb.com/
 * Version: 1.0.0
 *
 * @package P3_Neutraliser
 */

/**
 * The P3 Neutraliser plugin.
 */
class P3_Neutraliser_Plugin {
	/**
	 * The instance of this plugin.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * List of URLs that should return content of "1".
	 * e.g. Their licence checker.
	 *
	 * @var array
	 */
	protected $urls_that_should_return_1 = [
		'https://wptagname.space/',
	];

	/**
	 * List of domain names that should return a 403 (Forbidden) response.
	 *
	 * @var array
	 */
	protected $domains_that_should_return_403 = [
		'wpupdateserver.com', // Serves various JSON files to the dashboard.
		'wptagname.space', // The actual Pipdig update server.
		'pipdigz.co.uk', // Used for serving malicious "trigger" text files, and tracking of competitor hosts.
		'pipdig.co', // Main Pipdig domain.
		// 'pipdig.rocks', // This one is NOT blocked, as it seems to serve a legitimate purpose.
	];

	/**
	 * Private constructor.
	 */
	private function __construct() {
		$this->hook();
	}

	/**
	 * Returns the singleton instance of this plugin.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Registers the plugin's filters.
	 */
	public function hook() {
		add_filter( 'pre_http_request', [ $this, 'maybe_intercept_request' ], 20, 3 );
	}

	/**
	 * Whether a given URL should return "1" content.
	 *
	 * @param string $url The URL to inspect.
	 * @return bool
	 */
	public function should_return_1( $url ) {
		return in_array( $url, $this->urls_that_should_return_1 );
	}

	/**
	 * Whether a given URL should return a 403 code.
	 *
	 * @param string $url The URL to inspect.
	 * @return bool
	 */
	public function should_return_403( $url ) {
		$host = parse_url( $url, PHP_URL_HOST );
		$path = parse_url( $url, PHP_URL_PATH );

		$banned_domain = in_array( $host, $this->domains_that_should_return_403 );
		$bitbucket_banned = preg_match( '#\.?bitbucket.org$#i', $host ) && stripos( $path, '/pipdig/' ) !== false;

		return $banned_domain || $bitbucket_banned;
	}

	/**
	 * Generates a fake HTTP response.
	 *
	 * @param int    $code The response code.
	 * @param string $body The response body.
	 * @return array
	 */
	public function http_response( $code = 200, $body = '' ) {
		return [
			'response' => intval( $code ),
			'headers' => [],
			'body' => $body,
			'cookies' => [],
			'filename' => '',
		];
	}

	/**
	 * An HTTP response with code 200 (OK) and content "1".
	 *
	 * @return array
	 */
	public function http_response_1() {
		return $this->http_response( 200, '1' );
	}

	/**
	 * An HTTP response with code 200 (OK) and content "1".
	 *
	 * @return array
	 */
	public function http_response_403() {
		return new WP_Error(
			'pipdig-domain-neutralised',
			'Pipdig domains have been disabled by the P3 Neutraliser plugin'
		);
	}

	/**
	 * Inspects outgoing HTTP requests, and intercepts them if they are Pipdig-related.
	 *
	 * @param bool   $should_intercept Whether the request should be intercepted. Starts "falsy", and we may override by returing a fake response.
	 * @param array  $request The request being sent.
	 * @param string $url The URL being requested.
	 * @return mixed Either the original "falsy" flag, or our faked response.
	 */
	public function maybe_intercept_request( $should_intercept, $request, $url ) {
		if ( $this->should_return_1( $url ) ) {
			return $this->http_response_1();
		} elseif ( $this->should_return_403( $url ) ) {
			return $this->http_response_403();
		}

		return $should_intercept;
	}
}

P3_Neutraliser_Plugin::get_instance();
