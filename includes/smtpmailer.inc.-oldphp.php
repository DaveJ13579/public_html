<?php
// PHPMailer email function
function smtpmail($to, $subject, $message, $from) {
global $piercecty; 
if($_SERVER["SERVER_ADDR"] == "127.0.0.1") {$port = "25"; }// local server
 else {$port = "465"; }// production server 587? 465?(ssl)
set_include_path('/public_html/includes'); // adds the includes folder to the include path
//require_once 'class.phpmailer.php'; // this is in the includes folder. 
//require_once 'class.smtp.php'; // this is in the includes folder.
require_once 'PHPMailer.php'; // this is in the includes folder. 
require_once 'SMTP.php'; // this is in the includes folder.
// echo "$port $to $from<br />"; exit;
// add the unsubscribe footer
$message.="\n\n\n\nIf you do not want to receive email from the Pierce County Gleaning Project, send a note to info@piercecountygleaningproject.org and your email address will be removed from our list.";
// echo '<br />'.$message;exit; 
// look up the password 
$smtpq="select email, password from smtplogins where email='$from'";
$rsSmtp=mysqli_query($piercecty, $smtpq) or die(mysqli_error($piercecty));
if(!($rsSmtp && mysqli_num_rows($rsSmtp))) { // not in table so use default email address
	$smtpq="select email, password from smtplogins where email='info@piercecountygleaningproject.org'";
	$rsSmtp=mysqli_query($piercecty, $smtpq) or die(mysqli_error($piercecty));
	 } 
//echo mysqli_num_rows($rsSmtp).'<br />';exit;
if($rsSmtp && mysqli_num_rows($rsSmtp)){ // found a username and password
$row=mysqli_fetch_assoc($rsSmtp);
$password=$row['password']; 
$from=$row['email'];
echo "$port $to $from $password<br />"; exit;
	
$mail = new PHPMailer;
$mail->CharSet = 'UTF-8';
$mail->Encoding = 'base64';
$mail->SMTPDebug  = true; // enables SMTP debug information (for testing)
                       // 1 = errors and messages
                       // 2 = messages only    
					   // 3 = // Enable verbose debug output
//$mail->Host = "localhost";
$mail->isSMTP();
$mail->Host = 'mail.piercecountygleaningproject.org';  // Specify main SMTP server 
$mail->Port = $port;                    // TCP port to connect to localhost
$mail->SMTPAuth = true;                 // Enable SMTP authentication
$mail->Username = $from;  				// SMTP username
$mail->Password = $password;            // SMTP password
$mail->SMTPSecure = 'ssl';            // Enable TLS encryption, `ssl` also accepted
$mail->setFrom($from);
$mail->addAddress($to);				    // Add a recipient
$mail->addReplyTo($from);
//$mail->addCC('cc@example.com');
if($attach<>'') $mail->addAttachment($attach);         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML($html);                                  // Set email format to HTML
$mail->Subject = $subject;
$mail->Body    = $message;
//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    $msg='Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
	} else { $msg='Message has been sent';}
} // end of login found
//echo "<br /><br />$msg<br /><br />";
return $msg;
} // end of function
?>