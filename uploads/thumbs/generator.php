<?php 
include("../../library/thumbnailer.php");
$haystack=basename($_SERVER['REQUEST_URI']);

/* Check if ftb and fts not defined in url */
$tmp = explode("_", $haystack);
if($tmp[0]!='fts' && $tmp[0]!='ftb')
{
	//$haystack = "ftb_".$haystack;
	header("HTTP/1.0 404 Not Found");
	exit;
}
/* Check if ftb and fts not defined in url */

$pos1 = strpos($haystack, "_");
$pos2 = strpos($haystack, "_", $pos1+1);
$pos3 = strpos($haystack, "_", $pos2+1);
$requested_file = substr($haystack, $pos3+1);
$size = explode("_",substr($haystack,0, $pos3));



if(file_exists("../".$requested_file))
{
	$config['image']=$requested_file;
	$config['folder']="../";
	$config['compression']="100";
	
	$config['width'] =$size[1];
	$config['height']=$size[2];
	if (strstr($size[0], "fts")) {
		$config['fit_to_scale']=true;
		$config['fit_to_box']=true;
	}
	else
	{
		$config['fit_to_scale']=false;
		$config['fit_to_box']=true;
		$config['fit_aspect_ratio']= true;
	}
	$thumb1 = new thumbnailer($config);
    $thumb1->create_thumb();
    header('Content-Type: image/jpeg');
    echo readfile($haystack);
}
else
{
	header("HTTP/1.0 404 Not Found"); 
}
exit;
?>