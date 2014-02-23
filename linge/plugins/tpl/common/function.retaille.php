<?php

function jtpl_function_common_retaille($tpl, $url)
{
	
	if($url == ""){
		return null;
	}

	$extensions = array(
			1 => 'GIF', 2 => 'JPG',3 => 'PNG', 4 => 'SWF', 5 => 'PSD',
			6 => 'BMP', 7 => 'TIFF', 8 => 'TIFF', 9 => 'JPC', 10 => 'JP2', 11 => 'JPX',
			12 => 'JB2', 13 => 'SWC', 14 => 'IFF');
	
	$infos_image = getImageSize($url);
	
	
	$ch = curl_init ($url) ;
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1) ;
	$content = curl_exec ($ch) ;
	curl_close ($ch) ;
	
	$config = jApp::configPath('defaultconfig.ini.php');
	$ini = new jIniFileModifier ($config);
	$cache = $ini->getValue('imagecache','0');
	
	$rand  = rand(1,100000);
	$ok = jFile::write( $cache . '/var/upload/img'. $rand . '.' . strtolower($extensions[$infos_image[2]]) , $content);
	 
	$params = array('maxwidth'=>190, 'maxheight'=>242,'background'=>'#ffffff','zoom'=>100);
	$att = jImageModifier::get('../var/upload/img'. $rand . '.jpg', $params);
	
	if($att['src'] == ""){
		$image = $att['cache_path'];
	} else {
		$image = $att['src'];
	}	
	
	echo $image;
}

?>