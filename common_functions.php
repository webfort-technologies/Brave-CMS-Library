<?php //include("../connection.php");
function curPageURL() {

$http_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
 return $http_protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}

function rand_string( $length ) {
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	return substr(str_shuffle($chars),0,$length);
}

function make_alias($alias_name,$table_name,$exclude_id=0,$exclude_column='id')
{	
	
	$search_string = array('/[^A-Za-z0-9& ]/');
	$alias_name	=	 strtolower(str_replace(' ','-',str_replace('  ',' ',str_replace('&','and',trim(preg_replace($search_string, '', $alias_name))))));
	$alias_sql	=	"select * from $table_name where alias = '".$alias_name."' and $exclude_column<>$exclude_id" ;
	$alias_rs	=	mysql_query($alias_sql)or die(mysql_error().$alias_sql);
	if(mysql_num_rows($alias_rs))
	{
		$i = 1;
		$search = 'false';
		while($search == 'false')
		{
			$alias_name	=	$alias_name.'-'.$i; 
			$alias_sql	=	"select * from $table_name where alias = '".$alias_name."' and $exclude_column<>$exclude_id " ;
			$alias_rs	=	mysql_query($alias_sql) or die();
			if(!mysql_num_rows($alias_rs))
			{
				$search = 'true';
			}
			$i++;
		}
		return $alias_name;
	}
	else
	{ 
		return $alias_name;
	}
}
?>