<?php
include("pagination.php");
/*
	Author : Shishir Raven. 
	Website : shishirraven.com
	Purpose : To extend the current pagination class to include new facilities such as follows. 
	Created on : September 15, 2012
	Last Modified on : September 15, 1012
	
	
	1) Hover filters. 
	2) Records Searches. 
	3) Group Filters. 
	
	$config['query'] = "SELECT * FROM issue_comments WHERE id = '".$_REQUEST["issue_id"]."'";
	$config['emable_field_search'] = "true";
	
	$rec_page = new pagination_extended($config);
	$row_array  = $rec_page->get_array();
	
	$rec_page->show_links()
	
*/
//print_r($_REQUEST);

class pagination_extended extends pagination
	{
	var $where_clause	=	"";
	// CONSTRUCTOR
	function __construct($params=array()) 
	{
		
	//	exit;
		parent::__construct($params);
		$this->building_where_clause();
		//$this->get_current_page();
		$this->query_limit();
		
	}
	
	
	function column_search($table_field_name,$label)
	{
		$data = $_REQUEST;
		// REMOVING THE PREVIOUS sort_by COLUMN. 
		if(isset($data["pag_search_".$table_field_name]))
		{
			unset($data["pag_search_".$table_field_name]);
		}
		echo $label;
		?>
		<input type="" class="search_box" name="<?php echo "pag_search_".$table_field_name;?>"  id="<?php echo "pag_search_".$table_field_name;?>" value="<?php
		if(isset($_REQUEST["pag_search_".$table_field_name]))
		{
			echo $_REQUEST["pag_search_".$table_field_name];
		}
		?>">
		<input type="button" value="Go" onClick='location.replace("<?php echo $_SERVER['PHP_SELF']."?".http_build_query($data); ?>&<?php echo "pag_search_".$table_field_name;?>="+$("#<?php echo "pag_search_".$table_field_name;?>").val() )'/>
		<input type="button" value="Clear" onClick='location.replace("<?php echo $_SERVER['PHP_SELF']."?".http_build_query($data); ?>")'/>
		<?php
		
	}
	function create_filter_link($link, $label)
	{
		echo '	<a  href="'.$link.'"><div id="some_id" class="btn small " style="margin:2px;float:left;">'.$label.'</div></a>';
	}
	
	
	function create_filter_link_by_value($tablename, $columnname, $value, $label)
	{
		$data = $_REQUEST;
		if(isset($data["pag_search_".$tablename."--".$columnname]))
		{
			unset($data["pag_search_".$tablename."--".$columnname]);
		}
		$link = $_SERVER['PHP_SELF']."?".http_build_query($data)."&"."pag_search_".$tablename."--".$columnname."=".$value;
		echo '	<a  href="'.$link.'"><div id="some_id" class="btn small " style="margin:2px;float:left;">'.$label.'</div></a>';
	}
	
	
	function show_distinct_filter($tablename,$columname,$alias="")
		{
		$data = $_REQUEST;
		if(isset($data["pag_search_".$tablename."--".$columname]))
		{
			unset($data["pag_search_".$tablename."--".$columname]);
		}
		$myresult = mysql_query("select distinct($columname) from $tablename") or die(mysql_error());
		
		if($alias=="")
		{
			$searchitem =	$tablename."--".$columname;
		}
		else
		{
			$searchitem = 	$alias."--".$columname;
		}
		
		while($distinct_row = mysql_fetch_array($myresult))
		{
			$this->create_filter_link($_SERVER['PHP_SELF']."?".http_build_query($data)."&"."pag_search_".$searchitem."=".$distinct_row[0], $distinct_row[0]);
		}
	}
	
	
	function building_where_clause()
	{
		// Finding out all the get values that has starting with the string 'pag_search_'
		$data = $_REQUEST;
		/* echo "<pre>";
				print_r($_REQUEST);
		exit; */
		$where = array();
		$have = array();
		/* echo '<pre>';
		print_r($_REQUEST);
		
		exit;   */
		
			// Looping the data starts here, 
			foreach($data as $data_key => $data_value)
			{
				// checking the matching values. 
				if(strstr($data_key, 'pag_search_'))
				{
					// Starting to make the search array starts here. 
					if(strstr($data_key, '--'))
					{
					$data_key= str_replace('--','.', $data_key);
					}
					if($data_value=='active')
					{
						$where[] = $data_key. " = '". $data_value."'";
						array($data_key=>$data_value);
					}
					else 
						if($data_value!="")
						{
							$where[] = $data_key. " like '%". $data_value."%'";
						}
				}
				
				//LESSER THAN
				if(strstr($data_key, 'pag_lesser_search_'))
				{
					if(strstr($data_key, '--'))
					{
						$data_key= str_replace('--','.', $data_key);
					}
					if($data_value!="")
					{
						$where[] = $data_key. " <=". $data_value;
					}
				}
				
				//GREATER THAN
				if(strstr($data_key, 'pag_greater_search_'))
				{
					if(strstr($data_key, '--'))
					{
						$data_key= str_replace('--','.', $data_key);
					}
					if($data_value!="")
					{
						$where[] = $data_key. " >=". $data_value;
					}
				}
				
				if(strstr($data_key, 'pag_have_search_'))
				{	
					list($junction_table, $junc_first, $junc_sec, $first_table, $first_table_field, $sec_table, $sec_table_field) = explode("--", $data_key);
					
					//echo $junction_table.','.$junc_first.','.$junc_sec.','.$first_table.','.$first_table_field.','.$sec_table.','.$sec_table_field;
					if($data_value!="")
					{
						$data_value	=	implode(',',$data_value);
						$where[] = "$first_table.$first_table_field IN(select $junc_first from $junction_table where $junc_sec IN($data_value))";
					}
				
				//	echo $data_key;  exit;
					// Starting to make the search array starts here. 
					if(strstr($data_key, '--'))
					{
						$data_key= str_replace('--','.', $data_key); 
					}
					
					
				}
				
				if(strstr($data_key, 'pag_exact_search_'))
				{
					// Starting to make the search array starts here. 
					if(strstr($data_key, '--'))
					{
					$data_key= str_replace('--','.', $data_key);
					}
					if($data_value!="")
					{
						$where[] = $data_key. " = '".$data_value."'";
					}
					
				}
				if(strstr($data_key, 'pag_fulltext_search_'))
				{
					// Starting to make the search array starts here. 
					if(strstr($data_key, '--'))
					{
						$data_key= str_replace('--','.', $data_key);
						$data_key= str_replace('||',',', $data_key);
						$data_key_arr = explode('&&', $data_key); 
					}
					if($data_value!="")
					{
						$value	=	"+".str_replace(' ',' +',$data_value);
						$full_text_query =	array();
						foreach($data_key_arr as $data_key)
						{
							$full_text_query[] .= "MATCH ($data_key) AGAINST ('$value' IN BOOLEAN MODE)";
						}
				
						$where[]	=	implode(' OR ',$full_text_query); 
					}
				}
				if(strstr($data_key, 'date_pg_search'))
				{
					if($data_value!="")
					{
						// Starting to make the search array starts here. 
						if(strstr($data_key, '--'))
						{
						$data_key= str_replace('--','.', $data_key);
						}
						$myarray = explode(' to ',$data_value);
						$where[] = " (".$data_key. " between '".$myarray[0] ."' and '".$myarray[1]."') ";
					}
				}
				
			
			}
			// Checking to see if the where clause has more than one value. 
			if(count($where))
			{		
				$additional_statements = implode(' and  ',$where);
				if(strstr($this->query, 'where') || strstr($this->query, 'WHERE') || strstr($this->query, 'Where'))
				{
					$joinby = " and " ;
				}
				else
				{
					$joinby = " where ";
				}
				$this->where_clause = $joinby.$additional_statements;
				$before=$this->query;
				$after="";
				if(strstr(strtolower($this->query), 'group by'))
				{
					list($before, $after) = explode('group by', strtolower($this->query), 2);
					$after=" group by".$after;
				}
				$this->query = $before.str_replace('pag_search_','', $joinby.$additional_statements).$after;
				$this->query = str_replace('date_pg_search_','', $this->query );
				$this->query = str_replace('pag_exact_search_','', $this->query );
				$this->query = str_replace('pag_fulltext_search_','', $this->query );
				$this->query = str_replace('pag_have_search_','', $this->query );
				$this->query = str_replace('pag_lesser_search_','', $this->query );
				$this->query = str_replace('pag_greater_search_','', $this->query );
				
				 //echo $this->query; exit;
				 // exit;	
			}
		// Creating the where array of the pag_search variable. 
		 	if(count($have))
			{			
				$additional_statements = implode(' and  ',$have);
				if(strstr($this->query, 'having'))
				{
					$joinby = " and " ;
				}
				else
				{
					$joinby = " having ";
				}
				$before=$this->query;
				$after="";
				if(strstr(strtolower($this->query), 'group by'))
				{
					list($before, $after) = explode('group by', strtolower($this->query), 2);
					$after="group by".$after;
				}
				
				$this->query = $this->query.$joinby.$additional_statements;
				$this->query = str_replace('pag_have_search_','', $this->query );
				
				 //echo $this->query; exit;
				 // exit;	
			} 
	}
	
		
		// Creating the where array of the pag_search variable. 
	
	
	function scafolding()
	{
		// Function responsible for displaying the Array into a Table format. 
		$data_array = $this->get_array();
		echo "<table border='1'>";
		
		
		
		foreach($data_array as $datarow)
		{
			echo "<tr>";
			foreach($datarow as $datacolumn=>$datavalue)
			{
				echo "<td>".$datavalue."</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}
	
	function where_clause()
	{
		return $this->where_clause;
	}
	
	
}// Class Ends here 
	


?>