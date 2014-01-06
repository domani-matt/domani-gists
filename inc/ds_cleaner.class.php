<?php
if(!defined('RUN_CODE')){
	header('location:/gists/');
	exit;
}

mb_internal_encoding("UTF-8");

class ds_cleaner {

	private static $allowTags;
	private static $defaultTags;
	private static $stripTags;
	private static $encodeTags;
	private static $attr;

	function __construct($additionalTags=null){
		self::$stripTags = true;
		self::$encodeTags = false;
		self::$defaultTags = "<div><p><strong><em><span><ul><ol><li><a><br><br/><br /><sup><small>";
		self::_set_tags($additionalTags);
		self::$attr = explode(" ","href src type value width height alt title scrolling name sandbox rel target media coords autoplay controls preload loop disabled selected checked align content row role data max preload autoplay muted poster colspan rowspan");
	}

	public function default_tags(){
		self::_set_tags();
	}

	public function set_tags($additionalTags=null,$strict=false){
		self::_set_tags($additionalTags,$strict);
	}

	public function no_tags(){
		self::$allowTags = "";
	}

	public function all_tags(){
		self::$stripTags = false;
	}

	public function encode_tags(){
		self::$encodeTags = true;
	}

	public function decode_tags(){
		self::$encodeTags = false;
	}

	public function encode($obj,$tags=null){
		if(!is_numeric($obj)){
			if(is_array($obj) === true){
				foreach($obj as $i => $s){
					$obj[$i] = self::encode($s,$tags);
				}
			}
			else {
				$obj = self::_encode($obj,$tags);
			}
		}

		return $obj;
	}

	public function decode($obj){
		if(!is_numeric($obj)){
			if(is_array($obj) === true){
				foreach($obj as $i => $s){
				$obj[$i] = self::decode($s);
			}
		}
		else {
				$obj = self::_decode($obj);
			}
		}

		return $obj;
	}

	private function _set_tags($additionalTags=null,$strict=false){
		if($additionalTags !== null && is_string($additionalTags)){
			self::$allowTags = $strict === false ? $additionalTags : self::$defaultTags.$additionalTags;
		}
		else {
			self::$allowTags = self::$defaultTags;
		}
	}

	private function _encode($str,$tags=null){
		$str = mb_check_encoding($str, 'UTF-8') ? $str : utf8_encode($str);
		$str = self::_decode(trim($str));
		if($tags === null){
			if(self::$stripTags === true){
				$str = strip_tags($str,self::$allowTags);
			}
		}
		else {
			switch(strtolower($tags)){
				case "none":
					$str = strip_tags($str);
				break;
				case "all":
					$str = $str;
				break;
				default:
					$str = strip_tags($str,$tags);
				break;
			}
		}
		$str = self::_checkstring($str);
		if(self::$encodeTags === false){
			$n = preg_match_all("|&#60;\/(.*?)&#62;|msi",$str,$m,PREG_SET_ORDER);
			foreach($m as $z){
				$str = str_replace($z[0],self::_decode($z[0]),$str);
			}
			$n = preg_match_all("|&#60;(.*?)&#62;|msi",$str,$m,PREG_SET_ORDER);
			foreach($m as $z){
				$attrs = explode(" ",$z[1]);
				$tag = "<".array_shift($attrs);
				$a = " ".join(" ",$attrs);
				$a = preg_match_all("|\s+(.*?)=([\'\"])(.*?)([\'\"])|msi",$a,$attrs,PREG_SET_ORDER);
				if(!empty($attrs)){
					foreach($attrs as $attr){
						if(substr(strtolower($attr[1]),0,5) == "data-" || in_array($attr[1],self::$attr)){
							$tag .= " ".$attr[1]."=".$attr[2].$attr[3].$attr[4];
						}
					}
				}
				$tag .= ">";
				$str = str_replace($z[0],$tag,$str);
			}
		}
		return str_replace("&#160;"," ",$str);
	}

	private function _checkstring($str){
		$newstr = "";
		$c = strlen($str);
		for($i=0; $i < $c; $i++){
			$char = mb_substr($str,$i,1);
			// if you want additional languages to NOT be converted add them to the preg_match here
			// for example for HAN + THAI + ARABIC - preg_match("/\p{Han}|\p{Thai}|\p{Arabic}+/u", $char)
			// listing can be found here (Unicode Scripts section) - http://www.regular-expressions.info/unicode.html
			if((preg_match('/^[\x20-\x7f]*$/D',$char) == 0 && preg_match("/\p{Han}|\p{Cyrillic}|\p{Hiragana}|\p{Katakana}+/u", $char) == 0) || $char == "<" || $char == ">" || $char == "&"){
				$char = self::_convert_cb($char);
			}
			$newstr .= $char;
		}
		return $newstr;
	}

	private function _decode($string, $quote_style = ENT_COMPAT, $charset = "utf-8"){
		$string = html_entity_decode($string, $quote_style, $charset);
		$string = preg_replace_callback('~&#x([0-9a-fA-F]+);~i', array(get_class($this),'_chr_utf8_cb'), $string);
		$string = preg_replace('~&#([0-9]+);~e', 'self::_chr_utf8("\\1")', $string);
		return $string;
	}

	private function _chr_utf8_cb($matches){
		return self::_chr_utf8(hexdec($matches[1]));
	}

	private function _chr_utf8($num){
		if ($num < 128) return chr($num);
		if ($num < 2048) return chr(($num >> 6) + 192).chr(($num & 63) + 128);
		if ($num < 65536) return chr(($num >> 12) + 224).chr((($num >> 6) & 63) + 128).chr(($num & 63) + 128);
		if ($num < 2097152) return chr(($num >> 18) + 240).chr((($num >> 12) & 63) + 128).chr((($num >> 6) & 63) + 128).chr(($num & 63) + 128);
		return '';
	}

	private function _convert_cb($char) {
		list(, $ord) = unpack('N', mb_convert_encoding($char, 'UCS-4BE', 'UTF-8'));
		if(!is_numeric($ord) || $ord < 34){
			return $char;
		}
		return "&#".$ord.";";
	}
}
?>