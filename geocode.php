<?php

$config['maps_key'] = "";     //insert your google maps api key

class curl {

	function curl() {}

	function init_curl($ch,$url,$postfields=null,$follow=null,$cookie=null,$referer=null) {

		// Set url
		curl_setopt($ch, CURLOPT_URL, $url);

		// Enable Post
		if($postfields) {
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $postfields);
		}

		if($follow) {
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1 );
		}

		if($referer) {
			curl_setopt($ch, CURLOPT_REFERER, $referer);
		}

		//Enable SSL
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);


		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');

		//Return results as string
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		return $ch;

	} // end function


	/*
	Grabs a page
	*/

	function get_page($options) {

		//Set options
		foreach($options AS $key=>$value) {
			$$key = $value;
		}

		$ch = curl_init();
		$ch = $this->init_curl($ch,$url);
		$page = curl_exec($ch);
		curl_close($ch);
		return $page;


	}
}
 // end class


//get latitude and longitude by postcode
function get_lat_long($postcode,$domain=null) {

	global $config;

	if(!$domain) {
		$domain = "co.uk";
	}

	$url = "http://maps.google." . $domain . "/maps/geo?q=" . urlencode($postcode) . "&output=json&key=".$config['maps_key'];

	$curl = new curl();
	$json = $curl->get_page(array("url"=>$url));

	$store_data = json_decode(str_replace("&quot;","\"",htmlentities($json))); //Take care of accents

	$lng = $store_data->Placemark[0]->Point->coordinates[0];
	$lat = $store_data->Placemark[0]->Point->coordinates[1];

	//Return
	if($lng && $lat) {

		return array('lat'=>$lat,
		'lng'=>$lng
		);

	} else {

		return false;

	}
}

?>