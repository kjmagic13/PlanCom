<div class="wrap">

	<?php screen_icon('themes'); ?> <h2><?php echo $title; ?></h2>

	<div class="tabs">
		<ul>
			<li><a href="#backups">Backups</a></li>
			<li><a href="#restore">Restore Database</a></li>
		</ul>

		<div id="backups">

		<h3>The <em>tar.gz</em> files below contain the entire WordPress file system accompanied by a SQL database dump.</h3>

			<!-- <p id="feedback">
				<span>You've selected:</span> <span id="select-result">none</span>.
			</p> -->
			<p>
				<button>Delete Selected</button>
				<button>Download Selected</button>
			</p>
			<ol id="selectable">
				<?php echo $backup_list; ?>
			</ol>
		</div>
		<div id="restore">
			<h1>WARNING</h1>
		</div>
	</div>

</div>