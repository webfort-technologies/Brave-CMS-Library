<?php
/*
Programmer : Jay Prakash Jain
Purpose : Class to create dashboard


Example of how to use the class
include("../dashboard.php");
$config['query'] = "SELECT %s FROM activity_type %s";
$config['fieldnames'] = array(name=>'firsname', taskname=>'Task name', username=>'User Name' );
$a->initalize($dash_config);
$a->dashinsert();
*/
include("connection.php");

class Dashboard 
{
	 
	var $dashboard_id = 1;
	 
	//var $dashboard_name = "";
	var $query = "";
	var $form_field = "";
	 
		
	function __construct()
	{
	  //$this->dasres(); 
	  
	}
	function initalize($config)
	{
		$this->query =  preg_replace("#^select #i", "select %s,", $config['query']);
		//$this->form_field = explode(",",$config['fieldnames']);
		$this->form_field = $config['fieldnames'];	  
		$this->showform();
	}
		
	function dashinsert($pagename, $advansearch, $url) 
	{	
				
		if(isset($_POST['currentDashboardQuery']) && $_POST['currentDashboardQuery'] == 1)
		{
			$advan = json_encode($advansearch);
			$user_id = $_SESSION['userinfo']['user_id']; 
			$this->fields = $_POST['view_field'];
			$dashboard_name = $_POST['name'];
			$as = $_POST['function'].'('.$_POST['view_field'].')';
			$ass = $_POST['view_field'];		 
			//$querys = sprintf($this->query, $as);	
		  	$querys = str_replace('%s',$as,$this->query);
			//echo $this->query;
	 //	exit;
			$this->dashboard_id = $_REQUEST['dashboard_id'];
			
			
//$querys=$this->query;		
		 	$qry = "INSERT INTO  vt_dashlets (`id`,`userid`, `pagename`, `report`, `url`, `dashboard_id`,  `name`, `params` ) 
			VALUES ( NULL , '".$user_id."','".$pagename."', '".$advan."', '".$url."', $this->dashboard_id, '".$dashboard_name."',  '".addslashes($querys)."')";
			$res = mysql_query($qry) or die(mysql_error());
			$message=array('title'=>'DashBoard','text'=>'DashBoard Added successfully','type'=>'success');
 			$_SESSION['message'][]=$message;
			
			return $qry;
		}
	}
	
	function dasres()
	{
		$qry = "SELECT * FROM vt_dashlets";
		$res = mysql_query($qry);
		while($row = mysql_fetch_array($res))
		{
			echo	$row['name'].'-- '.$this->record($row['params']).'<br>';
		}
	}
	
	function record ($query)
	{
		$res = mysql_query($query);
		return $count;
		 
	}
	
	function showform()
	{
	?>
	<form method="post" action="" name = "xform" id = "xfrom" validate="true">
			<table class="normal">
				
				<tbody><tr><td>Name :</td><td><input mandatory="yes" type="text" value="" name="name"></td></tr>
				<tr><td>Dashboard :</td>
				<td>
				<?php 
				// finding from the datbase if the user_id exist and if not creating a generic one. 
				
				$totalcount =  mysql_query("select * from vt_dashboards where user_id='".$_SESSION['userinfo']['user_id']."'") or die(mysql_error());
				if(mysql_num_rows($totalcount)<=0)
				{
					mysql_query("insert into vt_dashboards(user_id,name) values('".$_SESSION['userinfo']['user_id']."','Generic')") or die(mysql_error());
				}
				
				$sql = mysql_query("select * from vt_dashboards where user_id='".$_SESSION['userinfo']['user_id']."'") or die(mysql_error());
				
				
				?>
				<select style="width:99%;" id="dashboard_id" name="dashboard_id">
				<?php while($row = mysql_fetch_array($sql))					 
						 {						 
						 ?>							
						  <option value="<?php echo $row['id']; ?>"><?php echo $row['name'];?></option>
						  <?} 
						 ?>
				</select>
				</td></tr>
				
				<tr>
					<td>Value :</td>
					<td>	
						<select id="function" name="function"><option value="COUNT">Total Number</option><option value="SUM">Total Sum</option><option value="AVG">Average</option><option value="MAX">Maximum</option></select>						 of
						 <select name="view_field">
						 <?php foreach($this->form_field as $key=>$val)					 
						 {						 
						 ?>							
						  <option value="<?php echo $val[0]; ?>"><?php echo $val[1];?> </option>
						  <?} 
						 ?>
						
						
						 </select>				
						 </td>
				</tr>
				<tr>
					<td colspan="4">
						<input type="hidden" value="1" name="currentDashboardQuery">
						<input type="submit" value="Done" name="submit">
					</td>
				</tr>
			</tbody></table>
			
			</form>
	
	
	<?php
	}
	
} 
?>