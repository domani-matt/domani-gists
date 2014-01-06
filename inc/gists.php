<?
if(!defined('RUN_CODE')){
	header('location:/');
	exit;
}

$data = get("users/".$request[0]."/gists");
$out = "";
$gists = "";
if(count($data) > 0){
	$gists .= "<div id=\"gistcode\">\n";
	foreach($data as $i => $d){
		if($i === 0){
			$info = $d['user'];
			$out .= "<h2><a href=\"$info[html_url]\" target=\"_blank\"><img src=\"$info[avatar_url]\" alt=\"$info[login]\" />$info[login]</a></h2>\n";
			$out .= "<ul id=\"gists\" class=\"unstyled\">\n";
		}
		$name = "";
		foreach($d['files'] as $f){
			$name = $clean->encode($f['filename'],"none");
			break;
		}
		$description = trim($d['description']);
		if(mb_strlen($description) > 255){
			$description = preg_replace("/\s+?(\S+)?$/","",mb_substr($description,0,256))."&#8230;";
		}
		$description = str_replace("<","&#60;",str_replace(">","&#62;",$clean->encode($description)));

		$out .= "	<li data-id=\"$d[id]\"><strong><a href=\"$d[html_url]\" target=\"_blank\"><i class=\"ico-github\"></i><i class=\"ico-github2\"></i></a>$name</strong>$description</li>\n";
		$gists .= "	<code id=\"gist-$d[id]\"></code>\n";
	}
	$out .= "</ul>\n";
	$gists .= "	<a href=\"#\" id=\"gistclose\"></a>\n</div>\n";
}
else {
	$out .= "<h3>".$request[0]."<br />does not have any public gists.</h3>\n";
}

echo $out.$gists;
?>
<script type="text/javascript" src="/assets/js/jquery.js"></script>
<script type="text/javascript" src="/assets/js/gist-embed.js"></script>
<script type="text/javascript" src="/assets/js/main.js"></script>
