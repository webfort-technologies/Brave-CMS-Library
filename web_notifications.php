<?php 
function pushify($title, $message,$logo,$url)
{
	$title = urlencode($title);
	$message = urlencode($message);
	$logo = urlencode($logo);
	$url = urlencode($url);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"https://pushify.com/api/v1/send-push");
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Token: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWNjZXNzIjp0cnVlLCJpZCI6IjU5N2FhZDFkOGU1ODk1MTgzNDE4YTUzYSIsImVtYWlsIjoieW91LmNhbi5tYWlsLnJhZ2h1QGdtYWlsLmNvbSIsImlhdCI6MTUwMTIxMjcyMywiZXhwIjoxNTE2NzY0NzIzfQ.-RUOvqxyEF0o2s3oUzfuYydsxZrNK7Itspb1a9GBYrI'));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "title=".$title."&message=".$message."&url=".$url."&logo=".$logo."&is_feed=1&category=597aaebc7432615b4ba01d42");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);

curl_close ($ch);

}
 ?>