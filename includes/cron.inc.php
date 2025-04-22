<?php
// site maintenance. shortens various database tables daily 
require_once('includes/dencode.inc.php'); 
require_once('includes/smtpmailer.inc.php');

$query="select value from store where name='maintenance'";
$rsQuery=mysqli_query($piercecty,$query); 
$row=mysqli_fetch_assoc($rsQuery);
$todaydate=date('Y-m-d');
if($row['value']<$todaydate) { // do all the maintenance

// truncate mailarchive to last month
$prevdate= date('Y-m-d', strtotime("-30 days"));
$query="delete from mailarchive where whensent<'$prevdate'";
$rsQuery=mysqli_query($piercecty, $query);
// truncate loginlog to last week
$prevdate= date('Y-m-d', strtotime("-7 days"));
$query="delete from loginlog where datein<'$prevdate'";
$rsQuery=mysqli_query($piercecty, $query);
// truncate pageslog to last seven days
$prevdate= date('Y-m-d G:i:s', strtotime("-7 days"));
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

// database backup
$filedate=date('l');
$filename='../database-backups/database-'.$filedate.'.sql';
$command="/usr/bin/mysqldump  --host=$hostname_piercecty --user=$username_piercecty --password=$password_piercecty $database_piercecty > $filename --no-tablespaces";
system($command);
	
//update totals file
$query="select year(h_date) as year, sum(totwgt) as pounds from harvests group by year(h_date) order by year(h_date)";
$rsQuery=mysqli_query($piercecty,$query);
$txt='';
while ($row=mysqli_fetch_assoc($rsQuery)) {
extract($row);
$txt.="$year,$pounds\n";
}

$err='none';
$myfile = fopen("Utilities/yeartotals.txt", "w");
if(!$myfile) $err=" yeartotals.txt could not be opened for writing. ";
fwrite($myfile, $txt);
fclose($myfile);

$email='piercecty@gleanweb.org';
$subject='Pierce County cron completed';
$message='Pierce County cron completed '.$txt."\n\nErrors: ".$err;
smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");

} // end of if last<today
?>