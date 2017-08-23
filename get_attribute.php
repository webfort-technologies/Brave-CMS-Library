<?php
include("../connection.php");

$config['category_id']=2;

$test =  new atribute($config);
$test->get_attribute();

class atribute{


var $category_id=0; // set the category id

	//constructtor function its called on the class inintialization
	public function __construct($params){
	
		 $this->initialize($params);
		
	
	}

	// initialize the value in variable
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
	
	// get the attrbute
	public function get_attribute(){
	
			// query for get attribute from the database
			$get_attribute_query = "SELECT attribute_management.id
										,attribute_management.field_name
										,attribute_type.attribute_type
									FROM attribute_management
									LEFT JOIN attribute_option ON attribute_management.id = attribute_option.attribute_id
									LEFT JOIN attribute_type ON attribute_management.attribute_type = attribute_type.id
									WHERE attribute_management.category_id = ".$this->category_id."
									GROUP BY attribute_management.id";
			$get_attribute_rs = mysqli_query($GLOBALS["___mysqli_ston"], $get_attribute_query) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."-".$get_attribute_query);
			
			//generate the html using generate_html function
			while($get_attribute = mysqli_fetch_assoc($get_attribute_rs)){
				
				$this->generate_html($get_attribute);
				
			}
	}
	
	
	// function create the html form
	public function generate_html($attribute){
		
			switch($attribute['attribute_type']) {
			
			case "Select":
				 echo $this->build_select_html($attribute);
				break;
			case "Radio":
				 echo $this->build_radio_html($attribute);
				break;
			case "CheckBox":
				echo $this->build_checkbox_html($attribute);
				
			}	
			
	}
	
	//function for generate the select html
	public function build_select_html($attribute){
		
			// get the options for the attribute
			$get_attribute_option_query = "select id,option_label from attribute_option where attribute_id=".$attribute['id'];
			$get_attribute_options_rs = mysqli_query($GLOBALS["___mysqli_ston"], $get_attribute_option_query) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."-".$get_attribute_option_query);
			
			$output .= "<div class='title'>".$attribute['field_name']."</div>";
			$output .= "<ul class='option'><li> <select id='".str_replace(" ","_",$attribute['field_name'])."'>";
			while($get_attribute_options = mysqli_fetch_assoc($get_attribute_options_rs)){
					$output .= "<option value=".$get_attribute_options['id'].">".$get_attribute_options['option_label']."</option>";
			}
			$output .= "</select></li></ul>";
			return $output;
			
	}
	
	//function for generate the radio html
	public function build_radio_html($attribute){
		
			// get the options for the attribute
			$get_attribute_option_query = "select id,option_label from attribute_option where attribute_id=".$attribute['id'];
			$get_attribute_options_rs = mysqli_query($GLOBALS["___mysqli_ston"], $get_attribute_option_query) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."-".$get_attribute_option_query);
			
			$output .= "<div class='title'>".$attribute['field_name']."</div>";
			$output .= "<ul class='option'>";
			while($get_attribute_options = mysqli_fetch_assoc($get_attribute_options_rs)){
				
				$output .="<li><input type='radio' name='".str_replace(" ","_",$attribute['field_name'])."' value='".$get_attribute_options['id']."'>".$get_attribute_options['option_label']."</li>";
			}
			$output .= "</ul>";
			return $output;
	}
	
	//function for generate the checkbox html
	public function build_checkbox_html($attribute){
		
			// get the options for the attribute
			$get_attribute_option_query = "select id,option_label from attribute_option where attribute_id=".$attribute['id'];
			$get_attribute_options_rs = mysqli_query($GLOBALS["___mysqli_ston"], $get_attribute_option_query) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."-".$get_attribute_option_query);
			
			$output .= "<div class='title'>".$attribute['field_name']."</div>";
			$output .= "<ul class='option'>";
			while($get_attribute_options = mysqli_fetch_assoc($get_attribute_options_rs)){
					
				$output .="<li><input type='checkbox' name='".str_replace(" ","_",$attribute['field_name'])."[]' value='".$get_attribute_options['id']."'>".$get_attribute_options['option_label']."</li>";
			}
			$output .= "</ul>";
			return $output;
		
	}
	
	



}



?>

