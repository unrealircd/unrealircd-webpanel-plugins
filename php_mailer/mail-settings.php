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
foreach($_POST as $key => $value)
{
	if ($key == "fromEmail")
		$config['smtp']["username"];
	elseif ($key == "smtpPass")
		$config['smpt']['smtpPass'];
	elseif ($key == "fromName")
		$config['smpt']['from_name'];
	elseif ($key == "smtpHost")
		$config['smtp']['host'];
	elseif ($key == "port")
		$config['smpt']['port'];
}
?>


<h4>Mail Settings</h4>
<div class="card card-body mb-3" style="max-width:50%">
	<form method="post" action="mail-settings.php" autocomplete="off" enctype="multipart/form-data">
		<div class="form-group row">
			<label for="fromEmail" class="col-sm-2 col-form-label">Email</label>
			<div class="col-sm-10">
				<input type="email" class="form-control" id="fromEmail" name="fromEmail" placeholder="yourname@example.com" value="<?php echo get_config("smtp::username") ?>">
			</div>
		</div>
		<div class="form-group row">
			<label for="smtpPass" class="col-sm-2 col-form-label">Email Password</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" id="smtpPass" name="smtpPass" value="<?php echo get_config("smtp::password") ?>">
			</div>
		</div>
		<div class="form-group row">
			<label for="fromName" class="col-sm-2 col-form-label">From Name</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="fromName" name="fromName" value="<?php echo get_config("smtp::from_name") ?>">
			</div>
		</div>
		<div class="form-group row">
			<label for="smtpHost" class="col-sm-2 col-form-label">SMTP Host</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="smtpHost" name="smtpHost" value="<?php echo get_config("smtp::host") ?>">
			</div>
		</div>
		<div class="form-group row">
			<label for="smtpPort" class="col-sm-2 col-form-label">SMTP Port</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="smtpPort" name="smtpPort" value="<?php echo get_config("smtp::port") ?>">
			</div>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="smtpEnc" id="radioOne" value="TLS" checked>
			<label class="form-check-label" for="radioOne">
			TLS Encryption
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="smtpEnc" id="radioTwo" value="SSL">
			<label class="form-check-label" for="radioTwo">
				SSL Encryption
			</label>
		</div>


		<div class="float-right">
			<input type="submit" class="btn btn-primary" value="Submit">
		</div>
	</form>
</div>
<?php
require_once "../../inc/footer.php";
