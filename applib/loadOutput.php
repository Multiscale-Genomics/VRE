<?php

require "../phplib/genlibraries.php";
redirectOutside();

if($_REQUEST){
//if($_POST){

	$wd  = $GLOBALS['dataDir']."/".$_SESSION['User']['id']."/.tmp/outputs_".$_REQUEST['project'];
	$indexFile = $wd.'/index';

	$results =Array();
	if(is_dir($wd)) {

		// check if content uncompressed

		if(file_exists($indexFile)) {
		
			$results = file($indexFile);
			//var_dump($results);

		}

	}else{

		// create $wd

		mkdir($wd);
		touch($indexFile);

	}


	// Get internal results
	//

	if(!count($results)) {

		$files = $GLOBALS['filesCol']->findOne(array('_id' => $_REQUEST['project']), array('files' => 1, '_id' => 0));

		foreach($files["files"] as $id) {

			$fMeta = iterator_to_array($GLOBALS['filesMetaCol']->find(array('_id' => $id,
																																			'data_type'  => "tool_statistics",
																																			'format'     =>'TAR',
																																			'compressed' =>"gzip")));
			if(count($fMeta) ) {
				$path = $GLOBALS['dataDir']."/".getAttr_fromGSFileId($id,'path');
				exec("tar --touch -xzf \"$path\" -C \"$wd\" 2>&1", $err);

				if(!count($err)) {

					$fp = fopen($indexFile, 'a');
					fwrite($fp, $id.PHP_EOL);
					fclose($fp);

				} else { echo "error!!!!"; }
			}
		}

		$results = file($indexFile);

	}

	echo '1';

}else{
	redirect($GLOBALS['URL']);
}


