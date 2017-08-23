<?php
class plugin {
	
	var $group_id=0;
	var $settings_data = array(); // This variable will save all the data which is fetched from the database
	var $settings_form; // This will contain the HTML form 
	var $uploaddir = "";
	var $validate_result = array();
	
	function __construct($params) {
		$this->initialize($params);
		$this->setupTables();
	}
	
	// Intialize funciton to make the config passed in configuration as instance variables. 
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

	//getting db details
	function setupTables(){

		$query = "CREATE TABLE IF NOT EXISTS `cms_input_validations` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `expression_name` varchar(150) NOT NULL DEFAULT '0',
		  `regular_expression` varchar(200) NOT NULL,
		  `message` text NOT NULL,
		  `created_on` datetime DEFAULT NULL,
		  `modified_on` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;

		CREATE TABLE IF NOT EXISTS `cms_plugin_attributes` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `plugin_id` int(11) NOT NULL COMMENT 'its fill with group id',
		  `setting_name` varchar(200) NOT NULL,
		  `unique_key` varchar(200) NOT NULL,
		  `key_type` varchar(100) NOT NULL,
		  `key_value` varchar(255) NOT NULL,
		  `created_on` datetime NOT NULL,
		  `modified_on` datetime NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;

		CREATE TABLE IF NOT EXISTS `cms_plugin_attributes_options` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `plugin_attribute_id` int(11) NOT NULL,
		  `field_value` varchar(200) NOT NULL,
		  `field_label` varchar(200) NOT NULL,
		  `created_on` datetime DEFAULT NULL,
		  `modified_on` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;

		CREATE TABLE IF NOT EXISTS `cms_plugin_attribute_validations` (
		  `attribute_id` int(11) NOT NULL,
		  `validation_id` int(11) NOT NULL,
		  `created_on` datetime DEFAULT NULL,
		  `modified_on` datetime DEFAULT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;

		CREATE TABLE IF NOT EXISTS `cms_plugin_group` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `group_name` varchar(200) DEFAULT NULL,
		  `created_on` datetime DEFAULT NULL,
		  `modified_on` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;

		CREATE TABLE IF NOT EXISTS `cms_plugin_input_types` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `input_type` varchar(200) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;

		CREATE TABLE IF NOT EXISTS `cms_plugin_settings` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `attribute_id` int(11) DEFAULT NULL,
		  `unique_key` varchar(200) DEFAULT NULL,
		  `key_value` varchar(200) DEFAULT NULL,
		  `created_on` datetime DEFAULT NULL,
		  `modified_on` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;";

		if(mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "SHOW TABLES LIKE 'cms_plugin_group'"))==0) 
		{
			  mysqli_query($GLOBALS["___mysqli_ston"], $query);
		}
	}
	
	// Show form will build a HTML form based on the attribute types marked in the database. 
	function show_form($params=array()){
		$this->initialize($params);
		$this->get_settings_from_db();
		$this->generate_html();
	}
	
	// Getting the current settings form the database. 
	function get_settings_from_db(){
		
		// Step 1 : create a query into the database table `plugin_settings` for attrubutes of Group ID which is passed ot this class. 
		$setting_query = 	"SELECT *,pattributes.unique_key as uniquekey,pattributes.id as attid, psettings.key_value as keyvalue 	FROM cms_plugin_group pgroup
							LEFT join cms_plugin_attributes pattributes on pattributes.plugin_id=pgroup.id
							LEFT join cms_plugin_settings psettings on psettings.attribute_id = pattributes.id
							where pgroup.group_name='".$this->group_id."'";

		$rs  = mysqli_query($GLOBALS["___mysqli_ston"], $setting_query) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))." - ".$setting_query);
		
		// Step 2 : Save this data into the instance array variable called $this->settings_data;
		$setting_keys = array();
		$setting_values = array();
		$formdata = array();
		while($row = mysqli_fetch_assoc($rs)){
			$formdata[] = array("unique_key" 	=> $row['uniquekey'],
								"key_type"   	=> $row['key_type'],
								"key_value"  	=> $row['keyvalue'],
								"setting_name"	=> $row['setting_name'],
								"attribute_id"	=> $row['attid']);
		}
		$this->settings_data = $formdata;
	}
	
	// function for save the updated post data
	function save_data($get_post)
	{
		//step 1 : validate the post request
		$validate_result = $this->verify_post_values($get_post);

		if(isset($validate_result)){
			// show the error keys
			$this->validate_result = $validate_result;
		} else {
			
			// Step 2 : Save/Update the data which is saved in the array variable $this->settings_data; back into database table `plugin_settings`
			foreach($this->settings_data as $key=>$value){
				$exist_query = "select * from cms_plugin_settings where attribute_id = '".$value['attribute_id']."' and unique_key='".$value['unique_key']."'";
				$exist_result = mysqli_query($GLOBALS["___mysqli_ston"], $exist_query);
				if(mysqli_num_rows($exist_result))
				mysqli_query($GLOBALS["___mysqli_ston"], "update cms_plugin_settings set key_value='".$value['key_value']."', modified_on='".date('Y-m-d H:i:s')."' where unique_key='".$value['unique_key']."' and attribute_id='".$value['attribute_id']."'");
				else
				mysqli_query($GLOBALS["___mysqli_ston"], "insert into cms_plugin_settings set key_value='".$value['key_value']."', unique_key='".$value['unique_key']."', attribute_id='".$value['attribute_id']."', created_on='".date('Y-m-d H:i:s')."', modified_on='".date('Y-m-d H:i:s')."'");
			}
		}
	}
	
	//function for check the post value from particular group or not
	function verify_post_values($get_post){
		
		//it have the validate status of all keys
		$validate_complete_post =array();
		
		//have the error keys
		$error_list = array();
		
		// Step 1 : Finding the values of setting in the following form array('setting1'=>'','setting2'=>), supppose it to be $ar
		$this->get_settings_from_db();

		// Step 2 : Loop this data. with key and Value	// Step 3 : Check isset for key is available in post. i.e $_POST[key]		// Step 4 if key exist $ar['key'] = $_POST[key];
		foreach($this->settings_data as $settings_data_key=>$settings_data_value){
				
			if($this->check_input_type($settings_data_key)=="file"){
				
				if($this->upload_file($settings_data_key)){
					$this->settings_data[$settings_data_key] = $_FILES[$settings_data_key]['name']; 
				}
			}			
			
			if(array_key_exists($settings_data_value['unique_key'], $get_post)){
			 	$this->settings_data[$settings_data_key]['key_value'] = $get_post[$settings_data_value['unique_key']];
			}
		
		}
		
		// step 4 : validate the data according to putting validation
		//echo '<pre>';print_r($this->settings_data);
		foreach($this->settings_data as $key=>$value){

			if($this->check_input_type($value['unique_key'])!="file"){
				array_push($validate_complete_post,$this->validate_key_data($value['unique_key'],$value));

				$ret = $this->validate_key_data($value['unique_key'],$value);
				
				foreach ($ret as $rkey => $rvalue) {
					if($rvalue['regular_expression']==0){
						array_push($error_list,array('unique_key'=>$value['unique_key'], 'message' => $rvalue['message']));
						$output = array('status'=>0,'keys'=>$error_list);
					}
				}
			}
		}
		return $output;
	}
	//function for check input type 
	
	function check_input_type($key){
		
		$input_check_query = "select key_type from cms_plugin_attributes where unique_key = '$key'";
		$input_check_rs = mysqli_query($GLOBALS["___mysqli_ston"], $input_check_query) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."-".$input_check_query);
		$input_check = mysqli_fetch_assoc($input_check_rs);
		return $input_check['key_type'];
	}
	
	//function for upload the file 
	function upload_file($key){

			$uploadfile = $this->uploaddir.basename($_FILES[$key]['name']);
			if(move_uploaded_file($_FILES[$key]['tmp_name'], $uploadfile)){
			return 1;
			} else {
			return 0;
			}
	}
	
	//function for validate the post key data
	function validate_key_data($key,$data){

		$result_list = array();
	
		//extracting the regular expression corrosponding to key
		$expression_query = "select cms_input_validations.regular_expression,cms_input_validations.message  from cms_plugin_attributes left join cms_plugin_attribute_validations on cms_plugin_attributes.id=cms_plugin_attribute_validations.attribute_id left join cms_input_validations on cms_input_validations.id=cms_plugin_attribute_validations.validation_id where cms_plugin_attributes.unique_key='".$key."' and cms_plugin_attribute_validations.validation_id!=0";

		$expression_records = mysqli_query($GLOBALS["___mysqli_ston"], $expression_query) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."-".$expression_query);
		while($row = mysqli_fetch_assoc($expression_records)){
			array_push($result_list,array('regular_expression' => preg_match($row['regular_expression'],$data['key_value']), 'message' => $row['message']));
		}
		return $result_list;
		
		die;
	}
	
	// function for generating html element
	function generate_html()
	{
		echo '<div class="row">';
		$i=1;
		foreach ($this->settings_data as $key => $value) {

			//if($i%1==0)
			//echo '</div><div class="row">';

			switch($value['key_type']) {
			case "text":
				echo $this->build_textbox_html($value);
				break;
			case "select":
				 echo $this->build_select_html($value);
				break;
				
			case "textarea":
				 echo $this->build_textarea_html($value);
				break;
			case "radio":
				 echo $this->build_radio_html($value);
				break;
				
			case "checkbox":
				echo $this->build_checkbox_html($value);
				break;
			case "date":
				echo $this->build_date_html($value);
				break;
			case "file":
				echo $this->build_file_html($value);		
				
			}

			$i++;
		}
		echo '</div>';
	} 
	
	function check_message($unique_key)
	{
		$message = '';
		foreach ($this->validate_result['keys'] as $value) {
			if($unique_key==$value['unique_key'])
			$message = $value['message'];
		}
		return $message;
	}

	// function for generating the textbox
	function build_textbox_html($attribute){
		$message = $this->check_message($attribute['unique_key']);
		
		return '<div class="form-group col-md-12 col-sm-12 col-xs-12 '.($message!=''?"has-error":"").'">
					<label>'.$attribute['setting_name'].'</label>
					<input type="text" class="form-control" placeholder=" Field value" name="'.$attribute['unique_key'].'" id="'.$attribute['unique_key'].'" value="'.(isset($_POST[$attribute['unique_key']])?$_POST[$attribute['unique_key']]:$attribute['key_value']).'" >
					'.
					($message!=''?"<span class=\"help-block error-message\"> ".$message."</span>":"")
					.'
				</div>';
	}
	
	// function for generating the select box
	function build_select_html($attribute){
		$message = $this->check_message($attribute['unique_key']);

		$options="";
		$query  = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT cms_plugin_attributes_options.field_label
								,cms_plugin_attributes_options.field_value
								FROM cms_plugin_attributes
								LEFT JOIN cms_plugin_attributes_options ON cms_plugin_attributes.id = cms_plugin_attributes_options.plugin_attribute_id
								WHERE cms_plugin_attributes.unique_key='".$attribute['unique_key']."'");
		while($row = mysqli_fetch_assoc($query)){

			if(isset($_POST[$attribute['unique_key']]))
			{
				if($row['field_value'] == $_POST[$attribute['unique_key']]){
					$options .= '<option value="'.$row['field_value'].'" selected>'.$row['field_label'].'</option>';
				} else{
					$options .= '<option value="'.$row['field_value'].'">'.$row['field_label'].'</option>';
				}
			}
			else
			{
				if($row['field_value'] == $attribute['key_value']){
					$options .= '<option value="'.$row['field_value'].'" selected>'.$row['field_label'].'</option>';
				} else{
					$options .= '<option value="'.$row['field_value'].'">'.$row['field_label'].'</option>';
				}
			}

		}
			
		return 	'<div class="form-group col-md-6 col-sm-12 col-xs-12 '.($message!=''?"has-error":"").'">
					<label> '.$attribute['setting_name'].'</label>
					<select class="form-control" placeholder="Plugin id" name="'.$attribute['unique_key'].'" id="'.$attribute['unique_key'].'"><option value="">Select</option>'.$options.'</select>
					'.
					($message!=''?"<span class=\"help-block error-message\"> ".$message."</span>":"")
				.'
				</div>';
	}
	
	// function for generating the textarea
	function build_textarea_html($attribute){
		$message = $this->check_message($attribute['unique_key']);
		return '<div class="form-group col-md-6 col-sm-12 col-xs-12 '.($message!=''?"has-error":"").'">
					<label> '.$attribute['setting_name'].'  </label>
					<textarea class="form-control" placeholder="Product short desc" name="'.$attribute['unique_key'].'" id="pag_search_product_short_desc" class="span4">'.(isset($_POST[$attribute['unique_key']])?$_POST[$attribute['unique_key']]:$attribute['key_value']).'</textarea>
					'.
					($message!=''?"<span class=\"help-block error-message\"> ".$message."</span>":"")
					.'
				</div>'; 
	}
	
	// function for generating the radio button
	function build_radio_html($attribute){
		$message = $this->check_message($attribute['unique_key']);
		$radio ="";
		$query  = mysqli_query($GLOBALS["___mysqli_ston"], "select cms_plugin_attributes_options.field_label,cms_plugin_attributes_options.field_value from  cms_plugin_attributes left join cms_plugin_attributes_options on cms_plugin_attributes.id=cms_plugin_attributes_options.plugin_attribute_id where cms_plugin_attributes.unique_key='".$attribute['unique_key']."'");
		while($row = mysqli_fetch_assoc($query)){
			$key =  $attribute['unique_key'];
			if($row['field_value'] == $attribute['key_value']){
			$radio .= '<label class="form-control"><input type="radio" name="'.$key.'" value="'.$row['field_value'].'" checked>&nbsp;&nbsp;&nbsp;&nbsp;'.$row['field_label'].'</label>';
			} else{
			$radio .= '<label class="form-control"><input type="radio" name="'.$key.'" value="'.$row['field_value'].'">&nbsp;&nbsp;&nbsp;&nbsp;'.$row['field_label'].'</label>';
				}
		}
		
		return 	'<div class="form-group col-md-6 col-sm-12 col-xs-12 '.($message!=''?"has-error":"").'">
					<label> '.$attribute['setting_name'].'</label>
					'.$radio.'
					'.
					($message!=''?"<span class=\"help-block error-message\"> ".$message."</span>":"")
					.'
					</div>';

	}
	
	// function for generating the checkbox
	function build_checkbox_html($attribute){
		if($attribute['key_value']==1){
			$checked="checked";
		} else {
			$checked="";
		}

		$message = $this->check_message($attribute['unique_key']);
		
		return '<div class="form-group col-md-6 col-sm-12 col-xs-12 '.($message!=''?"has-error":"").'">
					<label> '.$attribute['setting_name'].'  </label>
					<input class="" type="checkbox" value="'.$attribute['key_value'].'" name="'.$attribute['unique_key'].'" '.$checked.'>
					'.
					($message!=''?"<span class=\"help-block error-message\"> ".$message."</span>":"")
					.'
				</div>'; 
	}
	
	function build_date_html($attribute){
	
		$message = $this->check_message($attribute['unique_key']);
		return '<div class="form-group col-md-6 col-sm-12 col-xs-12 '.($message!=''?"has-error":"").'">
					<label> '.$attribute['setting_name'].'  </label>
					<label><input type="text" placeholder="'.$attribute['setting_name'].'"  class="form-control bootdatepickersingle"  name="'.$attribute['unique_key'].'" value="'.(isset($_POST[$attribute['unique_key']])?date("Y-m-d",strtotime($_POST[$attribute['unique_key']])):date("Y-m-d",strtotime($attribute['key_value']))).'"><span class="lbl"></span></label>
					'.
					($message!=''?"<span class=\"help-block error-message\"> ".$message."</span>":"")
					.'
				</div>'; 
		
	}
	
	function build_file_html($attribute){
	 	$message = $this->check_message($attribute['unique_key']);
      	$file_name = $this->uploaddir.$attribute['key_value']; 
		echo 	'<div class="form-group col-md-6 col-sm-12 col-xs-12 '.($message!=''?"has-error":"").'">
					<label>'.$attribute['setting_name'].'</label>
					<input type="file" name="'.$attribute['unique_key'].'"><span><img src="'.$file_name.'" style="height:150px; width:200px;"> </span>
					'.
					($message!=''?"<span class=\"help-block error-message\"> ".$message."</span>":"")
					.'
				</div>';			
	}
}

class getData {
	//getting value of any attribute
	public static function getValue($key) {
		$key_query = "select key_value from cms_plugin_settings where unique_key = '".$key."'";
		$key_result = mysqli_query($GLOBALS["___mysqli_ston"], $key_query);
		$key_row = mysqli_fetch_assoc($key_result);
		return $key_row['key_value'];
	}
}
?>