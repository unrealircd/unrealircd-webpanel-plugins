<?php
/**
  @title PHP Mailer
  @author Valware
  @contact valerie@valware.co.uk
  @description Provides support for PHPMailer() to send emails you care about
  @version 1.1.1
  @tested 0.9
  @minver 0.9
  @icon https://w7.pngwing.com/pngs/78/575/png-transparent-phpmailer-hd-logo.png
  @maxver *
  @license GPLv3
*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class php_mailer
{
	public $name = "php_mailer";
	public $author = "Valware";
	public $version = "1.1.1";
	public $description = "Send mail using PHPMailer()";
	public $email = "v.a.pond@outlook.com";

	function __construct()
	{
		Hook::func(HOOKTYPE_USER_LOGIN, 'php_mailer::user_login_notif');
		Hook::func(HOOKTYPE_USER_LOGIN_FAIL, 'php_mailer::user_login_fail_notif');
		Hook::func(HOOKTYPE_NAVBAR, 'php_mailer::navbar_hook');
	}

	public static function send_mail($to, $subject, $body)
	{
		$mail = new PHPMailer(true);
		try {
			//Server settings
			//$mail->SMTPDebug = 2;
			$mail->isSMTP();                                    // Send using SMTP
			$mail->Host       = get_config("smtp::host");       // Set the SMTP server to send through
			$mail->SMTPAuth   = true;                           // Enable SMTP authentication
			$mail->Username   = get_config("smtp::username");   // SMTP username
			$mail->Password = get_config("smtp::password");     // SMTP password
			$mail->SMTPSecure = get_config("smtp::encryption"); // Enable implicit TLS encryption
			$mail->Port       = get_config("smtp::port");       // TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

			//Recipients
			$mail->setFrom(get_config("smtp::username"), get_config("smtp::from_name"));
			$mail->addAddress($to['email'], $to['name']);       // Add a recipient

			//Content
			$mail->isHTML(true);                                // Set email format to HTML
			$mail->Subject = $subject;
			$mail->Body    = $body . "<br><br>Thank you for using UnrealIRCd!";
			$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			$mail->send();
		} catch (Exception $e) {
			die("Could not send mail:". $e);
		}
	}

	/**
	 *  Send a login notification to the admin (note-to-self)
	 * @param mixed $user
	 * @return void
	 */
	public static function user_login_notif($user)
	{
		self::send_mail(
			["email" => get_config("smtp::username"), "name" => get_config("smtp::from_name")],
			"New login to Unreal Admin Panel",
			"There was a new login to the admin panel.<br>User: \"$user->username\"<br>IP: \"".$_SERVER['REMOTE_ADDR']."\" (".$_SERVER['HTTP_CF_IPCOUNTRY'].")<br>".
			"User Agent: ".$_SERVER['HTTP_USER_AGENT']
		);

		if ($user->email)
			self::send_mail(
				["email" => $user->email, "name" => $user->first_name . " " . $user->last_name],
				"New login to your account",
				"Dear $user->first_name, <br><br>".
				"There was a new login to account: \"$user->username\"<br><br>".
				"Details:<br>".
				"IP: ".$_SERVER['REMOTE_ADDR']." (".$_SERVER['HTTP_CF_IPCOUNTRY'].")<br>".
				"User Agent: ".$_SERVER['HTTP_USER_AGENT']."<br><br>".
				"If this was not you, please contact your Panel Administrator."
			);
	}

	/**
	 * Send a notification about the failed attempt
	 */
	public static function user_login_fail_notif($user)
	{
		self::send_mail(
			["email" => get_config("smtp::username"), "name" => get_config("smtp::from_name")],
			"Failed login attempt - Unreal Admin Panel",
			"-- ADMIN NOTIFICATION --, <br><br>".
			"There was failed login attempt to account: \"$user->username\"<br><br>".
			"Details:<br>".
			"IP: ".$_SERVER['REMOTE_ADDR']." (".$_SERVER['HTTP_CF_IPCOUNTRY'].")<br>".
			"User Agent: ".$_SERVER['HTTP_USER_AGENT']."<br><br>"
		);
		$user = new PanelUser($user['login']);
		if ($user->email)
			self::send_mail(
				["email" => $user->email, "name" => $user->first_name . " " . $user->last_name],
				"Failed login attempt to your account",
				"Dear $user->first_name, <br><br>".
				"There was failed login attempt to your account: \"$user->username\"<br><br>".
				"Details:<br>".
				"IP: ".$_SERVER['REMOTE_ADDR']." (".$_SERVER['HTTP_CF_IPCOUNTRY'].")<br>".
				"User Agent: ".$_SERVER['HTTP_USER_AGENT']."<br><br>".
				"If this was not you, please contact your Panel Administrator."
			);
	}

	public static function navbar_hook(&$pages)
	{
		$page_name = "Mail";
		$page_link = "plugins/php_mailer/mail-settings.php";
		$pages["Settings"][$page_name] = ["script" => $page_link];
	}
}
