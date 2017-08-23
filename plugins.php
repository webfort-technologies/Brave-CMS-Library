<?php
/*
Programmer : Shishir Raven
Purpose : Class to create thumbnail at runtime for Bigger images
How it works 
a) Whenever we have to display thumbnail call a object as $a = new thumbnail('image.jpg','200',300,'images')
	Parameter 1 - Image Name
	Parameter 2 - Image width
	Parameter 3 - Image height
	Parameter 4 - Image foldoer
	
b) This code first checks the Thumbnail folder inside the Image folder to see if the thumbnail of that size if already available
	If thumbnail is avialable it displays the same thumbail
	If thumbail is not avialbale it creates one. 
	The name of the thumbail would be like image_200_300.jpg for dimensions 200 X 300

Note : Create a folder named thumb and give it wirte rights before you use the class. 

*/

Class cms_plugin 
{
	var $plugin_name="";

	//Initializer function - We pass a array. Matches array keys with the class variable. if they match sets class vaiable value equal to the array key value.
	
	function initializer($config= array())
	{
		if(count($config)>0) // Checking to see if the array is empty or not
		{
				foreach($config as $key=> $value) // Looping a config array extracting array key and its value into $key and $value
				{
						if(isset($this->$key)) // Is a Class variable there corresponing to the array key
						{
							$this->$key=$value; // Setting Class Variable to the array Key Value
						}
				}
		}
	}



	function __construct($config=array()) 
	{
	// running initializer with the configration array
		$this->initializer($config);
	}	

	function fetchSettingKeys()
	{
		// From the Settings Table pick settings and store into variables.

		$setttings_query ="SELECT
			`cms_settings`.`id`,
			`cms_settings`.`section_name`,
			`cms_settings`.`setting_key`,
			`cms_settings`.`setting_value`
			FROM `a_retailers`.`cms_settings`
			where 
			`cms_settings`.`section_name`='paypal'
			";
		$settings_rs = mysql_query($setttings_query) or die(mysql_error());

		while($settings_row = mysql_fetch_array($settings_rs))
		{
			if(isset($this->$settings_row['setting_key'])) // Is a Class variable there corresponing to the array key
			{
				$this->$settings_row['setting_key']=$settings_row['setting_value']; // Setting Class Variable to the array Key Value
			}

		}
	}


	function getPluginName()
	{
		return $plugin_name;
	}
	
}



?>