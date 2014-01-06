<?
define('RUN_CODE',1);
define('DOCROOT',dirname(__FILE__));
require_once(DOCROOT."/inc/creds.php");
require_once(DOCROOT."/inc/ds_cleaner.class.php");
require_once(DOCROOT."/inc/helper.php");
require_once(DOCROOT."/inc/userlist.php");

$clean = new ds_cleaner;
$request = parseRequest();
$request_length = count($request);
$is_index = $request_length === 0;
$title = "DOMANI | Tools | Gists";

if(!$is_index){
	if($request_length > 1){
		header("location:/".$request[0]."/");
		exit;
	}
	$title .= " | ".$request[0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Source+Code+Pro:300,400" />
<link rel="stylesheet" type="text/css" href="/assets/css/styles.css" />
<title><?=$title?></title>
</head>
<body>
<h1><a href="/"><i class="ico-github3"></i><i class="ico-domani-white"></i></a></h1>
<? require_once(DOCROOT."/inc/".($is_index ? "home" : "gists").".php"); ?>
</body>
</html>