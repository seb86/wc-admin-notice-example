<?php
/*
 * Plugin Name: WooCommerce Admin - Note Example
 * Plugin URI:  https://github.com/seb86/wc-admin-notice-example
 * Description: Adds a note to the merchant's inbox showing dummy text and two action buttons.
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

namespace WooCommerce\Admin\NoteExample;

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Admin\Notes\WC_Admin_Note;
use \Automattic\WooCommerce\Admin\Notes\WC_Admin_Notes;

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
		protected function __construct() {}

		/**
		 * Initialize the plugin.
		 *
		 * @access public
		 */
		public function init() {
			if ( did_action( 'plugins_loaded' ) ) {
				self::on_plugins_loaded();
			} else {
				add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), 99 );
			}
		}

		/**
		 * Loads the plugin when ready.
		 *
		 * @access public
		 */
		public function on_plugins_loaded() {
			// Don`t initialize the plugin if the minimum version of WooCommerce Admin or above is NOT installed.
			if ( ! version_compare( WC_ADMIN_VERSION_NUMBER, '0.22.0', '>=' ) ) {
				return;
			}

			// Load textdomain.
			$this->load_plugin_textdomain();

			// Create custom cron events.
			add_filter( 'cron_schedules', array( $this, 'add_weekly_schedule' ) );
			$this->create_events();

			// Cron event handler.
			add_action( 'wc_admin_daily', array( $this, 'do_wc_admin_event' ) );
			add_action( 'wc_admin_sunday', array( $this, 'do_wc_admin_event' ) );
		}

		/**
		 * Load Localisation files.
		 *
		 * @access protected
		 */
		protected function load_plugin_textdomain() {
			load_plugin_textdomain( 'wc-admin-note-example', false, basename( dirname( __DIR__ ) ) . '/languages' );
		}

		/**
		 * Adds weekly to the available cron schedules.
		 *
		 * @access public
		 * @param  array $schedules - The available schedules.
		 * @return array $schedules - The modified schedules.
		 */
		public function add_weekly_schedule( $schedules ) {
			$schedules['weekly'] = array(
				'interval' => 604800,
				'display'  => __( 'Once Weekly', 'wc-admin-note-example' )
			);

			return $schedules;
		}

		/**
		 * Schedule custom cron events.
		 *
		 * @access public
		 * @static
		 */
		public static function create_events() {
			if ( ! wp_next_scheduled( 'wc_admin_sunday' ) ) {
				wp_schedule_event( apply_filters( 'woocommerce_admin_note_example_schedule_time', strtotime( 'Sunday this week' ) ), apply_filters( 'woocommerce_admin_note_example_sunday_schedule_event', 'weekly' ), 'wc_admin_sunday' );
			}
		}

		/**
		 * Daily events to run.
		 *
		 * @access public
		 */
		public function do_wc_admin_event() {
			$this->possibly_add_note_example();
		}

		/**
		 * Possibly add note example.
		 *
		 * @access public
		 * @static
		 */
		public static function possibly_add_note_example() {
			// We only want to show the note if the store is setup. - Uncomment if you wish to use this check.
			/*$is_task_list_complete = get_option( 'woocommerce_task_list_complete', false );
			if ( ! $is_task_list_complete ) {
				//return;
			}*/

			// We want to show the note after 7 days since installing WC Admin. - Uncomment if you wish to use this check.
			/*$days_in_seconds = apply_filters( 'woocommerce_admin_note_example_show_after_admin', 7 * DAY_IN_SECONDS );
			if ( ! self::wc_admin_active_for( $days_in_seconds ) ) {
				return;
			}*/

			require_once( WC_ADMIN_ABSPATH . '/src/Notes/DataStore.php' );

			$data_store = \WC_Data_Store::load( 'admin-note' );

			// Do we already have this note? If so, we're done. - Uncomment if you wish to use this check.
			/*$note_ids = $data_store->get_notes_with_name( self::NOTE_NAME );
			if ( ! empty( $note_ids ) ) {
				return;
			}*/

			include_once( WC_ADMIN_ABSPATH . '/src/Notes/WC_Admin_Notes.php' );

			// We only want one note at any time. - Uncomment if you wish to delete any previous notes created with the same name.
			//WC_Admin_Notes::delete_notes_with_name( self::NOTE_NAME );

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
			self::create_new_note( array(
				'title'     => __( 'My Note Title', 'wc-admin-note-example' ),
				'content'   => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Iam enim adesse poterit. Prodest, inquit, mihi eo esse animo. <strong>Pollicetur certe.</strong> Duo Reges: constructio interrete.', 'wc-admin-note-example' ),
				'icon'      => 'reader',
				'note_name' => self::NOTE_NAME,
				'source'    => 'wc-admin-note-example',
				'actions'   => array(
					array(
						'name'    => 'do-something',
						'label'   => __( 'Click Me', 'wc-admin-note-example' ),
						'query'   => wc_admin_url(),
						'status'  => 'actioned',
						'primary' => false
					),
					array(
						'name'    => 'external-url',
						'label'   => __( 'View Repository', 'wc-admin-note-example' ),
						'query'   => 'https://github.com/seb86/wc-admin-notice-example',
						'status'  => 'actioned',
						'primary' => true
					)
				),
				'is_snoozable' => true,
				'locale'       => 'en_US'
			) );
		} // END possibly_add_note_example()

		/**
		 * Create a new note.
		 *
		 * @access public
		 * @param  array - The arguments of the note to use to create the note.
		 */
		public function create_new_note( $args = array() ) {
			require_once( WC_ADMIN_ABSPATH . '/src/Notes/WC_Admin_Note.php' );

			$note = new WC_Admin_Note();

			$default_args = array(
				'title'         => __( 'A Note Title', 'wc-admin-note-example' ),
				'content'       => __( 'Note content goes here.', 'wc-admin-note-example' ),
				'content_data'  => (object) array(),
				'type'          => WC_Admin_Note::E_WC_ADMIN_NOTE_INFORMATIONAL,
				'icon'          => 'info', // Use http://automattic.github.io/gridicons/ to find the icon you want to use.
				'note_name'     => '', // Note name is required.
				'source'        => 'woocommerce-admin',
				'date_created'  => '',
				'date_reminder' => '',
				'actions'       => array(),
				'is_snoozable'  => false,
				'locale'        => 'en_US'
			);

			foreach( $args['actions'] as $key => $action ) {
				$default_args['actions'][$key] = array(
					'name'    => 'action-' . $key,
					'label'   => sprintf( __( 'Button %1$s', 'wc-admin-note-example' ), $key ),
					'query'   => false,
					'status'  => WC_Admin_Note::E_WC_ADMIN_NOTE_ACTIONED,
					'primary' => false
				);
			}

			// Parse incoming $args into an array and merge it with $default_args
			$args = wp_parse_args( $args, $default_args );

			$note->set_title( $args['title'] );
			$note->set_content( $args['content'] );
			$note->set_content_data( $args['content_data'] );
			$note->set_type( $args['type'] );
			$note->set_locale( $args['locale'] );
			$note->set_icon( $args['icon'] );
			$note->set_name( $args['note_name'] );
			$note->set_source( $args['source'] );
			$note->set_date_created( $args['date_created'] );
			$note->set_date_reminder( $args['date_reminder'] );
			$note->set_is_snoozable( $args['is_snoozable'] );
			$note->clear_actions();

			// Create each action button for the note.
			foreach( $args['actions'] as $key => $action ) {
				$note->add_action( $action['name'], $action['label'], $action['query'], $action['status'], $action['primary'] );
			}

			// Save note.
			$note->save();
		} // END create_new_note()

		/**
		 * Test how long WooCommerce Admin has been active.
		 *
		 * @access public
		 * @static
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

return WC_Admin_Notes_Example::instance()->init();