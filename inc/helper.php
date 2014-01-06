<?
if(!defined('RUN_CODE')){
	header('location:/');
	exit;
}

function parseRequest(){
	$tmp = array();
	if(isset($_GET['u'])){
		$tmp = explode("/",$_GET['u']);
		$l = count($tmp) - 1;
		if(strlen($tmp[0]) < 1){
			array_shift($tmp);
		}
		if($l > 0 && strlen($tmp[$l]) < 1){
			array_pop($tmp);
		}
	}

	return $tmp;
}

function get($url_suffix){
	$ch = curl_init();
	$options = array(
		CURLOPT_URL => "https://api.github.com/$url_suffix?client_id=".API_ID."&client_secret=".API_SECRET,
		CURLOPT_USERAGENT => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36",
		CURLOPT_HEADER => false,
		CURLOPT_RETURNTRANSFER => true
	);
	curl_setopt_array($ch,$options);
	$response = json_decode(curl_exec($ch),1);
	return $response;
}

function get_img_data($src){
	return "data:image/png;base64,".base64_encode(file_get_contents($src));
}
?>