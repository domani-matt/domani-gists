<?
if(!defined('RUN_CODE')){
	header('location:/');
	exit;
}

sort($userlist);

$out = "";
foreach($userlist as $u){
	$uname = strtolower($u);
	$info = get("users/$uname");
	if(isset($info['message'])){
		$out = "<h3>$info[message]</h3>\n";
		break;
	}
	$out .= "	<li data-color=\"rgba(0,0,0,0.35)\"><a href=\"/$info[login]/\"><img src=\"$info[avatar_url]\" alt=\"$info[login]\" /><strong>$info[login]</strong>".(isset($info['name']) ? "<small>$info[name]</small>" : "")."</a></li>\n";
}
?>
<ul id="gistlist" class="unstyled clearfix">
<?=$out?>
</ul>
