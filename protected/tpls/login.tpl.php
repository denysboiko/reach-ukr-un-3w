<?php if(!isset($this_is_page) || $this_is_page != true) { exit(0); } ?>
<?php $t->extend('tpls/layouts/base.tpl.php') ?>

<?php
	$template_field = new Template('tpls/parts/form_field.tpl.php');
?>

<?php $t->start('title') ?>
	Log in
<?php $t->end() ?>

<?php $t->start('content') ?>

	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<form method="post" class="form-horizontal">
	
				<?php if(!$form->is_valid) : ?>
					<div class="form-group has-error">
						<?php if(empty($form->errors)) : ?>
							<span class="help-block col-md-10 col-md-offset-2">Form invalid</span>
						<?php endif ?>

						<?php foreach($form->errors as $error) : ?>
							<span class="help-block col-md-10 col-md-offset-2"><?= $error ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif ?>

				<?php $form->email->template = $template_field ?>
				<?= $form->email->render() ?>

				<?php $form->password->template = $template_field ?>
				<?= $form->password->render() ?>

				<div class="form-group text-center">
					<input type="submit" class="btn btn-primary btn-lg" value="Login">
				</div>
			</form>

			<hr>
			<p class="text-center">
				<a href="./registration.php">Registration</a>
			</p>
			<hr>
			<p class="text-center">
				Forgot password?
				<br>
				Go to <a href="./passwdrecovery.php">password recovery</a>
			</p>
		</div>
	</div>


<?php $t->end() ?>