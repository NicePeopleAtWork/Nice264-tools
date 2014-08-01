<?php

if(isset($argv[1]) && $argv[1]!=""){
	$url=$argv[1];
}else{
	$url="http://stage.nws.nice264.com/Smil/getContentIOS.m3u8?media=40d304559bbaf3d07097&system=nicetv&protocol=http_cupertino/playlist.m3u8";
}

if(isset($argv[2]) && $argv[2]!=""){
        $media=$argv[2];
}else{
        $media="video1";
}
system ("mkdir ".$media);
$playlist=download_page($url);
$playlist=explode("\n",$playlist);
$qualitiesnum=0;
$qualities=null;
$qua=1;
for($i=0;$i<count($playlist);$i++){
	$newplaylist=$playlist[$i];
	$pos=strpos($playlist[$i],"BANDWIDTH");
	if($pos !== false){
		$qualities[$qualitiesnum]=$playlist[$i+1];
		$qualitiesnum++;
	}
	$pos=strpos($playlist[$i],"http");
        if($pos !== false){
		$newplaylist="0".$qua."/chunklist.m3u8";
		$qua++;
	}
	system("echo \"".$newplaylist."\" >> ".$media."/playlist.m3u8");
}
$qua=1;
foreach($qualities as $quality){
	system ("mkdir ".$media."/0".$qua);
	$address=explode("/chunklist.m3u8",$quality);
	$address=$address[0];
	
	system ("wget -O ".$media."/0".$qua."/chunklist.m3u8 \"".$quality."\"");
	$chunklist=download_page($quality);
	$chunklist=explode("\n",$chunklist);
	$chunks=null;
	$chunknum=0;
	for($i=0;$i<count($chunklist);$i++){
		$pos=strpos($chunklist[$i],".ts");
		if($pos !== false){
			$chunks[$chunknum]=$chunklist[$i];
			$chunknum++;
		}
	}
	for($j=0;$j<count($chunks);$j++){
		echo  "wget -O ".$media."/0".$qua."/".$chunks[$j]." ".$address."/".$chunks[$j]."
";
		system ("wget -O ".$media."/0".$qua."/".$chunks[$j]." ".$address."/".$chunks[$j]);
	}
	$qua++;

}

function download_page($path){
	$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$path);
        curl_setopt($ch, CURLOPT_FAILONERROR,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $retValue = curl_exec($ch);
        curl_close($ch);
        return $retValue;
}
?>
