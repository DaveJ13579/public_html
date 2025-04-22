<?php
// site maintenance. shortens various database tables daily 
require_once('../Connections/piercecty.php');  
require_once('../includes/dencode.inc.php'); 
require_once('../includes/smtpmailer.inc.php');
// truncate mailarchive to last month
$prevdate= date('Y-m-d', strtotime("-30 days"));
$query="delete from mailarchive where whensent<'$prevdate'";
$rsQuery=mysqli_query($piercecty, $query);
// truncate loginlog to last week
$prevdate= date('Y-m-d', strtotime("-7 days"));
$query="delete from loginlog where datein<'$prevdate'";
$rsQuery=mysqli_query($piercecty, $query);
// truncate pageslog to last four days
$prevdate= date('Y-m-d', strtotime("-3 days"));
$query="delete from pageslog where whenview<'$prevdate'";
$rsQuery=mysqli_query($piercecty, $query);
// truncate hits to last 14 days
$prevdate= date('Y-m-d', strtotime("-14 days"));
$query="delete from hits where whenhit<'$prevdate'";
$rsQuery=mysqli_query($piercecty, $query);
// delete past harvest 'waiting' status
$todaydate= date('Y-m-d');
$query="delete rosters from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and h_date<'$todaydate' and rosters.status='waiting'";
$rsQuery=mysqli_query($piercecty, $query);
// close past harvests
$query="update harvests set status='closed' where h_date<'$todaydate'";
$rsQuery=mysqli_query($piercecty, $query);
// update maintenance date in store table
$query="update store set value='$todaydate' where name='maintenance'";
$rsQuery=mysqli_query($piercecty, $query);
// send update notices to those at exactly 12 months since contactdate
$prevdate= date('Y-m-d', strtotime("-12 months"));
$query="select ID_picker, fname, email from pickers where substring(contactdate,1,10)='$prevdate'";
$rsQuery=mysqli_query($piercecty, $query);
if(mysqli_num_rows($rsQuery)) {
while ($row=mysqli_fetch_assoc($rsQuery)) {
	$fname=$row['fname'];
	$ID_picker=$row['ID_picker'];
	$eID=encode($ID_picker);
	$email=$row['email'];
	if ($email<>'') {
		$subject = "Pierce County Gleaning Project registration renewal";
		$updatelink="http://www.piercecountygleaningproject.org/Pickers/ContactUpdate.php?ID=".$eID;
		$gleanslink="http://www.piercecountygleaningproject.org/harvestlist.php";
		$treeslink="http://www.piercecountygleaningproject.org/crop_registration.php";
		$message = 'Hello '.$fname.','."\n\n".'It has been about a year since you registered as a volunteer with Pierce County Gleaning Project ';
		$message .= 'or since you signed up for a harvest. If you wish to keep your email address and contact information active with us, ';
		$message .= 'please take a minute to update your registration at this web page:'."\n\n";
		$message .= $updatelink."\n\n".'Even if all of your contact information is correct, ';
		$message .= 'clicking on the \'Save changes\' button will renew your registration date.';
		$message .= "\n\n".'Thanks for helping us rescue nature\'s bounty for the benefit of our community! Hope to see you in a field or up a tree sometime very soon.';
		$message.="\n\n".'Common links:';
		$message.="\n".'Calendar of gleans and events: '.$gleanslink;
		$message.="\n".'Register your tree (donate fruit/veg): '.$treeslink;
		$message.="\n\n".'Pierce County Gleaning Project';
		smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");
	} // end of email<>''
} // end of if found picker
} // end of all emails

// backup database
$filedate=date('l');
$filename='../database-backups/database-'.$filedate.'.sql';
$command="/usr/bin/mysqldump  --host=$hostname_piercecty --user=$username_piercecty --password=$password_piercecty  $database_piercecty > $filename --no-tablespaces";
system($command);

//update totals file
$query="select year(h_date) as year, sum(totwgt) as pounds from harvests group by year(h_date) order by year(h_date)";
$rsQuery=mysqli_query($piercecty,$query);
$txt='';
while ($row=mysqli_fetch_assoc($rsQuery)) {
extract($row);
$txt.="$year,$pounds\n";
}
$myfile = fopen("../Utilities/yeartotals.txt", "w") or die("Unable to open file!");
fwrite($myfile, $txt);
fclose($myfile);
echo "Pierce County Gleaning Project cron job completed.";
?>