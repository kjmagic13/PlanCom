<?php

class Admin extends Application {

	function __construct() {
		parent::__construct();
		// create Admin Menu under 'Settings'
		add_action('admin_menu', array($this,'create_options_menu') );
		add_action( 'admin_enqueue_scripts', array($this,'enqueue_admin_styles') );
		add_action( 'admin_enqueue_scripts', array($this,'enqueue_admin_scripts') );
		// Ajax Functions
		add_action( 'wp_ajax_plancom_perform_tasks', array($this,'ajax_plancom_perform_tasks') );
		add_action( 'wp_ajax_plancom_save_options', array($this,'ajax_plancom_save_options') );
		add_action( 'wp_ajax_plancom_cp_percentage', array($this,'ajax_plancom_cp_percentage') );

		// Cron
		// add_action( 'wp', array($this,'plancom_cron_schedule') );
		// add_action( 'plancom_cron_hook', array($this,'php_execs') );
		// print_r( $this->cron_arr );
		$this->cronjob = wp_next_scheduled( 'plancom_cron_hook', $this->plancom_cron_tasks() );

	}

	function plancom_cron_schedule() {
		if ( false /*cron_ebabled*/ && !$this->cronjob ) {
			wp_schedule_event( time(), 'weekly', 'plancom_cron_hook', $this->plancom_cron_tasks() );
		}
	}
	function plancom_cron_tasks() {
		$settings = array();
		parse_str($this->settings, $settings);
		$settings = (object)$settings;
		// $create_backup_zip, $overwrite_demo_with_live, $ftp_backup
		return array(
			isset($settings->cron_create_backup_zip),
			isset($settings->cron_ftp_backup),
			isset($settings->cron_overwrite_demo_with_live),
			);
	}

	function create_options_menu() {
		add_menu_page( //create new top-level menu
		'Settings & Tasks', //Page Title
		'PlanCom Plugin', //Menu Title
		'manage_options', //Capability
		'plancom-plugin', //Menu Slug
		array($this,'render_view') //view functiocn
		);

		// register settings
		register_setting( 'plancom_plugin_settings', 'plancom_plugin_settings' );
	}

	function render_view() {
		$settings = array();
		parse_str($this->settings, $settings);

		// cron info
		if( $this->cronjob ) {
			$cron_string =  date('r', $this->cronjob) /*. ' <button id="">Clear</button>'*/;
		} else {
			$cron_string = 'No event scheduled.';
		}

		$inputs = array(
			'title'						=> 'PlanCom Settings & Tasks',
			'overwrite_demo_warning'	=> 'WARNING: Overwriting the demo site is irreversible. The entire WordPress file system and database will be replaced with the live site.',
			'settings'					=> $settings,
			'wp_get_schedules'			=> wp_get_schedules(),
			'cron_string' 				=> $cron_string,
			);

		$view = new View();
		$view->render( $this->plugin_path .'/views/admin.php', $inputs );
	}

	function ajax_plancom_save_options() {
		$data = $_POST['data'];

		// remove current cron job
		if( true ) {
			wp_clear_scheduled_hook( 'plancom_cron_hook', $this->plancom_cron_tasks() );
		}

		if( !update_option('plancom_plugin_settings', $data) ) {
			die('Failed to update settings.');
		}
	}

	function ajax_plancom_perform_tasks() {
		$args = array();
		parse_str($_POST['data'], $args);

		if( true /*$args['security']*/ ) { // nonce
			$this->php_execs(
				isset($args['create_backup_zip']),
				isset($args['overwrite_demo_with_live']),
				isset($args['ftp_backup'])
				);
		}
	}

	function php_execs( $create_backup_zip=false, $overwrite_demo_with_live=false, $ftp_backup=false ) {
		$settings = array();
		parse_str($this->settings, $settings);
		$settings = (object)$settings;
		// Variables
		$NOW = date("Y-m-d-").microtime(true);
		$WWW_DIR = "$settings->proj_dir/$settings->www_name";
		$DEMO_DIR = "$settings->proj_dir/$settings->demo_name";
		$WWW_DB_CREDS = "--user=".DB_USER." --password=".DB_PASSWORD." --host=".DB_HOST;
		$DEMO_DB_CREDS = "--user=$settings->demo_db_user --password=$settings->demo_db_pass --host=".DB_HOST;
		// Creates backup tar.gz contiaining the live WWW file system and SQL dump
		if( $create_backup_zip ) {
			// ensure db folder exists inside backup folder
			exec("mkdir --parents $settings->backups_dir/db || exit");
			exec("mysqldump $WWW_DB_CREDS ".DB_NAME." > $settings->backups_dir/db/$NOW.sql");
			exec("tar -zcvPf $settings->backups_dir/$NOW.tar.gz -C $settings->proj_dir/ $settings->www_name -C $settings->backups_dir/db/ $NOW.sql");
		}
		// Overwrites Demo Site with all contents of Live Site
		if( $overwrite_demo_with_live ) {
			exec("rm $DEMO_DIR/* -r");
			// esure Demo Directory exists then copy Live Site's file system
			exec("mkdir --parents $DEMO_DIR || exit");
			exec("cp -r $WWW_DIR/* $DEMO_DIR");
			// ensure db folder exists inside backup folder. Create temporary SQL file for demo transfer
			exec("mkdir --parents $settings->backups_dir/db || exit");
			$DEMO_DB_PATH = "$settings->backups_dir/db/".$NOW."_demo.sql";
			exec("mysqldump $WWW_DB_CREDS ".DB_NAME." > $DEMO_DB_PATH");
			// Replace all instances of the Live domain string with the Demo domain (TODO: array serialization issue?)
			exec("sed -i 's|".get_site_url()."|$settings->demo_domain|g' $DEMO_DB_PATH");
			// Replace wp-config.php DB_NAME
			exec("sed -i 's|".DB_NAME."|$settings->demo_db_name|g' $DEMO_DIR/wp-config.php");
			// Delete the .htaccess file
			exec("rm $DEMO_DIR/.htacess -r");
			// Drop then recreate Demo DB. Upload then remove SQL file
			exec("mysql $DEMO_DB_CREDS -e 'DROP DATABASE IF EXISTS $settings->demo_db_name'");
			exec("mysql $DEMO_DB_CREDS -e 'CREATE DATABASE IF NOT EXISTS $settings->demo_db_name'");
			exec("mysql $DEMO_DB_CREDS $settings->demo_db_name < $DEMO_DB_PATH");
			exec("rm $DEMO_DB_PATH -r");
			// Delete Demo Permalink Structure
			exec("mysql $DEMO_DB_CREDS $settings->demo_db_name -e 'DELETE FROM wp_options WHERE option_name = 'permalink_structure'");
		}
		// Remote FTP tar.gz file
		if( false /*$ftp_backup*/ ) {
			$file = "$settings->backups_dir/$NOW.tar.gz";
			$remote_file = "$settings->ftp_path/$NOW.tar.gz";
			$conn_id = ftp_connect($settings->ftp_server);
			$login_result = ftp_login($conn_id, $settings->ftp_user, $settings->ftp_pass);
			if(ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
				// die("successfully uploaded $file");
			} else {
				die("There was a problem while uploading $file");
			}
			ftp_close($conn_id);
		}
	}

	function ajax_plancom_cp_percentage() {
		$settings = array();
		parse_str($this->settings, $settings);
		$settings = (object)$settings;
		$WWW_DIR = "$settings->proj_dir/$settings->www_name";
		$DEMO_DIR = "$settings->proj_dir/$settings->demo_name";

		$io = popen('/usr/bin/du -sb '.$WWW_DIR, 'r');
		$WWW_SIZE = intval(fgets($io,80));
		pclose($io);
		$io = popen('/usr/bin/du -sb '.$DEMO_DIR, 'r');
		$DEMO_SIZE = intval(fgets($io,80));
		pclose($io);

		$percentage = strval(intval( $DEMO_SIZE / $WWW_SIZE * 100 ));
		die( $percentage );
	}

	function enqueue_admin_styles() {
		wp_register_style( 'plancom-admin', $this->plugin_url . '/assets/css/admin.css', false, '1.0.0' );
		wp_enqueue_style( 'plancom-admin' );
	}

	function enqueue_admin_scripts() {
		wp_enqueue_script( 'admin-js', $this->plugin_url . '/assets/js/admin.js', array('jquery'), ' ', false );
	}

}