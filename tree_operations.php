<?php
/*
Programmer : Shishir Raven
last Modified : Thursday Sept 05, 2013
Purpose : Class is responsible for rebuilding and updating the tree


/*   Example of how to use the class
include("library/tree_operations.php");
$config['image']="sample.jpg";
$thumb1 = new thumbnailer($config);
echo "<img src='".$thumb1->create_thumb()."'/>";
 */
Class tree_operations
{
	//Initializer function - We pass a array. Matches array keys with the class variable. if they match sets class vaiable value equal to the array key value.
	

	private $table_name = "";
	private $left_field = "lft";
	private $right_field = "rgt";
	private $parent_field = "pid";
	private $id_field = "id";
 

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
	function tree_operations($config)
	{
		// running initializer with the configration array
		$this->initializer($config);
	}	


	// Make Routes.


	public function rebuild_tree( $parent , $left )
	{

 	// the right value of this node is the left value + 1   
		$right = $left+1;   
		// get all children of this node   
		$query = 'SELECT id FROM ' . $this->table_name  .  

	                           ' WHERE ' . $this->parent_field.'="'.$parent.'";';
		$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . $query);   


	    while ($row = mysqli_fetch_array($result)) {   

	        // recursive execution of this function for each   

	        // child of this node   

	        // $right is the current right value, which is   

	        // incremented by the rebuild_tree function   

	        $right = $this->rebuild_tree($row['id'], $right);   

	    }   

	   

	    // we've got the left value, and now that we've processed   

	    // the children of this node we also know the right value   

	    mysqli_query($GLOBALS["___mysqli_ston"], 'UPDATE '. $this->table_name  . 
	    				' SET ' . 
	    					$this->left_field . '='.$left.', 
	    					' . $this->right_field . '='. $right.' 
	    				WHERE 
	    					id="'.$parent.'";');   

		 // return the right value of this node + 1   

	    return $right+1;   
	}

	function add_new($current_node)
	{
 		// Queries. 
		// UPDATE tree SET rgt=rgt+2 WHERE rgt>5;   
		// UPDATE tree SET lft=lft+2 WHERE lft>5;
		// Finding out the right of the current node. 
		$current_rs = mysqli_query($GLOBALS["___mysqli_ston"], "select ". 
									$this->right_field .
								" from ". 
									$this->table_name 
								."where 
									
								 ") or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		// Adding at new node inside the nodes. 
		mysqli_query($GLOBALS["___mysqli_ston"], "BEGIN");
		$current_row = mysqli_fetch_array($current_rs);
		$current_row_right = $current_row['" . $this->right_field . "'];
		$update_query1 = mysqli_query($GLOBALS["___mysqli_ston"],  "UPDATE " . $this->table_name . " SET rgt=rgt+2 WHERE rgt>". $current_row['" . $current_row_right . "'] );
		$update_query2 = mysqli_query($GLOBALS["___mysqli_ston"],  "UPDATE " . $this->table_name . " SET lft=lft+2 WHERE lft>". $current_row['" . $current_row_right . "'] );
		return array( 
					   'left'  => $current_row_right+1, 
					   'right' => $current_row_right+2
					 );
		if( $update_query1 && $update_query2)
		{
			mysqli_query($GLOBALS["___mysqli_ston"], "COMMIT");
		}
		else
		{
			mysqli_query($GLOBALS["___mysqli_ston"], "ROLLBACK");
		}
	}

	function run_page($url)
	{
		// Page is responsible for running a page from the router file. 

	}


}
include("../connection.php");
// Testing the Class Starts here. 
$config['table_name']="cms_product_categories";
$myobject = new tree_operations($config);
$myobject->rebuild_tree(3,1); 

?>