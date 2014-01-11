<div class="wrap">

	<?php screen_icon('themes'); ?> <h2><?php echo $title; ?></h2>

	<div class="tabs">
		<ul>
			<li><a href="#settings">Settings</a></li>
			<li><a href="#tasks">Manual Tasks</a></li>
		</ul>

		<div id="settings">
			<form id="plugin_settings">
				<?php settings_fields( 'plancom_plugin_settings' ); ?>
				<?php $enable_ftp = false; ?>
				<fieldset>
					<h3>General Settings</h3>
					<p>ABSPATH: <em><?php echo ABSPATH; ?></em></p>
					<label>
						<input name="proj_dir" value="<?php echo $settings['proj_dir']; ?>">
						Projects Directory <small>(The directory containing your live and demo sites. Ex. <em>/var/www</em>)</small>
					</label>
					<label>
						<input name="backups_dir" value="<?php echo $settings['backups_dir']; ?>">
						Backups Directory <small>(Ex. <em>/var/www/backups</em>)</small>
					</label>
					<label>
						<input name="www_name" value="<?php echo $settings['www_name']; ?>">
						Site Directory Name <small>(Ex. <em>wordpress</em>)</small>
					</label>
					<label>
						<input name="demo_name" value="<?php echo $settings['demo_name']; ?>">
						Demo Directory Name <small>(Ex. <em>wordpress_demo</em>)</small>
					</label>
					<label>
						<input value="<?php echo get_site_url(); ?>" disabled="disabled">
						Site URL
					</label>
					<label>
						<input name="demo_domain" value="<?php echo $settings['demo_domain']; ?>">
						Demo Site URL 
					</label>
				</fieldset>
				<fieldset>
					<h3>Live Database Information</h3>
					<label>
						<input value="<?php echo DB_HOST; ?>" disabled="disabled">
						Host
					</label>
					<label>
						<input value="<?php echo DB_NAME; ?>" disabled="disabled">
						Database Name
					</label>
					<label>
						<input value="<?php echo DB_USER; ?>" disabled="disabled">
						Username
					</label>
					<label>
						<input value="<?php echo DB_PASSWORD; ?>" disabled="disabled">
						Password
					</label>
				</fieldset>
				<fieldset>
					<h3>Demo Database Information</h3>
					<label>
						<input value="<?php echo DB_HOST; ?>" disabled="disabled">
						Host
					</label>
					<label>
						<input name="demo_db_name" value="<?php echo $settings['demo_db_name']; ?>">
						Database Name
					</label>
					<label>
						<input name="demo_db_user" value="<?php echo $settings['demo_db_user']; ?>">
						Username
					</label>
					<label>
						<input name="demo_db_pass" value="<?php echo $settings['demo_db_pass']; ?>">
						Password
					</label>
				</fieldset>
				<?php if($enable_ftp): ?>
				<fieldset>
					<h3>Remote FTP Credentials</h3>
					<label>
						<input name="ftp_server" value="<?php echo $settings['ftp_server']; ?>">
						Server
					</label>
					<label>
						<input name="ftp_user" value="<?php echo $settings['ftp_user']; ?>">
						User
					</label>
					<label>
						<input name="ftp_pass" value="<?php echo $settings['ftp_pass']; ?>">
						Password
					</label>
					<label>
						<input name="ftp_path" value="<?php echo $settings['ftp_path']; ?>">
						Path to save <em>tar.gz</em> file.
					</label>
				</fieldset>
				<?php
				endif; ?>
				<!-- <fieldset>
					<h3>Cron Settings</h3>

					<label>
						<input type="checkbox" name="cron_enabled" id="cron_enabled" value="1" <?php checked( $settings['cron_enabled'] ); ?>>
						<strong>Enable Cron</strong>
					</label>

					<div id="plancom_cron">

						<p>Next Scheduled Cron: <strong><?php echo $cron_string; ?></strong></p>

						<div class="accordion">
							<h3>Discalimer</h3>
							<div>
								<em>WordPress Cron</em> is an imprecise pseudo-cron system. See the following example for more information.

								<blockquote><small>If you’ve scheduled an email to be sent at 2:00 pm on Monday, then the email likely wouldn’t be sent precisely at that time, unless your website received a visit at exactly 2:00 pm. Instead, the email would be sent at the time of the first visit after 2:00 pm.</small></blockquote>

							</div>
						</div>

						<p>
							<strong>Interval</strong>
							<label>
								<select name="cron_interval">
									<?php ksort($wp_get_schedules); foreach( $wp_get_schedules as $sched_key => $sched_array ) : ?>
									<option value="<?php echo $sched_key ?>" <?php selected( $settings['cron_interval'], $sched_key ); ?>><?php echo $sched_key ?></option>
									<?php
									endforeach; ?>
								</select>
							</label>
						</p>

						<p>
							<strong>Tasks to perform automatically.</strong> <small>See the <em>Manual Tasks</em> tab for an in depth description of each task.</small>
							<label>
								<input type="checkbox" name="cron_create_backup_zip" value="1" <?php checked( $settings['cron_create_backup_zip'] ); ?>>
								Create tar.gz backup
							</label>
							<?php if($enable_ftp): ?>
							<label>&not;
								<input type="checkbox" name="cron_ftp_backup" value="1" <?php checked( $settings['cron_ftp_backup'] ); ?>>
								FTP Backup
							</label>
							<?php 
							endif; ?>
							<label>
								<input type="checkbox" name="cron_overwrite_demo_with_live" value="1" <?php checked( $settings['cron_overwrite_demo_with_live'] ); ?>>
								Overwrite demo directory and database
							</label>
						</p>
					</div>
				</fieldset> -->
				<p class="indeterminate-progressbar"></p>
				<?php submit_button(); ?>
			</form>
		</div>

		<div id="tasks">
			<form id="tasks">
				<fieldset>
					<input type="hidden" name="security" value="<?php echo wp_create_nonce('backup-options-security'); ?>" />
					<input type="hidden" name="action" value="backup_ajax_submit" />
					<label>
						<input type="checkbox" name="create_backup_zip" id="create_backup_zip" value="1">
						Create a <em>tar.gz</em> archive of the entire file system accompanied by a <em>.sql</em> database file.
					</label>
					<?php if($enable_ftp): ?>
					<label>&not;
						<input type="checkbox" name="ftp_backup" id="ftp_backup" value="1" disabled="disabled">
						FTP to remote server.
					</label>
					<?php 
					endif; ?>

					<label>
						<input type="checkbox" name="overwrite_demo_with_live" value="1" data-warning="<?php echo $overwrite_demo_warning;?>">
						Overwrite the Demo Site's file system and database with the Live file system and database.
					</label>
				</fieldset>
				<!-- <p class="indeterminate-progressbar"></p> -->
				<p id="percent_progressbar"></p>
				<?php submit_button('Perform Tasks'); ?>
			</form>
		</div>

	</div>

</div>