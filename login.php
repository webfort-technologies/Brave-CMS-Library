<?php
/*

Preamble      		: To Store Activity Log. 
Creation Date 		: 2015/02/19
Authors 			: Yogesh Yadav
Last Modified date  : 2015/02/19


	Example of how to user the class starts here
	
	$config = array();
	$config['email_id_or_username'] = "";

    $log_ob = new login($config);




*/
//print_r($_REQUEST);

class login
	{
	var $email_id_or_username="";

	
	// INITIALIZER
	function initialize($params = array())
	{
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}
	}
	

	
	// CONSTRUCTOR
	function __construct($params=array())
	{
		if (count($params) > 0)
		{
			$this->initialize($params);
		
		}	

		
		
	}
	
	function check_attempts(){
		$email_id_or_username=$this->email_id_or_username;
		$fetch_record_sql=mysql_query("SELECT block_until_time FROM vt_users where email='$email_id_or_username'");
		$fetch_record_data=mysql_fetch_array($fetch_record_sql);
		if($fetch_record_data['block_until_time']=='0000-00-00 00:00:00'){
			$this->Save();
			$this->getAttempts();
		}
	}

	//Insert value  in login_attempts
	function Save()
	{
		$email_id_or_username=$this->email_id_or_username;
		mysql_query("INSERT INTO login_attempts SET email_id_username='$email_id_or_username', ip='".$_SERVER['REMOTE_ADDR']."', created_on='".date("Y-m-d H:i:s"). "', modified_on='".date("Y-m-d H:i:s"). "'");
	}

	//Fetch No of attempts in last 15 min minutes
	function getAttempts()
	{
		$email_id_or_username=$this->email_id_or_username;
		$number_of_attempts = mysql_query("SELECT * FROM login_attempts where email_id_username='$email_id_or_username' and  ip='".$_SERVER['REMOTE_ADDR']."' and created_on >NOW() - INTERVAL 15 MINUTE");
		if(mysql_num_rows($number_of_attempts)>7){
			$unlock_time=date('Y-m-d H:i:s', strtotime('+30 minutes', strtotime(date("Y-m-d H:i:s"))));
			mysql_query("UPDATE vt_users SET block_until_time='".$unlock_time. "' where email='$email_id_or_username'");
   			$message=array('text'=>'You have Attempting 7 wrong password in past 15 minutes, you are block for 30 minutes.' ,'type'=>'error');
			$_SESSION['message'][]=$message;
		}
	}

	function unlock()
	{
		$email_id_or_username=$this->email_id_or_username;

		$fetch_record_sql=mysql_query("SELECT block_until_time FROM vt_users where email='$email_id_or_username'");
		$fetch_record_data=mysql_fetch_array($fetch_record_sql);
		if($fetch_record_data['block_until_time']!='0000-00-00 00:00:00'){
			if(date("Y-m-d H:i:s")>=$fetch_record_data['block_until_time'])
			{
				mysql_query("UPDATE vt_users SET block_until_time='0000-00-00 00:00:00' where email='$email_id_or_username'");
			}else{
				
				$date1 = strtotime($fetch_record_data['block_until_time']);
				$date2 = time();
				$subTime = $date1 - $date2;
				$m = ($subTime/60)%60;
				
				$message=array('text'=>"Your Account has been blocked due to filed Attempts. Please retry after ".$m." minutes"  ,'type'=>'error');
				$_SESSION['message'][]=$message;
			}
		}
	}



	
}// Class Ends here 
	


?>