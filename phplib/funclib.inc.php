<?php

function redirect($url) {
    header("Location:$url");
    exit;
}

function moment() {
    return date("Y/m/d*H:i:s");
}

function getConf($path){
	$handle = @fopen($path, "r");

	$buffer = '';
	if ($handle) {
		while (!feof($handle)) {
			$buffer .= fgetss($handle, 5000);
		}
		fclose($handle);
    }
    $results=array();
    foreach(explode(";",$buffer) as $a){
        $r = explode(":",$a);
        if (isset($r[1]))
            array_push($results,$r[1]);
    }
/*
	$a = explode(";",$buffer);
	$l = explode(":",$a[0]);
	$p = explode(":",$a[1]);
	$s = explode(":",$a[2]);

	$results = array($l[1],$p[1],$s[1]);
 */
	return $results;

}

function redirectOutside(){
	if(!checkLoggedIn()){
		redirect($GLOBALS['URL']);
	}else if(!checkTermsOfUse()) {
		if(pathinfo($_SERVER['PHP_SELF'])['filename'] != 'usrProfile') redirect($GLOBALS['PROFILE']);
	}
}

function redirectAdminOutside(){
	if(!checkAdmin()){
		redirectInside();
	}
}

function redirectToolDevOutside(){
	if(!checkToolDev()){
		redirectInside();
	}
}

function redirectInside(){
	redirect("/workspace/");
}

function checkIfSessionUser($url = "/workspace/"){
	if(checkLoggedIn()){
		if($url == 'index.php') $url = "/workspace/";
		redirect($url);
	}
}


function sanitizeString($s){
	return strip_tags(trim((string)$s));
}

function returnHumanDate($q){
	$d = explode("*", $q);
	$dma = explode("/", $d[0]);
	$hms = explode(":", $d[1]);

	//return $dma[0]."/".$dma[1]."/".$dma[2]."<div style=\"display:none;\">".$hms[0].":".$hms[1]."</div>";
	return $dma[0]."/".$dma[1]."/".$dma[2]." - ".$hms[0].":".$hms[1];

}

function returnHumanDateDashboard($q){
	$d = explode("*", $q);
	$dma = explode("/", $d[0]);
	$hms = explode(":", $d[1]);
	return "<span class=\"mt-action-date\">".$dma[0]."/".$dma[1]."/".$dma[2]."</span> <span class=\"mt-action-time\">".$hms[0].":".$hms[1]."</span>";
}

function is_multi_array( $arr ) {
	rsort( $arr );
	return isset( $arr[0] ) && is_array( $arr[0] );
}

function maxlength($in, $length) {
		
	return strlen($in) > $length ? substr($in,0,$length)."..." : $in;

}


?>
