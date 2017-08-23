<?php
/*
Programmer : Jay Prakash Jain
Purpose : Class to create for User capabiliyt


Example of how to use the class

include("library/export.php"); 
$a = new Export();
 
$a->export_query('read');
$a->export_table('read');

//Funtion to get all user ids below this user
$a->get_sub_usersids();
return true or false 
*/
require_once 'excel_reader2.php';
Class Export
{
	public  $table;
	 

	function __construct()
	{
			 // $this->export_table();
			// $this->table =  'client_contacts';
		   //  $this->expt();
	}	 
	
	public function downBackup($file)
	{
		header("Content-Description:File Transfer");;
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
   		header('Pragma: public');
   		header('Content-Disposition: attachment; filename='.basename($file));
   		header('Content-Type: application/octet-stream');
    	header('Content-Length: ' . filesize($file));    	
    	readfile($file);
		exit();
	}
	
	function export_table($table)
	{ 
		$csv_output = ""; 
		//$table = 'client_activities';	
		//$table = $this->table;
		//$file = 'csvexports.csv'; 
		$filess = 'backup/Table_'.$table.date("YmdHis").'.csv';
		if($_SESSION['userinfo']['role_id']!=1)
			{
				$user_query = " Where user_id=".$_SESSION['userinfo']['user_id'];
			}
		 $query = "SELECT * FROM $table".$user_query; 
		$result = mysql_query($query); 
		$colCount = mysql_num_fields($result); 
		$i = $colCount; 

		for($j = 0; $j < $i; $j++) 
		{ 
		$rowr = mysql_field_name($result, $j); 
		$csv_output .= '"' . $rowr . "\","; 
		} 
		$csv_output = rtrim($csv_output, ","); 
		$csv_output .= "\n"; 

		while ($row = mysql_fetch_row($result)) 
		{ 
		for ($j=0;$j<$i-1;$j++) 
		{ 
		$csv_output .='"'.$row[$j]."\","; 
		} 
		$csv_output .='"'.$row[$j]."\""; 
		$csv_output .= "\n"; 
		} 
		 
		//readfile('csvexport.csv'); 
		 $handle = fopen($filess,'w') or die("can't open file"); 
		fwrite($handle,$csv_output);
		fclose($handle);  
		
		 $this->downBackup($filess);
	}
	
	function export_query($query)
	{ 
		$csv_output = ""; 
		//$table = 'client_activities';	
		//$table = $this->table;
		//$file = 'csvexports.csv'; 
		$filess = 'backup/Table_'.date("YmdHis").'.csv';
		//$query = "SELECT * FROM $table"; 
		$result = mysql_query($query); 
		$colCount = mysql_num_fields($result); 
		$i = $colCount; 

		/*for($j = 0; $j < $i; $j++) 
		{ 
		$rowr = mysql_field_name($result, $j); 
		$csv_output .= '"' . $rowr . "\","; 
		} 
		$csv_output = rtrim($csv_output, ","); 
		$csv_output .= "\r\n"; */
		
		

		while ($row = mysql_fetch_row($result)) 
		{ 
		for ($j=0;$j<$i-1;$j++) 
		{ 
		$csv_output .='"'.$row[$j]."\","; 
		} 
		$csv_output .='"'.$row[$j]."\""; 
		$csv_output .= "\r\n"; 
		} 
		 
		//readfile('csvexport.csv'); 
		 $handle = fopen($filess,'w') or die("can't open file"); 
		fwrite($handle,$csv_output);
		fclose($handle);  
		
		 $this->downBackup($filess);
	}
	
	
	function Uploadcsv($table_name,$columns,$userid=-1)
	{
	
	if(isset($_FILES['csvupload']) && $_FILES['csvupload']['name'] != "")
		{	
  			$type = $_FILES["csvupload"]["type"];
			//$type = array("application/octet-stream", "text/csv",);
		
			if($type == "application/vnd.ms-excel" || $type = "application/octet-stream")
			{			
				 
			$allowedExts = array("xls");
			$extension = end(explode(".", $_FILES["csvupload"]["name"]));
			if (in_array($extension, $allowedExts))
			{
			
				 
				$target_path = "./backup/";
				
				$target_path1 = $target_path . basename( $_FILES['csvupload']['name']); 
			   
				if ($_FILES["csvupload"]["error"] > 0)
				{
					echo "Return Code: " . $_FILES["csvupload"]["error"] . "<br />";
				}
			   else
				{
					if(move_uploaded_file($_FILES["csvupload"]["tmp_name"],$target_path1))
					{
					 	 
							//rename($target_path1,$filename);
							$filename = addslashes(realpath('backup')."/".$_FILES['csvupload']['name']);
							$filename2 = addslashes(realpath('backup')."/temp.csv");
							
							$data = new Spreadsheet_Excel_Reader($filename);
							
						
							
							$this->outputCSV( $data->dumptoarray(),30,$filename2); 
							/*
							$columnnames = array();
						 
							 if (($handle = fopen($filename, "r")) !== FALSE)
							{
						 
							while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
							{
								$num = count($data);
								for ($c=0; $c < $num; $c++) 
								{
									$columnnames[] .=  $data[$c];
								 
								}
								break;
							}
							fclose($handle);
							}  */
							 
							if(isset($userid) && !empty($userid) && $userid!=-1)
								{
									$userid_query	= 'SET user_id='.$userid; 
								}
							
							elseif(isset($_SESSION['userinfo']['user_id']))		
								{
									$user_id = $_SESSION['userinfo']['user_id'];
									$userid_query	= 'SET user_id='.$user_id;
								}
							$sql ="LOAD DATA LOCAL INFILE '$filename2' INTO TABLE $table_name FIELDS TERMINATED BY ',' ENCLOSED BY '\"' ESCAPED BY '\\\\' LINES 
								 TERMINATED BY '\n' ($columns) $userid_query";
														
							$res = mysql_query($sql) or die(mysql_error());
						 
							//$affectedRows = $db->getAffectedRows($res);
							//echo '<h2>'.$affectedRows .' Record is update <br/><a href="index.php?option=com_adminuserimports&task=update_user">update</a></h2> ';
					}
				}
			}	 
			
			else
			{
			 echo 'tstst';

			}
			}else {	
			 echo $type."wrong type";		 

			// echo 'Invalid file type ';
			} 

		 } 
	}
	
	function outputCSV($data,$no_of_columns=30,$csv_name = "myfile.csv" ) { 
	$outputBuffer = fopen( $csv_name , 'w' );
        foreach($data as $val) {
			array_splice( $val , $no_of_columns );
			fputcsv($outputBuffer, $val);
        }
        fclose($outputBuffer);
 }

	  
}
?>