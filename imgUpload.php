<?php 
/*
File Upload Utility function.
Framework 		: Brave Framework.  
Author 			: Shishir Raven. 
Last Modified 	: 09 Feb 2019. 

Options. 
========
- Field Name. 
- File Max Size. 
- Destination Folder. 

=======================
Example for Upload. 
========================

include("library/image_uploader.php"); 

$result = imgUpload('image1','uploads',1);

if($result['success'])
{
	echo $result["filename"];
	echo $result["folder"];
	echo $result["destination"];
}
else
{
	echo "Error message". $result['message']; 
}
*/ 
function imgUpload($fieldName, $destination_fol,$maxFileSizeMB=2)
{
	$uploadFolder =    $destination_fol;
	
	$response = array();

	if (isset($_FILES[$fieldName])) 
	{
		$file = $_FILES[$fieldName];
		$file_type 		= $file["type"];
		$file_size 		= $file["size"];
		$file_tmp_name 	= $file['tmp_name'];
		/* -------------------------------------------------*/
		/* File Attached Check */
		/* -------------------------------------------------*/
		if(empty($file_tmp_name)) {
			$response["success"] = false;
			$response['message'] = "Please Check if the file is attached"; 
			return $response;
	    }
	    /* -------------------------------------------------*/
		/* File Extension check */
		/* -------------------------------------------------*/
		if($file_type != "image/jpg" and $file_type != "image/jpeg" and $file_type != "image/gif" and $file_type != "image/png") {
			$response["success"] = false;
			$response['message'] = "Invalid file type PNG,JPEG or GIF Expected1"; 
			return $response; 
		}
		/* -------------------------------------------------*/
		/* File Size Check */
		/* -------------------------------------------------*/
		if($file_size > 1e+6*$maxFileSizeMB) { //10 MB (size is also in bytes)
			$response["success"] = false;
	    	$response['message'] = 'File size bigger than allowed.';
			return $response;
		}
       
	    $file = $_FILES[$fieldName];
	    $filename = uniqid() . '.' . (pathinfo($file['name'], PATHINFO_EXTENSION) ? : 'png');
	    move_uploaded_file($file['tmp_name'], $uploadFolder . DIRECTORY_SEPARATOR  . $filename);
	    $response["success"]		= true;
	    $response['filename'] 		= $filename;
	    $response['folder'] 		= $destination_fol;
	    $response['destination'] 	= $uploadFolder . DIRECTORY_SEPARATOR  . $filename;
	    return $response; 
	} 
	else 
	{
	    $response['error'] = 'Error while uploading file';
		return $response; 
	}	
}
?>