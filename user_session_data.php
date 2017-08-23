<?php 
/*

Creation Date 		: 2013/02/27


Description: Class include the functions related to user session related actions.


**********************************************************************
	

*/



class userSessionData{			

	function destroyUserSession()
	{

		session_name("ramaraju");
		session_start();
		if(isset($_SESSION))
		{  
			$_SESSION=array();
			session_unset();
			session_destroy();		 
		}

		session_start();
		$message=array('text'=>'You Have Been Successfully logged out','type'=>'success');
		$_SESSION['message']=array();
		$_SESSION['message'][]=$message;
	}

	function checkUserSession()
	{

		if(isset($_SESSION['userinfo']['user_id']) && !empty($_SESSION['userinfo']['user_id']))
		{
			return true;
		}
		else
		{
			return false;
		}

	}

}
?>