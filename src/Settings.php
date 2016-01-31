<?php
/**
 * Created by PhpStorm.
 * User: Kathr
 * Date: 31/01/2016
 * Time: 20:50
 */

namespace WebberZone\BetterSearch;


class Settings implements \ArrayAccess {

	/**
	 * Plugin root dir, injected.
	 *
	 * @since   2.2
	 *
	 * @var string $bsearch_dir
	 */
	private $bsearch_dir = '';

	/**
	 * our options.
	 *
	 * @var array $options
	 */
	private $settings = [ ];

	/**
	 * Default options.
	 *
	 * @since    1.0
	 *
	 * @return    array    Default options array
	 */
	private function default_options() {
		$title       = __( '<h3>Popular Searches</h3>', 'better-search' );
		$title_daily = __( '<h3>Weekly Popular Searches</h3>', 'better-search' );

		// Get relevant post types.
		$args       = array(
			'public'   => true,
			'_builtin' => true,
		);
		$post_types = http_build_query( get_post_types( $args ), '', '&' );

		$custom_CSS = '
#bsearchform { margin: 20px; padding: 20px; }
#heatmap { margin: 20px; padding: 20px; border: 1px dashed #ccc }
.bsearch_results_page { max-width:90%; margin: 20px; padding: 20px; }
.bsearch_footer { text-align: center; }
.bsearch_highlight { background:#ffc; }
	';

		$badwords = include $this->bsearch_dir . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'badwords.php';

		$bsearch_settings = array(

			/* General options */
			'seamless'      => true,
			// Seamless integration mode
			'track_popular' => true,
			// Track the popular searches
			'track_admins'  => true,
			// Track Admin searches
			'track_editors' => true,
			// Track Editor searches
			'cache'         => true,
			// Enable Cache
			'meta_noindex'  => true,
			// Add noindex,follow meta tag to head
			'show_credit'   => false,
			// Add link to plugin page of my blog in top posts list

			/* Search options */
			'limit'         => '10',
			// Search results per page
			'post_types'    => $post_types,
			// WordPress custom post types

			'use_fulltext'   => true,
			// Full text searches
			'weight_content' => '10',
			// Weightage for content
			'weight_title'   => '1',
			// Weightage for title
			'boolean_mode'   => false,
			// Turn BOOLEAN mode on if true

			'highlight'       => false,
			// Highlight search terms
			'excerpt_length'  => '100',
			// Length of excerpt in words
			'include_thumb'   => false,
			// Include thumbnail in search results
			'link_new_window' => false,
			// Open link in new window - Includes target="_blank" to links
			'link_nofollow'   => true,
			// Includes rel="nofollow" to links in heatmap

			'badwords'        => implode( ',', $badwords ),
			// Bad words filter

			/* Heatmap options */
			'include_heatmap' => false,
			// Include heatmap of searches in the search page
			'title'           => $title,
			// Title of Search Heatmap
			'title_daily'     => $title_daily,
			// Title of Daily Search Heatmap
			'daily_range'     => '7',
			// Daily Popular will contain posts of how many days?

			'heatmap_limit'    => '30',
			// Heatmap - Maximum number of searches to display in heatmap
			'heatmap_smallest' => '10',
			// Heatmap - Smallest Font Size
			'heatmap_largest'  => '20',
			// Heatmap - Largest Font Size
			'heatmap_unit'     => 'pt',
			// Heatmap - We'll use pt for font size
			'heatmap_cold'     => 'CCCCCC',
			// Heatmap - cold searches
			'heatmap_hot'      => '000000',
			// Heatmap - hot searches
			'heatmap_before'   => '',
			// Heatmap - Display before each search term
			'heatmap_after'    => '&nbsp;',
			// Heatmap - Display after each search term

			/* Custom styles */
			'custom_CSS'       => $custom_CSS,
			// Custom CSS

		);

		/*
		 * Filters default options for Better Search
		 *
		 * @since	2.0.0
		 *
		 * @param	array	$bsearch_settings	default options
		 */

		return apply_filters( 'bsearch_default_options', $bsearch_settings );
	}

	public function __construct( $bsearch_dir ) {
		$this->bsearch_dir = $bsearch_dir;
// Upgrade table code.
		global $bsearch_db_version, $network_wide;

		$bsearch_settings_changed = false;

		$defaults = $this->default_options();

		$bsearch_settings = array_map( 'stripslashes', (array) get_option( 'ald_bsearch_settings' ) );
		unset( $bsearch_settings[0] ); // Produced by the (array) casting when there's nothing in the DB.

		foreach ( $defaults as $k => $v ) {
			if ( ! isset( $bsearch_settings[ $k ] ) ) {
				$bsearch_settings[ $k ]   = $v;
				$bsearch_settings_changed = true;
			}
		}
		if ( true === $bsearch_settings_changed ) {
			update_option( 'ald_bsearch_settings', $bsearch_settings );
		}

		$this->settings = $bsearch_settings;
	}

	public function save_settings() {

	}


	/**
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 *
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 * @since 5.0.0
	 */
	public function offsetExists( $offset ) {
		return array_key_exists( $offset, $this->settings );
	}

	/**
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 *
	 * @return mixed Can return all value types.
	 * @since 5.0.0
	 */
	public function offsetGet( $offset ) {
		return $this->offsetExists( $offset ) ? $this->settings[ $offset ] : null;
	}

	/**
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetSet( $offset, $value ) {
//		return $this->settings;
	}

	/**
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset( $offset ) {
		// TODO: Implement offsetUnset() method.
	}
}