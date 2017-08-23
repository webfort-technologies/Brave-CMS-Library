<?php 
/*

Creation Date 		: 2013/02/27


Description: Class include the functions related to file upload, delete.


**********************************************************************
	

*/



class uploadFile{			

	function uploadImage($file = array())
	{
		$response = array();
		if(!empty($file['tmp_name']))
	        {

				$filecheck = basename($file['name']);
				$ext = strtolower(substr($filecheck, strrpos($filecheck, '.') + 1));
				$file_type = $file["type"];
				$file_size = $file["size"];
	
				if (($file_type == "image/jpg" || $file_type == "image/jpeg" || $file_type == "image/gif" || $file_type == "image/png") && ( $file_size < 2120000))
					{
		
						
						$replace_str = array(" ", "&", "?","%");
						$picture     =    str_replace($replace_str, '_', $file['name']);    

						$newfile = strtotime(date("d-m-y H:i:s"))."@".$picture;
						$tmpName = $file['tmp_name'];
						$Attach_Dir_thumb    = '../../uploads/';
						$Attach_Name    = $Attach_Dir_thumb.$newfile;						



						if($newfile != '')
						{						
							if(move_uploaded_file($file['tmp_name'], $Attach_Name))
							{
								
								$response['resp'] = "success";								
								$response['file'] = $newfile;								
							}							
						}
						
					}
					else
					{		
						$response['resp'] = "No Image file found to upload.";
						$response['file'] = "";	
					
					}						
			}
			else 
			{							
				
				$response['resp'] = "Please check, file is attached.";
				$response['file'] = "";	
				
			}

			return $response;
			}

	

	function unlinkImage($filepath)
	{
		if(file_exists($filepath))
		{
			@unlink($filepath);
			return "ok";
		}
		return "not";

	}

}

?>