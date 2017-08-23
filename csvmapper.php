<?php
/*

CSVMAPPER class is created for import csv file in database.
Set Configuration of tool in csv_config.php


********************************
Show upload CSV form, Define in csvmapper class.

show_form()

********************************
Submitted CSV form and upload csv file and unset and set session values, Define in csvmapper class.

upload_csvfile()

********************************
Show Mapping form.
Map table fields with CSV fields, Define in csvmapper class.

show_mapper()

********************************
Create drop-down for csv fields. Internally used in csvmapper class.

show_first_row()

********************************
Check number of lines in CSV
Choose appropriate function in csvmapper class for insert data on the basis ("less then and equal" to $rows<=$presentrows_in_csv) 
of line present in CSV file, Define in csvmapper class.

decideInsertFunc()

*******************************
Insert simple csv-data in table.

insert_data()

*******************************
Insert ajax-call base csv-data in table.

insert_counter_data()

********************************

*/

class csvmapper
	{
	    var $upload_path = "";
	    var $table_name  = "";
	    var $table_fields = array();
	    var $header_row = array();
		var $uploaded_csv_name = "";
		var $ajax_page = "";
		var $ajax_page_runner = "";
		var $lines_percall = 1;
		var $check_csv_line = 100;
		var $hooks_file = "";
		var $errors = "";

	// INITIALIZER
	function initialize($params = array())
	{
		if (count($params) > 0)
		{
			// Settings the variable through configuration passed. "$key" used as config indexes.
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
				 	$this->$key = $val;
				}
			}
		}
	}

	// This function runs all the options. 
	function upload_and_process()
	{
		//Show upload CSV form, Define in csvmapper class.
		$this->show_form();

		//Submitted CSV form and upload csv file and unset and set session values, Define in csvmapper class.
		$this->upload_csvfile();

		if(trim($this->errors)=="")
		{
		//Map table fields with CSV fields, Define in csvmapper class.
		$this->show_mapper();
		
		//Choose appropriate function in csvmapper class for insert data on the basis ("less then and equal" to $rows<=$presentrows_in_csv) 
		//of line present in CSV file, Define in csvmapper class.
		$this->decideInsertFunc();
		}
	}

	// Constuctor 
	function csvmapper($params=array())
	{
		if (count($params) > 0)
		{
		 	$this->initialize($params);		
		}			 
	}
	
	
	//Render file upload form
	function show_form()
	{
		if(!isset($_POST['checkpost']))
		{
		?>
		 <form action="" method="post" enctype="multipart/form-data">
        	 <label for="file">Filename:</label>
         	 <input type="file" name="file" id="file">
       	 	 <input type="hidden" name="checkpost" id="checkpost">
         	 <input type="submit" name="submit" value="Submit" class="btn btn-primary">
		</form>
		<?php
		}
	}

    // Upload csv file
	function upload_csvfile()
	{	
     	if(isset($_POST['checkpost']))
		{	

			if(isset($_FILES) && !empty($_FILES) && (pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION) == "csv"))
			{			
				//upload csv file.
				$this->uploaded_csv_name = strtotime(date("Y-m-d H:i:s")).".csv";	
				move_uploaded_file($_FILES["file"]["tmp_name"],$this->upload_path.$this->uploaded_csv_name);
				
				//Re-intialize the session by unset the indexes.
				unset($_SESSION['csvlastcount']);
				unset($_SESSION['csv_handle']);
				unset($_SESSION['file_name']);
				unset($_SESSION['fileLineCount']);		
				
				//Set the intial value for file-name and csv-last-count to zero.
				$_SESSION['file_name'] = $this->uploaded_csv_name;
				$_SESSION['csvlastcount'] = 0;
			}
			else
			{	
				$this->errors = "File must be CSV format. with a .csv Extention";				
			}			
		}
    }

    function errors()
    {
    	return $this->errors;
    }
	
	
	// Render table-fields and csv fields mapping form
	function show_mapper()
    {    	
    	if(isset($_POST['checkpost']))
    	{
	 		//echo $this->table_name ."</br></br>";
	        echo "<form method='post' action=''>
	        <table class='table  table-condensed table-striped table-bordered'>";
	        echo "<tr>";
	  		echo '<td>Fields</td>';
			echo '<td>CSV Columns</td></tr>';

			foreach ($this->table_fields as $key=>$value)
			{    
				echo "<tr>";
				echo "<td>".$value."</td>";
				//show the list of csv field by show_first_row()				
				echo "<td>".$this->show_first_row()."</td>";
				echo "</tr>";
			} 
			echo "<tr>";
	  		echo '<td>
			
	  			 <label class="checkbox">
					<input type="checkbox" value="1" name="skipfirstrow" checked="checked" /><span class="lbl"> Skip first row</span>
				</label>			
				</td>';
			echo '<td><input type="submit" class="btn btn-primary" name="update" value="Start Import"></td></tr>';
			echo "</table>";
			echo "</form>";  
		}
	}
    
	// Create drop-down for csv fields.
    function show_first_row()
    {		
        $csvfile = fopen($this->upload_path.$this->uploaded_csv_name, "r");        
        $data = fgetcsv($csvfile, 1024, ",");
        $num = count($data);
        $str = "<select style='margin-bottom:2px;' name='fieldmaps[]'>";        
        $str .= "<option value=''>Select an Option</option>";
        for ($c=0; $c < $num; $c++) 
		{
			$str .= "<option value='$c'>" . $data[$c] ."</option>";
		}
        $str .= "</select>";   
        fclose($csvfile);
        return $str;
    }
	
	// Form post collected from mapping-form 
	function decideInsertFunc()
	{
		if(isset($_POST['update']) && !empty($_POST['update']))
		{	
			// get file lines.
			$number_rec = file($this->upload_path.$_SESSION['file_name']);
			
			//Set the post values, file lines in Session
			$_SESSION['skipfirstrow']=$_POST['skipfirstrow'];
			$_SESSION['fileLineCount']=count($number_rec);
			$_SESSION['fieldmaps'] = $_POST['fieldmaps'];
			
			//Check if csv has less lines as specified in variable.
			//Use insert_data() for less lines.
			//Use Ajax call for too many lines.
			if(count($number_rec)<= $this->check_csv_line)
			{	
				$this->insert_data();
			}
			else
			{
				// Redirect to "ajax.php" page
				
				echo '<script>location.replace("' . $this->ajax_page . '")</script>';
				//header("Location:" . $this->ajax_page);
			}
		}
		
	}
	
	
	//Insert simple csv-data in table.
    function insert_data()
    {	
		// Including hooks file if it is set and exists
		$has_hooks = false;
		if($this->hooks_file != "")
		{
			$hook_file =  $this->hooks_file;
			if (file_exists($hook_file)) 
			{
				// Load the hooks file
				include($hook_file);
				$has_hooks = true;
			}
			else
			{
				echo "Specified Hooks file is Not found <br> " . $hook_file;
			}
		} // Including hooks file finished.


    	if(isset($_POST['update']) && !empty($_POST['fieldmaps'])) 
		{			
			//Open uploaded CSV file
			if (($handle = fopen($this->upload_path.$_SESSION['file_name'], "r")) !== FALSE) {
				
				//Start loop on CSV file handler(pointer) for a single line.
				while (($csv_data = fgetcsv($handle, 1024, ",")) !== FALSE) {
					
					//Check that first-row(header line) would be skip from csv.
					if(isset($_POST['skipfirstrow']) && $_POST['skipfirstrow']==1 )
					{
						$_POST['skipfirstrow']=0;
						continue;
					}
					
					$values = array();
					//Loop to tables fields for build a insert query.
					foreach($this->table_fields as $key => $field)
					{
						if($_POST['fieldmaps'][$key]!="")
						{
							$values[$field] = $csv_data[$_POST['fieldmaps'][$key]];
						}
						else
						{
							$values[$field] = "";
						}
					}

					if($has_hooks)
						{
							$values = csv_hook::process_row($values);
						}
					
					$final_value= array();
					foreach($values as $valloop)
					{
						$final_value[] =  "'" . mysql_real_escape_string( $valloop ) . "'";
					}


					// Picking the class and the values. 
					//Insert a csv row data in database.
					$query ="INSERT INTO ".$this->table_name."(" . implode( ',' , $this->table_fields ) . ")VALUES(" . implode( ',' ,$final_value ) . ")";				
					mysql_query($query) or die(mysql_error());	
				}
				
				// Close the handler.
				fclose($handle);
			}
		}
    }
	
	//Render the insert progress-bar
	function ajaxRunner()
	{
		?>
			<h2> Processing CSV </h2>
			<div id="" class="">Percentage Uploaded:
				<span id="progress_text"></span>
			</div>
		
			<div  class="">Total No. Of record Inserted:
				<span id="progress_count"></span>
			</div>
			<div  class="progress progress-striped active">
				<div id="inner_loader" class="bar" style="width:0%"></div>
			</div>
			<p>Please do not refresh the Page</p>
			<script language="javascript">
			
				 $(document).ready(function(){				
					//insert csv record in database.
					csvProcess();
				 });
				 
				function csvProcess()
				{				
					csvCurrentCount = 0;
					$.ajax({
					  type: "POST",
					  url: "<?php echo $this->ajax_page_runner; ?>",
					async:true,
					}).done(function( msg ) {						
						msg= msg.trim();									
						if(msg!="done" && msg!="error")
						{	
							csvCurrentCount = parseInt(csvCurrentCount) + parseInt(msg);			
							percentProgress = (parseInt(csvCurrentCount)/<?php echo $_SESSION['fileLineCount']-$_SESSION['skipfirstrow']; ?>)*100
							$("#inner_loader").css("width",percentProgress+"%");
							$("#progress_text").html(Math.round(percentProgress)+'%');
							$("#progress_count").html(parseInt(csvCurrentCount));
							//Loop same process if end of file not reach.
							csvProcess();
						}
						
						if(msg=="done")
						{						
							$("#inner_loader").css("width","100%");
							$("#progress_text").html("100%");
						}
						
					});
				}	
			</script >
		<?php
	}
	
	//Insert ajax-call base csv-data in table.
    function insert_counter_data()
    {	
		//Open uploaded CSV file
		if(file_exists($this->upload_path.$_SESSION['file_name']))
		{
			$handle = fopen($this->upload_path.$_SESSION['file_name'], "r"); 
		}
		else
		{
			echo "error";
		}
		
		//Check function is calling first time.
		if((int)$_SESSION['csvlastcount']!=0){		
			fseek($handle,$_SESSION['csv_handle']);
		}
		
		$count=0;		


		// Including hooks file if it is set and exists
		$has_hooks = false;
		if($this->hooks_file != "")
		{
			$hook_file =  $this->hooks_file;
			if (file_exists($hook_file)) 
			{
				// Load the hooks file
				include_once($hook_file);
				$has_hooks = true;
			}
			else
			{
				echo "Specified Hooks file is Not found <br> " . $hook_file;
			}
		} // Including hooks file finished.

	
		while(($csv_data = fgetcsv($handle, 1024, ",")) !== FALSE)
		{	
			
			//Check that first-row(header line) would be skip from csv.
			if(isset($_SESSION['skipfirstrow']) && $_SESSION['skipfirstrow']==1 )
			{
				$_SESSION['skipfirstrow']=0;
				continue;
			}
			// Increment csv-row-count.
			$_SESSION['csvlastcount']=$_SESSION['csvlastcount']+1;
			
			//Increment count for add remaining lines to insert in database.
			$count = $count+1;
	
			$values = array();	
			$fieldmapp =array();
			$fieldmapp = (array)$_SESSION['fieldmaps'];
							
			//Loop to tables fields for build a insert query.
			foreach($this->table_fields as $key => $field)
			{
				$val =$fieldmapp[$key];
				
				if(!empty($val))
				{				
					$values[$field] = $csv_data[$val];
				}
				else
				{
					$values[$field] = "";
				}
			}
			
			if($has_hooks)
			{
				$values = csv_hook::process_row($values);
			}
			// Giving the data to be processed by the hooks. 
			

					$final_value= array();
					foreach($values as $valloop)
					{
						$final_value[] = "'" . mysql_real_escape_string( $valloop ) . "'";
					}

					//Insert a csv row data in database.
			$query ="INSERT INTO ".$this->table_name."(" . implode( ',' , $this->table_fields ) . ")VALUES(" . implode( ',' ,$final_value ) . ")";				
			mysql_query($query) or die(mysql_error());	
			
			//Set last handler pointer in Session.
			$_SESSION['csv_handle']=ftell($handle);
			
			//Check count that is reaches to set line-per-call, then loop will break.
			if($count>=$this->lines_percall)
			{					
				break;
			}			
		}
		
		//Check for end file reached, if not ajax return the CSV-row-count else done.
		if ((!feof($handle) === true)) {
			echo $_SESSION['csvlastcount'];
		}
		else
		{	
			// Done will return when complete CSV-data imported in database.
			echo "done";			
		}

		// Close the handler.
		fclose($handle);
    }	
	
}



?> 