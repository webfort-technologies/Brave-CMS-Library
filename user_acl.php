<?php
/*
Programmer : Jay Prakash Jain
Purpose : Class to check Capablities of the User. 

Example of how to use the class
	Step 1 : Include file -  include("library/user_acl.php"); 

	Step 2 : Put the following code where you wish to check. 

		$a = new ACL();
		$a->user_id = 5;
		$a->has_capability('$capability_id',$role_id);

		// this Funciton return true and false
		$a->require_capability($capability_id);

		// this fuction return true and false
		//Funtion to get all user ids below this user
$a->get_sub_usersids();
return true or false 
*/
Class ACL
{
	public  $user_id;
	public $roleList;
	public $parentroleList;
	var $user_role_ar = array();

	public  $link = 'test.php';

	function __construct()
	{

	}

	function isCapable($capablity_id)
	{
		// New function to pull up capablity Junction Table. 
		// Finding All the Roles that the user holds
		// Finding if the capablity is true for any for the roles. 
		$capablity = false;
		$this->getAllRoles();
		foreach($this->user_role_ar as $role_id)
		{
			if($this->has_capability($capablity_id,$role_id))
			{
				$capablity=true;
				break;
			}
		}
		return $capablity;
	}

	function getAllRoles()
	{
		// 1. Query to database for finding all roles
		$roles_rs = mysql_query("SELECT 
										*
									FROM
										acl_junction_user_role
									WHERE 
										user_id ='". $this->user_id ."'
									");
		// 2. Putting the results in the Role Array
		if(mysql_num_rows($roles_rs)>0)
		{
			$this->user_role_ar = array();
			while($roles_row = mysql_fetch_array($roles_rs))
			{
				$this->user_role_ar[] = $roles_row['role_id'];
			}
		}
	}


	function get_parent_role($role_id)
	{
		$sql = "select parent_id from acl_user_role where id = $role_id";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			return $row['parent_id'];
		} 
	}
	function has_capability($capability_id,$roleid=-1)
	{
		$roleid = ($roleid == -1)?$this->getRoleid():$roleid;
		if($roleid == 1)
		{
			return true;
		}
		
	
	
		 $query	= "SELECT * FROM acl_user_role_assignment where capability_id = ".$capability_id." and role_id = ".$roleid." ";
		$res	= mysql_query($query);	
		if(mysql_num_rows($res)>0)
		{
			$row = mysql_fetch_array($res);			
			if ($row['permission'] == 'Allow')
			{
				return true;
			} else if($row['permission'] == 'Inherit')
			{		
			 	 return $this->has_capability($capability_id,$this->get_parent_role($roleid));
				//$this->parent_role($this->getRoleid());
				 
			}			
			else
			{	
				return false;
			}
		}
	}
	
	
	

	function require_capability($capability_id)
	{		
		$query	= "SELECT * FROM acl_user_role_assignment where capability_id = ".$capability_id." and role_id = ".$this->getRoleid()." ";
		$res	= mysql_query($query);	
		if(mysql_num_rows($res)>0)
		{
			$row = mysql_fetch_array($res);	
			if ($row['permission'] == 'Allow')
			{
				return true;
			}  
			else
			{
				//echo 'falseasddas';
				return false;
			}				
		} 		 
	}
	
	function  get_sub_usersids()
	{
		//$rolesid = $this->getrolesid();
		 $role_id = $this->getRoleid();
		 $this->check1($role_id);
		//print_r($this->roleList);
		
		$user_id[] = $this->user_id;
		if(is_array($this->roleList))
		{		 
			$query	= "SELECT id FROM vt_users  where role_id IN ( ".implode(",",$this->roleList)." )";
			$res	= mysql_query($query);
			//$user_id	= array();
			 if(mysql_num_rows($res)>0)
			 {
				while($row = mysql_fetch_array($res))
				{
					$user_id[] = $row['id'];
				}
				$userid = implode(",", $user_id);
			 }			 
		}
		$userid = implode(",", $user_id);
		 //echo $userid."---";
		return $userid; 		 		
	}
	 
	
			 
	function check1($ids)
	{	
		global $data;

		if(empty($this->roleList))
		{
			//$this->roleList[] = $ids;
		}
		$sql = "select * from acl_user_role where parent_id = $ids";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_array($result))
			{
			 	$this->roleList[]=$row['id'];
				$this->check1($row['id']);			
			}			
		}
		//return $data;
	}
	 
	function  getrolesid()
	{
		$ss = $this->getRolename();
	//	echo '<pre>';
		//print_r($ss);
		$query = "SELECT id FROM acl_user_role where parent_id > ".$ss['parent_id']." ";
		$res = mysql_query($query);
		$rid = array();
		if(mysql_num_rows($res)>0){
		while($row = mysql_fetch_array($res))
		{
			$rid[] = $row['id'];
		}
	 	$rolesid = implode(",", $rid);		
		return $rolesid; 
		}
	}
	// Get the capabilityid	
	public function  getCapabilityid ($groupcapability_name, $capability_name)
	{
		$query = "SELECT id FROM acl_user_capability where capability_name = '".$capabilityname."' ";
 		$res = mysql_query($query);
		if(mysql_num_rows($res)>0){
		$row = mysql_fetch_array($res);
		$capability_id = $row['id'];	 
		 return  $capability_id;
		}		  	 	
	}
	
	public function  getCapabilityname ($capabilityid)
	{
		$query		= "SELECT capability_name FROM acl_user_capability where id = '".$capabilityid."' ";
		$res		= mysql_query($query);
		$row		= mysql_fetch_array($res);
		$capability_name = $row['capability_name'];
		return $capability_name;		
	}
	
	function getRoleid()
	{
		$query		= "SELECT role_id FROM vt_users where id = ".$this->user_id." ";
		$res		= mysql_query($query);
		if(mysql_num_rows($res)>0){
		$row		= mysql_fetch_array($res);
		$role_id	= $row['role_id'];		
		return $role_id;	
		}
	}
	
	function getRolename()
	{
	 	$query		= "SELECT id, role_name, parent_id FROM acl_user_role where id = ".$this->getRoleid()." ";
		$res		= mysql_query($query);
		$row		= mysql_fetch_array($res);
		$role_name	= $row['role_name'];		
		//return $row;
		return $role_name	;
	}	
}

//$a = new ACL();
//$a->user_id = 6;
// echo $a->getusersid();
 //$a->require_capability('read');

?>