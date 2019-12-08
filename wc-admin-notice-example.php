<?php
/*
 * Plugin Name: WooCommerce Admin - Note Example
 * Plugin URI:  https://sebastiendumont.com
 * Description: Adds a note to the merchant's inbox showing dummy text.
 * Author:      Sébastien Dumont
 * Author URI:  https://sebastiendumont.com
 * Version:     1.0.0
 * Text Domain: wc-admin-note-example
 * Domain Path: /languages/
 *
 * Requires at least: 5.2.0
 * Requires PHP: 5.6.20
 * WC requires at least: 3.6.0
 * WC tested up to: 3.8.1
 *
 * Copyright: © 2019 Sébastien Dumont, (mailme@sebastiendumont.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! class_exists( 'WC_Admin_Notes_Example' ) ) {

	class WC_Admin_Notes_Example {

		/**
		 * Name of the note for use in the database.
		 */
		const NOTE_NAME = 'wc-admin-note-example';

		/**
		 * Plugin Version
		 *
		 * @access public
		 * @static
		 */
		public static $version = '1.0.0';

		/**
		 * The single instance of the class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Get class instance.
		 *
		 * @return object Instance.
		 */
		public static function instance() {
			if ( null === static::$instance ) {
				static::$instance = new static();
			}
			return static::$instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @access public
		 * @return void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cloning this object is forbidden.', 'wc-admin-note-example' ), self::$version );
		} // END __clone()

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @access public
		 * @return void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'wc-admin-note-example' ), self::$version );
		} // END __wakeup()

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct() {
			// Initialize the plugin if the minimum version of WooCommerce Admin or above is installed.
			if ( version_compare( WC_ADMIN_VERSION_NUMBER, '0.22.0', '>=' ) ) {
				$this->init();
			}
		}

		/**
		 * Cron event handlers.
		 */
		public function init() {
			if ( did_action( 'plugins_loaded' ) ) {
				self::on_plugins_loaded();
			} else {
				add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), 99 );
			}
		}

		public function on_plugins_loaded() {
			$this->load_plugin_textdomain();

			add_action( 'wc_admin_daily', array( $this, 'do_wc_admin_daily' ) );
		}

		/**
		 * Load Localisation files.
		 */
		protected function load_plugin_textdomain() {
			load_plugin_textdomain( 'wc-admin-note-example', false, basename( dirname( __DIR__ ) ) . '/languages' );
		}

		/**
		 * Daily events to run.
		 */
		public function do_wc_admin_daily() {
			wp_die('Boo');
			$this->possibly_add_note_example();
		}

		/**
		 * Possibly add note example.
		 */
		public static function possibly_add_note_example() {
			// We only want to show the note if the store is setup.
			$is_task_list_complete = get_option( 'woocommerce_task_list_complete', false );
			if ( ! $is_task_list_complete ) {
				//return;
			}

			// We want to show the note after 7 days since installing WC Admin.
			$days_in_seconds = apply_filter( 'woocommerce_admin_note_example_show_after_admin', 7 * DAY_IN_SECONDS );
			if ( ! $this->wc_admin_active_for( $days_in_seconds ) ) {
				//return;
			}

			require_once( WC_ADMIN_ABSPATH . '/src/Notes/DataStore.php' );

			$data_store = \WC_Data_Store::load( 'admin-note' );

			// Do we already have this note? If so, we're done.
			$note_ids = $data_store->get_notes_with_name( self::NOTE_NAME );
			if ( ! empty( $note_ids ) ) {
				return;
			}

			require_once( WC_ADMIN_ABSPATH . '/src/Notes/WC_Admin_Notes.php' );

			// We only want one note at any time.
			WC_Admin_Notes::delete_notes_with_name( self::NOTE_NAME );

			/**
			 * Filter to allow for disabling this note.
			 *
			 * @param boolean default true
			 */
			$note_example_enabled = apply_filters( 'woocommerce_admin_note_example_enabled', true );

			if ( ! $note_example_enabled ) {
				return;
			}

			// Create a new note.
			$this->create_new_note( array(
				'title' => __( 'My Note Title', 'wc-admin-note-example' ),
				'content' => __( '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Iam enim adesse poterit. Prodest, inquit, mihi eo esse animo. <b>Pollicetur certe.</b> Duo Reges: constructio interrete.</p>', 'wc-admin-note-example' ),
				'icon' => 'post',
				'note_name' => self::NOTE_NAME,
				'source' => 'wc-admin-note-example',
				'actions' => array(
					array(
						'name'  => 'do-something',
						'label' => __( 'Do Something', 'wc-admin-note-example' ),
						'query' => '#'
					)
				)
			), true );
		} // END possibly_add_note_example()

		/**
		 * Create a new note.
		 *
		 * @access public
		 * @param  array  - The arguments of the note to use to create the note.
		 * @param  bool   - True if note is snoozable, false otherwise by default.
		 * @param  string - Language of the note. Defaults to "en_US".
		 */
		public function create_new_note( $args = array(), $is_snoozable = false, $locale = 'en_US' ) {
			require_once( WC_ADMIN_ABSPATH . '/src/Notes/WC_Admin_Note.php' );

			$note = new WC_Admin_Note( array(
				'is_snoozable' => $is_snoozable,
				'locale'       => $locale
			) );

			$default_args = array(
				'title'        => __( 'A Note Title', 'wc-admin-note-example' ),
				'content'      => __( 'Note content goes here.', 'wc-admin-note-example' ),
				'content_data' => (object) array(),
				'type'         => $note::E_WC_ADMIN_NOTE_INFORMATIONAL,
				'icon'         => 'info', // Types of notes: info, notice, product, post, phone, trophy, thumbs-up
				'note_name'    => '', // Note name is required.
				'source'       => 'woocommerce-admin',
				'actions'      => array()
			);

			foreach( $args['actions'] as $key => $action ) {
				$default_args['actions'][$key] = array(
					'name'    => 'action-' . $key,
					'label'   => sprintf( __( 'Button %1$s', 'wc-admin-note-example' ), $key ),
					'query'   => false,
					'status'  => $note::E_WC_ADMIN_NOTE_ACTIONED,
					'primary' => true
				);
			}

			// Parse incoming $args into an array and merge it with $defaults
			$args = wp_parse_args( $args, $default_args );

			$note->set_title( $args['title'] );
			$note->set_content( $args['content'] );
			$note->set_content_data( $args['content_data'] );
			$note->set_type( $args['type'] );
			$note->set_icon( $args['icon'] );
			$note->set_name( $args['note_name'] );
			$note->set_source( $args['source'] );

			// Create each action button for note.
			foreach( $args['actions'] as $key => $action ) {
				$note->add_action( $action->name, $action->label, $action->query, $action->status, $action->primary );
			}

			// Save note.
			$note->save();
		} // END create_new_note()

		/**
		 * Test how long WooCommerce Admin has been active.
		 *
		 * @access public
		 * @param  int  $seconds - Time in seconds to check.
		 * @return bool Whether or not WooCommerce admin has been active for $seconds.
		 */
		public static function wc_admin_active_for( $seconds ) {
			// Getting install timestamp reference class-wc-admin-install.php.
			$wc_admin_installed = get_option( 'wc_admin_install_timestamp', false );

			if ( false === $wc_admin_installed ) {
				return false;
			}

			return ( ( time() - $wc_admin_installed ) >= $seconds );
		} // END wc_admin_active_for()

	} // END class

} // END if class exists

return WC_Admin_Notes_Example::instance();