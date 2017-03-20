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

if (isset($_REQUEST['BNSId'])) {
    $_SESSION['BNSId'] = $_REQUEST['BNSId'];
}

if (!isset($_SESSION['BNSId'])) {
	print errorPage("Error Id not found", "Id not provided");
}else{
	// look for SGE data from BNS session var
	$sessionFile=$GLOBALS['baseDirBigASim']."/".$GLOBALS['tmpDirBigNASim']."/".$_SESSION['BNSId']."/projectData.bin";
	if (! is_file($sessionFile)){
		print errorPage("Error Id nit found", "The provided session Id is not active anymore");
		exit(0);
	}else{
	        $session_BigNASim=unserialize(file_get_contents($sessionFile));
		
		if (isset($session_BigNASim['SGE']))
        		$_SESSION[SGE]=$session_BigNASim['SGE'];
	}
}

prepUserWorkSpace();

#### process op
if (isset($_REQUEST['fn'])) {
//    $rfn = $_SESSION['curDir'] . "/" . $_REQUEST['fn'];
    $rfn = $_REQUEST['fn'];
}

$fileData = $GLOBALS['cassandraIds']->findOne(array('_id' => $_REQUEST['fn'], 'owner' => $_SESSION['BNSId']));

switch ($_REQUEST['op']) {
    case 'newFolder' :
//        mkdir($rfn, 0777);
//        chmod($rfn, 0777);
        break;

    case 'upDir':
//        upDir();
        break;

    case 'downDir':
//        downDir($_REQUEST['fn']);
        break;

    case 'gotoDir':
//        $_SESSION['curDir'] = $_REQUEST['fn'];
        break;

    case 'downloadFile' :
	$ext = pathinfo($_REQUEST['fn'], PATHINFO_EXTENSION);
        $contentType        = "application/octet-stream";
        $content_types_list = mimeTypes();
        if (array_key_exists($ext, $content_types_list)){
            $contentType = $content_types_list[$ext];
	}
        $content = getGSFile($GLOBALS['cassandra'], $_REQUEST['fn']);

        header("Content-Disposition: attachment;filename=\"" . $_REQUEST['fn'] . "\"");
        header('Content-Type: ' . $contentType);
//        header("Accept-Ranges: bytes");
//        header("Pragma: public");
//        header("Expires: -1");
//        header("Cache-Control: no-cache");
//        header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
        header("Content-Length: " . $fileData['size']);
	print $content;
	exit;
        break;
    case 'downloadtgz' :
        $dirTmp = $GLOBALS['tmpDir']."/".$_SESSION['BNSId'];
        $newName= $_SESSION['BNSId'].".tar.gz";
	$tmpFn  = $GLOBALS['tmpDir']."/$newName"; 
        exec ("rm -rf $dirTmp $newName");
	if (!file_exists($dirTmp))
        	mkdir($dirTmp);
        $filec = $GLOBALS['cassandraIds']->find(array('owner' => $_SESSION['BNSId']));
	while($filec->hasNext()){
		$f=$filec->getNext();
		saveGSFile($GLOBALS['cassandra'],$f['_id'],"$dirTmp/".$f['_id']);
	}
//        while ($f = $filec->hasNext()) {
//	    print $f['_id']." ";
//            saveGSFile($GLOBALS['cassandra'],$f['_id'],$dirTmp);
//        }
        $cmd = "/bin/tar -czf $tmpFn -C $dirTmp .  2>&1";
        exec($cmd,$output);
	$errStr = implode(" ", $output);
        header('Content-type: application/x-gzip');
        header('Content-Disposition: attachment; filename="' .$newName . '"');
        print passthru("/bin/cat \"$tmpFn\"");
        unlink("$tmpFn");
        exec ("rm -rf $dirTmp");
        break;

    case 'openPlainFileEdit' :
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

        $content = getGSFile($GLOBALS['cassandra'], $_REQUEST['fn']);

        header('Content-Type: ' . $contentType);
//        header("Accept-Ranges: bytes");
//        header("Pragma: public");
//        header("Expires: -1");
//        header("Cache-Control: no-cache");
//        header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
        header("Content-Length: " . $fileData['size']);
        print $content;
        exit;
        break;

    case 'uploadFile':
	ini_set('upload_max_filesize', '900M');
	ini_set('post_max_size', '900M');
	ini_set('max_input_time', '2000');
	ini_set('max_execution_time', '2000');
	$_SESSION['usedDisk'] = getUserSpace();

        if (empty($_FILES['fn']['name'])) {
            $errStr = "ERROR: Recieving blank. Files larger than $postMax will result in error";
            break;
        }
        for ($i = 0; $i < count($_FILES['fn'][tmp_name]); ++$i) {
           if (!$_FILES['fn']['error'][$i]) {
		if ($_FILES['fn']['size'][$i]>return_bytes(ini_get('post_max_size')) || $_FILES['fn']['size'][$i]>return_bytes(ini_get('upload_max_filesize')) ){
            		$errStr = "ERROR: File size (".$_FILES['fn']['size'][$i].") larger than POST_MAX_SIZE (".ini_get('post_max_size').") or UPLOAD_MAX_FILESIZE (".ini_get('upload_max_filesize').")"  ;
			break;
		}
		if ($_FILES['fn']['size'][$i] > ($GLOBALS['disklimit']-$_SESSION['usedDisk'])){
            		$errStr = "ERROR: Cannot upload file. Not enough space left in the workspace. Contact to [AUTHOR]@mmb.pcb.ub.es";
			break;
		}
                move_uploaded_file($_FILES['fn']['tmp_name'][$i],$GLOBALS['tmpDir']."/".$_FILES['fn']['name'][$i]);
		print "xxxxxxxxxxxxxxxxxxxxxxxxxxxx " . $GLOBALS['tmpDir']."/".$_FILES['fn']['name'][$i];
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
            }
	    print "uploadGSFile(".$GLOBALS['cassandra'].", ".$_FILES['fn']['name'][$i].", ".$GLOBALS['tmpDir'].")";
	    uploadGSFile($GLOBALS['cassandra'], $_FILES['fn']['name'][$i], $GLOBALS['tmpDir']);
        }
	print "caca";
        break;

    case 'delete':
        $errStr = "Are you sure you want to remove '" . $_REQUEST['fn'] . "' <a href=\"BNSdatamanager/workspace.php?op=deleteSure&fn=" . $_REQUEST['fn'] . "\">[ Yes ]</a> <a href=\"BNSdatamanager/workspace.php\">[ Cancel ]</a>";
        break;
    case 'deleteSure':
        $GLOBALS['cassandra']->remove($_REQUEST['fn']);
        $GLOBALS['cassandraIds']->remove(array('_id'=> $_REQUEST['fn']));
        break;

//    case 'deleteDirOk':
//        exec("rm -r \"$rfn\" 2>&1", $output);
//        $errStr = implode(" ", $output);
//        break;

//    case 'deleteDir':
//        if (count(glob("$rfn/*")) == 0) {
//            rmdir($rfn);
//        } else {
//            $errStr = "Could not remove. Not empty Folder. <a href=\"BNSdatamanager/workspace.php?op=deleteDirOk&fn=" . $_REQUEST['fn'] . "\">[ Delete anyway ]</a>";
//        }
//        break;

    case 'close':
        session_destroy();
        redirect('index.php');
        break;

    case 'unzip':
    case 'untar':
        $tmpDir = $GLOBALS["tmpDir"];
        $tmpFn = "$tmpDir/".$_REQUEST['fn'];
        saveGSFile($GLOBALS['cassandra'], $_REQUEST['fn'], $tmpFn);

	$ext     = pathinfo($_REQUEST['fn'], PATHINFO_EXTENSION);
        $newName = str_replace(".$ext","",$_REQUEST['fn']);
        $newFn   = "$tmpDir/$newName";
        switch ($ext) {
            case 'tar':
		#touch option force tar to update uncompressed files atime - required by the expiration time
                $cmd = "tar --touch -xf \"" . $tmpFn . "\" 2>&1";
                break;
            case 'zip':
            case 'gz':
            case 'tgz':
                if (pathinfo(pathinfo($rfn, PATHINFO_FILENAME), PATHINFO_EXTENSION) == 'tar')
                    $cmd = "tar --touch -xzf \"" . $tmpFn . "\" 2>&1";
                else
                    $cmd = "gunzip -f \"" . $tmpFn . "\" 2>&1";
                break;
        }
        exec($cmd, $output);
        $errStr = implode(" ", $output);
        uploadGSFile($GLOBALS['cassandra'], $newName, $tmpDir);
        $GLOBALS['cassandraIds']->update (
                   array('_id' => $newName),
	           array(
                    '_id' => $newName,
                    'owner' => $_SESSION['BNSId'],
                    'size' => filesize($newFn),
                    'mtime' => $fileData['mtime'],
                    'description' => $fileData['description']
                ),array('upsert'=> 1)
                );
	unlink($newFn);
        $GLOBALS['cassandra']->remove(array('filename'=>$_REQUEST['fn']));
        $GLOBALS['cassandraIds']->remove(array('_id'=>$_REQUEST['fn']));
        break;

    case 'zip':
        $tmpDir = $GLOBALS["tmpDir"];
        $tmpFn = "$tmpDir/".$_REQUEST['fn'];
        saveGSFile($GLOBALS['cassandra'], $_REQUEST['fn'], $tmpFn);
        $cmd = "gzip -f \"$tmpFn\" 2>&1";
        exec($cmd, $output);
        $errStr = implode(" ", $output);
        uploadGSFile($GLOBALS['cassandra'], $_REQUEST['fn'].".gz",$tmpDir);
        $GLOBALS['cassandraIds']->update (
                   array('_id' => $_REQUEST['fn'].".gz"),
	           array(
                    '_id' => $_REQUEST['fn'].".gz",
                    'owner' => $_SESSION['BNSId'],
                    'size' => filesize($_REQUEST['fn'].".gz"),
//                    'mtime' => new MongoDate(filemtime($_REQUEST['fn'].".gz")),
                    'mtime' => $fileData['mtime'],
                    'description' => $fileData['description']
                ),array('upsert'=> 1)
                );
	unlink($tmpFn.".gz");
        $GLOBALS['cassandra']->remove(array('filename'=>$_REQUEST['fn']));
        $GLOBALS['cassandraIds']->remove(array('_id'=>$_REQUEST['fn']));
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

//print userMenu();

$_SESSION['usedDisk'] = getUserSpace();
$_SESSION['usedDisk']=$_SESSION['usedDisk']*100;
$usedDiskPerc = sprintf('%.2f', ($_SESSION['usedDisk'] / $GLOBALS['disklimit']) * 100);
if ($_SESSION['usedDisk'] < $GLOBALS['disklimit']) {
    $_SESSION['accionsAllowed'] = "enabled";
} else {
    $_SESSION['accionsAllowed'] = "disabled";
    $usedDiskPerc=100;
}
    ?>
    <div class='progress'>
        <div class='prgbar' style="width:<?php echo $usedDiskPerc; ?>%"></div>
        <div class='prginfo'>
            <span style='float: left;'>Disk use</span>
            <span style='float: right;'><?php print formatSize($_SESSION['usedDisk']) . " of " . formatSize($GLOBALS['disklimit']) . " ($usedDiskPerc%)"; ?></span>
        </div>
    </div>
<!--
<div class="clone-url">
    <span class="clone-url-title" title="Path to copy/paste into PMES when FTP storage is ftp://datamanager.bsc.es/">&nbsp;PMES&nbsp;</span>
    <div style="float:right;">
        <div style="display:inline; float:left; font-family:Courier New; font-size:14px;">ftp://datamanager.bsc.es/pmesData/</div>
        <input onClick="this.select();" class="clone-url-link" type="text" value="getFtpLink"  size="40" readonly/>
    </div>
</div>
-->

<div style="clear:both"></div>
<br/>
<br/>
<?php
if ($errStr) {
    print "<div class=\"notify\"><img src=\"BNSdatamanager/images/delete.png\" align=\"left\" style=\"height:20px;\">$errStr</div>";
    $errStr = '';
}

print showFolder();

if (isset($_SESSION['errorData'])) {
    print printErrorData();
}
print footerTP();
