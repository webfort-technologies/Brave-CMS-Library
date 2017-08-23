<?php 

class login_example
{
	var $loginTableName='vt_users';
	var $loginUserField='email';
	var $loginPasswordField='password';
	var $maximum_attempts='';
	var $time_maximum_attempts=''; // Time for which the attempts counts should be considered.

	var $username ='';
	var $password ='';


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

	// Funciton `Authenticate` will take `username` and `password` and reply
	// in a boolean value. 
	function authenticate($username, $password)
	{
		$this->username = $username;
		$this->password = $password;

		$login_check_query="SELECT * FROM $this->loginTableName where $this->loginUserField='".$username."' and $this->loginPasswordField='".$password."'";
		$login_check_rs= mysql_query($login_check_query) or die(mysql_error().$login_check_query);


		if(mysql_num_rows($login_check_rs)>0)
		{
			$row = mysql_fetch_array($login_check_rs);	

			//FETCH USER ROLE FROM JUNCTION TABLE
			$role_array         =	array();
			$user_role_sql	=	"select * from acl_junction_user_role where user_id=".$row['id']."";
			$user_role_rs	=	mysql_query($user_role_sql);
			while($user_role=	mysql_fetch_array($user_role_rs))
			{
				$role_array[]	= $user_role['role_id'];
			}

			$row['role_id']	=	implode(",", $role_array);

			if($row['is_enabled']==1)
			{
				//Unique string, this will use to chat
				$uniqueId = uniqid().strtotime(date('YmdHis'));

				$_SESSION['userinfo']['user_id'] = $row['id'];	
				$_SESSION['userinfo']['role_id'] = $row['role_id'];
				$_SESSION['userinfo']['firstname'] = $row['first_name'];
				$_SESSION['userinfo']['middle_name'] = $row['middle_name'];
				$_SESSION['userinfo']['lastname'] = $row['last_name'];
				$_SESSION['userinfo']['username'] = $row['username'];			
				$_SESSION['userinfo']['email'] = $row['email'];
				$_SESSION['userinfo']['login_role'] = $row['role_id'];

				if(in_array('10', $role_array))
				{
					$key_query = "select junc.unique_key from vt_users users
					left join chat_support_executives exec on exec.user_id = users.id
					left join chat_executives_organizations cexec on cexec.executive_id = exec.id
					left join chat_admin_n_org_junction junc on junc.organization_id= cexec.organization_id
					where users.id='".$row['id']."'";
					$key_rs = mysql_query($key_query);
					$key_row = mysql_fetch_assoc($key_rs);

					$_SESSION['userinfo']['uniqueid'] = $uniqueId;
					$_SESSION['userinfo']['instance_id'] = $key_row['unique_key'];
				}

				$token = rand(); 
				$name=$_SESSION['userinfo']['firstname'].' '.$_SESSION['userinfo']['middle_name'].' '.$_SESSION['userinfo']['lastname'];				

				$this->logSuccessfulLogin();

				$message=array('text'=> constant("LANG_LOGIN_SUCESSFULLY_LOGGED_IN") ,'type'=>'success');
				$_SESSION['message'][]=$message;

				if(isset($_SESSION['last_page_name']) && !empty($_SESSION['last_page_name']))
				{
					$page_name = $_SESSION['last_page_name']; 
					header("location:".$page_name); exit;
				}

				header("location:dashboard.php");
				exit;

			}
			elseif($row['token']!="")
			{
				//STORE USERINO IF LOGIN UNSUCCESSFUL
				 $this->logAccountDeactivate();

				$message=array('title'=> constant("LANG_LOGIN_ACCOUNT_NOT_ACCTIVATED_YET") ,'text'=> constant("LANG_LOGIN_PLEASE_FOLLOW_THE_INSTRUCTION")." <p><a href='resend_instructions.php?".$login_with."=".$_POST[$login_with]."&pass=".$_POST['password']."' class='btn btn-danger'>".constant("LANG_LOGIN_RE_SEND_INSTRUCTION")."</a></p>",'type'=>'error');
				$_SESSION['message'][]=$message;
			}
			else
			{
				//STORE USERINFO IF LOGIN UNSUCCESSFUL
				 $this->logAccountDeactivateByAdmin();

				$message=array('title'=> constant("LANG_LOGIN_ACCOUNT_DEACTIVATED") ,'text'=> constant("LANG_LOGIN_PLEASE_CONTACT_SITE_ADMINISTRATOR") ,'type'=>'error');
				$_SESSION['message'][]=$message;
			}
		}
		else
	    {	

	    	if(isset($_SESSION['login_attempt'])){	
	    		$_SESSION['login_attempt']+=1;  
	    	}else{
	    		$_SESSION['login_attempt']=1;
	    	}

	    	$this->logUsernamePasswordUnmatch();
			$message=array('title'=> $this->loginUserField .' '.constant("LANG_LOGIN_AND_PASSWORD_DO_NOT_MATCH"),'text'=> constant("LANG_LOGIN_PLAESE_CHECK_YOUR_LOGIN_DETAILS_AND_TRY_AGAIN") ,'type'=>'error');
			$_SESSION['message'][]=$message;

	    }


	}

	function logSuccessfulLogin()
	{

		$config = array();
		$config['user_id'] = $_SESSION['userinfo']['user_id'];
		$config['organization_id'] = "";
		$config['activity_type']="login_attempt";
		$config['log_message']="User ".$name." successfully logged in after ".$_SESSION['login_attempt']." Attempts.";
	    $log_ob = new activity_log($config);
	}

	function logAccountDeactivate()
	{
		$config = array();
		$config['user_id'] ="";
		$config['organization_id'] = "";
		$config['activity_type']="login_attempt_failed";
		$config['log_message']="Account Not activated Yet.";
	    $log_ob = new activity_log($config);

	}

	function logAccountDeactivateByAdmin()
	{
		$config = array();
		$config['user_id'] = "";
		$config['organization_id'] = "";
		$config['activity_type']="login_attempt_failed";
		$config['log_message']="Deactivate by the admin.";
	    $log_ob = new activity_log($config);

	}

	function logUsernamePasswordUnmatch()
	{
		$config = array();
		$config['user_id'] = "";
		$config['organization_id'] = "";
		$config['activity_type']="login_attempt_failed";
		$config['log_message']=$this->loginUserField." and password do not match.";
	    $log_ob = new activity_log($config);

	}

	function is_user_blocked($username)
	{
		$remaning_time ='';
		$this->username = $username;
		$check_blocked_sql = "SELECT TIMEDIFF(block_until_time,NOW()) as remaining_time
										FROM vt_users
										WHERE email = '$this->username'
										and
										block_until_time>NOW()
							 ";

		$check_blocked_rs = mysql_query($check_blocked_sql) or die(mysql_error()."-".$check_blocked_sql);
		$checked_blocked_row = mysql_fetch_array($check_blocked_rs);
		if(mysql_num_rows($check_blocked_rs))
		{
			$remaning_time = $checked_blocked_row['remaining_time'];
			$message=array('text'=>"Your Account has been blocked due to filed Attempts. Please retry after ".$remaning_time." minutes"  ,'type'=>'error');
			$_SESSION['message'][]=$message;
		}

		return mysql_num_rows( $check_blocked_rs)>0 ? $remaning_time:false;
	}

	function block_on_limit($username)
	{
		// Check attempts in the past $time_maximum_attempts are greater then $maximum_attempts;
		// If true block the user. 

		//Insert Record  in login_attempts
		$this->username = $username;
		mysql_query("INSERT INTO login_attempts SET email_id_username='$username', ip='".$_SERVER['REMOTE_ADDR']."', created_on='".date("Y-m-d H:i:s"). "', modified_on='".date("Y-m-d H:i:s"). "'");

		//Fetch No of attempts in last 15 min minutes if true bloack for 30 minutes
		$number_of_attempts = mysql_query("SELECT * FROM login_attempts where email_id_username='$username' and  ip='".$_SERVER['REMOTE_ADDR']."' and created_on >NOW() - INTERVAL 15 MINUTE");
		if(mysql_num_rows($number_of_attempts)>$this->maximum_attempts){
			$unlock_time=date('Y-m-d H:i:s', strtotime($this->time_maximum_attempts.' minutes', strtotime(date("Y-m-d H:i:s"))));
			mysql_query("UPDATE vt_users SET block_until_time='".$unlock_time. "' where email='$username'");
		}
		return mysql_num_rows($number_of_attempts)>$this->maximum_attempts?true:false;

	}



	function setupDatbaseStructure(){

		mysql_query("CREATE TABLE IF NOT EXISTS `login_attempts` (
			  `id` int(111) NOT NULL AUTO_INCREMENT,
			  `email_id_username` varchar(200) NOT NULL,
			  `ip` varchar(100) NOT NULL,
			  `created_on` datetime NOT NULL,
			  `modified_on` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;") or die(mysql_error());

		mysql_query("CREATE TABLE IF NOT EXISTS `activity_log` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL,
			  `organization_id` int(11) NOT NULL,
			  `ip` varchar(30) NOT NULL,
			  `activity_type` enum('login_attempt','addition_to_organization','change_password','user_registration','login_attempt_failed') NOT NULL,
			  `referral_url` varchar(500) NOT NULL,
			  `log_message` text NOT NULL,
			  `created_on` datetime NOT NULL,
			  `modified_on` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;") or die(mysql_error());

		

	}




	// Getter Setters for loginTableName
	public function setLoginTableName($loginTableName)
	{
		$this->loginTableName = $loginTableName;
	}
	public function getLoginTableName($loginTableName)
	{
		return $this->loginTableName;
	}

	// Getters & Setters for 

/*	public function setloginUserField($loginUserField)
	{
		$this->loginUserField = $loginUserField;
	}
	public function getLoginTableName($loginTableName)
	{
		return $this->loginTableName;
	}*/

}


//
/* $login_ob = new login_example();
 $login_ob->is_user_blocked('yogesh@technoscore.net');
 if($login_ob->authenticate('yogesh','123456'))
 {

 }
*/

 ?>