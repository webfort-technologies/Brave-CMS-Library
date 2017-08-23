<?php 
	include_once("users.php");
	/*
	Purpose: Class to perform operations on table vt_users
	*/
	class users_extended extends users{
		var $record_id;
		
		function setrecord_id($record_id)
		{
			$this->record_id = $record_id;
		}

		function getrecord_id()
		{
			return $this->record_id;
		}
		
		$this->record_id = parent::create_user_in_db();
	}
 ?>