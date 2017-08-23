<?php 
/*
Purpose - Provide a JSON Api Class. 
Methods Suppported = create_administrator, create_organization
*/
include ("users.php");
include ("create_instance.php");

class support_chat_api
{
	var $valid_methods = array('create_administrator','create_organization');
	var $data = '';
	var $record_id = '';
	var $org_id = '';
	var $unique_key= '';

	function __construct(){

	}

	function json_action($json_string)
	{
		$data = json_decode($json_string,true);	
		if($data === null) {
			throw new Exception('Invalid JSON');
		}

		$this->action($data['method'], $data['parameters']);
	}

	function action($method, $parameters)
	{
		if(!in_array($method, $this->valid_methods))
		{
			throw new Exception('Invalid Method');
		}
		$method_action = "action_" . $method; 
		$this->$method_action($parameters);
	}

	function action_create_administrator($parameters){
		$registeration = new users();
		foreach ($parameters as $key => $value) {
			$setmthd = "set".$key;
			$registeration->$setmthd($value);
		}
		$registeration->create_user_in_db();
		$this->record_id = $registeration->record_id;

	}
	function action_create_organization($parameters){
		$organization = new create_instance();
		$organization->setuser_id($this->record_id);
		foreach ($parameters as $key => $value) {
			$setmthd = "set".$key;
			$organization->$setmthd($value);
		}
		$organization->create_organization();
		$this ->org_id = $organization->org_id;
		$organization->setorg_id($this->org_id);
		$organization->create_link();
		$this->unique_key = $organization->unique_key;
	}


}

 ?>