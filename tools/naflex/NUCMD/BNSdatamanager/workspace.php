<?php
/*
 * 
 */
require_once "phplib/globals.inc.php";
require_once "phplib/users.inc.php";
require_once "phplib/check.inc.php";
require_once 'phplib/projects.inc.php';
require_once 'phplib/sge_functions.inc.php';

#set_include_path('BNSdatamanager');

//JL BigNASim recuperem id de $_SESSION o URL

if (isset($_REQUEST['BNSId']) ){
    $_SESSION['BNSId'] = $_REQUEST['BNSId'];

    // load User info from Mongo
    $user =checkUserLoginExists($_REQUEST['BNSId']);
    if (isset($_SESSION['User']['id']) && $_SESSION['User']['id']  != $user['id']){
	$_SESSION['curDir'] = $user['id'];
	$_SESSION['User']= $user;
    }
    if (! isset($_SESSION['curDir']))
	$_SESSION['curDir'] = $user['id'];
}

if (isset($_SESSION['BNSId']) ) {

	if (! isset($_SESSION['User']['id']))
	        $_SESSION['User']=checkUserLoginExists($_SESSION['BNSId']);
	
	if (! isset ($_SESSION['User']['id'])){
		print errorPage("Error", "No login information retrieved for id ".$_SESSION['BNSId']. ". <br/>Reset [ <a href=\"gesUser.php?op=loginForm\">login</a> ], please");
		exit(0);
	}
	
	// look for SGE data from BNS session var
	$sessionFile=$GLOBALS['baseDirBigASim']."/".$GLOBALS['tmpDirBigNASim']."/".$_SESSION['BNSId']."/projectData.bin";
	if ( is_file($sessionFile)){
	    $session_BigNASim=unserialize(file_get_contents($sessionFile));
	    if (isset($session_BigNASim['SGE']))
        	$_SESSION[SGE]=$session_BigNASim['SGE'];
	}
}else{
	print errorPage("Error", "BNS identifier not provided. <br/>Reset [ <a href=\"gesUser.php?op=loginForm\">login</a> ], please");
	exit(0);
}

//set currentDir and create home
prepUserWorkSpace();


#### process op
if (isset($_REQUEST['fn'])) {
    $rfn = $_REQUEST['fn'];
    $fileData = $GLOBALS['cassandraIds']->findOne(array('_id' => $_REQUEST['fn'], 'owner' => $_SESSION['BNSId']));
}


switch ($_REQUEST['op']) {

    case 'newFolder' :
       if (! $_SESSION['User']['Uploader'] == "on"){
             $errStr = "Error: User". $_SESSION['User']['id']." is not allowed to write into the workspace. Register a user with the right configuration";
             break;
        }

	$r = createGSDirBNS($GLOBALS['cassandraIds'],$rfn);
        break;
    case 'upDir':
        upDir();
        break;
    case 'downDir':
	if (isGSDirBNS($GLOBALS['cassandraIds'], $_REQUEST['fn']))
	     downDir($_REQUEST['fn']);
	else
	    $errStr=" Cannot open directory. ".$_REQUEST['fn']. " is not a directory ";
        break;
    case 'gotoDir':
	if (isGSDirBNS($GLOBALS['cassandraIds'], $_REQUEST['fn']))
	    $_SESSION['curDir'] = $_REQUEST['fn'];
	else
	    $errStr=" Cannot open directory. ".$_REQUEST['fn']. " is not a directory ";
        break;

    case 'downloadFile' :
	$ext = pathinfo($_REQUEST['fn'], PATHINFO_EXTENSION);
        $contentType        = "application/octet-stream";
        $content_types_list = mimeTypes();
        if (array_key_exists($ext, $content_types_list)){
            $contentType = $content_types_list[$ext];
	}
        header("Content-Disposition: attachment;filename=\"" . basename($_REQUEST['fn']) . "\"");
        header('Content-Type: ' . $contentType);
        header("Content-Length: " . $fileData['size']);
	getGSFile($GLOBALS['cassandra'], $_REQUEST['fn']);
	exit;
        break;

    case 'downloadtgz' :
        $dirTmp = $GLOBALS['tmpDir']."/".$_SESSION['BNSId'];
        $newName= $_REQUEST['fn'].".tar.gz";
	$tmpZip = $GLOBALS['tmpDir']."/".basename($newName); 

        exec("rm -rf $dirTmp");
	if (!file_exists($dirTmp))
        	mkdir($dirTmp);

	$r = saveGSDirBNS($_REQUEST['fn'],$dirTmp);
        if ($r==0 || !is_dir($dirTmp)){
	    if (!isset($_SESSION['errorData']) )
		$_SESSION['errorData']['error'][]= "Error extracting directory ".$_REQUEST['fn']." . Cannot write temporal directory $dirTmp";
	    break;
	}
//        while ($f = $filec->hasNext()) {
//	    print $f['_id']." ";
//            saveGSFile($GLOBALS['cassandra'],$f['_id'],$dirTmp);
//        }

        $cmd = "/bin/tar -czf $tmpZip -C $dirTmp .  2>&1";
        exec($cmd,$output);
        if ( !is_file($tmpZip) ){
            $errStr= "Uncompressed file not created. ";
            if ($output)
                $errStr.= implode(" ", $output)."</br> <a href=\"javascript:history.go(-1)\">[ OK ]</a>";
            break;
        }

        header('Content-type: application/x-gzip');
        header('Content-Disposition: attachment; filename="' .basename($newName) . '"');
        print passthru("/bin/cat \"$tmpZip\"");
        unlink("$tmpZip");
        exec ("rm -rf $dirTmp");
        break;

    case 'openPlainFileEdit':
//        if (isset($_REQUEST['text'])) {
//            file_put_contents($rfn, $_REQUEST['text']);
//            break;
//        }

//        $text = file_get_contents($rfn);
        ?>
        <!--<form action="" method="post">
            <textarea style="border:2px solid #92b854; background-color: #fefbfa;"  cols=150 rows=37 name="text"><?php echo htmlspecialchars($text) ?></textarea>
            </br>
            <input type="submit" value="Save edited file"/>
            <input type="reset" />
        </form>--!>
        <?php
        exit;
        break;


    case 'openPlainFile' :
        $fileInfo = pathinfo($_REQUEST['fn']);
        $contentType = "text/plain";
        $fileExtension = $fileInfo['extension'];
        $content_types_list = mimeTypes();
        if (array_key_exists($fileExtension, $content_types_list))
            $contentType = $content_types_list[$fileExtension];

        header('Content-Type: ' . $contentType);
//        header("Accept-Ranges: bytes");
//        header("Pragma: public");
//        header("Expires: -1");
//        header("Cache-Control: no-cache");
//        header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
        header("Content-Length: " . $fileData['size']);
        getGSFile($GLOBALS['cassandra'], $_REQUEST['fn']);
        exit;
        break;

    case 'uploadFile':
	if (! $_SESSION['User']['Uploader'] == "on"){
		$errStr = "Error: User". $_SESSION['User']['id']." is not allowed to upload files. Register a user with the right configuration";
		break;
	}
	ini_set('upload_max_filesize', '900M');
	ini_set('post_max_size', '900M');
	ini_set('max_input_time', '2000');
	ini_set('max_execution_time', '2000');
	$_SESSION['usedDisk'] = getUserSpace();

        if (empty($_FILES['fn']['name'][0])) {
            $errStr = "ERROR: Recieving blank. </br> <a href=\"javascript:history.go(-1)\">[ OK ]</a>";
            break;
	}
        for ($i = 0; $i < count($_FILES['fn'][tmp_name]); ++$i) {
           if (!$_FILES['fn']['error'][$i]) {
		if ($_FILES['fn']['size'][$i]>return_bytes(ini_get('post_max_size')) || $_FILES['fn']['size'][$i]>return_bytes(ini_get('upload_max_filesize')) ){
            		$errStr = "ERROR: File size (".$_FILES['fn']['size'][$i].") larger than POST_MAX_SIZE (".ini_get('post_max_size').") or UPLOAD_MAX_FILESIZE (".ini_get('upload_max_filesize').") </br> <a href=\"javascript:history.go(-1)\">[ OK ]</a>"  ;
			break;
		}
		if ($_FILES['fn']['size'][$i] > ($GLOBALS['disklimit']-$_SESSION['usedDisk'])){
            		$errStr = "ERROR: Cannot upload file. Not enough space left in the workspace. Contact to [AUTHOR]@mmb.pcb.ub.es. </br> <a href=\"javascript:history.go(-1)\">[ OK ]</a>";
			break;
		}

	        if ( $GLOBALS['cassandraIds']->find(array('_id' => $_FILES['fn']['name'][$i]))->count() > 0 ){
			foreach (range(1, 99) as $N) {
			    $newName= $_FILES['fn']['name'][$i]."_".$N;
			    if ($GLOBALS['cassandraIds']->find(array('_id' => $newName))->count() == 0){
				$_FILES['fn']['name'][$i] = $newName;
				break;
			    }
			}
		}
                move_uploaded_file($_FILES['fn']['tmp_name'][$i],$GLOBALS['tmpDir']."/".$_FILES['fn']['name'][$i]);
            } else {
                $code= $_FILES['fn']['error'][$i];
		$errMsg = array( 
	 		0=>"[UPLOAD_ERR_OK]:  There is no error, the file uploaded with success", 
		        1=>"[UPLOAD_ERR_INI_SIZE]: The uploaded file exceeds the upload_max_filesize directive in php.ini", 
		        2=>"[UPLOAD_ERR_FORM_SIZE]: The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form", 
		        3=>"[UPLOAD_ERR_PARTIAL]: The uploaded file was only partially uploaded", 
		        4=>"[UPLOAD_ERR_NO_FILE]: No file was uploaded", 
		        6=>"[UPLOAD_ERR_NO_TMP_DIR]: Missing a temporary folder", 
		        7=>"[UPLOAD_ERR_CANT_WRITE]: Failed to write file to disk",
		        8=>"[UPLOAD_ERR_EXTENSION]: File upload stopped by extension"
		);
		if(isset($errMsg[$code])){
			$errStr = "ERROR [code $code] ".$errMsg[$code];
		}else{
			$errStr = "Unknown upload error";
		}
		$errStr.="</br> <a href=\"javascript:history.go(-1)\">[ OK ]</a>";
            }
	    $insertData=array(
			'_id'   => $_SESSION['curDir']."/".$_FILES['fn']['name'][$i],
			'owner' => $_SESSION['BNSId'],
			'size'  => filesize($GLOBALS['tmpDir']."/".$_FILES['fn']['name'][$i]),
			'mtime' => new MongoDate(filemtime($GLOBALS['tmpDir']."/".$_FILES['fn']['name'][$i])),
			'userUpload'=> 1
			);	    
	    uploadGSFileBNS($_SESSION['curDir']."/".$_FILES['fn']['name'][$i], $GLOBALS['tmpDir']."/".$_FILES['fn']['name'][$i], $insertData);

	    //uploadGSFile($GLOBALS['cassandra'], $_FILES['fn']['name'][$i], $GLOBALS['tmpDir']);
	    //$GLOBALS['cassandraIds']->update (
            //   array('_id' => $_FILES['fn']['name'][$i]),
            //   array(
            //        '_id'   => $_FILES['fn']['name'][$i],
            //        'owner' => $_SESSION['BNSId'],
            //        'size'  => filesize($GLOBALS['tmpDir']."/".$_FILES['fn']['name'][$i]),
	    //	    'mtime' => new MongoDate(filemtime($GLOBALS['tmpDir']."/".$_FILES['fn']['name'][$i])),
	    //	    'userUpload'=> 1
	    //   ),array('upsert'=> 1)
            //);
	    //unlink($GLOBALS['tmpDir']."/".$_FILES['fn']['name'][$i]);
        }
        break;

    case 'delete':
        $errStr = "Are you sure you want to remove '" . basename($_REQUEST['fn']) . "' <a href=\"BNSdatamanager/workspace.php?op=deleteSure&fn=" . $_REQUEST['fn'] . "\">[ Yes ]</a> <a href=\"BNSdatamanager/workspace.php\">[ Cancel ]</a>";
        break;

    case 'deleteSure':
	deleteGSFileBNS($_REQUEST['fn']);
	break;

    case 'deleteDirOk':
	deleteGSDirBNS($_REQUEST['fn']);
        break;

    case 'deleteDir':
	if (count($fileData['files']) == 0)
	    deleteGSFileBNS($_REQUEST['fn']);
	else
            $errStr = "Could not remove. Not empty Folder. <a href=\"BNSdatamanager/workspace.php?op=deleteDirOk&fn=" . $_REQUEST['fn'] . "\">[ Delete anyway ]</a>";
        break;

    case 'close':
        session_destroy();
        redirect('index.php');
        break;

    case 'unzip':
    case 'untar':
        $tmpDir = $GLOBALS["tmpDir"];
        $tmpZip = "$tmpDir/".basename($_REQUEST['fn']);

        saveGSFile($GLOBALS['cassandra'], $_REQUEST['fn'], $tmpZip);

	$ext     = pathinfo($_REQUEST['fn'], PATHINFO_EXTENSION);
        $newName = str_replace(".$ext","",$_REQUEST['fn']);
        $tmpFn   = "$tmpDir/".basename($newName);
        switch ($ext) {
            case 'tar':
		#touch option force tar to update uncompressed files atime - required by the expiration time
                $cmd = "tar --touch -xf \"" . $tmpZip . "\" 2>&1";
                break;
            case 'zip':
                $cmd = "unzip -o \"" . $tmpZip . "\" 2>&1";
                break;
            case 'gz':
            case 'tgz':
                if (pathinfo(pathinfo($rfn, PATHINFO_FILENAME), PATHINFO_EXTENSION) == 'tar')
                    $cmd = "tar --touch -xzf \"" . $tmpZip . "\" 2>&1";
                else
                    $cmd = "gunzip -f \"" . $tmpZip . "\" 2>&1";
                break;
        }
        exec($cmd, $output);

	if (is_file($tmpFn)){
	    $info = array(
                    '_id'   => $newName,
                    'owner' => $_SESSION['BNSId'],
                    'size'  => filesize($tmpFn),
                    'mtime' => $fileData['mtime'],
                    'description' => $fileData['description']
            );
	    $r = uploadGSFileBNS($newName,$tmpFn,$info);
	    if ($r == 0)
		break
	    unlink($newFn);
	    deleteGSFileBNS($_REQUEST['fn']);

	}elseif(is_dir($tmpFn)){
		$errStr=" Error inflating $newName. Directories cannot be uncompressed </br> <a href=\"javascript:history.go(-1)\">[ OK ]</a>";

	}else{
	    $errStr= "Uncompressed file not created. ";
	    if ($output)
		$errStr.= implode(" ", $output)."</br> <a href=\"javascript:history.go(-1)\">[ OK ]</a>";
	}
        break;

    case 'zip':
        $tmpDir = $GLOBALS["tmpDir"];
        $tmpFn = "$tmpDir/".basename($_REQUEST['fn']);
        $tmpZip= "$tmpDir/".basename($_REQUEST['fn']).".gz";

        saveGSFile($GLOBALS['cassandra'], $_REQUEST['fn'], $tmpFn);
        $cmd = "gzip -f \"$tmpFn\" 2>&1";
        exec($cmd, $output);

	if (file_exists($tmpZip)){
	    $info = array('_id' => $_REQUEST['fn'].".gz",
                    'owner' => $_SESSION['BNSId'],
                    'size' => filesize($tmpZip),
                    'mtime' => $fileData['mtime'],
                    'description' => $fileData['description']
	    );
	    $r = uploadGSFileBNS($_REQUEST['fn'].".gz",$tmpZip,$info);
	    if ($r == 0)
		break;
	    unlink($tmpFn.".gz");
	    deleteGSFileBNS($_REQUEST['fn']);
	}else{
	    $errStr = "Compressed ZIP file not created.";
	    if ($output)
		$errStr .= implode(" ", $output)."</br> <a href=\"javascript:history.go(-1)\">[ OK ]</a>";
	}
	unset($_REQUEST['op']);
        break;

//    case 'tar':
//        $cmd = "tar --touch  -cf \"" . $_REQUEST['fn'] . ".tar\" \"" . $_REQUEST['fn'] . "\" 2>&1";
//        chdir($_SESSION['curDir']);
//        exec($cmd, $output);
//        $errStr = implode(" ", $output);
//        chdir($_SESSION['User']->dataDir);
        break;
}


#### print web

print headerTP('');

$_SESSION['usedDisk'] = getUserSpace();
$usedDiskPerc = sprintf('%.2f', ($_SESSION['usedDisk'] / $GLOBALS['disklimit']) * 100);
if ($_SESSION['usedDisk'] < $GLOBALS['disklimit']) {
    $_SESSION['accionsAllowed'] = "enabled";
} else {
    $_SESSION['accionsAllowed'] = "disabled";
    $usedDiskPerc=100;
}
?>
<div style="clear:both"></div>
<br/>
<br/>


<div class='progress'>
    <div class='prgbar' style="width:<?php echo $usedDiskPerc; ?>%"></div>
    <div class='prginfo'>
         <span style='float: left;'>Disk use</span>
         <span style='float: right;'><?php print formatSize($_SESSION['usedDisk']) . " of " . formatSize($GLOBALS['disklimit']) . " ($usedDiskPerc%)"; ?></span>
     </div>
</div>

<?php if ($_SESSION['User']['Uploader'] == "on"){ ?>
	    <div style="float:right;">
		<b><a target="_blank" class="linkButton" href="BNSdatamanager/deposition.php" >  Initiate Deposition </a></b>
	    </div>
<?php }?>

<div style="clear:both"></div>
<br/>
<br/>

<?php
if ($errStr) {
    print "<div class=\"notify\"><img src=\"BNSdatamanager/images/delete.png\" align=\"left\" style=\"height:20px;\">$errStr</div>";
    $errStr = '';
}
if (isset($_SESSION['errorData'])) {
    print printErrorData();
    unset($_SESSION['errorData']);
}
?>

<form name="gesdir" action="BNSdatamanager/workspace.php" method="post" enctype="multipart/form-data"> 

    <span style="font-weight:bold">Current data session:</b> &nbsp;&nbsp;&nbsp;&nbsp;
        <?php print navigation(); ?>
    </span>

    <br/> <hr>
    <?php
    print showFolder( array('owner'=> $_SESSION['BNSId']) );
    ?>


     <?php if ($_SESSION['User']['Uploader'] == "on"){ ?>
          <a onclick="javascript:toggleVis('folderName');document.getElementById('fileUpload').style.visibility = 'hidden'"
           class="<?php print $_SESSION['accionsAllowed']; ?>"> [ Add folder ] </a>
          <a onclick="javascript:toggleVis('fileUpload');ob=document.getElementById('folderName').style; ob.visibility = 'hidden'; ob.display = 'none';"
            class="<?php print $_SESSION['accionsAllowed']; ?>"> [ Upload File] </a>
     <?php }   ?>


       <div id="folderName" style="visibility:hidden; display:none;">
            <input name="fn" size="40"/>
            <input type="submit" onclick="document.gesdir.op.value = 'newFolder'; document.gesdir.submit()" value="Go"/>
      </div>
      <div id="fileUpload" style="visibility:hidden; display:none">
    	    <input type="file" name="fn[]" id='fn' size="80" multiple/>
	    <input onclick="validateUpload(document.gesdir,<?php print $GLOBALS['disklimit'] - $_SESSION['usedDisk']; ?>)" type="submit" value="Upload">
      </div>




</form>


<?php
if (isset($_SESSION['errorData'])) {
    print printErrorData();
    unset($_SESSION['errorData']);
}
print footerTP();
