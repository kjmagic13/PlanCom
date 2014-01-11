<?php
/**
 * Plugin Name: PlanCom
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Application {
	function __construct() {
		global $wpdb;

		// Activatie/Deactivate/Uninstall
		// register_activation_hook( __FILE__, array($this,'activate') );
		// register_deactivation_hook( __FILE__, array($this,'deactivate') );
		// register_uninstall_hook( __FILE__, array($this,'uninstall') );

		// Variables
		$this->plugin_dir = 'plancom_plugin';
		$this->plugin_path = ABSPATH . "wp-content/plugins/" . $this->plugin_dir;
		$this->plugin_url = plugins_url('/') . $this->plugin_dir;
		$this->plugin_db_table = $wpdb->prefix . 'plancom_options';
		$this->settings = get_option('plancom_plugin_settings');
		$this->wysiwyg = get_option('plancom_wysiwyg_settings');
		$this->cron_arr = _get_cron_array();

		// Load application style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array($this,'enqueue_application_styles') );
		add_action( 'admin_enqueue_scripts', array($this,'enqueue_application_scripts') );

		// Add weekly cron schedule
		add_filter( 'cron_schedules', array($this,'cron_add_weekly') );
		

	}
	function enqueue_application_styles() {
		wp_register_style( 'jquery-ui', 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css', false, '1.10.3' );
		wp_enqueue_style( 'jquery-ui' );
	}
	function enqueue_application_scripts() {
		wp_enqueue_script( 'jquery-ui-cdn', 'http://code.jquery.com/ui/1.10.3/jquery-ui.js', array('jquery'), ' ', false );
		wp_enqueue_script( 'application-js', $this->plugin_url . '/assets/js/application.js', array('jquery'), ' ', false );
	}
	function cron_add_weekly( $schedules ) {
		$schedules['weekly'] = array(
			// 'interval' => 500,
			'interval' => 604800,
			'display' => __( 'Once Weekly' )
			);
		return $schedules;
	}
	function activate() {
	}
	function deactivate() {
	}
	function uninstall() {
	}
}
$app = new Application();

// load library
foreach( glob( $app->plugin_path .'/lib/*.php' ) as $filename ){
	require_once $filename;
}
// load controllers
foreach( glob( $app->plugin_path .'/controllers/*.php' ) as $filename ){
	require_once $filename;
}

// load plugin
new Admin();
// new BackupManager();
new Workflow();

//
// register_activation_hook( __FILE__, array( 'Plugin_Name', 'activate' ) );
// register_deactivation_hook( __FILE__, array( 'Plugin_Name', 'deactivate' ) );