<?php
/**
 * Plugin Name: Orderly Asynchronous Javascript
 * Plugin URI: https://github.com/plaidpowered/orderly-async-js
 * Description: Dequeues the JavaScript loaded in your footer, and loads it in the background, in order of wp_enqueue stack.
 * Version: 0.1
 * Author: Paul Houser
 * Author URI: https://plaidpowered.com/custom-development
 * Text Domain: orderly-async
 */

class OrderlyAsyncLoader {

	public $async_stack = [];

	public static $build_version = "0.1";

	function __construct() {

		// this plugin will kill IE. just leave.
		if ( function_exists( 'is_IE' ) && is_IE() ) {
			return;
		}

		$this->setup_package_info();

		$this->async_stack = [];

		add_action( 'script_loader_tag', array( $this, 'unload_scripts' ), 9999, 3 );
		add_action( 'wp_footer', array( $this, 'output_async_stack' ), 9999 );

	}

	function unload_scripts( $tag, $handle, $src ) {

		if ( $src[0] === '<' ) {
			return $tag;
		}

		$this->async_stack[] = [
			'key' => $handle,
			'url' => $src,
		];

		return '';

	}

	function output_async_stack() {

		if ( empty( $this->async_stack ) ) {
			return;
		}

		?>

		<script>window.asyncScriptStack = <?php echo json_encode( $this->async_stack ); ?>;</script>
		<script async defer src="<?php echo plugins_url( WP_DEBUG ? 'async-loader.js?ver=' . time() : 'async-loader.min.js?ver=' . self::$build_version, __FILE__ ); ?>"></script>

		<?php

	}


	/**
	 * Loads package.json from plugin root and sets up private variables
	 */
	private function setup_package_info() {

		if ( ! file_exists( __DIR__ . '/package.json' ) ) {
			return;
		}

		$packagefile = file_get_contents( __DIR__ . '/package.json' );
		if ( empty( $packagefile ) ) {
			return;
		}

		$this->package = json_decode( $packagefile );
		if ( empty( $this->package ) ) {
			return;
		}

		if ( isset( $this->package->version ) ) {
			self::$build_version = $this->package->version;
		}

	}

}

new OrderlyAsyncLoader();