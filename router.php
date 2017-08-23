
<?php
/*
Programmer : Shishir Raven
last Modified : Friday Aug 16, 2013
Purpose : Class responsible for creating routing for Clean URL's 


/*   Example of how to use the class
include("../router.php");
$config['image']="sample.jpg";
$config['folder']="images";
$config['width'] ="100";
$config['height']="300";
$config['compression']="100";
$config['fit_to_box']=true;
$thumb1 = new thumbnailer($config);
echo "<img src='".$thumb1->create_thumb()."'/>";
 */
Class router
{

	var $image="";
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
	
	// Constructor  
	function router($config)
	{
		// running initializer with the configration array
		$this->initializer($config);
	}	


	// Make Routes.

	function create_route($url)
	{
		// Takes URL as input and outputs a clean working URL. 

		// fetching script_name and page_name
		$script_name = "";
		$get_array = $_GET;
		// Finding the mataching variables and converison into Links start here. 
		// /Coolstuff - 


		//http_build_query($data);

		// 2. Finding the matching element and Coverting them to Route Array. 

	}

	function parse_route($url)
	{
		// Takes a route and converts it
		// into specific set of Get Statements. 

	}

	function run_page($url)
	{
		// Page is responsible for running a page from the router file. 

	}


}



?>