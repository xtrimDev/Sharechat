<?php /** @noinspection ALL */

/**
 * This file used to create pages for admin panel.
 */

/** Loading all required files. */ 
require_once "config.php";
require_once "url.php";
require_once ('PHP_mailer/PHPMailerAutoload.php');

/** This manages the page content. */
class simple_male_transfter_protocol
{
    public $to;
    public $subject;
    public $message;
    public $error = false;

    public function sent() {
        $mail = new PHPMailer(); 
        $mail->IsSMTP(); 
        $mail->SMTPAuth = true; 
        $mail->SMTPSecure = 'tls';
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587; 
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        //$mail->SMTPDebug = 2; 
        $mail->Username = SMTP_EMAIL;
        $mail->Password = SMTP_PASSWORD;
        $mail->SetFrom(SITE_NAME);
        $mail->Subject = $this->subject;
        $mail->Body = $this->message;
        $mail->AddAddress($this->to);
        $mail->SMTPOptions=array('ssl'=>array(
            'verify_peer'=>false,
            'verify_peer_name'=>false,
            'allow_self_signed'=>false
        ));
        
        if(!$mail->Send()) {
            $this->error = $mail->ErrorInfo;
            return false;
        }else{
            return true;
        }
    }
}