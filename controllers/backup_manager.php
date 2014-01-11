<?php

class BackupManager extends Application {

	function __construct() {
		parent::__construct(); 
		// create Admin Menu under 'Settings'
		add_action('admin_menu', array($this,'create_options_menu') );
		add_action( 'admin_enqueue_scripts', array($this,'enqueue_backup_manager_styles') );
		add_action( 'admin_enqueue_scripts', array($this,'enqueue_backup_manager_scripts') );
		// Ajax Functions
		add_action( 'wp_ajax_delete_backup', array($this,'ajax_delete_backup') );
	}

	function create_options_menu() {
		add_submenu_page( //create sub-level menu
		'plancom-plugin', //submenu of
		'Backup Manager', //Page Title
		'Backup Manager', //Menu Title
		'manage_options', //Capability
		'backup-manager', //Menu Slug
		array($this,'render_view') //view function
		);

		//call register settings function
		// add_action( 'backup_manager_init', 'register_theme_menu_settings' );
	}

	function render_view() {
		$inputs = array(
			'title'			=> 'PlanCom Backup Manager',
			'backup_list'	=> $this->list_backups(),
			);

		$view = new View();
		$view->render( $this->plugin_path .'/views/backup_manager.php', $inputs );
	}

	function enqueue_backup_manager_styles() {
		wp_register_style( 'plancom-backup-manager', $this->plugin_url . '/assets/css/backup_manager.css', false, '1.0.0' );
		wp_enqueue_style( 'plancom-backup-manager' );
	}

	function enqueue_backup_manager_scripts() {
		wp_enqueue_script( 'backup-manager-js', $this->plugin_url . '/assets/js/backup_manager.js', array('jquery'), ' ', false );
	}

	function list_backups() {
		$dir    = '/var/www/backups';
		$backups = scandir($dir);

		foreach( $backups as $key => $backup ) {
			if( $backup == '.' || $backup == '..' || $backup == 'db' ){continue;}
			$string .= "<li class=\"ui-widget-content $key\">";
			$string .= "$backup";
			// $string .= "<a href=\"trash/script/when/js/off\" title=\"Delete this image\" class=\"ui-icon ui-icon-trash\">Delete image</a>";
			$string .= "</li>";
		}
		return @$string;
	}

	function ajax_delete_backup() {
		
	}

}