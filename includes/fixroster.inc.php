<?php
function fixroster($harvest, $piercecty) {

$done=false;
//compile wait list
$waitersq="select * from rosters where ID_harvest=$harvest and (status='waiting') order by regdate";
$rsWaiters=mysqli_query($piercecty,$waitersq) or die(mysqli_error($piercecty));

while(!$done){ // cycle through the roster promoting or demoting until no slots or no seats

// count seats	
$seatsq="select sum(seats) as seats from rosters where ID_harvest=$harvest and (status='signup' or status='leader' or status='assisted')";
$rsSeats=mysqli_query($piercecty,$seatsq) or die(mysqli_error($piercecty));
$seatsrow=mysqli_fetch_assoc($rsSeats);
$seatsavail=$seatsrow['seats'];

// count filled slots
$onrosterq="select count(ID_picker) as onroster from rosters where ID_harvest=$harvest and (status='leader' or status='signup' or status='assisted')";
$rsOnroster=mysqli_query($piercecty,$onrosterq) or die(mysqli_error($piercecty));
$onrosterrow=mysqli_fetch_assoc($rsOnroster);
$onroster=$onrosterrow['onroster'];

//get pick_num
$picknumq="select pick_num from harvests where ID_harvest=$harvest";
$rsPicknum=mysqli_query($piercecty, $picknumq) or die(mysqli_error($piercecty));
$picknumrow=mysqli_fetch_assoc($rsPicknum);
$picknum=$picknumrow['pick_num'];
$slots=$picknum-$onroster;

if($seatsavail<0) { //find the newest waitseat on actual roster
	$neederq="select rosters.ID_picker, pickers.fname, email,seats from rosters, pickers where rosters.ID_picker=pickers.ID_picker and ID_harvest=$harvest and (status='signup' or status='leader' or status='assisted') and seats<0 order by rosters.regdate desc";
	$rsNeeder=mysqli_query($piercecty,$neederq) or die(mysqli_error($piercecty));
	$neederrow=mysqli_fetch_assoc($rsNeeder);
	$needer=$neederrow['ID_picker'];
	$fname=$neederrow['fname'];
	$email=$neederrow['email'];
	$seats=$neederrow['seats'];
	
// demote to waitseat status
$demoteq="update rosters set status='waiting' where ID_harvest=$harvest and ID_picker=$needer";
$rsDemote=mysqli_query($piercecty,$demoteq) or die(mysqli_error($piercecty));
//	emailer($needer);
	$switch='demote';
	$waitstatus="waitstatus.php?ID=".encode($needer);
	emailer($needer, $fname, $email, $harvest, $switch, $waitstatus,$seats);
	++$seatsavail;
	if($slots<$picknum) ++$slots;
	} // end of if negative seats available

if(!$slots) break;

// fetch a waiter
$waitrow=mysqli_fetch_assoc($rsWaiters);
$waiter=$waitrow['ID_picker'];
if(!$waiter) break;
$seats=$waitrow['seats'];
if($seats<0 and $seatsavail<1) continue;

//promote the waiter
$promoteq="update rosters set status='signup' where ID_harvest=$harvest and ID_picker=$waiter";
$rsPromote=mysqli_query($piercecty, $promoteq) or die(mysqli_error($piercecty));
//emailer($waiter);
	$waiterq="select fname, email from pickers where ID_picker=$waiter";
	$rsWaiter=mysqli_query($piercecty,$waiterq) or die(mysqli_error($piercecty));
	$waiterrow=mysqli_fetch_assoc($rsWaiter);
	$fname=$waiterrow['fname'];
	$email=$waiterrow['email'];
	$switch='promote';
	$thanksgoto ="hthank.php?pt=".encode($waiter)."&ht=".encode($harvest); 
	emailer($waiter, $fname, $email, $harvest, $switch, $thanksgoto, $seats);

// recompile wait list
$waitersq="select * from rosters where ID_harvest=$harvest and (status='waiting') order by regdate";
$rsWaiters=mysqli_query($piercecty,$waitersq) or die(mysqli_error($piercecty));

} // end of while(!done)
} // end of fixroster function
?>