<div class="wrap">
    <h2><?php _e( 'WP Timesheets Settings' , 'wp-timesheets' )?></h2>
	
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">

			<div id="post-body-content">
				<form method="post" action="options.php"> 
					<?php @settings_fields('wpts_options'); ?>

					<?php @do_settings_sections('wp_timesheets_settings'); ?>

					<?php @submit_button(); ?>
				</form>
			</div>

			<div id="postbox-container-1" class="postbox-container">
				<div id="formatdiv" class="postbox ">
					<h3 class="hndle"><span><?php _e( 'About WP Timesheets' , 'wp-timesheets' )?></span></h3>
					<div class="inside">
						<p>WP Timesheets is a simple timesheet app within WordPress to manage time data of your WordPress users.</p>
						<p>You may use the <a href="https://wordpress.org/support/plugin/wp-timesheets" target="_blank">support forum</a> for bugs or feature requests, 
							but as this is a niche use-case plugin, all development is only done on paid basis. </p>
						<p>You may reach out to me by <a href="mailto:akshay.raje+wptimesheets@gmail.com">email</a> or on <a href="https://twitter.com/akshayraje" target="_blank">Twitter</a></p>
					</div>
				</div>
			</div>

		</div> <!-- #post-body -->
	</div> <!-- #poststuff -->	
	
</div>