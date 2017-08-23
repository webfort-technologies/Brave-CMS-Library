<?php
include("connection.php");
class AdvaceSearch {

	function __construct()
	{
	  //$this->dasres();
		  
	}
	// initalize the search form 
	function initalize($config)
	{
		$result = array();
		
		for($i= 0; $i<count($config);$i++) {
			$result[$i]['column_values'][] = $config[$i]['table'];
			
			for($k=0;$k<count($config[$i]['columns']);$k++){
				$j =1;
				$result[$i]['column_values'][] = $config[$i]['columns'][$k];
				$result[$i]['column_alias'][] =  $config[$i]['column_alias'][$k];
				$j++;
			}
			
				
		}
			
			//echo '<pre>';print_r($result) ;
			return $result;
	}
	function buildSearchQuery($data){
		 // echo "<pre>";  
		
		// print_r($_SESSION['client_contact']);
		
		$filter_query = array();
		  for($i=0;$i<count($data['grps']);$i++){
			
			$search = "";
			$operator_type = $data['operators'][$i];
			switch($operator_type){
				case 'LIKE':
				case 'NOT LIKE':
					if($data['grps'][$i+1] == 1){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. " '%".$data['terms'][$i]."%' ".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '%".$data['terms'][$i]."%' ".")";
						$i = $i+1;
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '%".$data['terms'][$i]."%'";
					}
					
					
				break;
				case '=':
				case '!=':
					if($data['grps'][$i+1] == 1){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'".")";
						$i = $i+1;
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'";
					}
					
					break;
				case '&gt;':
				case '&gt;=':
				case '&lt;':
				case '&lt;=':
					
					if(is_integer($data['terms'][$i])){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. "".$data['terms'][$i]."".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. "".$data['terms'][$i]."".")";
						$i = $i+1;
						
						
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. "".$data['terms'][$i]."";
					}
						
					break;
				
				case 'BETWEEN':
				case 'NOT BETWEEN':
					if(is_integer($data['terms'][$i])){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. "".$data['terms'][$i]."".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. "".$data['terms'][$i]."".")";
						$i = $i+1;
						
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]." ".$data['terms'][$i]."";
					}
					
				break;
				case 'IN':
				case 'NOT IN':
					if(is_integer($data['terms'][$i])){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. " ('".$data['terms'][$i]."')".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " ('".$data['terms'][$i]."')".")";
						$i = $i+1;
						
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '"." ('".$data['terms'][$i]."')";
					}
					
				break;
				case 'REGEXP':
				case 'NOT REGEXP':
					if(is_integer($data['terms'][$i])){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'".")";
						$i = $i+1;
						
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'";
					}
				
				break;
			}
			
			//$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i].$operator_val;
			
		  }
			
		
		  
		  for($k=0;$k<count($filter_query);$k++){
				$query  .=" ".$filter_query[$k];
		  }
		   	    $_SESSION['advan_search'] = $query;
				
				$message=array('title'=>'Filter','text'=>'Your Filter is Apply','type'=>'success');
 
				$_SESSION['message'][]=$message;
			 
			 
		   return $query;
			//echo $query ; exit;
	}
	
	
	function advan_sea($pagename){
	
		//echo '<pre>';
		 //echo $_SESSION['client_contact']['grps'];
		$data =  $_SESSION[$pagename];
		//print_r($_SESSION);
		$filter_query = array();
		  for($i=0;$i<count($data['grps']);$i++){
			
			$search = "";
			$operator_type = $data['operators'][$i];
			switch($operator_type){
				case 'LIKE':
				case 'NOT LIKE':
					if($data['grps'][$i+1] == 1){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. " '%".$data['terms'][$i]."%' ".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '%".$data['terms'][$i]."%' ".")";
						$i = $i+1;
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '%".$data['terms'][$i]."%'";
					}
					
					
				break;
				case '=':
				case '!=':
					if($data['grps'][$i+1] == 1){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'".")";
						$i = $i+1;
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'";
					}
					
					break;
				case '&gt;':
				case '&gt;=':
				case '&lt;':
				case '&lt;=':
					
					if(is_integer($data['terms'][$i])){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. "".$data['terms'][$i]."".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. "".$data['terms'][$i]."".")";
						$i = $i+1;
						
						
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. "".$data['terms'][$i]."";
					}
						
					break;
				
				case 'BETWEEN':
				case 'NOT BETWEEN':
					if(is_integer($data['terms'][$i])){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. "".$data['terms'][$i]."".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. "".$data['terms'][$i]."".")";
						$i = $i+1;
						
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]." ".$data['terms'][$i]."";
					}
					
				break;
				case 'IN':
				case 'NOT IN':
					if(is_integer($data['terms'][$i])){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. " ('".$data['terms'][$i]."')".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " ('".$data['terms'][$i]."')".")";
						$i = $i+1;
						
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '"." ('".$data['terms'][$i]."')";
					}
					
				break;
				case 'REGEXP':
				case 'NOT REGEXP':
					if(is_integer($data['terms'][$i])){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'".")";
						$i = $i+1;
						
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'";
					}
				
				break;
			}
			
			//$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i].$operator_val;
			
		  }
			 
		  
		  for($k=0;$k<count($filter_query);$k++){
				$query  .=" ".$filter_query[$k];
		  }
		   	     
			
		   return $query;
		 
			//echo $query ; exit;
	}
	
	
	function Display_filter($pagename){
		 
		 
		$data =  $_SESSION[$pagename];
		//echo '<pre>';
		//print_r($data);
		
		?>
		<table>
		<?php foreach($data['grps'] as $key => $val ){ ?>
		<tr>
		<td>
		<select id="grps[]" name="grps[]">
			<?php if( $data['grps'][$val] == 1){
			echo  '<option selected="selected" value="1">Grouped with Previous</option>';
			echo '<option value="0">UnGrouped</option>';
			} 
			if($data['grps'][$val] != 1){
			echo '<option selected="selected" value="0">UnGrouped</option>';	
			echo '<option   value="1">Grouped with Previous</option>';
			} 

			?>
			
			 
		
			 </select>
		</td>
		<td>
		<select id="cons[]" name="cons[]">
			<?php if( $data['cons'][$val] == "AND"){
			echo  '<option selected="selected" value="AND">AND</option>';
			echo '<option value="OR">OR</option>';
			} 
			if($data['cons'][$val] == "OR"){
			echo '<option selected="selected" value="OR">OR</option>';	
			echo '<option value="AND">AND</option>';
			} 

			?>
			 
		</select>
		</td>
		<td><?php echo  $data['fields'][$val]; ?>
		
		<select id="fields[]" name="fields[]">
		
		
		
	 
		
		
		
		
		
		
		<?php 	
				for($i=0; $i<count($table_value);$i++){ 
					$j=0;
					for($k=1;$k<count($table_value[$i]['column_values']);$k++){
					
				?>
					<option value="<?php echo $table_value[$i]['column_values'][0].".".$table_value[$i]['column_values'][$k];?>" >
						<?php echo trim($table_value[$i]['column_alias'][$j]); ?>
					</option>
					
				<?php 
					$j++;
				} }?>

		</select>
		
		</td>
		<td><?php echo  $data['operators'][$val]; ?>
		<select id="operators[]" name="operators[]">
			<option selected="selected" value="LIKE">Similar</option>
			<option value="NOT LIKE">Not Similar</option>
			<option value="=">Exactly</option>
			<option value="!=">Not Exactly</option>
			<option value="&gt;">Greater Than</option>
			<option value="&gt;=">Greater Than &amp; Equal</option>
			<option value="&lt;">Less Than</option>
			<option value="&lt;=">Less Than &amp; Equal</option>
			<option value="BETWEEN">In Range</option>
			<option value="NOT BETWEEN">Not in Range</option>
			<option value="IN">In Set</option>
			<option value="NOT IN">Not In Set</option>
			<option value="REGEXP">Matching Regex</option>
			<option value="NOT REGEXP">Not Matching Regex</option>
		</select>
		</td>
		<td>
		<input type="text" name="terms[]" value="<?php echo  $data['terms'][$val]; ?>">
		</td>
		</tr>
		<? } ?>
		<tr><td colspan="4"><a href="?remove=true">clear</a></td> <a href="#">save</a></td></tr>
		</table>
		
		 
		
	 
		
		<?php 
		$filter_query = array();
		  for($i=0;$i<count($data['grps']);$i++){
			
			$search = "";
			$operator_type = $data['operators'][$i];
			switch($operator_type){
				case 'LIKE':
				case 'NOT LIKE':
					if($data['grps'][$i+1] == 1){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. " '%".$data['terms'][$i]."%' ".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '%".$data['terms'][$i]."%' ".")";
						$i = $i+1;
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '%".$data['terms'][$i]."%'";
					}
					
					
				break;
				case '=':
				case '!=':
					if($data['grps'][$i+1] == 1){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'".")";
						$i = $i+1;
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'";
					}
					
					break;
				case '&gt;':
				case '&gt;=':
				case '&lt;':
				case '&lt;=':
					
					if(is_integer($data['terms'][$i])){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. "".$data['terms'][$i]."".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. "".$data['terms'][$i]."".")";
						$i = $i+1;
						
						
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. "".$data['terms'][$i]."";
					}
						
					break;
				
				case 'BETWEEN':
				case 'NOT BETWEEN':
					if(is_integer($data['terms'][$i])){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. "".$data['terms'][$i]."".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. "".$data['terms'][$i]."".")";
						$i = $i+1;
						
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]." ".$data['terms'][$i]."";
					}
					
				break;
				case 'IN':
				case 'NOT IN':
					if(is_integer($data['terms'][$i])){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. " ('".$data['terms'][$i]."')".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " ('".$data['terms'][$i]."')".")";
						$i = $i+1;
						
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '"." ('".$data['terms'][$i]."')";
					}
					
				break;
				case 'REGEXP':
				case 'NOT REGEXP':
					if(is_integer($data['terms'][$i])){
						$filter_query[] = $data['cons'][$i]." (".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'".$data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'".")";
						$i = $i+1;
						
					}else{
						$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i]. " '".$data['terms'][$i]."'";
					}
				
				break;
			}
			
			//$filter_query[] = $data['cons'][$i]." ".$data['fields'][$i]." ".$data['operators'][$i].$operator_val;
			
		  }
			
		
		  
		  for($k=0;$k<count($filter_query);$k++){
				$query  .=" ".$filter_query[$k];
		  }
		   	    $_SESSION['advan_search'] = $query;
				
				$message=array('title'=>'Filter','text'=>'Your Filter is Apply','type'=>'success');
 
				$_SESSION['message'][]=$message;
			 
			 
		  //return $_SESSION['advan_search'];
			  $query ;//exit;
	}
	
	function insert_report($pagename)
	{	
		$title = $_POST['title'];
		$data =  $_SESSION[$pagename];
		$qry = json_encode($data);
		$user_id = $_SESSION['userinfo']['user_id']; 	
		$sql = "INSERT INTO `advance_search` (`id` ,`user_id`, `title`, `pagename` ,`query`)VALUES (NULL , '$user_id', '$title',  '$pagename', '$qry')";
		mysql_query($sql) or die(mysql_error());
		
	}
	
	function show_report($pagename)
	{	
		$data =  $_SESSION[$pagename];
		$qry = json_encode($data);
		$user_id = $_SESSION['userinfo']['user_id']; 
	
		$sql = "SELECT * FROM advance_search where pagename = '$pagename' and user_id = '$user_id' ";
		$res = mysql_query($sql) or die(mysql_error());
		if(mysql_num_rows($res)>0)
		{
					
			echo '<hr><form method="post" action=""><table>
			<tr>	<td>Load Report <select name="Pre_advan_search">
			<option value=""> Select Report</option>';
		
			while($row = mysql_fetch_array($res))
			{
			 
			echo '<option value="'.$row['id'].'">'.$row['title'].'</option>';
				
			}
			echo '</select></td>';
			echo '<td><input type="submit" name="pre_report" value="Go"></td> </tr>';
			echo '</table> </form>';
		}
 	}
	
	
	function Pre_advan_search($pagename, $id)
	{	
		$user_id = $_SESSION['userinfo']['user_id']; 	
		$sql = "SELECT * FROM advance_search where id = '$id' and user_id = '$user_id' ";
		$res = mysql_query($sql) or die(mysql_error());
		if(mysql_num_rows($res)>0)
		{
			$row = mysql_fetch_array($res);
			$_SESSION['report_name'] = $row['title'];
			$_SESSION['report_id'] = $row['id'];
			$_SESSION['report_page'] = $row['pagename'];
			
			$_SESSION[$pagename] = json_decode($row['query'],true);
			//print_r(json_decode($row['query']));
		}
 		 
	}
	
	 
	
		
	
}
 
?>