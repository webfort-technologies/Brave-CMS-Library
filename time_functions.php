<?php 




function min_to_hour_small($mins) 
{ 
	$hours = 0 ;
	$minutes = $mins;
	$message="";
	if($mins>60)
	{
     	$hours = (int) ($mins/60);   
		$minutes = $mins%60;
		$message = $hours;
		if($hours>1)
		{
			$message.=" h ";
		}
		else
		{
			$message.=" h ";			
		}
		if($minutes>0)
		{
		 	$message .= $minutes. " m ";	
		}
	}
	else
	{
		if($minutes>0)
		{
			$message = $minutes. " m ";	
		}
	}
	return $message;
} 


function min_to_hour($mins) 
{ 
	$hours = 0 ;
	$minutes = $mins;
	$message="";
	if($mins>60)
	{
     	$hours = (int) ($mins/60);   
		$minutes = $mins%60;
		$message = $hours;
		if($hours>1)
		{
			$message.=" hours ";
		}
		else
		{
			$message.=" hour ";			
		}
		if($minutes>0)
		{
		 	$message .= $minutes. " minutes";	
		}
	}
	else
	{
		if($minutes>0)
		{
			$message = $minutes. " minutes";	
		}
	}
	return $message;
} 

function timeago($date) {
   $timestamp = strtotime($date);	
   
   $strTime = array("second", "minute", "hour", "day", "month", "year");
   $length = array("60","60","24","30","12","10");

   $currentTime = time();
   if($currentTime >= $timestamp) {
		$diff     = time()- $timestamp;
		for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
		$diff = $diff / $length[$i];
		}

		$diff = round($diff);
		return $diff . " " . $strTime[$i] . "(s) ago ";
   }
}

function readableDate($date)
{
	return date("F jS, Y", strtotime($date));
}
function readableDateTime($date)
{
	return date("F j, Y, g:i a", strtotime($date));
}
function readableDateTime2($date)
{
	return date("jS F Y g:i A", strtotime($date));
}

function readableTimeFromDAte($date)
{
	return date("g:i A", strtotime($date));
}
	
 ?>