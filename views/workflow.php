<div class="wrap">

	<?php screen_icon('themes'); ?> <h2><?php echo $title; ?></h2>

	<div class="tabs">
		<ul>
			<li><a href="#wysiwyg">WYSIWYG</a></li>
		</ul>

		<div id="wysiwyg">

			<h3>WYSIWYG Options</h3>

			<div id="role_tabs" class="tabs">
				<ul>
					<?php global $wp_roles; foreach($wp_roles->roles as $name => $role) : if( @$role['capabilities']['edit_posts'] ) : ?>
					<li>
						<a href="#<?php echo $name; ?>"><?php echo $name; ?></a>
					</li>
					<?php
					endif; endforeach; ?>
				</ul>

				<form>
					<?php /*foreach role*/ foreach( $wysiwyg as $role => $rows ) : ?>
					<div id="<?php echo $role; ?>">
						<div class="mce_container">
							<?php /*foreach row*/ foreach( $rows as $row => $icons ) : if( $row == 'formats' ){ continue; } ?>
							<ul class="<?php echo $row; ?> sortable connectedSortable" data-role="<?php echo $role; ?>">
								<?php /*foreach icon*/ foreach( $icons as $key => $icon ) : ?>
								<li class="plugin_icon mce_icon_<?php echo $icon; ?>" title="<?php echo $icon; ?>"></li>
								<?php
								endforeach; ?>
							</ul>
							<?php
							endforeach; ?>

							<!-- formats -->
							<p>
								<strong>Formats</strong>
								<ul class="formats" data-role="<?php echo $role; ?>">
									<li><label><input type="checkbox" <?php if( in_array('p', $rows['formats'] ) ){ echo 'checked'; }; ?> value="true" name="p">
										Paragraph
									</label></li>
									<li><label><input type="checkbox" <?php if( in_array('pre', $rows['formats'] ) ){ echo 'checked'; }; ?> value="true" name="pre">
										Preformatted
									</label></li>
									<li><label><input type="checkbox" <?php if( in_array('h1', $rows['formats'] ) ){ echo 'checked'; }; ?> value="true" name="h1">
										Heading 1
									</label></li>
									<li><label><input type="checkbox" <?php if( in_array('h2', $rows['formats'] ) ){ echo 'checked'; }; ?> value="true" name="h2">
										Heading 2
									</label></li>
									<li><label><input type="checkbox" <?php if( in_array('h3', $rows['formats'] ) ){ echo 'checked'; }; ?> value="true" name="h3">
										Heading 3
									</label></li>
									<li><label><input type="checkbox" <?php if( in_array('h4', $rows['formats'] ) ){ echo 'checked'; }; ?> value="true" name="h4">
										Heading 4
									</label></li>
									<li><label><input type="checkbox" <?php if( in_array('h5', $rows['formats'] ) ){ echo 'checked'; }; ?> value="true" name="h5">
										Heading 5
									</label></li>
									<li><label><input type="checkbox" <?php if( in_array('h6', $rows['formats'] ) ){ echo 'checked'; }; ?> value="true" name="h6">
										Heading 6
									</label></li>
								</ul>
							</p>

							<?php /* generate row if row is empty */ $emptys =  array('row1','row2','library'); foreach( $emptys as $row ) : if( !isset($rows[$row]) ) :?>
							<ul class="<?php echo $row; ?> sortable connectedSortable" data-role="<?php echo $role; ?>"><ul>
							<?php endif;
							endforeach; ?>

						</div>
						<button class="reset_icons" data-role="<?php echo $role; ?>">Reset <?php echo $role; ?></button>
					</div>

					<?php
					endforeach; ?>
				</form>

			</div>

			<button id="update_icons">Update All</button>
			<button class="reset_icons" data-role="all">Reset All</button>

		</div>
	</div>

</div>