<div class="content">
    <h1>Testing..</h1>
	<div>
	<?php if ($this->users) { ?>
		<?php foreach ($this->users as $user) { ?>
			<div><?php echo $user->user_id . ' ' . $user->user_name; ?></div>
		<?php } ?>
	<?php } ?>
	</div>
</div>
