<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function showFolder() {
    ?>
    <form name="gesdir" action="BNSdatamanager/workspace.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="op" value="">
        <?php
        if (!isset($_SESSION['curDir'])) {
            $_SESSION['errorData']['internal'][] = "No current directory set. Reset [ <a href=\"BNSdatamanager/index.php\">login</a> ], please";
            return FALSE;
        }

        print "<span style=\"font-weight:bold\">Current Data Session:</b> &nbsp;&nbsp;&nbsp;&nbsp;";
        print navigation();
        print "</span>";

	$base = "BNSdatamanager/workspace.php?BNSId=".$_SESSION['BNSId'];
        $order = isset($_GET['order']) ? $_GET['order'] : 'asc';
	
	// Print table header
        ?>

        <br/> <hr>
        <table style="width:100%; border:1px solid;border-collapse:collapse">
        <colgroup span="8">
                <col style="width:35px;"></col>
                <col style="width:145px;"></col>
                <col style="width:70px;"></col>
                <col></col>
                <col style="width:100px;"></col>
                <col style="width:60px;"></col>
                <col style="width:50px;"></col>
                <col style="width:70px;"></col>
        </colgroup>

        <thead>
                <tr>
                    <th><a style="float:right" href="<?php echo $base;?>"><img src="BNSdatamanager/images/reload.png" align="right"/></a></th>
                    <th><a style="color:#fff;" href='<?php echo $base;?>&sort=date&order=<?php echo (($order == 'desc') ? 'asc' : 'desc');?>'><img src="BNSdatamanager/images/sort.png" align="left"/>Date</a></th>
                    <th><a style="color:#fff;" href='<?php echo $base;?>&sort=size&order=<?php echo (($order == 'desc') ? 'asc' : 'desc');?>'><img src="BNSdatamanager/images/sort.png" align="left"/>Size</a></th>
                    <th><a style="color:#fff;" href='<?php echo $base;?>&sort=name&order=<?php echo (($order == 'desc') ? 'asc' : 'desc');?>'><img src="BNSdatamanager/images/sort.png" align="left"/>Name</a></th>
                    <th colspan="2">&nbsp;</th>
                    <th colspan="2" style="text-align:right;">Expiration
                    </th>
                </tr>
        </thead>
        <?php

	// Collect files

        $files = array();
        $filesC = $GLOBALS['cassandraIds']->find(array('owner' => $_SESSION['BNSId']));

        while ($fileData = $filesC->getNext()) {
                $key = $fileData['_id'];
                if ($_GET['sort'] == "date")
                    $key = $fileData['mtime']->sec;
                if ($_GET['sort'] == "size")
                    $key = $fileData['size'];
                $key = check_key_repeats($key, $files);
                if (!isset($key))
                    break;
		$fileData['mtime']=$fileData['mtime']->sec;
                $files[$key] = $fileData;
        }
        if ($_GET['order'] == 'asc') {
            ksort($files);
        } else {
            krsort($files);
        }

	$filesPending= getPendingFiles($_SESSION['BNSId'],$files);
	$filesAll=array_merge($files,$filesPending);


	// Print first row with folder info

        $sz = 'BKMGTP';
	$factor = floor((strlen($_SESSION['usedDisk']) - 1) / 3);
	$size = sprintf("%.2f", $_SESSION['usedDisk'] / pow(1024, $factor));
        ?>
	<tr style="border-bottom: 1px solid #000; border-bottom-style: dotted;">
        	<td width="20"> <img src="BNSdatamanager/images/folder.png" height="15px" width="15px"> </td>
            	<td>&nbsp;</td>
            	<td><?php print $size . " " . @$sz[$factor]; ?></td>
            	<td style="font-weight: bold;"><?php print $_SESSION['BNSId'];?></td>
            	<td></td>
            	<td><a href="BNSdatamanager/workspace.php?op=downloadtgz&fn=<?php print urlencode($_SESSION['BNSId']); ?>"><img src="BNSdatamanager/images/downloadtgz.png" style="height:22px"  title="download tgz"></a> </td>
            	<td colspan=2>&nbsp;</td>
	</tr>

        <?php
	// Print somthing in case of no data

        if (count($filesAll) == 0){
                ?>
                <tr style="height:70px;">
                    <td colspan="8" style="text-align:center;">No data yet</td>
                </tr>
                <?php
        }

	// Print each file info
        $n = 0;
	$autoRefresh=0;
        foreach ($filesAll as $f){
            $fn = $f['_id'];
	    $description = str_replace(",","<br/>",$f['description']);
            $n++;
//	    print "<tr onmouseout=\"document.getElementById('description$n').style.visibility='hidden'\" >";
//          print "<tr>";

            $ext = pathinfo($fn, PATHINFO_EXTENSION);
            $content_types_list = mimeTypes();
            $tt = $content_types_list[$ext];
            $openFunction = ($tt == "text/plain" ? "openPlainFile" : "downloadFile");

            $lb = array();
            $icon = "BNSdatamanager/images/document.png";
	    $isTraj=0;
            switch (strtolower($ext)) {
                case 'tar':
                    $icon = "BNSdatamanager/images/compressed.png";
                    $lb[] = "untar";
                    $lb[] = "zip";
                    break;
                case 'gz':
                    $icon = "BNSdatamanager/images/compressed.png";
                    $lb[] = "unzip";
                    break;
                case 'tgz':
                    $icon = "BNSdatamanager/images/compressed.png";
                    $lb[] = "unzip";
                    break;
                case 'fa':
                case 'fasta':
                case 'fna':
                case 'fas':
                    $lb[] = "zip";
                    break;
                case 'fastq':
                case 'fq':
                    $lb[] = "zip";
                    break;
                case 'error':
                case 'err':
                    $icon = "BNSdatamanager/images/errFile.png";
                    $lb[] = "zip";
                    break;
                case 'out':
                    $icon = "BNSdatamanager/images/outFile.png";
                    $lb[] = "zip";
                    break;
                case 'log':
                    $icon = "BNSdatamanager/images/logFile.png";
                    $lb[] = "zip";
                    break;
		case 'pdb':
                    $icon = "BNSdatamanager/images/pdbFile.png";
                    break;
                case 'crd':
                case 'dcd':
                case 'cdf':
                case 'xtc':
                case 'trr':
                case 'gro':
			$isTraj=1;
                default :
                    $lb[] = "zip";
            }
            $factor = floor((strlen($f['size']) - 1) / 3);
            $size = sprintf("%.2f", $f['size'] / pow(1024, $factor));
	    $time = strftime('%d %b %G - %H:%M', $f['mtime']);
            $days2expire = intval($GLOBALS['days2expire'] - ((time() - $f['mtime']) / (24 * 3600))); # crontab set to erase files in X days


	    // print real files
	    if ( isset($f['state']) && $f['state']== "RUNNING" ){
		$autoRefresh=1;
                ?>
                <td></td>
                <td style="font-style: oblique;">
		    <?php print $time; ?>
		</td>
                <td style="letter-spacing:-0.2px;font-weight: bold;color:#1982d1;text-align:right;">
		    <?php print $f['state']; ?>
		</td>
		<td class="not-active">
		    <a href="BNSdatamanager/workspace.php?op=<?php print $openFunction;?>&fn=<?php print urlencode($fn); ?>" disabled="disabled"><?php print $fn; ?></a>
                    <a href="javascript:toggleVis('description<?php print $n ?>');"><img src="BNSdatamanager/images/more.png" style="height:12px" title="description"></a>
		</td>
                <td class="not-active">
                    <a href="BNSdatamanager/workspace.php?op=downloadFile&fn=<?php print urlencode($fn); ?>">
		    <img src="BNSdatamanager/images/download.png" style="height:22px" title="download">
		    </a>
                </td>
                <td class="not-active">
                    <a href="BNSdatamanager/workspace.php?op=zip&fn=<?php print urlencode($fn);?>" disabled="disabled">
		    <img src="BNSdatamanager/images/zip.png" style="height:22px" title="zip"/></a>
                </td>
                <td class="not-active">
		    <a href="BNSdatamanager/workspace.php?op=delete&fn=<?php print urlencode($fn) ?>">
		    <img src="BNSdatamanager/images/delete.png" style="height:22px" title="delete"></a>
		</td>
                <td></td>
                </tr>
	        <?php
		
	    }else{
                ?>
                <td width="20">
		    <img src="<?php print $icon; ?>" height="18px" width="18px"/>
		</td>
                <td>
		    <?php print $time; ?>
		</td>
                <td style="text-align:right;">
		    <?php print $size . " " . @$sz[$factor]; ?>
		</td>
		<td>
		    <a href="BNSdatamanager/workspace.php?op=<?php print $openFunction;?>&fn=<?php print urlencode($fn); ?>" target="_blank"><?php print $fn; ?></a>
		   <?php if ($description){?>
	             <a href="javascript:toggleVis('description<?php print $n ?>');"><img src="BNSdatamanager/images/more.png" style="height:12px" title="description"></a>
		<?php }?>
		</td>
                <td>
                    <a href="BNSdatamanager/workspace.php?op=downloadFile&fn=<?php print urlencode($fn); ?>">
		    <img src="BNSdatamanager/images/download.png" style="height:22px" title="download">
		    </a>
		    <?php if ($isTraj == 1){ ?>
                        <a href="fromBNStoNAFlex.php?traj=<?php print $fn;?>" target="_blank"><img src="images/NA_Flex_Logo.png" style="height:26px" title="analyse NA flexibility"></a>
		    <?php }?>
                </td>
                <td>
                    <?php foreach ($lb as $l) {
                        $accionsAllowed = (($l == "unzip" || $l == "untar") ? $_SESSION[accionsAllowed] : "enabled");
                        print "<a href=\"BNSdatamanager/workspace.php?op=$l&fn=" . urlencode($fn) . "\" class=\"$accionsAllowed\">";
			print "<img src=\"BNSdatamanager/images/$l.png\" style=\"height:22px\"  title=\"$l\"></a>";
                    }
                    ?>
                </td>
                <td>
		    <a href="BNSdatamanager/workspace.php?op=delete&fn=<?php print urlencode($fn) ?>">
		    <img src="BNSdatamanager/images/delete.png" style="height:22px" title="delete"></a>
		</td>
                <td>
		    <?php print $days2expire; ?> days
                </td>
                </tr>
	        <?php
	    }?>
	   
            <tr id="description<?php print $n;?>" style="visibility:hidden; display:none;border: 1px solid #ddd; background:#eee;">
               <td colspan="9" style="text-align:center;padding:14px; 4px;"><?php print $description;?></td>
            </tr>

                <?php
        }
	if ( $autoRefresh==1){
		?>
		<tr>
		    <td colspan="9" style="background: #555;">
		    <a href="<?php echo $base;?>"><img style="display: block;margin-left: auto;margin-right: auto;" src="BNSdatamanager/images/reload.png"/></a>
		    </td>
		</tr>
		<?php
	}
        ?>
        </table>

        <p><hr></p>
        <div>
<!--            <a onclick="javascript:toggleVis('folderName');
                    document.getElementById('fileUpload').style.visibility = 'hidden'" class="<?php print $_SESSION['accionsAllowed']; ?>">
                [ Add folder ] </a>
            <a onclick="javascript:toggleVis('fileUpload');
                    ob = document.getElementById('folderName').style;
                    ob.visibility = 'hidden';
                    ob.display = 'none';" class="<?php print $_SESSION['accionsAllowed']; ?>">
                [ Upload File] </a>
            <div id="folderName" style="visibility:hidden; display:none;">
                <input name="fn" size="40"/>
                <input type="submit" onclick="document.gesdir.op.value = 'newFolder';
                        document.gesdir.submit()" value="Go"/>
            </div>
            <div id="fileUpload" style="visibility:hidden; display:none">
                <input type="file" name="fn[]" id='fn' size="40" multiple/>
                <input type="submit" onclick="validateUpload(this.form,<?php print $GLOBALS['disklimit'] - $_SESSION['usedDisk']; ?>)" value="Go"/>
            </div>-->
        </div>
    </form>
    <?php
    //closedir($dh);
}

function getPendingFiles($sessionId,$files){
	print "___FILES___";
	print_r($files);
	print "___SGE___";
	print_r($_SESSION['SGE']);
	$filesPending=Array();
	foreach ($_SESSION['SGE'] as $pid => $j){
		$runJobs = getRunningJobs($j['fileId']);
		if (isset($runJobs[$pid])){
			//set as running job
			$f['_id']=$j['name'];
			$f['mtime']=strtotime($runJobs[$pid]['start']);
			$f['size']="";
			$f['description']=$j['desc'];
			$f['state']="RUNNING";
			array_push($filesPending,$f);
		}else{
			$ids=Array();
			foreach ($files as $k => $v){
				array_push($ids,$v['_id']);
			}
			$filesGenerated=preg_grep("/$j[fileId]/", $ids);
			// set job finished
			if (count($filesGenerated)>0){
				unset($_SESSION['SGE'][$pid]); // no permission

			// jobs nor finished nor running: in error OR deleted OR SESSION[sge] not updated
			}else{
				$tmpDir=$GLOBALS['baseDirBigASim']."/".$GLOBALS['tmpDirBigNASim']."/".$_SESSION['BNSId'];
				$basename="CURL.".$j['fileId'];
				$errF= "$tmpDir/".$basename.".err";
				$outF= "$tmpDir/".$basename.".out";
				if (is_file($errF)){
				    if (preg_grep("/err/i",file($errF)) || preg_grep("/error/i",file($outF)) ){
					// set job in error
					foreach (Array(".err",".out") as $ext){
						$tmpF= "$tmpDir/".$basename.$ext;
						uploadGSFile($GLOBALS['cassandra'], $basename.$ext,$tmpDir);
						$GLOBALS['cassandraIds']->update(
				                   array('_id' => $basename.$ext),
				                   array(
				                    '_id' => $basename.$ext,
				                    'owner' => $_SESSION['BNSId'],
					            'size' => filesize("$tmpF"),
				                    'mtime' => new MongoDate(filemtime($tmpF)),
				                    'description' => "Trajectory <strong>".$j['name']." </strong> not generated.<br/>".$j['desc']
				                ),array('upsert'=> 1)
				                );
					        //unlink($tmpF);
						unset($_SESSION['SGE'][$pid]); // no permission
	
						$f['_id']=$basename.$ext;
						$f['mtime']=filemtime($tmpF);
						$f['size']=filesize("$tmpF");
						$f['description']=$j['desc'];
						array_push($filesPending,$f);
					}
				    }else{
					print "Warning: Job ".$j['name']." do not success. Any trajectory generated, nor errors founds at ".$errF."\n";
				    	// set job as deleted
				    }
				    
				}else{
 				    // set as funny job
				    //$f['_id']=$j['name'];
				    //$f['mtime']="";
				    //$f['size']="";
				    //$f['description']=$j['desc'];
				    //$f['state']="OLD";
				    //array_push($filesPending,$f);
				    print "Warning: Job ".$j['name']." do not success. Any trajectory generated, not job logs found\n";
				}
			  }
		 }
	}
	return $filesPending;
}

function topDir() {
    return ($_SESSION['curDir'] == $_SESSION['User']->dataDir);
}

function upDir() {
    if (!topDir())
        $_SESSION['curDir'] = dirname($_SESSION['curDir']);
}

function downDir($fn) {
    if (file_exists($_SESSION['curDir'] . "/$fn"))
        if (filetype($_SESSION['curDir'] . "/$fn") == "dir")
            $_SESSION['curDir'].="/$fn";
}

function getUserSpace($fn = '') {
//    if (!$fn)
//        $fn = $_SESSION['User']->dataDir;
//$data = explode("\t", exec("du -sb $fn"));
//return $data[0];
    return calcGSUsedSpace($_SESSION['BNSId']);
}

function navigation() {
    $rootDir = $_SESSION['curDirFTP'];
    $cdir = $_SESSION['curDir'];
    $d = dirname($cdir);
    $dirs = array();
    if (!topDir()) {
        while ($d and ( $d != $_SESSION['User']->dataDir)) {
            $dirs[] = "<a href=\"BNSdatamanager/workspace.php?op=gotoDir&fn=$d\">" . pathinfo($d, PATHINFO_FILENAME) . "</a>";
            $d = dirname($d);
        }
        $dirs[] = "<a href=\"BNSdatamanager/workspace.php?op=gotoDir&fn=$d\">$rootDir</a>";
    }
//    return join(' > ', array_reverse($dirs)) . "> " . pathinfo($cdir, PATHINFO_FILENAME);
    return $cdir;
}

function formatSize($bytes) {
    $types = array('B', 'KB', 'MB', 'GB', 'TB');
    for ($i = 0; $bytes >= 1024 && $i < ( count($types) - 1 ); $bytes /= 1024, $i++)
        ;
    return( round($bytes, 2) . " " . $types[$i] );
}

function mimeTypes() {

    $mime_types = array(
        "log" => "text/plain",
        "txt" => "text/plain",
        "err" => "text/plain",
        "out" => "text/plain",
        "csv" => "text/plain",
        "pdb" => "chemical/x-pdb",
        "crd" => "chemical/x-pdb",
        "xyz" => "chemical/x-xyz",
        "cdf" => "application/octet-stream",
        "xtc" => "application/octet-stream",
        "trr" => "application/octet-stream",
        "gro" => "application/octet-stream",
        "dcd" => "application/octet-stream",
        "exe" => "application/octet-stream",
        "gtar" => "application/octet-stream",
        "tar" => "application/x-tar",
        "gz" => "application/application/x-gzip",
        "tgz" => "application/application/x-gzip",
        "z" => "application/octet-stream",
        "rar" => "application/octet-stream",
        "bz2" => "application/x-gzip",
        "zip" => "application/zip",
        "h" => "text/plain",
        "htm" => "text/html",
        "html" => "text/html",
        "gif" => "image/gif",
        "bmp" => "image/bmp",
        "ico" => "image/x-icon",
        "jfif" => "image/pipeg",
        "jpe" => "image/jpeg",
        "jpeg" => "image/jpeg",
        "jpg" => "image/jpeg",
        "rgb" => "image/x-rgb",
        "svg" => "image/svg+xml",
        "png" => "image/png",
        "tif" => "image/tiff",
        "tiff" => "image/tiff",
        "ps" => "application/postscript",
        "eps" => "application/postscript",
        "js" => "application/x-javascript",
        "pdf" => "application/pdf",
        "doc" => "application/msword",
        "xls" => "application/vnd.ms-excel",
        "ppt" => "application/vnd.ms-powerpoint",
        "sh" => "application/x-sh",
        "tsv" => "text/tab-separated-values");
    return $mime_types;
}

function check_key_repeats($key, $hash) {
    if (!isset($key) || !isset($hash)) {
        return NULL;
    }
    if (array_key_exists($key, $hash)) {
        $key++;
        $key = check_key_repeats($key, $hash);
        return $key;
    } else {
        return $key;
    }
}

