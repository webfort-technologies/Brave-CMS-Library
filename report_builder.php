<?php
/*
Programmer : Shishir Raven
Website : shishirraven.com
Last Modified on. 2 Feb 2014

Description : 
The script can be used to from Having Clause to filter out an existing query. 

It lets you do the following. 

1. Create and Save Queries filters in the Data. 
2. Process those queries again. 

Example to use the function. 

include("connection.php");
include("library/report_builder.php");

$label_mapping = array();
$label_mapping['id'] = 'Id';
$label_mapping['username'] = 'UserName';

$config 					= array();
$config['sql_query'] 		= "SELECT * from  vt_users";
$config['label_mapping'] 	= $label_mapping;
$config['report_key'] 		= "test_users_to_delete";
$reportbuilder 				= new reportbuilder($config);

// Use where you want the report builder to run. 
$reportbuilder->router();

// Type of Date functions- 

MySql Functions. 

NOW()
CURDATE()
CURTIME()
DATE()
DATE_ADD()
DATE_SUB()
*/
class reportbuilder{

	var $sql_query = "";
	var $report_key ="";
	var $label_mapping =array();

	// Constructor Starts here
	function __construct($params = array() )
	{
		$this->installation_sql();
		if (count($params) > 0)
		{
			$this->initialize($params);
			$this->installation_sql();
		}
	}

	// initalize the search form 
	function initialize( $params = array() )
	{
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}
	}

	// landing Screen
	function router()
	{
		if(isset($_GET['route']) && $_GET['route']=="add_report")
		{
			$this->add_report();
		}
		if(isset($_GET['route']) && $_GET['route']=="delete_condition")
		{
			$this->delete_condition();
		}
		if(isset($_GET['route']) && $_GET['route']=="delete_report")
		{
			$this->delete_report();
		}
		else if(isset($_GET['route']) && $_GET['route']=="modify_report")
		{
			$this->update_report_name();
		}
		else if(isset($_GET['route']) && $_GET['route']=="add_group")
		{
			$this->add_group();
		}
		else if(isset($_GET['route']) && $_GET['route']=="add_conditions")
		{
			if(isset($_GET['save']) && !empty($_GET['save']))
			{	
				$this->add_conditions_save();
			}
			else
			{
				$this->add_conditions();
			}
		}
		else if(isset($_GET['route']) && $_GET['route']=="report_delete_group")
		{
			$this->report_delete_group();
		}
		else
		{
			$this->default_page();
		}
	}

	function update_report_name()
	{
		$udpate_query ="UPDATE `cms_report`
						SET
						`report_name` = '" . $_POST['report_name']  . "'
						WHERE id ='" . $_POST['id'] . "'
						";
		mysql_query($udpate_query ) or die(mysql_error().$udpate_query);
		die("<script>location.replace('".$_SERVER["SCRIPT_NAME"]."?report_id=". $_POST['id'] ."');</script>");

	}

	function manage_conditions()
	{
		?>
		<hr>

		<div class="">
			<p class="alert alert-success">
				WHERE <?php $this->query_displayed(0); ?>
			</p>
			<p class="alert alert-success">
				WHERE <?php //echo $this->generating_have_query(0); ?>
			</p>
		</div>
		<?php
	}

	function delete_condition()
	{
		mysql_query("delete from cms_report_query_conditions where id='".$_GET['id']."'") or die(mysql_error());
		die("<script>location.replace('".$_SERVER["SCRIPT_NAME"]."?report_id=".$_GET['report_id']."');</script>");
	}


	function add_report()
	{
		$insert_query ="INSERT INTO `cms_report`
			(
			`report_name`,
			`sql_query`,
			`created_on`,
			`modified_on`,
			`report_key`)
			VALUES
			(
			'New Report',
			'".$this->sql_query."',
			'".date('Y-m-d H:i:s')."',
			'".date('Y-m-d H:i:s')."',
			'".$this->report_key."'
			)";
			mysql_query($insert_query ) or die(mysql_error().$insert_query);
			echo "<meta http-equiv='Location' content='".$_SERVER["SCRIPT_NAME"]."' >";
			die("<script>location.replace('".$_SERVER["SCRIPT_NAME"]."?report_id=".mysql_insert_id()."');</script>");
	}

	function delete_report()
	{
		if(mysql_query("delete from cms_report where id='".$_GET['report_id']."'") or die(mysql_error()))
		{
			mysql_query("delete from cms_report_query_groups where report_id='".$_GET['report_id']."'") or die(mysql_error());
		}
		die("<script>location.replace('".$_SERVER["SCRIPT_NAME"]."');</script>");
	}
	
	function default_page()
	{
		$this->report_selector();
		
		// See if a report is preselected. 
		if(isset($_GET['report_id']))
		{	
			$this->report_edit();
			$this->manage_conditions();
			$this->modify_conditions();
			$this->add_css();
		}
		else
		{
			$this->select_report_message();
		}
	}

	function prepare_first_group()
	{
		// If there are no existing groups then adding a new group. 
		$cms_report_query_groups_query ="select * from cms_report_query_groups where report_id='".$_GET['report_id']."' and pid=0";
		$cms_report_query_groups_rs = mysql_query($cms_report_query_groups_query) or die(mysql_error()."</br> Failed Query");
		//Loop Placed here. 
		if(mysql_num_rows($cms_report_query_groups_rs)==0)
		{
			$insert_query ="INSERT INTO `cms_report_query_groups`
			(
			`report_id`,
			`pid`,
			`created_on`,
			`modified_on`,
			`join_relation`)
			VALUES
			(
			'". $_GET['report_id']. "',
			'0',
			'".date('Y-m-d H:i:s')."',
			'".date('Y-m-d H:i:s')."',
			'and'
			)";
			mysql_query($insert_query) or die(mysql_error());
		}
	}

	function modify_conditions()
	{
		$this->prepare_first_group();
		$this->recurrsive_box_display(0);
	}

	function report_edit()
	{
		// Finding the form that we need to edit. 
		// finding the report_name
		$cms_report_query ="select * from cms_report where id='".$_GET['report_id']."'";
		$cms_report_rs = mysql_query($cms_report_query) or die(mysql_error()."</br> Failed Query");
		//Loop Placed here. 
		$cms_report_row = mysql_fetch_array($cms_report_rs);
		?>
		<form action="?route=modify_report" method="POST">
			<input type="hidden" name="id" value="<?php echo $cms_report_row["id"]; ?>" >
			<div class="control-group">
				<label class="control-label" for="form-field-1">Report Name</label>
				<div class="controls">
					<input value="<?php echo $cms_report_row["report_name"]; ?>" type="text" id="form-field-1" name="report_name" placeholder="Type Report Name">			<input type="submit" class="btn btn-primary" value="Save Changes">
					<?php if(isset($_GET['report_id']) && !empty($_GET['report_id'])){ ?>
					<a href="?route=delete_report&report_id=<?php echo $_GET['report_id']?>" class="btn btn-small"><i class="icon-remove"></i> Delete</a>
					<?php } ?>
				</div>
			</div>
		</form>
		<hr>
		<?php
	}

	function select_report_message()
	{
	?>
		<div class="alert alert-info fade in">
	      <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
	      Select a Report from above menu to edit. <br>
	      Or <br> Click the following report to create a new report
	      <a href="?route=add_report">+ Add a New Report</a>
	    </div>
	<?php
	}

	function add_group(){
			mysql_query("INSERT INTO `cms_report_query_groups`
			(
			`report_id`,
			`group_name`,
			`pid`,
			`created_on`,
			`modified_on`,
			`join_relation`)
			VALUES
			(
			'".$_GET['report_id']."',
			'',
			'".$_GET['pid']."',
			'".date('Y-m-d H:i:s')."',
			'".date('Y-m-d H:i:s')."',
			'".$_GET['join_relation']."'
			)
			") or die(mysql_error());
			die("<script>location.replace('".$_SERVER["SCRIPT_NAME"]."?report_id=".  $_GET['report_id'] ."');</script>");
	}

	function report_selector()
	{
	?>
		<form action="" method="GET">
			<select name="report_id" >
				<?php
				$cms_report_query ="select * from cms_report where report_key='".$this->report_key."'";
				$cms_report_rs = mysql_query($cms_report_query) or die(mysql_error()."</br> Failed Query");
				//Loop Placed here. 
				while($cms_report_row = mysql_fetch_array($cms_report_rs))
				{
				?>
				<option
					<?php 
						if(isset($_GET['report_id']) && $_GET['report_id']== $cms_report_row["id"])
						{
							echo ' selected ="selected" ';
						}
					 ?>
				 value="<?php echo $cms_report_row["id"] ;?>">
				 <?php echo $cms_report_row["report_name"] ;?></option>
				
				<?php 
				}
				?>
			</select>
			<input type="submit" class="btn btn-primary" value="Select Report">&nbsp;&nbsp;&nbsp;<a href="?route=add_report">+ Add a New Report</a>
	</form>
	<hr>

	<?php
	}

	function installation_sql()
	{

		$mysql_query_array = array();
		// Creation of CMS_REPORT Table. 
		$mysql_query_array[] = "CREATE TABLE  IF NOT EXISTS `cms_report` (
								  `id` int(11) NOT NULL AUTO_INCREMENT,
								  `report_name` varchar(200) DEFAULT NULL,
								  `sql_query` text,
								  `created_on` datetime DEFAULT NULL,
								  `modified_on` datetime DEFAULT NULL,
								  `report_key` varchar(200) DEFAULT NULL,
								  PRIMARY KEY (`id`)
								)";

		// Creation of CMS_REPORT_QUERY_CONDITIONS Table. 
		 $mysql_query_array[] =  	"CREATE TABLE  IF NOT EXISTS `cms_report_query_conditions` (
								  `id` int(11) NOT NULL AUTO_INCREMENT,
								  `group_id` varchar(45) DEFAULT NULL,
								  `field_name` varchar(45) DEFAULT NULL,
								  `join_with` enum('and','or') DEFAULT NULL,
								  `condition_symbol` varchar(45) DEFAULT NULL,
								  `condition_match_value1` varchar(200) DEFAULT NULL,
								  `condition_match_value2` varchar(200) DEFAULT NULL,
								  PRIMARY KEY (`id`))";
	
		// Creation of CMS_REPORT_QUERY_CONDITIONS Table. 
		 $mysql_query_array[] = "CREATE TABLE  IF NOT EXISTS `cms_report_query_groups` (
								  `id` int(11) NOT NULL AUTO_INCREMENT,
								  `report_id` int(11) DEFAULT NULL,
								  `group_name` varchar(200) DEFAULT NULL,
								  `pid` int(11) DEFAULT '0',
								  `created_on` datetime DEFAULT NULL,
								  `modified_on` datetime DEFAULT NULL,
								  `join_relation` enum('and','or') DEFAULT NULL,
								  PRIMARY KEY (`id`))";


		// Running all the queries. 
		foreach($mysql_query_array as $query)
		{
			mysql_query($query) or die(mysql_error());
		}
	
	}

	function add_css(){
		?>
<style>
								.condition_group
								{
									border:1px solid #DDD;
									border-left:5px solid #DDD;
									padding:10px;
									position:relative;
									font-size:20px;
									line-height: 25px;
									margin: 20px 0px 20px 0px;
								}
								.condition_group:hover >.condition_toolbar
								{
									display:block;
								}
								.join_type{
									font-size:20px;
								}
								.condition_toolbar{
									width:400px;
									right:0px;
									bottom:0px;
									position:absolute;
									display:none;
									margin-top:10px;
									background-color:#EEE;
								}
							</style>

		<?php
	}

	function add_conditions()
	{

		$cms_report_query ="select * from cms_report where id ='".$_GET['report_id']."'";
		$cms_report_rs = mysql_query($cms_report_query) or die(mysql_error()."</br> Failed Query");
		//Loop Placed here. 
		$cms_report_row = mysql_fetch_array($cms_report_rs);
		$report_query =  $cms_report_row["sql_query"];
		// echo $report_query;
		// exit;
		$res = mysql_query($report_query) or die(mysql_error());
		?>
		<!-- Adding the Required Javascript. -->
		<link rel="stylesheet" type="text/css" media="all" href="assets/js/daterangepicker/daterangepicker-bs2.css" />
     	<script type="text/javascript" src="assets/js/daterangepicker/moment.js"></script>
      	<script type="text/javascript" src="assets/js/daterangepicker/daterangepicker.js"></script>
      	<script>
      	$(document).ready(function(){

      		$('#text_fields').hide();
      		$('#date_fields').hide();

     

      		$(document).on('change','.condition_symbol',function(){
      			
      			$('#condition_match_value1').val("");
      			var condition_value  = $('.condition_symbol').val(); 
      			if(condition_value=="date_between")
      			{
      				$('#condition_match_value1').daterangepicker({
                                timePicker: true,
                                timePickerIncrement: 30,
                                separator: " and ",
                                format: '"YYYY-MM-DD HH:mm:ss"'
                    });
      			}
      			else if(condition_value=="date_equal_to" || condition_value=="date_less_than" || condition_value=="date_greater_than")
      			{
      					$('#condition_match_value1').val("<?php echo date("Y-m-d") ?>");
      				    $('#condition_match_value1').daterangepicker({ singleDatePicker: true, format:'YYYY-MM-DD' });
      			}
      		});

      		$('#field_name').change(function(){

      			// Hiding all the fileds starts here. 
      			$('#text_fields').hide();
      			$('#date_fields').hide();
      			$('#date_fields').removeClass('condition_symbol');
      			$('#text_fields').removeClass('condition_symbol');

      			$('#condition_match_value1').val("");
      			var field_type = $("option:selected",this).attr('rel');
      			// Removing the date picker

      			if(typeof($('#condition_match_value1').data('daterangepicker'))!="undefined")
       			{
       				$('#condition_match_value1').data('daterangepicker').remove();
       			}

      			if(field_type=="datetime")
      			{
      					$('#date_fields').show();
  						$('#date_fields').show();
  						$('#date_fields').attr('name','condition_symbol');
  						$('#date_fields').addClass('condition_symbol');
  						$('#text_fields').attr('name','text_fields');
  						$('.condition_symbol').trigger('change');
      			}
      			else
      			{
	      				$('#text_fields').show();
	      				$('#text_fields').attr('name','condition_symbol');
	      				$('#text_fields').addClass('condition_symbol');
	      				$('#date_fields').attr('name','date_fields');
      			}
      		})

			$('#field_name').trigger('change');
      	});

  	
      	</script>
							
				<div class="page-header position-relative">
					<h1>Condition Builder</h1>
				</div>
				<div class="well">
					<form action="?route=add_conditions&report_id=<?php echo $_GET['report_id']; ?>&save=1" method="POST">
					<h4>Add Condition</h4>

					<input type="hidden" name="group_id" value="<?php echo $_GET['group_id']; ?>">
					<select name="join_with" id="fields">
						<option value="and">AND</option>
						<option value="or">OR</option>
					</select>


					<select name="field_name" id="field_name">
						<?php 
						$numOfCols = mysql_num_fields($res);
						for($i=0; $i<$numOfCols; $i++)
						{
						?>
						<option rel="<?php echo mysql_field_type($res, $i); ?>" value="<?php echo mysql_field_name($res, $i); ?>"><?php 

						$field_name = mysql_field_name($res, $i);

						if(isset($this->label_mapping[$field_name]))
						{
							echo $this->label_mapping[$field_name];
						}
						else
						{
							echo $field_name;
						}

						 ?>  </option>
						<?php } ?>
					</select>

					<select name="condition_symbol" class="" id="text_fields">
						<option value="greater_than">Greater than</option>
						<option value="less_than">less than</option>
						<option value="exactly">Exactly Equal to</option>
						<option value="like">Like</option>
					</select>

					<select  name="condition_symbol" class="" id="date_fields">
						<option value="date_between">Between</option>
						<option value="date_equal_to">Equal to</option>
						<option value="date_greater_than">Greater Than</option>
						<option value="date_less_than">Less Than</option>
						<option value="date_less_than_n_days_from_current_day">Less Than n days from current day</option>
						<option value="date_greater_than_n_days_from_current_day">Greater Than n days from current day</option>
					</select>

					

				


					<input type="text" id="condition_match_value1" name="condition_match_value1" value ="">
					<input type="hidden" name="condition_match_value2" value ="notused">

					<input class="btn btn-primary" type="submit" value="Save Condition">
					</form>
				</div>
			<?php

	}

	function add_conditions_save()
	{
		mysql_query("
		INSERT INTO `cms_report_query_conditions`
		(
		`group_id`,
		`field_name`,
		`join_with`,
		`condition_symbol`,
		`condition_match_value1`,
		`condition_match_value2`)
		VALUES
		(
		'" .$_POST['group_id']. "',
		'" .$_POST['field_name']. "',
		'" .$_POST['join_with']. "',
		'" .$_POST['condition_symbol']. "',
		'" .$_POST['condition_match_value1']. "',
		'" .$_POST['condition_match_value2']. "'
		);
		") or die(mysql_error()); 
		die("<script>location.replace('".$_SERVER["SCRIPT_NAME"]."?report_id=".  $_GET['report_id'] ."');</script>");
	}

	function report_delete_group()
	{
		mysql_query("delete from cms_report_query_groups where id='".$_GET['id']."'") or die(mysql_error());
		die("<script>location.replace('".$_SERVER["SCRIPT_NAME"]."?report_id=".  $_GET['report_id'] ."');</script>");
	}
	
	function query_displayed($pid)
	{

	$cms_report_query_groups_query ="select * from cms_report_query_groups where report_id='".$_GET['report_id']."' and  pid=".$pid." order by id desc";
	$cms_report_query_groups_rs = mysql_query($cms_report_query_groups_query) or die(mysql_error()."</br> Failed Query");
	//Loop Placed here. 
	$flag=1;
	while($cms_report_query_groups_row = mysql_fetch_array($cms_report_query_groups_rs))
	{

	?>
		 <?php 
			 if($flag==0)
			 {
			 	echo strtoupper($cms_report_query_groups_row['join_relation']);
			 }
			 $flag=0;
			 ?> 
				(
	
				<?php
				$cms_report_query_conditions_query ="select * from cms_report_query_conditions where group_id='".$cms_report_query_groups_row['id']."'";
				$cms_report_query_conditions_rs = mysql_query($cms_report_query_conditions_query) or die(mysql_error()."</br> Failed Query");
				//Loop Placed here. 
				$flagtwo = 1 ;
				while($cms_report_query_conditions_row = mysql_fetch_array($cms_report_query_conditions_rs))
				{
				?>
	
						
					<?php
					if($flagtwo == 0)
					{
					  echo $cms_report_query_conditions_row['join_with'];
					}
					$flagtwo=0;
					?>

					<?php 
					echo $cms_report_query_conditions_row['field_name'];
					 ?>
					<?php 
					echo $cms_report_query_conditions_row['condition_symbol'];
					 ?>
					 <?php 
					echo "'".$cms_report_query_conditions_row['condition_match_value1']."'";
					 ?>

				

				<?php 
				}
				?>

		
			<?php //echo $cms_report_query_groups_row['group_name']; ?>
			<?php $this->query_displayed($cms_report_query_groups_row['id']); ?>
		)
	<?php 
	}
}




	function having_query($pid)
	{
		$cms_report_query_groups_query ="select * from cms_report_query_groups where report_id='".$_GET['report_id']."' and  pid=".$pid." order by id desc";
		$cms_report_query_groups_rs = mysql_query($cms_report_query_groups_query) or die(mysql_error()."</br> Failed Query");
		//Loop Placed here. 
		$flag=1;
		$final_query ="";
		while($cms_report_query_groups_row = mysql_fetch_array($cms_report_query_groups_rs))
		{
			 if($flag==0)
			 {
			 	 $final_query .strtoupper($cms_report_query_groups_row['join_relation']);
			 }
			 $flag=0;
			 $final_query .= "( ";
			 
				$cms_report_query_conditions_query ="select * from cms_report_query_conditions where group_id='".$cms_report_query_groups_row['id']."'";
				$cms_report_query_conditions_rs = mysql_query($cms_report_query_conditions_query) or die(mysql_error()."</br> Failed Query");
				//Loop Placed here. 
				$flagtwo = 1 ;
				while($cms_report_query_conditions_row = mysql_fetch_array($cms_report_query_conditions_rs))
				{
				
					if($flagtwo == 0)
					{
						$final_query .= " ".$cms_report_query_conditions_row['join_with']. " ";
					}
					$flagtwo=0;
			$having_clause = $cms_report_query_conditions_row['field_name'];
			switch($cms_report_query_conditions_row['condition_symbol'])
			{
				case 'greater_than' : $having_clause .= ' > ';
				$having_clause .= "'".$cms_report_query_conditions_row['condition_match_value1']."'";
				break;
				case 'less_than' 	: $having_clause .= ' < ';
				$having_clause .= "'".$cms_report_query_conditions_row['condition_match_value1']."'";
				break;
				case 'exactly'		: $having_clause .= ' = ';
				$having_clause .= "'".$cms_report_query_conditions_row['condition_match_value1']."'";
				break;
				case 'like' 		: $having_clause .= ' like ';
				$having_clause .= "'".$cms_report_query_conditions_row['condition_match_value1']."'";
				break;
				case 'date_between' : $having_clause .= ' between ';
				$having_clause .= $cms_report_query_conditions_row['condition_match_value1'];
				break;
				case 'date_equal_to' 	: $having_clause = "date(".$having_clause.")" . ' = ';
				$having_clause .= "'".$cms_report_query_conditions_row['condition_match_value1']."'";
				break;	
				case 'date_greater_than' 	: $having_clause = "date(".$having_clause.")" . ' > ';
				$having_clause .= "'".$cms_report_query_conditions_row['condition_match_value1']."'";
				break;	
				case 'date_less_than' 	: 
				$having_clause = "date(".$having_clause.")" . ' < ';
				$having_clause .= "'".$cms_report_query_conditions_row['condition_match_value1']."'";
				
				break;
				case 'date_less_than_n_days_from_current_day' 	: $having_clause .= "< DATE_ADD(NOW(),INTERVAL ".$cms_report_query_conditions_row['condition_match_value1']." DAY)";
				break;	
				case 'date_greater_than_n_days_from_current_day' : $having_clause .= "> DATE_ADD(NOW(),INTERVAL ".$cms_report_query_conditions_row['condition_match_value1']." DAY)";
				break;
			}

			$final_query .= $having_clause;
			}
			$final_query .= " ".$this->having_query($cms_report_query_groups_row['id']); 
			$final_query .= " ".")"; 
		}
		return $final_query; 
	}



	
	
	function recurrsive_box_display($pid)
	{

	$cms_report_query_groups_query ="select * from cms_report_query_groups where report_id='".$_GET['report_id']."' and pid=".$pid." order by id desc";
	$cms_report_query_groups_rs = mysql_query($cms_report_query_groups_query) or die(mysql_error()."</br> Failed Query");
	//Loop Placed here. 
	$flag =1;
	while($cms_report_query_groups_row = mysql_fetch_array($cms_report_query_groups_rs))
	{
	?>
		<?php if($flag ==0) 
		{

		?>
		<div class="join_type"><?php echo $cms_report_query_groups_row['join_relation']; ?></div>
		<?php 
		}
		$flag =0;
		 ?>
		<div class="condition_group" style="border-color:<?php echo '#' . strtoupper(dechex(rand(0,10000000))); ?>;">
			<?php //echo $cms_report_query_groups_row['group_name']; ?>

			<div class="condition_box">
				<?php
				$cms_report_query_conditions_query ="select * from cms_report_query_conditions where group_id='".$cms_report_query_groups_row['id']."'";
				$cms_report_query_conditions_rs = mysql_query($cms_report_query_conditions_query) or die(mysql_error()."</br> Failed Query");
				//Loop Placed here. 
				$flagtwo = 1 ;
				while($cms_report_query_conditions_row = mysql_fetch_array($cms_report_query_conditions_rs))
				{
				?>
					<div class="join_type">
					
					<?php
					if($flagtwo == 0)
					{
						?><div class="label label-success">
						<?php
					  echo $cms_report_query_conditions_row['join_with'];
					  ?>
					  </div>
					  <?php
					}
					$flagtwo=0;
					?>
					
					</div>
					<div class="">
	
					<?php 
					echo $cms_report_query_conditions_row['field_name'];
					 ?>
					 <span style="color:green;">
					<?php 
					echo $cms_report_query_conditions_row['condition_symbol'];
					 ?>
					</span>
					 <?php 
					echo "'".$cms_report_query_conditions_row['condition_match_value1']."'";
					 ?>
					 <a onclick="return confirm('do you wish to delete this query')" 
						href="?route=delete_condition&id=<?php echo $cms_report_query_conditions_row['id']; ?>&report_id=<?php if(isset($_GET['report_id']))
						{
							echo $_GET['report_id'];
						}
					?>">
						<i class="icon-remove"></i>
						</a>
					</div>

				<?php 
				}
				?>
			</div>

			<?php $this->recurrsive_box_display($cms_report_query_groups_row['id']); ?>
			<div class="condition_toolbar">
				<div class="row-fluid">
					<div class="span6">

				
						<a type="submit" href="?route=add_group&pid=<?php echo $cms_report_query_groups_row['id']; ?>&join_relation=or&report_id=<?php 							if(isset($_GET['report_id']))
							{
								echo $_GET['report_id'];
							}
						 ?>" class="btn btn-minier btn-danger">
							<i class="icon-bolt"></i>
							+ OR GROUP
						</a>
							<a type="submit" href="?route=add_group&pid=pid=<?php echo $cms_report_query_groups_row['id']; ?>&join_relation=and&report_id=<?php 							if(isset($_GET['report_id']))
							{
								echo $_GET['report_id'];
							}
						 ?>" class="btn btn-minier btn-danger">
							<i class="icon-bolt"></i>
							+ AND GROUP
						</a>

					</div>
					<div class="span6">
						<a href="?route=add_conditions&group_id=<?php echo $cms_report_query_groups_row['id']; ?>&report_id=<?php if(isset($_GET['report_id']))
						{
							echo $_GET['report_id'];
						}
						?>" class="btn btn-minier btn-info">
							<i class="icon-bolt"></i>
							Add CONDITION
						</a>
						<div class="pull-right">
							<a onclick="return confirm('do you wish to delete this group')" 
							href="?route=report_delete_group&id=<?php echo $cms_report_query_groups_row['id']; ?>&report_id=<?php if(isset($_GET['report_id']))
							{
								echo $_GET['report_id'];
							}
						 ?>">
								<i class="icon-remove"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php 
	}
}

	function report_selector_front()
	{
	?>
		<div class="span4">
		<form action="" method="GET">
			<select name="report_id" >
				<option value=''>Show All</option>
				<?php
				$cms_report_query ="select * from cms_report where report_key='".$this->report_key."'";
				$cms_report_rs = mysql_query($cms_report_query) or die(mysql_error()."</br> Failed Query");
				//Loop Placed here. 
				while($cms_report_row = mysql_fetch_array($cms_report_rs))
				{
				?>
				<option
					<?php 
						if(isset($_GET['report_id']) && $_GET['report_id']== $cms_report_row["id"])
						{
							echo ' selected ="selected" ';
						}
					 ?>
				 value="<?php echo $cms_report_row["id"] ;?>">
				 <?php echo $cms_report_row["report_name"] ;?></option>
				
				<?php 
				}
				?>
			</select>
			<input type="submit" class="btn btn-primary btn-mini" value="GO">
	</form>
	</div>
	<?php
	}
	
	function having_query2($pid)
	{
	$cms_report_query_groups_query ="select * from cms_report_query_groups where report_id='".$_GET['report_id']."' and  pid=".$pid." order by id desc";
	$cms_report_query_groups_rs = mysql_query($cms_report_query_groups_query) or die(mysql_error()."</br> Failed Query");
	//Loop Placed here. 
	$flag=1;
	while($cms_report_query_groups_row = mysql_fetch_array($cms_report_query_groups_rs))
	{
		 if($flag==0)
		 {
			echo strtoupper($cms_report_query_groups_row['join_relation']);
		 }
		 $flag=0;

		$cms_report_query_conditions_query ="select * from cms_report_query_conditions where group_id='".$cms_report_query_groups_row['id']."'";
		$cms_report_query_conditions_rs = mysql_query($cms_report_query_conditions_query) or die(mysql_error()."</br> Failed Query");
		//Loop Placed here. 
		$flagtwo = 1 ;
		while($cms_report_query_conditions_row = mysql_fetch_array($cms_report_query_conditions_rs))
		{
			if($flagtwo == 0)
			{
				$having_clause .= ' '.$cms_report_query_conditions_row['join_with'];
			}
			$flagtwo=0;


			$having_clause .= ' '.$cms_report_query_conditions_row['field_name'];
	
			switch($cms_report_query_conditions_row['condition_symbol'])
			{
				case 'greater_than' : $having_clause .= ' > ';
				$having_clause .= "'".$cms_report_query_conditions_row['condition_match_value1']."'";
				break;
				case 'less_than' 	: $having_clause .= ' < ';
				$having_clause .= "'".$cms_report_query_conditions_row['condition_match_value1']."'";
				break;
				case 'exactly'		: $having_clause .= ' = ';
				$having_clause .= "'".$cms_report_query_conditions_row['condition_match_value1']."'";
				break;
				case 'like' 		: $having_clause .= ' like ';
				$having_clause .= "'".$cms_report_query_conditions_row['condition_match_value1']."'";
				break;
				case 'date_between' 		: $having_clause .= ' between ';
				$having_clause .= $cms_report_query_conditions_row['condition_match_value1'];
				break;
				case 'date_equal_to' 	: $having_clause = "date(".$having_clause.")" . ' = ';
				$having_clause .= "'".$cms_report_query_conditions_row['condition_match_value1']."'";
				break;	
				case 'date_greater_than' 	: $having_clause = "date(".$having_clause.")" . ' > ';
				$having_clause .= "'".$cms_report_query_conditions_row['condition_match_value1']."'";
				break;	
				case 'date_less_than' 	: $having_clause = "date(".$having_clause.")" . ' < ';
				$having_clause .= "'".$cms_report_query_conditions_row['condition_match_value1']."'";
				break;
				case 'date_less_than_n_days_from_current_day' 	: $having_clause .= "< DATE_ADD(CURDATE(),INTERVAL ".$cms_report_query_conditions_row['condition_match_value1'].")";
				break;	
				case 'date_greater_than_n_days_from_current_day' : $having_clause .= "> DATE_ADD(CURDATE(),INTERVAL ".$cms_report_query_conditions_row['condition_match_value1'].")";
				break;
			}
			//$having_clause .= $cms_report_query_conditions_row['condition_symbol'];
		}
		 //echo $cms_report_query_groups_row['group_name']; 
		$this->having_query($cms_report_query_groups_row['id']); 
		return $having_clause;
	}
}
}
?>