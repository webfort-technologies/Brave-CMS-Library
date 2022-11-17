<?php
include("pagination_extended.php");
/*

	Author : Shishir Raven. 
	Website : shishirraven.com
	Purpose : To extend the current pagination class to include new facilities such as follows. 
	Created on : September 15, 2012
	Last Modified on : Feburary, 02 2018
	
	1) Hover filters. 
	2) Records Searches. 
	3) Group Filters. 
	
	$config['query'] = "SELECT * FROM issue_comments WHERE id = '".$_REQUEST["issue_id"]."'";
	$config['enable_field_search'] = "true";
	
	$rec_page = new pageination_extended($config);
	$row_array  = $rec_page->get_array();
	
	$rec_page->show_links()
	
	SHORT CODES FOR SEARCH 
	---------------
	1. Like Search
	---------------
	Prefix. 
	s__ 	: Basic Search /  field = %term$ 
	
	Example : 
	<input name="s__table_name--fieldname"/>
	---------------------------------------------------------

	--------------------
	2. Less than Search
	--------------------
	Prefix. 
	lts__ 	: Value Lesser than  / field  <= %term%
	
	Example : 
	<input name="lts__table_name--fieldname"/>
	---------------------------------------------------------

	--------------------
	3. Greater than Search
	--------------------
	Prefix. 
	gts__ 	: Value Lesser than  / field  <= %term%
	
	Example : 
	<input name="gts__table_name--fieldname"/>
	---------------------------------------------------------

	--------------------

	--------------------
	4. EXACT SEARCH
	--------------------
	Prefix. 
	es__ 	: Exact Phase Search  / field = 'term'
	
	Example : 
	<input name="es__table_name--fieldname"/>
	---------------------------------------------------------

	--------------------
	5. FULL TEXT SEARCH
	--------------------
	Prefix. 
	fts__ 	: Full Text Search 
	
	Example : 
	<input name="fts__table_name--fieldname"/>
	---------------------------------------------------------

	--------------------
	5. MULTIPLE ELEMENTS SEARCH
	--------------------
	Prefix. 
	ms__ 	: Multiple Search 
	
	Example : 
	<name="ms__table_name--fieldname"/>
	---------------------------------------------------------






	--------------------
	4. Junction Search. 
	--------------------
	Where to apply.
	On More than one Foriegn key fields are linked to a single record. 
    Like more than one categories can be applied to a single record. 

	Prefix. 
	js__ 	: 

	Structure of Field Name : First Table -> Junction -> Relational Table. 
	a. First Table Name. 
	b. Juction Table Name. 
	c. Junciton Field 1. 
	d. Junciton Field 2. 
	e. Relationall table. 
	
	Example : 
	<select multiple name="js--firstTabeName--junctionTableName--junctionField1--juncitonField1--relationalTable"/>
	--------------------------------------------------------

	*/
class pagination_with_search extends pagination_extended
	{
	
	var $where_clause	=	"";
	var $fql_where  	=	"";
	// CONSTRUCTOR
	function __construct($params=array()) 
	{
		$this->prepareFQL();
		parent::__construct($params);	
	}

	function prepareFQL()
	{
		// ====================================================
		// LOADING RESULTS FORM FQL 
		// ====================================================
		$fql_where="";
		if(isset($_GET['filter']) && $_GET['filter']!="")
		{
		  $fql_where = "where ".html_entity_decode($_GET["filter"]); 
		}
		// ====================================================

		$fql_where = preg_replace_callback(
            '#(datestring|now|endOfDay|endOfWeek|endOfMonth|endOfYear|startOfDay|startOfWeek|startOfMonth|startOfYear)\((.*?)\)#',
            function ($matches){
              $date_ar = array(); 
              $date_ar['datestring']         = "";
              $date_ar['now']         = "now";
              $date_ar['endOfDay']    = "12 pm";
              $date_ar['endOfWeek']   = "Sunday this week 12pm";
              $date_ar['endOfMonth']  = "last day of this month 12pm";
              $date_ar["endOfYear"] ='last day of december this year 12pm';
              $date_ar["startOfDay"] = "12am";
              $date_ar["startOfWeek"] ="first day of this month 12am";
              $date_ar["startOfMonth"] ="Monday this week 12am";
              $date_ar["startOfYear"] ="first day of January this year 12am";

              $final_time = time();
              if($date_ar[$matches[1]]!="")
              {
              	$final_time = strtotime($date_ar[$matches[1]]); 	
              }

              if(trim($matches[2])!="")
              {
              	$final_time= strtotime($matches[2],$final_time);
              }

              $final_date = date('Y-m-d H:i:s',$final_time);
             // $final_date = str_replace(" 00:00:00", "", $final_date);
              return "'".$final_date."'";
            }
            ,$fql_where);
			$this->fql_where = $fql_where;
	}
	
	function postExeuctionQueryhook()
	{
		if(isset($_GET['filter']) && $_GET['filter']!="")
		{
			$this->query = "select * from (".$this->query.") as fql ".$this->fql_where;
		}
		//echo $this->query; 
		//exit; 
	}
	
	function building_where_clause()
	{	
		// Finding out all the get values that has starting with the string 'pag_search_'
		$data = $_REQUEST;
		$where = array();
		$have = array();
		
		// Looping the data starts here, 
		foreach($data as $data_key => $data_value)
		{
			$data_key = str_replace("___", "--", $data_key);
			//-----------------------------------------------
			// LESSER THAN SEARCH
			//-----------------------------------------------
			if(strstr($data_key, 'lts__'))
			{
				$data_key= str_replace('lts__','',$data_key);
				if(strstr($data_key, '--'))
				{
					$data_key= str_replace('--','.', $data_key);
				}
				if($data_value!="")
				{
					$where[] = $data_key. " <=". $data_value;
				}
			}
			//-----------------------------------------------

			//-----------------------------------------------
			// GREATER THAN SEARCH
			//-----------------------------------------------
			if(strstr($data_key, 'gts__'))
			{
				$data_key= str_replace('gts__','',$data_key);
				if(strstr($data_key, '--'))
				{
					$data_key= str_replace('--','.', $data_key);
				}
				if($data_value!="")
				{
					$where[] = $data_key. " >=". $data_value;
				}
			}

			//-----------------------------------------------
			// JUNCTION SEARCH
			//-----------------------------------------------
			if(strstr($data_key, 'rs__'))
			{	
				$data_key= str_replace('rs__','',$data_key);
				list($table, $junction_table, $jun_field1, $jun_field2,$relational_table) = explode("--", $data_key);
				if($data_value!="")
				{
					$data_value	=	implode(',',$data_value);
					$where[] = "$table.id IN (select $jun_field1 from $junction_table where $jun_field2 IN ($data_value))";
				}
			}
			// ----------------------------------------------
			
			//-----------------------------------------------
			// EXACT PHRASE SEARCH
			//-----------------------------------------------	
			if(strstr($data_key, 'es__'))
			{
				$data_key= str_replace('es__','',$data_key);
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
			// ----------------------------------------------
			
			//-----------------------------------------------
			// FULL TEXT SEARCH
			//-----------------------------------------------
			if(strstr($data_key, 'fts__'))
			{
				$data_key= str_replace('fts__','',$data_key);
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
			
					$where[]	=	'('.implode(' OR ',$full_text_query).')'; 
				}
			}
			// -----------------------------------------------------------

			//-------------------------------------------------------------
			// MULTIPLE SEARCH
			//-------------------------------------------------------------
			if(strstr($data_key, 'ms__'))
			{	
				$data_key= str_replace('ms__','',$data_key);
				// Starting to make the search array starts here. 
				if(strstr($data_key, '--'))
				{
					$data_key= str_replace('--','.', $data_key);
				}
				if(is_array($data_value) && !empty($data_value))
				{	
					$multiple_query = array();
					// For Array come in where clause
					foreach($data_value as $pkey=>$pvalue)
					{
						$str = $data_key. " = ". $pvalue."";
						//if(!in_array($str,$multiple_query))
						$multiple_query[] = $str;
					}	
					$where[]	=	'('.implode(' OR ',$multiple_query).')'; 
				}
				else
				{
					if($data_value!="")
					{
						$where[] = $data_key. " = ". $data_value."";
					}
				}
			} 
			// --------------------------------------------------------------
			
			//-------------------------------------------------------------
			// DATE SEARCH
			//-------------------------------------------------------------
			if(strstr($data_key, 'ds__'))
			{
				$data_key= str_replace('ds__','',$data_key);
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
			// --------------------------------------------------------------

			//-----------------------------------------------
			// LIKE SEARCH
			//-----------------------------------------------  
			if(strstr($data_key, 's__'))
			{
				$data_key= str_replace('s__','',$data_key);
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
			//-----------------------------------------------
			
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
			$this->query = $before.$joinby.$additional_statements.$after;
			/*$this->query = $before.str_replace('lts__','', $joinby.$additional_statements).$after;
			$this->query = $before.str_replace('gts__','', $joinby.$additional_statements).$after;
			$this->query = $before.str_replace('ms__','', $joinby.$additional_statements).$after;
			$this->query = $before.str_replace('js__','', $joinby.$additional_statements).$after;
			$this->query = $before.str_replace('es__','', $joinby.$additional_statements).$after;
		    $this->query = $before.str_replace('s__','', $joinby.$additional_statements).$after;*/
			
			
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
		} 
	}
	
	function where_clause()
	{
		return $this->where_clause;
	}
	
	
}// Class Ends here 
	


?>