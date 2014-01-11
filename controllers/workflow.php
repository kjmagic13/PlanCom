<?php

class Workflow extends Application {

	function __construct() {
		parent::__construct();
		// create Admin Menu under 'Settings'
		add_action('admin_menu', array($this,'create_options_menu') );
		add_action( 'admin_enqueue_scripts', array($this,'enqueue_workflow_styles') );
		add_action( 'admin_enqueue_scripts', array($this,'enqueue_workflow_scripts') );
		// Ajax Functions
		add_action( 'wp_ajax_plancom_save_wysiwyg', array($this,'ajax_plancom_save_wysiwyg') );
		add_action( 'wp_ajax_plancom_reset_mce_icons', array($this,'wp_ajax_plancom_reset_mce_icons') );
		// Custom MCE Buttons
		add_filter("mce_buttons", array($this,'custom_mce_icons_row1'), 0);
		add_filter("mce_buttons_2", array($this,'custom_mce_icons_row2'), 0);
		add_filter('tiny_mce_before_init', array($this,'base_custom_mce_format') );
	}

	function create_options_menu() {
		add_submenu_page( //create sub-level menu
		'plancom-plugin', //submenu of
		'Workflows', //Page Title
		'Workflows', //Menu Title
		'manage_options', //Capability
		'workflows', //Menu Slug
		array($this,'render_view') //view functiocn
		);

		// register settings
		register_setting( 'plancom_wysiwyg_settings', 'plancom_wysiwyg_settings' );
		// initiate defaults if not set
		if( empty( $this->wysiwyg ) ) { update_option( 'plancom_wysiwyg_settings', $this->default_mce_icons(true) ); }
	}

	function render_view() {
		$inputs = array(
			'title'				=> 'Workflows',
			'wysiwyg'			=> $this->wysiwyg
			);

		$view = new View();
		$view->render( $this->plugin_path .'/views/workflow.php', $inputs );
	}

	function ajax_plancom_save_wysiwyg() {
		$data = $_POST['data'];
		if( !update_option('plancom_wysiwyg_settings', $data) ) {
			die('Failed to update settings.');
		}
	}

	function wp_ajax_plancom_reset_mce_icons() {
		$data_role = $_POST['data'];
		global $wp_roles; $updated = array();
		foreach ($wp_roles->roles as $role => $role_data) {
			if( !isset($role_data['capabilities']['edit_posts']) ) { continue; }
			if( $data_role == $role || $data_role == 'all' ) {
				$updated[$role] = $this->default_mce_icons(false);
			} else {
				$updated[$role] = $this->wysiwyg[$role];
			}
		}
		if( !update_option('plancom_wysiwyg_settings', $updated ) ) {
			die('Failed to reset icons.');
		}
	}

	function default_mce_icons($all=false) {
		global $wp_roles; $all_defaults = array();
		$defaults = array(
			'row1' 		=> array(
				'bold','italic','strikethrough','bullist','numlist','blockquote','justifyleft','justifycenter','justifyright','link','unlink','wp_more','fullscreen','wp_adv'
				),
			'row2' 		=> array(
				'formatselect','underline','justifyfull','forecolor','pastetext','pasteword','removeformat','charmap','outdent','indent','undo','redo','help'
				),
			'library' 	=> array(
				'spellchecker','media'
				),
			'formats'	=> array(
				'p','pre','h1','h2','h3','h4','h5','h6'
				)
			);

		foreach ($wp_roles->roles as $role => $role_data) {
			if( !isset($role_data['capabilities']['edit_posts']) ) { continue; }
			$all_defaults[$role] = $defaults;
		}

		if($all) { return $all_defaults; } else { return $defaults; }
	}

	function get_current_user_role() {
		require (ABSPATH . WPINC . '/pluggable.php');
		global $wpdb, $current_user; get_currentuserinfo();
		$role = $wpdb->get_var("SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'wp_capabilities' AND user_id = {$current_user->ID}");
		if(!$role) return 'non-user';
		$rarr = unserialize($role);
		$roles = is_array($rarr) ? array_keys($rarr) : array('non-user');
		return $roles[0];
	}
	function custom_mce_icons_row1() {
		if( empty($this->wysiwyg) ) {
			$role = $this->get_current_user_role();
			return $this->wysiwyg[$role]['row1'];
		} else {
			$rows = $this->default_mce_icons();
			return $rows['row1'];
		}
	}
	function custom_mce_icons_row2() {
		if( empty($this->wysiwyg) ) {
			$role = $this->get_current_user_role();
			return $this->wysiwyg[$role]['row2'];
		} else {
			$rows = $this->default_mce_icons();
			return $rows['row2'];
		}
	}
	function base_custom_mce_format($init) {
		$role = $this->get_current_user_role();
		$init['theme_advanced_blockformats'] = implode( ',', $this->wysiwyg[$role]['formats'] );
		return $init;
	}

	function enqueue_workflow_styles() {
		wp_register_style( 'plancom-workflow', $this->plugin_url . '/assets/css/workflow.css', false, '1.0.0' );
		wp_enqueue_style( 'plancom-workflow' );
	}

	function enqueue_workflow_scripts() {
		wp_enqueue_script( 'workflow-js', $this->plugin_url . '/assets/js/workflow.js', array('jquery'), ' ', false );
	}

}