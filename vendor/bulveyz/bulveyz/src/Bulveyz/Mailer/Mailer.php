<?php

namespace Bulveyz\Mailer;

use PHPMailer\PHPMailer\PHPMailer;

/*
 * Mailer Class
 *
 * Simplifies work with the PhpMailer class
 */

class Mailer
{
	public static $mail;

	public static function smtpStart(Array $settings = [])
	{
    self::$mail = new PHPMailer();        
    
    if (isset($settings['debug'])) {
    	self::$mail->SMTPDebug = $settings['debug'];
    } else {
    	self::$mail->SMTPDebug = 0;
    }                            

    self::$mail->isSMTP();

    if (isset($settings['smtpAuth'])) {
    	self::$mail->SMTPAuth = boolval($settings['smtpAuth']);
    } else {
    	self::$mail->SMTPAuth = true;
    }             

    self::$mail->Host = getenv('SMTP_SERVER');

    self::$mail->Username = getenv('SMTP_USER_NAME');                 

    self::$mail->Password = getenv('SMTP_PASSWORD');                          

    self::$mail->SMTPSecure = getenv('SMTP_SECURE');                          
	}  
}