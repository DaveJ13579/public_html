<?php
function emailer($IDpicker, $fname, $email, $harvest, $switch, $goto, $seats) { 
global $piercecty;
// construct date and time string
$datequery="select date_format(h_date,'%M %e, %Y')  as harvday, time_format(h_time,'%l:%i %p') as harvtime from harvests where ID_harvest=$harvest";
$rsHarvday=mysqli_query($piercecty,$datequery) or die(mysqli_error($piercecty));
$harvdayrow=mysqli_fetch_assoc($rsHarvday);

$goto = 'http://www.piercecountygleaningproject.org/'.$goto; // adds on domain to relative link passed into the function
if($switch=='normal') {
	$subject = "Pierce County Gleaning Project roster status";
	$cancelgoto="http://www.piercecountygleaningproject.org/cancel.php?ID=".encode($IDpicker)."&h=".encode($harvest);
	$historygoto="http://www.piercecountygleaningproject.org/volunteer.php";
	$message = 'Hello '.$fname.','."\n\n".'You have signed up for a gleaning trip sponsored by Pierce County Gleaning Project on '.$harvdayrow['harvday'].' at '.$harvdayrow['harvtime'].'. Go to this web page for details: '."\n".$goto.'.';
	$message.="\n\n".'If you find that you cannot attend and want to cancel this sign up, it may allow someone else to take your place. Go to this page to cancel your sign up: '."\n".$cancelgoto;
	$message.="\n\n".'You can check your attendance history, and verify your signup for this gleaning trip, any time at this web page: '."\n".$historygoto; 
	$message.="\n\n".'Pierce County Gleaning Project';
    if($email<>'') 	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");

	// echo $email."<br />". $headers."<br />".$message;
} // end of switch normal

if($switch=='waiting') {
	$subject = "Pierce County Gleaning Project roster status";
	$confirmgoto="http://www.piercecountygleaningproject.org/confirm.php?ID=".encode($IDpicker)."&s=".$seats."&h=".encode($harvest);
	$message = 'Hello '.$fname.','."\n\n".'You have asked to be added to the waiting list of a gleaning trip sponsored by Pierce County Gleaning Project on '.$harvdayrow['harvday'].' at '.$harvdayrow['harvtime'].'. ';
	$message.= 'To get on the roster, you must confirm this request by going to the website. Just click on the link below to be ';
	$message.= 'added to the waiting list:'."\n\n";
	$message.= $confirmgoto;
	$message.="\n\n".'Pierce County Gleaning Project';
    if($email<>'') 	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");
	// echo $email."<br />". $headers."<br />".$message;
} // end of switch waiting

if($switch=='promote') {
	$thanksgoto ="http://www.piercecountygleaningproject.org/hthank.php?pt=".encode($IDpicker)."&ht=".encode($harvest); 
	$subject = "Pierce County Gleaning Project roster status";
	$cancelgoto="http://www.piercecountygleaningproject.org/cancel.php?ID=".encode($IDpicker)."&h=".encode($harvest); 
	$historygoto="http://www.piercecountygleaningproject.org/volunteer.php";
	$message = 'Hello '.$fname.','."\n\n".'You have been added to the roster of the gleaning trip on '.$harvdayrow['harvday'].' at '.$harvdayrow['harvtime'].' that you were on the waiting list for. Go to this web page for details: '."\n".$thanksgoto.'.';
	$message.="\n\n".'If you find that you cannot attend and want to cancel this sign up, it may allow someone else to take your place. Go to this page to cancel your sign up: '."\n".$cancelgoto;
	$message.="\n\n".'You can check your attendance history, and verify your signup for this gleaning trip, any time at this web page: '."\n".$historygoto; 
	$message.="\n\n".'Pierce County Gleaning Project';
	if($email<>'') 	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");
} // end of switch promote

if($switch=='demote') {
	$subject = "Pierce County Gleaning Project roster status";
	$statuslink="http://www.piercecountygleaningproject.org/waitstatus.php?ID=".encode($IDpicker);
	$message = 'Hello '.$fname.','."\n\n".'We are sorry, but the carpool seat that was reserved for you is no longer available because the driver cannot attend.';
	$message .=' You have been placed on the waiting list.';
	$message .= 'You will be sent an email if another carpool seat is available and you are moved back to the actual roster. ';
	$message .= 'That email will have the address and directions for the harvest. Because you are now on the waiting list, ';
	$message .= 'you do not need to check the Harvests page. You should:'."\n\n";
	$message .= '- Check your email before the glean to see if you have been added to the actual roster'."\n\n";
	$message .= '- Check the following web page to see your position on the waiting list:'."\n\n";
	$message .= $statuslink;
	$message .="\n\n".'Pierce County Gleaning Project';
    if($email<>'') 	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");

} // end of switch demote
} // end of emailer function
?>