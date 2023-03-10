<?php

/**
 * WordPress Slideshow Settings
 *
 *
 * @package    WP_Slideshow
 * @subpackage Settings
 * @since      1.0.0
 */


/**
 * It's a singleton class that adds a menu page to the admin menu.
 *
 * @package    WP_Slideshow
 */
final class WP_Slideshow_Settings {
	/* It makes this class a singleton. */
	use WP_Slideshow_Singleton;

	/**
	 * Current screen id on which to show the upload and slides box
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $screen_id;
	/**
	 * Name of the option to retrieve.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $option;


	/**
	 * This adds a new menu item to the WordPress admin menu.
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'init' ) );
		$this->screen_id = 'toplevel_page_wordpress-slideshow-plugin';
		$this->option    = 'wordpress_slideshow_slides';
	}


	/**
	 * It adds a menu page to the WordPress admin menu
	 *
	 * @return void
	 */
	public function menu(): void {
		add_menu_page(
			'WordPress Slideshow',
			'WP Slide',
			'manage_options',
			'wordpress-slideshow-plugin',
			array( $this, 'page' ),
			'dashicons-slides',
			2
		);

		add_meta_box(
			'wordpress-slideshow-slides',
			__( 'Slides', 'wordpress-slideshow' ),
			array( $this, 'render_slides' ),
			$this->screen_id,
			'normal',
			'default',
		);

		add_meta_box(
			'wordpress-slideshow-upload-slides',
			__( 'Upload', 'wordpress-slideshow' ),
			array( $this, 'render_upload' ),
			$this->screen_id,
			'side'
		);
	}

	/**
	 * It gets the images from the database, and then includes the slides.php template file
	 * and render slides
	 *
	 * @return void
	 */
	public function render_slides(): void {
		$images = get_option( $this->option );
		include __DIR__ . '/../templates/slides.php';
	}

	/**
	 * It renders the upload form in metabox.
	 *
	 * @return void
	 */
	public function render_upload(): void {
		$images = get_option( $this->option, array() );
		wp_nonce_field( basename( __FILE__ ), 'gallery_meta_nonce' );
		include __DIR__ . '/../templates/upload.php';
	}

	/**
	 * It's a wrapper for the page template
	 *
	 * @return void
	 */
	public function page(): void {
		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}
		wp_enqueue_style( 'wordpress-slideshow' );
		wp_enqueue_script( 'wordpress-slideshow' );
		wp_enqueue_style( 'wordpress-slideshow-notification' );
		wp_enqueue_script( 'wordpress-slideshow-notification' );
		$screen      = get_current_screen();
		$columns     = absint( $screen->get_columns() );
		$columns_css = '';

		if ( $columns ) {
			$columns_css = " columns-$columns";
		}
		include __DIR__ . '/../templates/page.php';
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
	}

	/**
	 * Registers a setting and its data.
	 *
	 * @return void
	 */
	public function init(): void {
		register_setting(
			'wordpress_slideshow_settings',
			'wordpress_slideshow_slides',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
			)
		);
	}

	/**
	 * It sanitizes the option input.
	 *
	 * @param mixed $input The value inputted by the user.
	 *
	 * @return array An array of values.
	 */
	public function sanitize( $input ): array {
		$sanitary_values = array();
		if ( isset( $input ) && is_array( $input ) ) {
			$sanitary_values = $input;
		}

		return $sanitary_values;
	}

}
