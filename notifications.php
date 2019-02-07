<?php 
/*
Purpose: When user opens the page, there is a need to show hime the result of last operation. 
for example when a record is deleted there should be a message confirming that what he requested. 
has been deleted. So to do that this class will .

1. Save the message that user wants into the session. 
2. Show the message. 
3. Delete the message after first show. 

So actually the message that is used here is short lived. It will show only one. 
and will auto hide. If user misses the message it would not matter much. 

*/

/* Instead of class we will use functions as it is more Convenient. */
function  create_notification($message)
{
	$_SESSION['notification'][] = $message;
}

function  show_notification()
{
	/* Checking to see if the Session Exists and has notifications. */
	if(isset($_SESSION) && isset($_SESSION['notification']) && count($_SESSION['notification'])>0)
	{
		foreach($_SESSION['notification'] as $notifcation_message)
		{
		?>
		<div class="notif-alerts alert alert-warning alert-dismissible fade show" role="alert">
		  <?php echo addslashes($notifcation_message); ?>
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		    <span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<?php
		} /* For Each */
		// Hide After 4 Seconds. 
		?>
		<script>
			setTimeout(function(){ 
				alert("Hello"); 
				$(".notif-alerts").hide(); 
			}, 4000);
		</script>	
		<?php
		/* Removing all the notifcations that the user has seen*/
		$_SESSION['notification'] = array();
	} /* IF */
} /* Function Show Notification*/
?>