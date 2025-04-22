<?php 
// from a $jobstr stored in roster table, return string of jobnames chosen
function jobnames($jobstr) {
global $piercecty;
$jobnames='';
$jobsq="select jobID, jobname from jobs order by jobID";
$rsJobs=mysqli_query($piercecty,$jobsq);
while($row=mysqli_fetch_assoc($rsJobs)){
	if(substr($jobstr,$row['jobID']-1,1)=='1') $jobnames.=$row['jobname'].'-';
	}
return $jobnames;
}
// from a $jobstr stored in roster table, return array of job texts chosen
function jobtexts($jobstr) {
global $piercecty;
$jobtexts=array();
$jobsq="select jobID, jobtext from jobs order by jobID";
$rsJobs=mysqli_query($piercecty,$jobsq);
while($row=mysqli_fetch_assoc($rsJobs)){
	if(substr($jobstr,$row['jobID']-1,1)=='1') $jobtexts[]=$row['jobtext'];
	}
return $jobtexts;
}
?>