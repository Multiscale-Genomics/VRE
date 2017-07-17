<?php
ob_start();

if($_POST){

	function progress($resource,$download_size, $downloaded, $upload_size, $uploaded)
	{
		ob_get_clean();
		if($download_size > 0)
         	echo (number_format($downloaded / $download_size  * 100))."\n";
    	ob_flush();
		flush();
	}

	$source = $_POST['url'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $source);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progress');
	curl_setopt($ch, CURLOPT_NOPROGRESS, false); // needed to make progress function work
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	$data = curl_exec ($ch);
	$error = curl_error($ch); 
	curl_close ($ch);

	$nameFile = explode("/", $source);

	$destination = "../files/".$nameFile[sizeof($nameFile) - 1];
	$file = fopen($destination, "w+");
	fputs($file, $data);
	fclose($file);

	if(filesize($destination) == 0) {
		unlink($destination);
		echo 'ko'."\n";
	}else{
		echo 'ok'."\n";
	}

}else{
	redirect($GLOBALS['URL']);
}

?>
