<?php 
/*

Preamble      		: To make Validation process simpler. 
Creation Date 		: 2013/03/25
Authors 			: Shishi Raven & Vishnu Sharma. 
Last Modified date  : 2013/03/25
 
-------- INPUT---------------

Script Prameters. 

1ST PARAMETER

$conifg Array :
	
	Description : Will contain the marking of the Validations to the Variables. 
	
	Example. 
		
	$config = array();
	$config['email'] 	= "account_email, alternative_email";
	$config['required']	= "username,firstname,lastname";

**********************************************************************
	
2ND PARAMETER. 

$variables_array 

	Description: This array could be a $_POST, $_GET, $_REQUEST array. or any other array that will 
		contain the variables to be validated. 
		
--------------------

--- OUTPUT ------------

result : Result array would be either an error array or false. 

	Example of possible outputs. 
	
	1) result = false; 
	2) result = array(
						array(
							'error_field'	=> 'name',
							'error' 		=> "Field should not be empty"
							),
						array(
							'error_field'	=> 'email',
							'error' 		=> "Field should be an valid email."
							)
					
					);

-----------------------------------------------------------------------
Example Config. 

$config = array();
$config['array_to_validate'] 	= $_POST;
$config['email'] 				= "account_email,alternative_email";
$config['required']				= "username,lastname";
$config['numeric']				= "mobile";
$config['url']					= "url";
$config['alpha']				= "firstname";
$config['alphanumeric']			= "password";
$config['unique_from_table']	= array(
										array(
											'field_name' =>'',
											'table_name' =>'vt_users',
											'table_field'=>'username'
										),
										array(
											'field_name' =>'',
											'table_name' =>'vt_users',
											'table_field'=>'username'
										),
									);
$config['compare']					= array(
										array(
											'field_name' =>'username',
											'compare_field_name' =>'firstname',
										),
										array(
											'field_name' =>'lastname',
											'compare_field_name' =>'firstname'
										)
									);
$config['min_character_limit']		= array(
										array(
											'field_name' =>'username',
											'no_of_character' =>'20',
										),
										array(
											'field_name' =>'lastname',
											'no_of_character' =>'30'
										)
									);
$config['max_character_limit']		= array(
										array(
											'field_name' =>'username',
											'no_of_character' =>'20',
										),
										array(
											'field_name' =>'lastname',
											'no_of_character' =>'30'
										)
									);



------------------------------

Example to Use. 

$config = array();
$config['array_to_validate'] 	= $_POST;
$config['email'] 				= "account_email,alternative_email";
$config['required']				= "username,firstname,lastname";
	
$my_validator = new validator($config);
$error = $my_validator->process_validation();

if( $error == false )
{
	echo "No Error Found";
}
else
{
	echo "<pre>";
			print_r($error);
	echo "</pre>";
			
}

*/


// Class Defination starts here. 
//include('connection.php');
include('../languages/'.$_SESSION['cms_language'].'/validate.php');

class validator{
		
	var $array_to_validate = "";
	var $email = "";
	var $required = "";
	var $numeric = "";
	var $iban = "";
	var $url = "";
	var $alpha = "";
	var $alphanumeric = "";
	var $unique_from_table = "";
	var $compare = "";
	var $min_character_limit = "";
	var $max_character_limit = "";
	var $min_value_limit = "";
	var $max_value_limit = "";
	var $photo = "";
	var $value_exist_in_table="";
	var $terms_and_condition="";
	var $upload="";
	var $postal_code="";
	var $phone_number="";
	var $bankname="";
	
	// Error Array. 
	var $error_array = array();
		
		
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
	function validator( $params = array() )
	{
		
		if (count($params) > 0)
		{
			$this->initialize($params);
		
		}
	}
	
	// REQUIRED FIELD
	function validate_for_required()
	{
		$required_array = array();
		if(trim($this->required)!="")
		{
			$required_array = explode(',',$this->required);
		}
	
		foreach($required_array as $required_element)
		{	
			if(trim($required_element)!='')
			{
				$value_to_check = mysql_real_escape_string(trim($this->array_to_validate[trim($required_element)]));
				if(trim($value_to_check)=="")
				{
					$this->error_array[] = array(
									'error_field'	=> trim($required_element),
									'error' 		=> constant("LANG_VALIDATE_NOT_EMPTY")
								); 
				}
			}
		}
	}
	
	
	// EMAIL VALIDATION
	function validate_for_email()
	{
		$required_array = array();
		if(trim($this->email)!="")
		{
			$required_array = explode(',',$this->email);
		}
		foreach($required_array as $required_element)
		{
			if(trim($required_element)!='')
			{
				$value_to_check =mysql_real_escape_string(trim($this->array_to_validate[trim($required_element)])); 
				if($value_to_check!='')
				{
					if(!filter_var($value_to_check, FILTER_VALIDATE_EMAIL))
					{
						$this->error_array[] = array(
										'error_field'	=> trim($required_element),
										'error' 		=> constant("LANG_VALIDATE_VALID_EMAIL")
									); 
					}
				}
			}
		}
	}

	// POSTAL CODE VALIDATION
	function postal_code()
	{
		$required_array = array();
		if(trim($this->postal_code)!="")
		{
			$required_array = explode(',',$this->postal_code);
		}
		foreach($required_array as $required_element)
		{
			if(trim($required_element)!='')
			{
				$value_to_check =mysql_real_escape_string(trim($this->array_to_validate[trim($required_element)])); 
				if($value_to_check!='')
				{
					$country_code="US";					 
					$ZIPREG=array(
						"US"=>"^([0-9]{4}[ ]+[a-zA-Z]{2})$"
					);
					 
					if ($ZIPREG[$country_code]) {
					 
						if (!preg_match("/".$ZIPREG[$country_code]."/i",$value_to_check)){
							//Validation failed, provided zip/postal code is not valid.
						$this->error_array[] = array(
										'error_field'	=> trim($required_element),
										'error' 		=> constant("LANG_VALIDATE_VALID_POSTAL_CODE")
									); 
						}
					}
				}
			}
		}
	}
	
	// PHONE NUMBER VALIDATION
	function phone_number()
	{
		$required_array = array();
		if(trim($this->phone_number)!="")
		{
			$required_array = explode(',',$this->phone_number);
		}
		foreach($required_array as $required_element)
		{
			if(trim($required_element)!='')
			{
				$value_to_check =mysql_real_escape_string(trim($this->array_to_validate[trim($required_element)])); 
				if($value_to_check!='')
				{
					$pattern = '/^0(6[\s-]?[1-9]\d{7}|[1-9]\d[\s-]?[1-9]\d{6}|[1-9]\d{2}[\s-]?[1-9]\d{5})$/';
					 
			
					 
						if (!preg_match($pattern, $value_to_check)){
							//Validation failed, provided zip/postal code is not valid.
						$this->error_array[] = array(
										'error_field'	=> trim($required_element),
										'error' 		=> constant("LANG_VALIDATE_VALID_POSTAL_CODE")
									); 
						
					}
				}
			}
		}
	}
	
	// NUMERIC FIELD VALIDATION
	function validate_for_numeric()
	{
		$required_array = array();
		if(trim($this->numeric)!="")
		{
			$required_array = explode(',',$this->numeric);
		}
		foreach($required_array as $required_element)
		{
			if(trim($required_element)!='')
			{
				$value_to_check = mysql_real_escape_string(trim($this->array_to_validate[trim($required_element)]));
				if($value_to_check!='')
				{
					if(!is_numeric($value_to_check))
					{
						$this->error_array[] = array(
										'error_field'	=> trim($required_element),
										'error' 		=> constant("LANG_VALIDATE_NUMERIC")
									);
					}
				}
			}
		}
	}

	function validate_for_iban()
	{
		$required_array = array();
		if(trim($this->iban)!="")
		{
			$required_array = explode(',',$this->iban);
		}
		foreach($required_array as $required_element)
		{
			if(trim($required_element)!='')
			{
				$value_to_check = mysql_real_escape_string(trim($this->array_to_validate[trim($required_element)]));
				$iban = strtolower(str_replace(' ','',$value_to_check));
			    $Countries = array('al'=>28,'ad'=>24,'at'=>20,'az'=>28,'bh'=>22,'be'=>16,'ba'=>20,'br'=>29,'bg'=>22,'cr'=>21,'hr'=>21,'cy'=>28,'cz'=>24,'dk'=>18,'do'=>28,'ee'=>20,'fo'=>18,'fi'=>18,'fr'=>27,'ge'=>22,'de'=>22,'gi'=>23,'gr'=>27,'gl'=>18,'gt'=>28,'hu'=>28,'is'=>26,'ie'=>22,'il'=>23,'it'=>27,'jo'=>30,'kz'=>20,'kw'=>30,'lv'=>21,'lb'=>28,'li'=>21,'lt'=>20,'lu'=>20,'mk'=>19,'mt'=>31,'mr'=>27,'mu'=>30,'mc'=>27,'md'=>24,'me'=>22,'nl'=>18,'no'=>15,'pk'=>24,'ps'=>29,'pl'=>28,'pt'=>25,'qa'=>29,'ro'=>24,'sm'=>27,'sa'=>24,'rs'=>22,'sk'=>24,'si'=>19,'es'=>24,'se'=>24,'ch'=>21,'tn'=>24,'tr'=>26,'ae'=>23,'gb'=>22,'vg'=>24);
			    $Chars = array('a'=>10,'b'=>11,'c'=>12,'d'=>13,'e'=>14,'f'=>15,'g'=>16,'h'=>17,'i'=>18,'j'=>19,'k'=>20,'l'=>21,'m'=>22,'n'=>23,'o'=>24,'p'=>25,'q'=>26,'r'=>27,'s'=>28,'t'=>29,'u'=>30,'v'=>31,'w'=>32,'x'=>33,'y'=>34,'z'=>35);

			    if(isset($Countries[substr($iban,0,2)]) && (strlen($iban) == $Countries[substr($iban,0,2)]))
			    {

				        $MovedChar = substr($iban, 4).substr($iban,0,4);
				        $MovedCharArray = str_split($MovedChar);
				        $NewString = "";

				        foreach($MovedCharArray AS $key => $value){
				            if(!is_numeric($MovedCharArray[$key])){
				                $MovedCharArray[$key] = $Chars[$MovedCharArray[$key]];
				            }
				            $NewString .= $MovedCharArray[$key];
				        }

				        if(bcmod($NewString, '97') != 1)
						{
							$this->error_array[] = array(
								'error_field'	=> trim($required_element),
								'error' 		=> constant("LANG_VALIDATE_IBAN")
							);
						}
				       
		 		}else{
				            $this->error_array[] = array(
								'error_field'	=> trim($required_element),
								'error' 		=> constant("LANG_VALIDATE_IBAN")
							);
				    }
		 	}	
	    }
	      
	}
	
	
	// URL VALIDATION
	function validate_for_url()
	{
		$required_array = array();
		if(trim($this->url)!="")
		{
			$required_array = explode(',',$this->url);
		}

		foreach($required_array as $required_element)
		{
			if(trim($required_element)!='')
			{
				$value_to_check =$this->array_to_validate[trim($required_element)];
				if($value_to_check!='')
				{
					if(!preg_match('/((((?:http|https|ftp):\/\/)|(www\.))(?:[A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?[^\s\"\']+)/i',$value_to_check))
					{
						$this->error_array[] = array(
										'error_field'	=> trim($required_element),
										'error' 		=> constant("LANG_VALIDATE_URL")
									);
					}
				}
			}
		}
	}
	
	
	// ALPHA VALIDATION FOR LETTERS ONLY
	function validate_for_alpha()
	{
		$required_array = array();
			if(trim($this->alpha)!="")
		{
				$required_array = explode(',',$this->alpha);
		}

	
		foreach($required_array as $required_element)
		{
			if(trim($required_element)!='')
			{
				$value_to_check = mysql_real_escape_string(trim($this->array_to_validate[trim($required_element)]));
				if($value_to_check!='')
				{
					if(!preg_match("/^[a-zA-Z .]+$/",$value_to_check))
					{
						$this->error_array[] = array(
										'error_field'	=> trim($required_element),
										'error' 		=> constant("LANG_VALIDATE_ONLY_LETTER")
									);
					}
				}
			}
		}
	}
	
	// ALPHA VALIDATION FOR BANK ACCOUNT NAME ONLY
	function validate_for_bankname()
	{
		$required_array = array();
		if(trim($this->bankname)!="")
		{
				$required_array = explode(',',$this->bankname);
		}

	
		foreach($required_array as $required_element)
		{
			if(trim($required_element)!='')
			{
				$value_to_check = mysql_real_escape_string(trim($this->array_to_validate[trim($required_element)]));
				if($value_to_check!='')
				{
					if(1 === preg_match('~[0-9]~', $value_to_check))
					{
						$this->error_array[] = array(
										'error_field'	=> trim($required_element),
										'error' 		=> constant("LANG_VALIDATE_BANK_NAME")
									);
					}
				}
			}
		}
	}
	
	// ALPHANUMERIC VALIDATION FOR LETTERS,NUMBERS AND PERIODS ONLY
	function validate_for_alphanumeric()
	{
		$required_array = array();
	
		if(trim($this->alphanumeric)!="")
		{
			$required_array = explode(',',$this->alphanumeric);
		}
		
		foreach($required_array as $required_element)
		{
			if(trim($required_element)!='')
			{
				$value_to_check = mysql_real_escape_string(trim($this->array_to_validate[trim($required_element)]));
				if($value_to_check!='')
				{
					if(!preg_match('/^[a-zA-Z0-9]+$/',$value_to_check))
					{
						$this->error_array[] = array(
										'error_field'	=> trim($required_element),
										'error' 		=> constant("LANG_VALIDATE_ALPHANUMERIC")
									);
					}
				}
			}
		}
	}
	
	
	// UNIQUE FROM TABLE
	function validate_for_unique_from_table()
	{
		$required_array = array();
		
		if(!empty($this->unique_from_table))
		{
			$required_array = $this->unique_from_table;
		}
		
		foreach($required_array as $unique)
			{	
				$table_name = $unique['table_name'];
				$table_field = $unique['table_field'];
				$field_name = $unique['field_name'];
				$value_to_check = mysql_real_escape_string(trim($this->array_to_validate[$unique['field_name']]));
				if($value_to_check!='')
				{
					$where_clause = "";
					$field_to_left = (isset($unique['field_to_left']) && !empty($unique['field_to_left']))?$unique['field_to_left']:"";
					if(!empty($field_to_left))
					{
						$left_value = mysql_real_escape_string(trim($this->array_to_validate[$field_to_left]));
					}
					if(!empty($left_value))
					{
						$where_clause = " and id<>$left_value";
					}
					$query = "select * from $table_name where $table_field ='$value_to_check' $where_clause";
					$query_rs = mysql_query($query) or die(mysql_error());
					
					
					if( mysql_num_rows($query_rs) )
						{
							if(isset($unique['field_name_lang']) && !empty($unique['field_name_lang'])){
								$field_name_lang = $unique['field_name_lang'];

								$this->error_array[] = array(
									'error_field'	=> trim($field_name),
									'error' 		=> $field_name_lang
								); 
							}else{
							$this->error_array[] = array(
									'error_field'	=> trim($field_name),
									'error' 		=> ucfirst(str_replace("_",' ',trim($field_name))).' '.constant("LANG_VALIDATE_ALREADY_EXIST")
								); 	
							}					
						}
				}
			}	
	}
	
	
	// COMPARE FIELDS
	function validate_for_compare()
	{
		$required_array = array();
		if(!empty($this->compare))
		{
			$required_array = $this->compare;
		}
		
		foreach($required_array as $required_element)
		{
			if(!empty($required_element))
			{
				$field_name = $required_element['field_name'];
				$compare_field_name = $required_element['compare_field_name'];
				$field_name_lang = (isset($required_element['field_name_lang']) && !empty($required_element['field_name_lang']))?$required_element['field_name_lang']:$field_name;
				$compare_field_name_lang = (isset($required_element['compare_field_name_lang']) && !empty($required_element['compare_field_name_lang']))?$required_element['compare_field_name_lang']:$compare_field_name;
				$compare_type 	=	(isset($required_element['compare_type']) && !empty($required_element['compare_type']))?$required_element['compare_type']:"";
				$value_to_check = trim($this->array_to_validate[$field_name]); 
				if($value_to_check!='')
				{
					$compare_value_to_check = trim($this->array_to_validate[$compare_field_name]);

					if($compare_type == 'date')
					{	
						list($D,$M,$Y) = explode('-', mysql_real_escape_string($value_to_check));
						$value_to_check = date('Y-m-d',mktime(0,0,0,$M,$D,$Y));
						 
						 list($D,$M,$Y) = explode('-', mysql_real_escape_string($compare_value_to_check));
						$compare_value_to_check =  date('Y-m-d',mktime(0,0,0,$M,$D,$Y));
						if(strtotime($value_to_check) > strtotime($compare_value_to_check))
						{
							$this->error_array[] = array(
											'error_field'	=> trim($compare_field_name),
											'error' 		=> $compare_field_name_lang.' '.constant("LANG_VALIDATE_COMPARE_GREATER").' '.$field_name_lang
										);
						}
					}
					elseif($compare_type == 'greater')
					{	
						if($value_to_check > $compare_value_to_check)
						{
							$this->error_array[] = array(
											'error_field'	=> trim($compare_field_name),
											'error' 		=> $compare_field_name_lang.' '.constant("LANG_VALIDATE_COMPARE_GREATER").' '.$field_name_lang
										);
						}
					}
					elseif($compare_type == 'lesser')
					{
						if($value_to_check < $compare_value_to_check)
						{
							$this->error_array[] = array(
											'error_field'	=> trim($compare_field_name),
											'error' 		=> $compare_field_name_lang.' '.constant("LANG_VALIDATE_COMPARE_LESSER").' '.$field_name_lang
										);
						}
					}
					else
					{
						if($value_to_check != $compare_value_to_check)
						{
							$this->error_array[] = array(
											'error_field'	=> trim($compare_field_name),
											'error' 		=> constant("LANG_VALIDATE_DOES_NOT_MATCH_WITH").' '.ucfirst(str_replace("_",' ',trim($field_name))).' '.constant("LANG_VALIDATE_FIELD")
										);
						}
					}
				}
			}
		}
	}



	// LIMIT THE NO OF CHARACTER FIELDS
	function min_character_limit()
	{
		$required_array = array();
		if(!empty($this->min_character_limit))
		{
			$required_array = $this->min_character_limit;
		}
		
		foreach($required_array as $required_element)
		{
			if(!empty($required_element))
			{
				$field_name = $required_element['field_name'];
				$no_of_character = trim($required_element['no_of_character']);
				$value_to_check = trim($this->array_to_validate[$field_name]); 
				if($value_to_check!='')
				{	
					$no_of_character_value = $no_of_character;
					if(strlen($value_to_check) < $no_of_character_value)
					{
						if(isset($required_element['field_name_lang']) && !empty($required_element['field_name_lang'])){
								$field_name_lang = $required_element['field_name_lang'];

								$this->error_array[] = array(
									'error_field'	=> trim($field_name),
									'error' 		=> $field_name_lang
								); 
							}else{
						$this->error_array[] = array(
										'error_field'	=> trim($field_name),
										'error' 		=> constant("LANG_VALIDATE_MINIMUM_OF").' '.$no_of_character_value.' '.constant("LANG_VALIDATE_CHARACTERS_REQUIRED")
									);
						}
					}
				}
			}
		}
	}
	
	
	// LIMIT THE MAX NO OF CHARACTER FIELDS
	function max_character_limit()
	{
		$required_array = array();
		if(!empty($this->max_character_limit))
		{
			$required_array = $this->max_character_limit;
		}
		
		foreach($required_array as $required_element)
		{
			if(!empty($required_element))
			{
				$field_name = $required_element['field_name'];
				$no_of_character = trim($required_element['no_of_character']);
				$value_to_check = trim($this->array_to_validate[$field_name]); 
				if($value_to_check!='')
				{	
					$no_of_character_value = $no_of_character;
					if(strlen($value_to_check) > $no_of_character_value)
					{
						if(isset($required_element['field_name_lang']) && !empty($required_element['field_name_lang'])){
								$field_name_lang = $required_element['field_name_lang'];

								$this->error_array[] = array(
									'error_field'	=> trim($field_name),
									'error' 		=> $field_name_lang
								); 
							}else{
						$this->error_array[] = array(
										'error_field'	=> trim($field_name),
										'error' 		=> constant("LANG_VALIDATE_CHARACTER_LIMIT_EXCEEDED").'</n><br>'.constant("LANG_VALIDATE_YOU_CAN_ENTER_ONLY").' '.$no_of_character_value.' '.constant("LANG_VALIDATE_CHARACTERS")
									);
						}
					}
				}
			}
		}
	}

	// COMPARE LIMIT VALUE MINIMUM
	function min_value_limit()
	{
		$required_array = array();
		if(!empty($this->min_value_limit))
		{
			$required_array = $this->min_value_limit;
		}
		
		foreach($required_array as $required_element)
		{
			if(!empty($required_element))
			{
				$field_name = $required_element['field_name'];
				$min_value_of_integer = trim($required_element['min_value']);
				$value_to_check = trim($this->array_to_validate[$field_name]); 
				if($value_to_check!='')
				{	
					if(($value_to_check) < $min_value_of_integer)
					{
						$this->error_array[] = array(
										'error_field'	=> trim($field_name),
										'error' 		=> constant("LANG_VALIDATE_FIELD_SHOULD_BE_GREATER_THAN").' '.$min_value_of_integer.'.'
									);
					}
				}
			}
		}
	}
	
	
	// COMPARE LIMIT VALUE MAXIMUM
	function max_value_limit()
	{
		$required_array = array();
		if(!empty($this->max_value_limit))
		{
			$required_array = $this->max_value_limit;
		}
		
		foreach($required_array as $required_element)
		{
			if(!empty($required_element))
			{
				$field_name = $required_element['field_name'];
				$max_value_of_integer = trim($required_element['max_value']);
				$value_to_check = trim($this->array_to_validate[$field_name]); 
				if($value_to_check!='')
				{	
					if(($value_to_check) > $max_value_of_integer)
					{
						$this->error_array[] = array(
										'error_field'	=> trim($field_name),
										'error' 		=> constant("LANG_VALIDATE_FIELD_SHOULD_BE_LESS_THAN").' '.$max_value_of_integer.'.'
									);
					}
				}
			}
		}
	}
	
	// VALIDATE PHOTO
	function validate_for_photo()
	{
		$required_array = array();
		if(trim($this->photo)!="")
		{
			$required_array = explode(',',$this->photo);	
		}
	
		foreach($required_array as $required_element)
		{	
			if(trim($required_element)!='')
			{
				$value_to_check = mysql_real_escape_string(trim($this->array_to_validate[trim($required_element)]));
				if($value_to_check!='')
				{
					$path_parts = pathinfo(strtolower($value_to_check));
					if(!isset($path_parts['extension']) || !($path_parts['extension']=='jpg' || $path_parts['extension']=='jpeg' || $path_parts['extension']=='gif' || $path_parts['extension']=='png'))
					{
						$this->error_array[] = array(
										'error_field'	=> trim($required_element),
										'error' 		=> constant("LANG_VALIDATE_FIELD_SHOULD_BE").' jpg,jpeg,gif,png.'
									); 
					}
				}
			}
		}
	}
	
	
	function validate_for_upload()
	{
		$required_array = array();
		if(!empty($this->upload))
		{
			$required_array = $this->upload;
		}
	
		foreach($required_array as $required_element)
		{	
			$field_name = $required_element['field_name'];
			if(!empty($required_element['extensions']))
			{
				$extensions_field = $required_element['extensions'];
				$extensions = explode(',',$extensions_field);	
			}
			$file_name = mysql_real_escape_string(trim($this->array_to_validate[trim($field_name)]));
			if($file_name!='')
			{
				$path_parts = pathinfo(strtolower($file_name));

				if(!isset($path_parts['extension']) || !in_array($path_parts['extension'],$extensions))
				{
					$this->error_array[] = array(
									'error_field'	=> trim($field_name),
									'error' 		=> constant("LANG_VALIDATE_FIELD_SHOULD_BE").' '.strtoupper($extensions_field).' '. constant("LANG_VALIDATE_FIELD_SHOULD_BE_TYPE")
								); 
				}
			}
		}
	}
	
	
	
	// UNIQUE FROM TABLE
	function validate_for_value_exist_in_table()
	{
		$required_array = array();
		
		if(!empty($this->value_exist_in_table))
		{
			$required_array = $this->value_exist_in_table;
		}
		
		foreach($required_array as $exist)
			{	
				$table_name = $exist['table_name'];
				$table_field = $exist['table_field'];
				$field_name = $exist['field_name'];
				$value_to_check = mysql_real_escape_string(trim($this->array_to_validate[$exist['field_name']]));
				if($value_to_check!='')
				{
					$query = mysql_query("select * from $table_name where $table_field ='$value_to_check'") or die(mysql_error());
					
					if(!mysql_num_rows($query) )
						{
							$this->error_array[] = array(
									'error_field'	=> trim($field_name),
									'error' 		=> constant("LANG_VALIDATE_THIS_CODE_IS_NOT_CORRECT")
								); 						
						}
				}
			}	
	}
	
	// TERMS AND CONDITION
	function validate_for_terms_and_condition()
	{
		$required_array = array();
		if(trim($this->terms_and_condition)!="")
		{
			$required_array = explode(',',$this->terms_and_condition);
		}
	
		foreach($required_array as $required_element)
		{	
			if(trim($required_element)!='')
			{
				if(!isset($this->array_to_validate[trim($required_element)]))
				{
					$this->error_array[] = array(
									'error_field'	=> trim($required_element),
									'error' 		=> constant("LANG_VALIDATE_PLEASE_ACCEPT_THE_TERMS_OF_USE_AND_RULES")
								); 
				}
			}
		}	
	}
	
	
	
	function process_validation()
	{
		$this->validate_for_required();
		$this->validate_for_email();
		$this->validate_for_numeric();
		$this->validate_for_iban();
		$this->validate_for_url();
	    $this->validate_for_bankname();
		$this->validate_for_alpha();
		$this->validate_for_alphanumeric();
		$this->validate_for_unique_from_table();
		$this->validate_for_compare();
		$this->min_character_limit();
		$this->max_character_limit();
		$this->min_value_limit();
		$this->max_value_limit();
		$this->validate_for_photo();
		$this->validate_for_value_exist_in_table();
		$this->validate_for_terms_and_condition();
		$this->validate_for_upload();
		$this->postal_code();
		$this->phone_number();
		if(count($this->error_array))
		{
			return $this->error_array;
		}
		else
		{
			return false;
		}
	
	}

}
	
	

/* 	
$config = array();
$config['array_to_validate'] 	= $_GET;
$config['email'] 				= "account_email,alternative_email";
$config['required']				= "username,lastname";
$config['numeric']				= "mobile";
$config['url']					= "url";
$config['alpha']				= "firstname";
$config['alphanumeric']			= "password";
$config['unique_from_table']	= array(
										array(
											'field_name' =>'',
											'table_name' =>'vt_users',
											'table_field'=>'username'
										),
										array(
											'field_name' =>'',
											'table_name' =>'vt_users',
											'table_field'=>'username'
										),
									);
$config['compare']					= array(
										array(
											'field_name' =>'username',
											'compare_field_name' =>'firstname',
										),
										array(
											'field_name' =>'lastname',
											'compare_field_name' =>'firstname'
										)
									);
$my_validator = new validator($config);
$error = $my_validator->process_validation();

if( $error == false )
{
	echo "No Error Found";
}
else
{
	echo "<pre>";
			print_r($error);
	echo "</pre>";
		
}   */
?>