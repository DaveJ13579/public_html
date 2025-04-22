<?php
// PHPMailer email function
function smtpmail($to, $subject, $message, $from) {
set_include_path('/public_html/includes'); // adds the includes folder to the include path
//require_once 'PHPMailer.php'; // this is in the includes folder. 
require_once 'class.phpmailer.php'; // this is in the includes folder. 

$message.="\n\n\n\nIf you do not want to receive email from the Pierce County Gleaning Project, send a note to info@piercecountygleaningproject.org and your email address will be removed from our list.";

$mail = new phpmailer;
$mail->Host = 'mail.piercecountygleaningproject.org';  
$mail->setFrom($from);
$mail->addAddress($to);				    
$mail->addReplyTo($from);
$mail->Subject = $subject;
$mail->Body = $message;
if(!$mail->send()) {
    $msg='Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
	} else { $msg='Message has been sent';}
return $msg;
} // end of function
?>