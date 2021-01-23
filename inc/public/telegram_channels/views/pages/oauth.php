<div class="widget-box-add-account">
	
	<div class="headline m-b-30">
		<div class="title fs-18 fw-5 text-info"><i class="far fa-plus-square"></i> <?php _e('Get access token')?></div>
		<div class="desc"><?php _e("Get and enter your Telegram access token to get profile")?></div>
	</div>


	<form class="actionForm" action="<?php _e( get_module_url('token') )?>" method="POST" data-redirect="<?php _e( get_module_url('index/general') )?>">
		<div class="form-group">
			<a href="https://telegram.me/botfather" target="_blank" class="btn btn-social btn-block"><i class="fas fa-external-link-square-alt"></i> <?php _e('Create Telegram bot')?></a>
		</div>

		<div class="form-group">
			<label for="access_token"><?php _e("Enter access token")?></label>
			<input type="text" class="form-control" id="access_token" name="access_token">
		</div>
		
		<button type="submit" class="btn btn-block btn-info m-t-15"><?php _e('Submit')?></button>
	</form>

	<ul class="list-group m-t-25">
		<li class="list-group-item active bg-info text-uppercase"><i class="far fa-question-circle"></i> <?php _e("How to get access token")?></li>
		<li class="list-group-item"><?php _e("Step 1: Click button Create Telegram bot") ?></li>
		<li class="list-group-item"><?php _e("Step 2: To create a new Telegram bot, send command /newbot.") ?></li>
		<li class="list-group-item"><?php _e("Step 3: Give the Telegram bot a friendly name.") ?></li>
		<li class="list-group-item"><?php _e("Step 4: Give the Telegram bot a unique username.") ?></li>
		<li class="list-group-item"><?php _e("Step 5: Copy the Telegram bot's access token.") ?></li>
		<li class="list-group-item"><?php _e("Step 6: Enter the Telegram bot's access token") ?></li>
	</ul>

</div>