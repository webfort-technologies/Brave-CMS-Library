<?php
include("pagination_extended.php");
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
	$config['enable_field_search'] = "true";
	
	$rec_page = new pageination_extended($config);
	$row_array  = $rec_page->get_array();
	
	$rec_page->show_links()
	
*/
//print_r($_REQUEST);

class pagination_extended_search extends pagination_extended
	{
	
	var $where_clause	=	"";
	// CONSTRUCTOR
	function __construct($params=array()) 
	{
		
		parent::__construct($params);
		
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
				
						$where[]	=	'('.implode(' OR ',$full_text_query).')'; 
					}
				}
	
				if(strstr($data_key, 'pag_multiple_search_'))
				{	
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
				$this->query = str_replace('pag_multiple_search_','', $this->query );
				
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
	
	function perpage_selector_restaurant()
	{
		?>
		<form name="per_page2" action="" class="form-horizontal" role="form" method="get">
			<div class="perPage">
		<div class="form-group">

    	<label for="" class="control-label" style="padding-top:0;">Show per page</label>
			<?php 
			$per_page1 = "";
			$per_page2 = "";
			$per_page3 = "";
			$per_page4 = "";
			$per_page5 = "";
			$per_page6 = "";
			if($this->rows_per_page==100)
			{
				$per_page1="selected='selected'";
			}
			else if($this->rows_per_page==50)
			{
				$per_page2="selected='selected'";
			}
			else if($this->rows_per_page==30)
			{
				$per_page6="selected='selected'";
			}	
			else if($this->rows_per_page==20)
			{
				$per_page3="selected='selected'";
			}	
			else if($this->rows_per_page==5)
			{
				$per_page5="selected='selected'";
			}
			else
			{
				$per_page4="selected='selected'";
			}
			
			?> 
			
			<select class="no-padding" name="per_page" id="per_page" onChange='location.replace("<?php
			$data= $_REQUEST;
			if(isset($data["per_page"]))
			{
				unset($data["per_page"]);
			}
			echo "restaurant_search.php?".http_build_query($data); ?>&<?php echo "per_page";?>="+$("#per_page").val() )'>
			
				<option <?php echo $per_page5;?>>5</option>
				<option <?php echo $per_page4;?>>10</option>
				<option <?php echo $per_page3;?>>20</option>
				<option <?php echo $per_page6;?>>30</option>
				<option <?php echo $per_page2;?>>50</option>
				<option <?php echo $per_page1;?>>100</option>
			</select></div>
			</div>
		</form>
		
		<?php
	}
	
	function show_links_google_type_restaurant($hash_string="")
	{
		// Return if total not of pages is 1. 
		$this->page_name = 'restaurant_search.php';
		if($this->total_pages==1)
		{
			return;
		}


		$cur_page_number=1;
		$link="";
		$querystring_array = $_REQUEST;
		

		if(isset($_REQUEST[$this->page_no_variable]))
		{
			$cur_page_number=$_REQUEST[$this->page_no_variable];
		}
		
		$querystring_array[$this->page_no_variable]=$cur_page_number;
		
		
		
		
			
			
		$this->pagebreak_google_type($cur_page_number,$this->total_pages);

		
		
		if($cur_page_number!=1)
		{
		$querystring_array[$this->page_no_variable] = 1;
		$query_string = http_build_query($querystring_array);	
		$link .= "<li class='".$this->link_class."' ><a href='".$this->page_name."?".$query_string.$hash_string."'>First</a></li>";
		
		
		$prev_link=	$cur_page_number-1;
		$querystring_array[$this->page_no_variable] = $prev_link;
		$query_string = http_build_query($querystring_array);	
		$link .= "<li class='".$this->link_class."'><a href='".$this->page_name."?".$query_string.$hash_string."'>&laquo; Prev</a></li>&nbsp;";
		echo $link;
		}


			$current_class ="";
			for($i=$this->mins;$i<=$this->maxs;$i++)
		{
			
			
			// Adding Classes to the Start TAG
			if($i==$this->cur_page)
			{
			//exit();
					$temp_link_start= str_replace(">", " class='".$this->link_class_current."'>",$this->link_page_start);
			}
			else
			{
					$temp_link_start= str_replace(">", " class='".$this->link_class."'>",$this->link_page_start);
			}
			$querystring_array[$this->page_no_variable] = $i;
			$query_string = http_build_query($querystring_array);	
			
			
			if($cur_page_number==$i)
			{
				$current_class = "class='".$this->link_class_current."'";
			}
			
			if($i==$this->cur_page)
			{
				$linkclass = $this->link_class_current;
			}
			else
			{
				$linkclass = $this->link_class;
			}
			
			$link = "<li class='".$linkclass."' ><a ".$current_class." href='".$this->page_name."?".$query_string.$hash_string."'>".$i."</a></li>";
			echo $temp_link_start.$link.$this->link_page_end;
			$current_class="";
		}
		
		$link="";

		if($cur_page_number!=$this->total_pages)
		{
		$prev_link=	$cur_page_number+1;
		$querystring_array[$this->page_no_variable] = $prev_link;
		$query_string = http_build_query($querystring_array);	
		
		
		$link .= "&nbsp;<li class='".$this->link_class."' ><a href='".$this->page_name."?".$query_string.$hash_string."'>Next&nbsp;&raquo;</a></li>";
		
		$querystring_array[$this->page_no_variable] = $this->total_pages;
		$query_string = http_build_query($querystring_array);	
		$link .= "<li class='".$this->link_class."' ><a href='".$this->page_name."?".$query_string.$hash_string."'>Last </a></li>";
		echo $link;

		
		}

	}

	function where_clause()
	{
		return $this->where_clause;
	}
	
	
}// Class Ends here 
	


?>