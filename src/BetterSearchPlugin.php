<?php

namespace WebberZone\BetterSearch;


/**
 * Class BetterSearchPlugin
 * @package WebberZone\BetterSearch
 *
 * Core Plugin Class for BetterSearch
 */
class BetterSearchPlugin {

	/**
	 * Plugin root dir, injected.
	 *
	 * @since   2.2
	 *
	 * @var string $bsearch_dir
	 */
	private $bsearch_dir = '';

	/**
	 * Holds the URL for Better Search folder
	 *
	 * @since    1.0
	 *
	 * @var string $bsearch_url
	 */
	public $bsearch_url = '';

	/**
	 * current database version of Better Search, replacement for global variable
	 *
	 * @since    2.2
	 *
	 * @var string db_version
	 */
	const db_version = '1.0';

	/**
	 * Settings Object
	 *
	 * @since 2.2
	 *
	 * @var Settings $settings
	 */
	public $settings;

	/**
	 * BetterSearchPlugin constructor.
	 *
	 * @param string $bsearch_dir
	 * @param string $bsearch_url
	 */
	public function __construct( $bsearch_dir, $bsearch_url ) {
		$this->bsearch_dir = $bsearch_dir;
		$this->bsearch_url = $bsearch_url;

		$this->settings = new Settings( $bsearch_dir );

		add_action( 'plugins_loaded', array( $this, 'language_init' ) );
	}

	/**
	 * Function to load translation files.
	 *
	 * @since    1.3.3
	 */
	public function language_init() {
		load_plugin_textdomain( 'better-search', false, $this->bsearch_dir . DIRECTORY_SEPARATOR . 'languages' );
	}

	/**
	 * Function to read options from the database.
	 *
	 * @since    1.0
	 *
	 * @return    array    Better Search options array
	 */
	public function read_options() {

		/**
		 * Filters options read from DB for Better Search
		 *
		 * @since    2.0.0
		 *
		 * @param    array $bsearch_settings Read options
		 */
		return apply_filters( 'bsearch_read_options', (array) $this->settings );
	}
}