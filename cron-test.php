<?php
// site maintenance. shortens various database tables daily 
require_once('Connections/piercecty.php');  
require_once('includes/dencode.inc.php'); 
require_once('includes/smtpmailer.inc.php');

echo "Pierce County Gleaning Project cron job completed at ".date("h:i:sa");
?>