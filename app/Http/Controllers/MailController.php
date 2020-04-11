<?php
/**
 * Created by PhpStorm.
 * User: Jream
 * Date: 12/9/2019
 * Time: 10:47 PM
 */
namespace App\Http\Controllers;

use PHPMailer\PHPMailer\PHPMailer;

class MailController extends Controller
{
	private $mail;
	public function __construct() {
		$this->mail = new PHPMailer();
		$this->mail->IsSMTP();
		$this->mail->Host = get_option('smtp_host');
		$this->mail->SMTPAuth = true;
		$this->mail->Username = get_option('smtp_username');
		$this->mail->Password = get_option('smtp_password');
		$this->mail->SMTPSecure = get_option('type_encrytion');
		$this->mail->Port = get_option('smtp_port');
	}

	public function setEmailFrom($from, $from_label = ''){
		$this->mail->setFrom($from, $from_label);
	}

	public function setEmailTo($to){
		$this->mail->AddAddress($to);
	}

	public function setReplyTo($reply_to){
		$this->mail->addReplyTo($reply_to);
	}

	public function sendMail($subject, $body){
		$this->mail->Subject = $subject;
		$this->mail->Body    = $body;
		$this->mail->WordWrap = 50;
		$this->mail->IsHTML(true);
		return $this->mail->send();
	}
}