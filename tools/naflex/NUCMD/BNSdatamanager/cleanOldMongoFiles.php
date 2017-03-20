<?php
/*
 *
 */

require_once "/var/www/BigNASim/BNSdatamanager/phplib/mongoDB.inc.php";
require_once "/var/www/BigNASim/BNSdatamanager/phplib/users.inc.php";

$today      = date("Y-m-d h:i:s",strtotime("now"));
$limit      = date('Y-m-d',strtotime('-3 days')); // 3 days of grace given
$limitMtime = date('Y-m-d',strtotime('-93 days')); // $GLOBALS['days2expire'] + 3 days of grace given

print "[$today] Deleting files with expiration date < $limit \n";

$files = $GLOBALS['cassandraIds']->find(array(
					      'expiration' => array( '$lte'   => new MongoDate((strtotime($limit)))),
					      'files'      => array('$exists' => false),
					      'parentDir'  => array('$not'    => new MongoRegex("/^\w+\/submissions/"))
					));
$count = $files->count();

print "[$today] Total number of files to be removed is: $count\n";

if ($count > 0 ){
      foreach ($files as $file){
	$parent =  $file['parentDir'];
	print "[$today] Deleting FILE: ".$file['_id']."\tEXPIRATION DATE: ".strftime('%Y-%m-%d',$file['expiration']->sec)."\tMTIME: ".strftime('%Y-%m-%d',$file['mtime']->sec)."\n";

	//delete file
	$r = deleteGSFileBNS($file['_id'],0);

	if (!$r){
	    print "[$today] ERROR: ".$_SESSION['errorData']['mongoDB'][0]. "\n";
	    unset($_SESSION['errorData']['mongoDB']);
	    continue;
	}

	//delete parent directory if it becomes empty
	$fileParent  = $GLOBALS['cassandraIds']->findOne(array('_id' => $parent));
	if (isset($fileParent['files']) && count($fileParent['files']) == 0){
		print "[$today] Deleting DIRECTORY: ".$fileParent['_id']."\tEMPTY PARENT DIRECTORY\n";
		$r = deleteGSDirBNS($parent,0);
		if (!$r){
		    print "[$today] ERROR: ".$_SESSION['errorData']['mongoDB'][0]. "\n";
		    unset($_SESSION['errorData']['mongoDB']);
		    continue;
		}
	}
      }
}

// delete files with NO expiration date based on mtime
$files2 = $GLOBALS['cassandraIds']->find(array('expiration' => array( '$exists' => false),
					       'files'      => array( '$exists' => false),
					       'mtime' => array( '$lte'   => new MongoDate((strtotime($limitMtime))))
					));
if ($files2->count() > 0 ){
    foreach ($files2 as $file){
	$parent =  $file['parentDir'];
	print "[$today] Delete FILE: ".$file['_id']."\tMTIME:".strftime('%Y-%m-%d',$file['mtime']->sec)."\tOWNER:".$file['owner']."\tNO EXPIRATION DATE\n";

	$r = deleteGSFileBNS($file['_id'],0);
	if (!$r){
	    print "[$today] ERROR: ".$_SESSION['errorData']['mongoDB'][0]. "\n";
	    unset($_SESSION['errorData']['mongoDB']);
	    continue;
	}

	$fileParent  = $GLOBALS['cassandraIds']->findOne(array('_id' => $parent));
	if (isset($fileParent['files']) && count($fileParent['files']) == 0){
		$r = deleteGSDirBNS($parent,0);
		if (!$r){
		    print "[$today] ERROR: ".$_SESSION['errorData']['mongoDB'][0]. "\n";
		    unset($_SESSION['errorData']['mongoDB']);
		    continue;
		}
	}
    }
}

//delete Anonomous with no data
$anons = $GLOBALS['users']->find(array( 'Name' => "Anonymous" ));

foreach ($anons as $user){
	$files3  =  $GLOBALS['cassandraIds']->find(array(
							'owner' => $user['id'],
					      		'_id'  =>  array('$ne' => $user['id'])
						));
	if ($files3->count() == 0){
		print "[$today] Deleting USER: ".$user['id']." |  EMPTY\n";
		delUser($user['id']);
	}
}
?>
