<?php
include("connection.php");

class Reporting {
	var $query = "";
	function __construct()
	{
	  //$this->dasres();
		  
	}
	function initalize($config)
	{
			
			
		//	select %s,t.id as id,t.name as task_name,task_statuses.name as task_status,task_types.name as task_type,vtu.username as assign_username,vt.username as create_username FROM `tasks` as t left join task_statuses on t.`task_status_id` = task_statuses. id left join task_types on t.`task_type_id` = task_types.id left join vt_users as vtu on t.`assigned_to` = vtu.id left join vt_users as vt on t.`created_by` = vt.id where 1 %sr
		
			//echo $this->query; exit;
		
	}
	
	
	// initalize the search form 
	
	function computeGraph($data, $qry){
			//echo $qry; exit;
		$obj = new AdvaceSearch();
		
			$qry_str =  preg_replace("#^select #i", "select %s,", $qry);
			
			//$qry_str =  preg_replace("#and 1=1 #i", "%sr", $qry_str);
			//$qry_str =  preg_replace("#where 1 #i", "%sr", $qry_str);
			
			//echo $qry_str; exit;
		if(!empty($data['filter'])){
					$filter_query =  $obj->buildSearchQuery($data['filter']);
		}
		if($data['graph_type'] == 'pie'){
			
			$qury_part = "";
			if(!empty($data['value_function'])){
				$qury_part_fun = ", ".$data['value_function']."(".$data['c1_field'].")";
			}
			if($data['sort_field'] == 'c1_val'){
				$qury_part_sort = " order by ".  $data['c1_field'];
			}else{
				$qury_part_sort = " order by ". $data['c2_field'];
			}
			if(!empty($data['value_limit']) && is_numeric($data['value_limit'])){
				$qry_part_limit = "LIMIT 0, ".$data['value_limit'];
			}else{
				$qry_part_limit = "LIMIT 0,25 ";
			}
				$reprt_select	= $data['c1_field']. $qury_part_fun;
				
				$filter_query_with = $filter_query. " GROUP BY ".$data['c1_field']." ".$qury_part_sort. " ".$data['sort_order'];  
				
				
			//$querys = str_replace('%s',$reprt_select,$qry_str); // replace the %s from the query with report query
			//$querys = str_replace('%sr',$filter_query_with,$qry_str); // replace the %sr from the query with report query
			$querys = str_replace('%s',$reprt_select,$qry_str);
			$querys = $querys." ".$filter_query_with." ".$qry_part_limit;
			
			
			//$query =  "SELECT ".$data['c1_field']. $qury_part_fun ." FROM ".$_POST['table_name']." WHERE 1 ".$filter_query." GROUP BY ".$data['c1_field']." ORDER BY ".$qury_part_sort ." ".$data['sort_order']. " ".$qry_part_limit ;
			//echo $querys; exit;
			 session_start();
			 $_SESSION['reporting']['repoet_query'] = $querys;
			 
			
			$resource = mysql_query($querys) or die();
			  while($row =  mysql_fetch_row($resource)){
				$result[] = $row;
			  }
			
				return $result;
		
			
		}		
		
		
	
		//exit;
		
	}
	
	
	
	
}

?>