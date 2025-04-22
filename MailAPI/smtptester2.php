<?php
set_include_path('/public_html/includes'); // adds the includes folder to the include path
require 'PHPMailer.php';
require 'SMTP.php';

$mail = new PHPMailer();
$mail->isSMTP();
$mail->Host = 'mail.piercecountygleaningproject.org';
$mail->SMTPAuth = true;
$mail->Username = 'info@piercecountygleaningproject.org';
$mail->Password = 'Zaq1xsw2@';
$mail->setFrom('info@piercecountygleaningproject.org');
$mail->addAddress('dyates@gleanweb.org');
$mail->Subject = 'Here is the subject';
$mail->Body    = 'sent with smtptester2.php.';
$mail->send();
echo 'send completed';
?>
 ?>