<?php
require_once "../../inc/common.php";
require_once "../../inc/header.php";

if (!current_user_can(PERMISSION_MANAGE_PLUGINS))
{
	echo "<h4>Access denied</h4>";
	require_once "../../inc/footer.php";
	die();
}
var_dump($_POST);
?>


<h4>Mail Settings</h4>
<div class="card card-body mb-3">
	<form method="post" action="mail-settings.php" autocomplete="off" enctype="multipart/form-data">
		<div class="form-group row">
			<label for="fromEmail" class="col-sm-2 col-form-label">Email</label>
			<div class="col-sm-10">
				<input type="email" class="form-control" id="fromEmail">
			</div>
		</div>
		<div class="form-group row">
			<label for="smtpPass" class="col-sm-2 col-form-label">Email Password</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" id="smtpPass">
			</div>
		</div>
		<div class="form-group row">
			<label for="fromName" class="col-sm-2 col-form-label">From Name</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="fromName">
			</div>
		</div>
		<div class="form-group row">
			<label for="smtpHost" class="col-sm-2 col-form-label">SMTP Host</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="smtpHost">
			</div>
		</div>
		<div class="form-group row">
			<label for="smtpHost" class="col-sm-2 col-form-label">SMTP Port</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="smtpPort">
			</div>
		</div>
		<input type="submit" class="btn btn-primary" value="Submit" name="Submit">
	</form></div>
<?php
require_once "../../inc/footer.php";
