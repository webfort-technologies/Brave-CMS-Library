<?php 
	/*
	Purpose: Class to perform operations on table vt_users
	Example of "How To Use" the class starts here
	$vt_users_ob = new users();
	$vt_users_ob->setid($value);
	$vt_users_ob->setusername($value);
	$vt_users_ob->setpassword($value);
	$vt_users_ob->settoken($value);
	$vt_users_ob->setaccount_type($value);
	$vt_users_ob->setis_enabled($value);
	$vt_users_ob->setfirst_name($value);
	$vt_users_ob->setmiddle_name($value);
	$vt_users_ob->setlast_name($value);
	$vt_users_ob->setstreet($value);
	$vt_users_ob->setcity($value);
	$vt_users_ob->setstate($value);
	$vt_users_ob->setcountry($value);
	$vt_users_ob->setzipcode($value);
	$vt_users_ob->setisd_code($value);
	$vt_users_ob->setmobile($value);
	$vt_users_ob->setlandline($value);
	$vt_users_ob->setemail($value);
	$vt_users_ob->setbirth_date($value);
	$vt_users_ob->setprofile_image($value);
	$vt_users_ob->setgender($value);
	$vt_users_ob->setcreated_on($value);
	$vt_users_ob->setmodified_on($value);
	$vt_users_ob->settimezone($value);
	$vt_users_ob->setblock_until_time($value);
	$vt_users_ob->setis_request_outsourced($value);
	*/
	class users{
				 var $record_id;
                 var $id;
                 var $username;
                 var $password;
                 var $token;
                 var $account_type;
                 var $is_enabled;
                 var $first_name;
                 var $middle_name;
                 var $last_name;
                 var $street;
                 var $city;
                 var $state;
                 var $country;
                 var $zipcode;
                 var $isd_code;
                 var $mobile;
                 var $landline;
                 var $email;
                 var $birth_date;
                 var $profile_image;
                 var $gender;
                 var $created_on;
                 var $modified_on;
                 var $timezone;
                 var $block_until_time;
                 var $is_request_outsourced;
		function setid($id)
		{
			$this->id = $id;
		}
		function getid()
		{
			return $this->id;
		}
		function setusername($username)
		{
			if($username=="")
			{
				throw new Exception ('UserName is required');
			}
			$this->username = $username;
		}
		function getusername()
		{
			return $this->username;
		}
		function setpassword($password)
		{
			$this->password = $password;
		}
		function getpassword()
		{
			return $this->password;
		}
		function settoken($token)
		{
			$this->token = $token;
		}
		function gettoken()
		{
			return $this->token;
		}
		function setaccount_type($account_type)
		{
			$this->account_type = $account_type;
		}
		function getaccount_type()
		{
			return $this->account_type;
		}
		function setis_enabled($is_enabled)
		{
			$this->is_enabled = $is_enabled;
		}
		function getis_enabled()
		{
			return $this->is_enabled;
		}
		function setfirst_name($first_name)
		{
			if($first_name=="")
			{
				throw new Exception ('First Name is required');
			}
			$this->first_name = $first_name;
		}
		function getfirst_name()
		{
			return $this->first_name;
		}
		function setmiddle_name($middle_name)
		{
			$this->middle_name = $middle_name;
		}
		function getmiddle_name()
		{
			return $this->middle_name;
		}
		function setlast_name($last_name)
		{
			if($last_name=="")
			{
				throw new Exception ('Last Name is required');
			}
			$this->last_name = $last_name;
		}
		function getlast_name()
		{
			return $this->last_name;
		}
		function setstreet($street)
		{
			if($street=="")
			{
				throw new Exception ('Street is required');
			}
			$this->street = $street;
		}
		function getstreet()
		{
			return $this->street;
		}
		function setcity($city)
		{
			if($city=="")
			{
				throw new Exception ('City is required');
			}
			$this->city = $city;
		}
		function getcity()
		{
			return $this->city;
		}
		function setstate($state)
		{
			if($state=="")
			{
				throw new Exception ('State is required');
			}
			$this->state = $state;
		}
		function getstate()
		{
			return $this->state;
		}
		function setcountry($country)
		{
			if($country=="")
			{
				throw new Exception ('Country is required');
			}
			$this->country = $country;
		}
		function getcountry()
		{
			return $this->country;
		}
		function setzipcode($zipcode)
		{
			if($zipcode=="")
			{
				throw new Exception ('Zipcode is required');
			}
			$this->zipcode = $zipcode;
		}
		function getzipcode()
		{
			return $this->zipcode;
		}
		function setisd_code($isd_code)
		{
			$this->isd_code = $isd_code;
		}
		function getisd_code()
		{
			return $this->isd_code;
		}
		function setmobile($mobile)
		{
			$this->mobile = $mobile;
		}
		function getmobile()
		{
			return $this->mobile;
		}
		function setlandline($landline)
		{
			$this->landline = $landline;
		}
		function getlandline()
		{
			return $this->landline;
		}
		function setemail($email)
		{
			if($email=="")
			{
				throw new Exception ('Email is required');
			}
			if(filter_var($email, FILTER_VALIDATE_EMAIL) === false)
			{
				throw new Exception ('Email should be valid email address');
			}
			$this->email = $email;
		}
		function getemail()
		{
			return $this->email;
		}
		function setbirth_date($birth_date)
		{
			$this->birth_date = $birth_date;
		}
		function getbirth_date()
		{
			return $this->birth_date;
		}
		function setprofile_image($profile_image)
		{
			$this->profile_image = $profile_image;
		}
		function getprofile_image()
		{
			return $this->profile_image;
		}
		function setgender($gender)
		{
			if($gender=="")
			{
				throw new Exception ('Gender is required');
			}
			$this->gender = $gender;
		}
		function getgender()
		{
			return $this->gender;
		}
		function setcreated_on($created_on)
		{
			$this->created_on = $created_on;
		}
		function getcreated_on()
		{
			return $this->created_on;
		}
		function setmodified_on($modified_on)
		{
			$this->modified_on = $modified_on;
		}
		function getmodified_on()
		{
			return $this->modified_on;
		}
		function settimezone($timezone)
		{
			if($timezone=="")
			{
				throw new Exception ('Timezone is required');
			}
			$this->timezone = $timezone;
		}
		function gettimezone()
		{
			return $this->timezone;
		}
		function setblock_until_time($block_until_time)
		{
			$this->block_until_time = $block_until_time;
		}
		function getblock_until_time()
		{
			return $this->block_until_time;
		}
		function setis_request_outsourced($is_request_outsourced)
		{
			$this->is_request_outsourced = $is_request_outsourced;
		}
		function getis_request_outsourced()
		{
			return $this->is_request_outsourced;
		}
		function validate_fields()
		{ 
		// Return JSON Error 
			$error = array();
			// Checking Required Validation
			if($this->username=="")
			{
				$error[] = array('field_name'=>'username','error_message'=>'UserName is required');
			}
			if($this->first_name=="")
			{
				$error[] = array('field_name'=>'first_name','error_message'=>'First Name is required');
			}
			if($this->last_name=="")
			{
				$error[] = array('field_name'=>'last_name','error_message'=>'Last Name is required');
			}
			if($this->street=="")
			{
				$error[] = array('field_name'=>'street','error_message'=>'Street is required');
			}
			if($this->city=="")
			{
				$error[] = array('field_name'=>'city','error_message'=>'City is required');
			}
			if($this->state=="")
			{
				$error[] = array('field_name'=>'state','error_message'=>'State is required');
			}
			if($this->country=="")
			{
				$error[] = array('field_name'=>'country','error_message'=>'Country is required');
			}
			if($this->zipcode=="")
			{
				$error[] = array('field_name'=>'zipcode','error_message'=>'Zipcode is required');
			}
			if($this->email=="")
			{
				$error[] = array('field_name'=>'email','error_message'=>'Email is required');
			}
			if($this->gender=="")
			{
				$error[] = array('field_name'=>'gender','error_message'=>'Gender is required');
			}
			if($this->timezone=="")
			{
				$error[] = array('field_name'=>'timezone','error_message'=>'Timezone is required');
			}
			// Checking Email Validation
			if(filter_var($this->email, FILTER_VALIDATE_EMAIL) === false)
			{
				$error[] = array('field_name'=>'email','error_message'=>'Email should be valid email address');
			}
			//check for unique fields
			return $error;
		}
		function create_user_in_db()
		{
			$fields = array();
			$fields[] = 'id';
			$fields[] = 'username';
			$fields[] = 'password';
			$fields[] = 'token';
			$fields[] = 'account_type';
			$fields[] = 'is_enabled';
			$fields[] = 'first_name';
			$fields[] = 'middle_name';
			$fields[] = 'last_name';
			$fields[] = 'street';
			$fields[] = 'city';
			$fields[] = 'state';
			$fields[] = 'country';
			$fields[] = 'zipcode';
			$fields[] = 'isd_code';
			$fields[] = 'mobile';
			$fields[] = 'landline';
			$fields[] = 'email';
			$fields[] = 'birth_date';
			$fields[] = 'profile_image';
			$fields[] = 'gender';
			$fields[] = 'created_on';
			$fields[] = 'modified_on';
			$fields[] = 'timezone';
			$fields[] = 'block_until_time';
			$fields[] = 'is_request_outsourced';
			$insert_sql = "insert into vt_users (".implode(',',$fields).") values('$this->id',
			'$this->username',
			'$this->password',
			'$this->token',
			'$this->account_type',
			'$this->is_enabled',
			'$this->first_name',
			'$this->middle_name',
			'$this->last_name',
			'$this->street',
			'$this->city',
			'$this->state',
			'$this->country',
			'$this->zipcode',
			'$this->isd_code',
			'$this->mobile',
			'$this->landline',
			'$this->email',
			'$this->birth_date',
			'$this->profile_image',
			'$this->gender',
			'$this->created_on',
			'$this->modified_on',
			'$this->timezone',
			'$this->block_until_time',
			'$this->is_request_outsourced'
			)";
			mysql_query($insert_sql) or die(mysql_error().">".$insert_sql);
			$this->record_id = mysql_insert_id();
		}
	}
 ?>