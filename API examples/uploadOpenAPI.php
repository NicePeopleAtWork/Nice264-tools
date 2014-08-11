<?php

require_once( str_replace('//','/',dirname(__FILE__).'/') .'../openApi/niceLib/php/nice264OpenApi/oAuth/OAuth.php'); 

$base_address = "http://19.api.nice264.com/1.9";

$oauth_timestamp = time();	
$oauth_signature_method = "HMAC-SHA1";
$oauth_nonce = md5("nonce");

$oauth_consumer_key = "<your_key_here>";
$oauth_consumer_secret = "<your_secret_here>";

echo "-----------------------------------------------\n";
echo "-  Nice264 API: curl generator                -\n";
echo "-----------------------------------------------\n";

$operation = "POST";

$service = "/Media/upload/";

$aqp = "title=pedrinprovasergi&media_file=@/home/pere/Videos/DWFExamples.mp4";

echo "-----------------------------------------------\n";
echo "-----------------------------------------------\n";
echo "API call:  " . $base_address .  $service . "\n";
echo "oauth_consumer_key: " . $oauth_consumer_key . "\n";
echo "oauth_consumer_secret: " . $oauth_consumer_secret . "\n";
echo "oauth_nonce: " . $oauth_nonce . "\n";
echo "oauth_timestamp: " . $oauth_timestamp . "\n";

echo "-----------------------------------------------\n";

// building the url and the secret
$params = array('oauth_consumer_key' => $oauth_consumer_key
, 'oauth_consumer_secret' => $oauth_consumer_secret
, 'oauth_nonce' => $oauth_nonce
, 'oauth_timestamp' => $oauth_timestamp
, 'oauth_token' => ''
, 'oauth_signature_method' => $oauth_signature_method);


// take params from the url if any
$p2 = explode('?', $service);
$service_string = $p2[0];
if(count($p2)>1) {
	$query_string = $p2[1];
	$query_string_params = explode('&', $query_string);

	foreach ($query_string_params as $p=>$v ) {
		$x = explode('=',$v);
		$params[$x[0]]=$x[1];
	}
}

// add the additional query parameters:
if ($aqp != null){
	$aqp2 = explode('&', $aqp);
	foreach ($aqp2 as $p => $v){
		$x = explode('=', $v);
		$params[$x[0]]=$x[1];
	}

}

unset($params['oauth_consumer_secret']);

if ($operation == "POST"){
	unset($params['media_file']);
}
echo "----- params: "; 
print_r($params); 
echo "--------*-*-*-------------------\n\n";


// will urlencode params and values and then build the string
$query = OAuthUtil::build_http_query($params);


echo "---------------------------------------\n\n";
echo "Compose query string from all parameters, as a lexicographically ordered list (note params and values need to be urlencoded first):\n $query \n";

$s = Array();
$s[] = $operation;
$s[] = OAuthUtil::urlencode_rfc3986($base_address . $service_string);
$s[] = OAuthUtil::urlencode_rfc3986($query);

echo "---------------------------------------\n";
echo "Build the token. Use operation, urlencoded address, urlencoded query string (query string is urlencoded again, params had already been urlencoded))\n";
echo print_r($s,1) . "\n"; 
//echo "\n\n";
//echo print_r($s) . "\n"; 
//echo "\n\n";

echo "-----------------------------------------------\n";
echo $s[0]."\n\n";
echo $s[1]."\n\n";
echo $s[2]."\n\n";

echo "-----------------------------------------------\n";
$ss = $s[0]."&".$s[1]."&".$s[2];
echo $ss . "\n";


$token = base64_encode(hash_hmac('sha1', $ss, OAuthUtil::urlencode_rfc3986($oauth_consumer_secret)."&", true));
echo "Token: $token \n";


$token2 = OAuthUtil::urlencode_rfc3986($token);
echo "Url encoded Token: $token2 \n";

$nonce2 = OAuthUtil::urlencode_rfc3986($oauth_nonce);

// add aqp
if ($aqp != ""){
        $aqp2 = explode('&', $aqp);
        foreach ($aqp2 as $p => $v){
		if ($operation == 'PUT'){
			$aqp_string = $aqp_string . " -d '$v' ";
		} else if ($operation == 'POST'){
			$aqp_string = $aqp_string . " --form '$v' ";
	
		} else $aqp_string = "";
	}

} else $aqp_string = "";


echo "-----------------------------------------------\n";
echo "curl call:\n";
$curl_call = "curl $aqp_string -v -N -X $operation -H 'Authorization: OAuth oauth_consumer_key=\"$oauth_consumer_key\",oauth_token=\"\",oauth_signature_method=\"$oauth_signature_method\",oauth_signature=\"$token2\",oauth_timestamp=\"$oauth_timestamp\",oauth_nonce=\"$nonce2\"' $base_address" . "$service\n\n";
echo $curl_call . "\n";
echo "-----------------------------------------------\n";
$xx = getInput("Press Enter to execute the prior curl\n");

passthru($curl_call);

function getInput($msg){
  fwrite(STDOUT, "$msg: ");
  $varin = trim(fgets(STDIN));
  return $varin;
}

?>
