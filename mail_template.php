<?php 
//include("../connection.php");
require 'phpmailer/class.phpmailer.php';
class mailtemplate{

	var $array_to_replace		=	"";
	var $send_to				=	"";
	var $send_from				=	"";
	var $template_name			=	"";

// INITIALIZER
	function initialize( $params = array() )
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
	function mailtemplate( $params = array() )
	{
		if (count($params) > 0)
		{
			$this->initialize($params);
		}
	}
	
	//Function to get template by its alias
	function get_template()
	{
		if(trim($this->template_name)!='')
		{
			$template_alias	=	$this->template_name;
			$template_sql	=	"select * from cms_mail_template where template_alias = '".$template_alias."'";
			$template_rs	=	mysql_query($template_sql);
			$template 		=	mysql_fetch_array($template_rs);
			
			return $template;
		}
	}
	
	//Function to replace its placeholder with value
	function replace_template_placeholder($template = array())
	{
		$replace	=	array();
		$template_text	=	$template['template'];
		if($template_text!='')
		 {
			$replace	=	 $this->array_to_replace;
			foreach($replace as $key=>$r)
			{
				$template_text	=	str_replace("[$key]","$r",$template_text);
			}
			return $template_text;	
		 }
	
	}
	
	function fetch_smtp_setting()
	{
		$settings_sql	=	"select * from settings where id ='1'";
		$settings_rs	=	mysql_query($settings_sql);
		$settings_row	=	mysql_fetch_array($settings_rs);
		return $settings_row;
	}

	//Function to send mail
	function sendmail()
	{	
		$template 			= 	$this->get_template();
		$template_message	=	$this->replace_template_placeholder( $template );
		$to     			= 	$this->send_to;
		//$from      			= 	'autoreply@retailerhub.com';
		$subject 			= 	$template['email_subject'];
		$message 			= 	$template_message;
		
		//SMTP PARAMETERS
		$settings			=	$this->fetch_smtp_setting();
		$host				=	$settings['host_name'];
		$smtp_username 		=	$settings['smtp_username'];
		$smtp_password		=	$settings['smtp_password']; 
		
		$from      			= 	$settings['default_email'];
		
		//PHPMAILER
		$mail = new PHPMailer;
		$mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'http';
        $mail->Host = $host;
        $mail->Port = 25;
        $mail->Username = $smtp_username; // this is email on godaddy account
        $mail->Password = $smtp_password;
        $mail->FromName = 'SeeItInStore.com';


		//$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

		$mail->From = $from;
		$mail->addAddress($to);  // Add a recipient


		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $subject;
		$mail->Body    = $message;
		if($mail->send())
		{
			//Enter in mail log.
			$mail_log_sql	= "insert into mail_log(mail_to,mail_subject,mail_message,date_time) values('".$to."','".$subject."','".$message."',NOW())";
			$mail_log_rs	= mysql_query($mail_log_sql);
			return true;
		}
		else
		{
			echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo; 
			return false;
		}
	}



}



 /* $config = array();
$config['array_to_replace'] 	= array(
										'name'  =>'vishnu',
										'email'	=>'vishnu@technoscore.net',
									);
$config['send_to'] 				= "vishnu@technoscore.net";
$config['send_from'] 				= "vishnu@technoscore.net";
$config['template_name'] 		= "contact_us";


$mailtemplate = new mailtemplate($config);
$value = $mailtemplate->sendmail();  */

?>