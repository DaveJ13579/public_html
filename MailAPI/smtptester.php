<?php
require_once('../Connections/piercecty.php');
require_once('../includes/smtpmailer.inc.php');
 $from = "info@piercecountygleaningproject.org";
 $to = "dyates@gleanweb.org";
 $subject = "Test smtp";
 $message = "Hi,\n\nHow are you?";
 echo 'Sending message<br />';
echo smtpmail($to, $subject, $message, $from);
 echo '<br />returned from function';
 ?>