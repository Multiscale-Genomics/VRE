<?php

require_once "/var/www/BigNASim/BNSdatamanager/phplib/mongoDB.inc.php";

$F = fopen("files.csv", "r") or die("Unable to open file!");
while ($line = fgets($F)) {
	$fnGrid = $GLOBALS['cassandra']->findOne(array("filename"=>$fn));
	if (isset($fsGrid['_id'])){
		$exist0=1;
	}else{
		$exist0=0;
	}
	$l = explode(",",$line);
	$fn=$l[0];
	if (!preg_match("/^ANONUSER/",$fn) && !preg_match("/^BNSUSER/",$fn) ){
		$exist         = 0;
		$hasParent     = 0;
		$parentHasFiles= 0;
		$nfiles        = 0;
		$ids = $GLOBALS['cassandraIds']->findOne(array( '_id' => $fn));

		if (isset($ids['_id']))
			$exist = 1;
		if ($exist){
			if (isset($ids['parentDir']))
				$hasParent= $ids['parentDir'];
			if ($hasParent){
				$parentHasFiles= isGSDirBNS($GLOBALS['cassandraIds'],$hasParent);
				$parent = $GLOBALS['cassandraIds']->findOne(array('_id'  => $hasParent));
				$nfiles = count($parent['files']);
			}
			$GLOBALS['cassandraIds']->remove(array('_id'=> $fn));
			$GLOBALS['cassandra']->remove($fn);
			if ($hasParent){
				$GLOBALS['cassandraIds']->update(
					array('_id'=> $hasParent),
					array('$pull' => array("files"=>$fn))
				);
			}
		}else{
	    		$GLOBALS['cassandra']->remove($fn);
		}
		print "DELE $exist0\t$exist\t$hasParent\t$parentHasFiles\t$nfiles\t$fn\n";
		
	}else{
		//print "SAVE $fn\n";
	}
}
fclose($F);
//$files_ids = $GLOBALS['db3']->getGridFS("chunks")->find(array( 'files_id' => new MongoId('553f5a0d429acf2181c767e8')));

//foreach ($files_ids as $files_id){
//	print "dsdsdd->".$files_id['files_id']."\n";
//}

?>
