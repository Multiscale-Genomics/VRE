<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function showFolder() {
    ?>
        <input type="hidden" name="op" value="">
        <?php
        if (!isset($_SESSION['curDir'])) {
            $_SESSION['errorData']['error'][] = "No current directory set. Reset login please";
            return FALSE;
        }
	$dirData = $GLOBALS['cassandraIds']->findOne(
		array(
		    'owner' => $_SESSION['BNSId'],
		     '_id' => $_SESSION['curDir']
		)
	);
	$base = "BNSdatamanager/workspace.php?BNSId=".$_SESSION['BNSId'];
        $order = isset($_GET['order']) ? $_GET['order'] : 'asc';
	
	// Print table header
        ?>

        <table class="fs" >
        <colgroup span="8">
                <col></col>
                <col></col>
                <col></col>
                <col style="width:100%;"></col>
                <col></col>
                <col></col>
                <col></col>
                <col></col>
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

	// Collect and sort files

        $files = array();
        $dirs  = array();

	//$filesC = $GLOBALS['cassandraIds']->find(array('owner' => $_SESSION['BNSId']));

	if (isset($dirData['files']) && count($dirData['files'])>0 ){
	    foreach ($dirData['files'] as $d) {
		$fData = $GLOBALS['cassandraIds']->findOne(array('_id' => $d));
                $key = $fData['_id'];
                if ($_GET['sort'] == "date")
                    $key = $fData['mtime']->sec;
                if ($_GET['sort'] == "size")
                    $key = $fData['size'];
		
		$fData['mtime'] = $fData['mtime']->sec;		

		if (preg_match("/(dir|home)/", $fData['type'])){
		   $uniq = check_key_repeats($key, $dirs);
		   if (isset($uniq))
			$dirs[$key] = $fData;
		}else{
		   $uniq = check_key_repeats($key, $files);
		   if (isset($uniq))
			$files[$key] = $fData;
		}
	    }
	}

        if ($_GET['order'] == 'asc') {
            ksort($files);
        } else {
            krsort($files);
        }
        if ($_GET['order'] == 'asc') {
            ksort($dirs);
        } else {
            krsort($dirs);
        }

	// Add pending file to the list

	$filesPending= getPendingFiles($_SESSION['BNSId'],$files);
	$filesAll=array_merge($files,$filesPending);

	// Print folders

        $sz = 'BKMGTP';
	foreach ($dirs as $f) {
	    $fn=$f[_id];
	    $permissions = (isset($f['permissions']) ? $f['permissions'] : "777") ;
	    $sizeRaw = getSizeDirBNS($fn);
            $factor  = floor((strlen($sizeRaw) - 1) / 3);
            $size = sprintf("%.2f", $sizeRaw / pow(1024, $factor));
	    //$size = sprintf("%.2f", $_SESSION['usedDisk'] / pow(1024, $factor));
            $time = strftime('%d %b %G - %H:%M', $f['mtime']);
            $days2expire = intval($GLOBALS['days2expire'] - ((time() - $f['mtime']) / (24 * 3600))); # crontab set to erase files in X days
          ?>
          <tr>
            <td width="20px;"> <img src="BNSdatamanager/images/folder.png" height="15px" width="15px"> </td>
            <td><?php print $time;?> </td>
            <td><?php print $size . " " . @$sz[$factor]; ?></td>
            <td style="font-weight: bold;"> <a href="BNSdatamanager/workspace.php?op=downDir&fn=<?php print urlencode($fn);?>"> <?php echo basename($fn);?> </a></td>
            <td><a href="BNSdatamanager/workspace.php?op=downloadtgz&fn=<?php print urlencode($fn); ?>"><img src="BNSdatamanager/images/downloadtgz.png" style="height:22px"  title="download tgz"></a> </td>
            <td><a href="BNSdatamanager/workspace.php?op=tar&fn=<?php print urlencode($fn); ?>"><img src="BNSdatamanager/images/tar.png" style="height:22px" title="tar folder"></a> </td>
            <td colspan="2">
		<?php if ($permissions == "000"){ ?>
		    BLOCKED
		<?php }else{ ?>
		    <a href="BNSdatamanager/workspace.php?op=deleteDir&fn=<?php echo urlencode($fn);?>"><img src="BNSdatamanager/images/delete.png" style="height:22px" title="delete"></a>
		<?php } ?>
	    </td>
          </tr>
          <?php
	}


	// Print somthing in case of no data

        if (count($filesAll) == 0 && count($dirs)==0){
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
	    $permissions = (isset($f['permissions']) ? $f['permissions'] : "777") ;
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
                case 'zip':
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
		    <a href="BNSdatamanager/workspace.php?op=<?php print $openFunction;?>&fn=<?php print urlencode($fn); ?>" target="_blank"><?php print basename($fn); ?></a>
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
		<?php if ($permissions == "000"){ ?>
                    <td colspan="2">
		        BLOCKED
		    </td>
		<?php }else{ ?>
                    <td>
		        <a href="BNSdatamanager/workspace.php?op=delete&fn=<?php print urlencode($fn) ?>">
		        <img src="BNSdatamanager/images/delete.png" style="height:22px" title="delete"></a>
		    </td>
                    <td>
		        <?php print $days2expire; ?> days
                    </td>
		<?php } ?>
                </tr>
	        <?php
	    }?>
	   
            <tr id="description<?php print $n;?>" style="visibility:hidden; display:none; border: 1px solid #dddddd; background:#eeeeee;">
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


        <div>
<!---       <a onclick="javascript:toggleVis('folderName');
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

            </div>
---!>
        </div>

    <?php
}

function getPendingFiles($sessionId,$files){
	$filesPending=Array();
	if (isset($_SESSION['SGE'])){
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
                        $filesMongo=preg_grep("/$j[fileId]/", $ids);

			// set job finished
			if (count($filesMongo)>0){
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

						//uploadGSFile($GLOBALS['cassandra'], $basename.$ext,$tmpDir);
						//$GLOBALS['cassandraIds']->update(
				                //   array('_id' => $basename.$ext),
				                //   array(
				                //    '_id' => $basename.$ext,
				                //   'owner' => $_SESSION['BNSId'],
					        //    'size' => filesize("$tmpF"),
				                //    'mtime' => new MongoDate(filemtime($tmpF)),
				                //    'description' => "Trajectory <strong>".$j['name']." </strong> not generated.<br/>".$j['desc']
				                //),array('upsert'=> 1)
				                //);

						$insertData=array(
				                        '_id'   => $_SESSION['BNSId']."/".$basename.$ext,
				                        'owner' => $_SESSION['BNSId'],
				                        'size'  => filesize("$tmpF"),
				                    	'mtime' => new MongoDate(filemtime($tmpF)),
				                    	'description' => "Trajectory <strong>".$j['name']." </strong> not generated.<br/>".$j['desc'],
				                    	'parentDir' => $_SESSION['BNSId']
			                        );
						uploadGSFileBNS($_SESSION['curDir']."/".$basename.$ext, $tmpF, $insertData);

	
						//$f['_id']  =$_SESSION['BNSId']."/".$basename.$ext;
						//$f['owner']=$_SESSION['BNSId'];
						//$f['mtime']=filemtime($tmpF);
						//$f['size'] =filesize("$tmpF");
						//$f['description']= $j['desc'];
			                    	//$f['parentDir'] = $_SESSION['BNSId'];

						//array_push($filesPending,$f);
						$insertData['mtime'] = $insertData['mtime']->sec;
						array_push($filesPending,$insertData);

					        unlink($tmpF);
						unset($_SESSION['SGE'][$pid]); // no permission
					}
				    }else{
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
				}
			  }
		 }
	    }
	}
	return $filesPending;
}


function topDir() {
    return ($_SESSION['curDir'] == $_SESSION['BNSId']);
}

function upDir() {
    if (!topDir())
        $_SESSION['curDir'] = dirname($_SESSION['curDir']);
}

function downDir($fn) {
    $fnData = $GLOBALS['cassandraIds']->findOne(array('_id' => $fn));
    if (! empty($fnData)) {
	if (isset($fnData['type']) && $fnData['type'] == "dir"){
		$_SESSION['curDir'] = $fn;
	}else{
	    $_SESSION['errorData'][error][]="Cannot change directory. $fn is not a directory ";
	}
    }
}

function getUserSpace($fn = '') {
//    if (!$fn)
//        $fn = $_SESSION['User']->dataDir;
//$data = explode("\t", exec("du -sb $fn"));
//return $data[0];
    return calcGSUsedSpace($_SESSION['BNSId']);
}

function navigation() {
    $cdir = $_SESSION['curDir'];

    $fnData = $GLOBALS['cassandraIds']->findOne(array('_id' => $cdir));
    if (empty($fnData))
	$_SESSION['errorData'][error][]="Current directory is not found. Restart login, please";
    $d = (isset($fnData['parentDir'])? $fnData['parentDir'] : 0);
    
    $dirs = array();
    if (!topDir()) {
        while ($d and ( $d != $_SESSION['BNSId'] ) ) {
            $dirs[] = "<a href=\"BNSdatamanager/workspace.php?op=gotoDir&fn=$d\">" . basename($d). "</a>";
    	    $fnData = $GLOBALS['cassandraIds']->findOne(array('_id' => $d));
	    if (empty($fnData))
	        $_SESSION['errorData'][error][]="Directory $d not found. Error in navigation menu";
	    $d = (isset($fnData['parentDir'])? $fnData['parentDir'] : 0);
        }
        $dirs[] = "<a href=\"BNSdatamanager/workspace.php?op=gotoDir&fn=$d\">".basename($d)."</a>";
    }
    return join(' > ', array_reverse($dirs)) . "> " . pathinfo($cdir, PATHINFO_FILENAME);
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

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

function printRow_file($var,$var2,$tableID,$var2Data,$varSelect,$var2Select,$required){
       $var  =trim($var);
       $var2 =trim($var2);
       $varSelect  =trim($varSelect);
       $var2Select =trim($var2Select);
	?>

          <tr>
            <td>
		<select name="<?php echo $var;?>" <?php echo $required;?>>
	    	    <option selected disabled>Select from uploaded files</option>
                    <?php
                    //$uploadsC = $GLOBALS['cassandraIds']->find(array('owner' => $_SESSION['BNSId'],'userUpload'=>1,'parentDir' =>"//"));
		    $uploadsC = $GLOBALS['cassandraIds']->find(array('owner' => $_SESSION['BNSId'],
								     'userUpload'=>1,
								     'parentDir' => array('$not' => new MongoRegex('/'.$_SESSION['BNSId'].'/submissions\//i'))
								    ));
		    if ($uploadsC->count() > 0 ){
	                    foreach ($uploadsC as $file) {
				$name = str_replace($_SESSION['BNSId']."/","",$file['_id']);
				if ($file['_id'] == $varSelect){
		                        print "  <option selected value=\"".$file['_id']."\">$name</option>";
				}else{
		                        print "  <option value=\"".$file['_id']."\">$name</option>";
				}
	                    }
		    }else{
			    $_SESSION['errorData'][error][]="Any uploaded file found. Upload your data at the <a href=\"BNSdatamanager/workspace.php\">workspace</a>";
		    }
                    ?>
		</select>
            </td>
            <td>
		<select style="width:240px;"  name="<?php echo $var2;?>" <?php echo $required;?>>
	    	    <option selected disabled>Select file format</option>
                    <?php
		    if (count($var2Data) > 0 ){
	                    foreach ($var2Data as $value => $name) {
				if ($value == $var2Select){
		                        print "  <option selected value=\"$value\">$name</option>";
				}else{
		                        print "  <option value=\"$value\">$name</option>";
				}
	                    }
		    }else{
			    $_SESSION['errorData'][error][]="No format options given";
		    }
                    ?>
		</select>
            </td>
            <td>
		<a href="javascript:void(0);" onclick="deleteRow('<?php echo $tableID;?>',this);">(x)</a>
            </td>
          </tr>
	<?php
}

function printRow_publication($tableID,$pubTitle,$pubAuth,$pubJourn,$pubYear,$pubVol,$pubDOI){
       $pubTitle = trim($pubTitle);
       $pubAuth  = trim($pubAuth);
       $pubJourn  = trim($pubJourn);
       $pubYear  = trim($pubYear);
       $pubVol  = trim($pubVol);
       $pubDOI  = trim($pubDOI);
	?>
	    <tr>
            	 <td>
                    <input type="text" name="pubTitle[]" value="<?php echo $pubTitle;?>" size="28"/>
                </td>
            	 <td>
                    <input type="text" name="pubAuth[]" value="<?php echo $pubAuth;?>"/>
                </td>
            	 <td>
                    <input type="text" name="pubJourn[]" value="<?php echo $pubJourn;?>" size="12"/>
                </td>
            	 <td>
                    <input type="text" name="pubYear[]" value="<?php echo $pubYear;?>" size="4"/>
                </td>
            	 <td>
                    <input type="text" name="pubVol[]" value="<?php echo $pubVol;?>" size="14"/>
                </td>
            	 <td>
                    <input type="text" name="pubDOI[]" value="<?php echo $pubDOI;?>"/>
                </td>
            	 <td>
                    <a href="javascript:void(0);" onclick="deleteRow('<?php echo $tableID;?>',this);">(x)</a>
               </td> 
           </tr>
        <?php
}
function parseMetadata ($xml){
	return 0;
}


function hash2list($k,$max_subtrees){
    if ($max==0){
        print "ERROR: Too many sublevels to show\n";
    }else {
      $max--;
      foreach ( $data as $k=>$v){
        print "<ul>";
        if (is_array($v)){
            print "<li>$k &nbsp; : &nbsp;";
            hash2list($v,$max);
            print "</li>";
        }else{
            print "<li>$k &nbsp; : &nbsp; $v</li>";
        }
        print "</ul>";
      }
    }
}


function ontoMongo2hash2($col){
    $ontology=Array();
    $levels=Array(
		Array(1,9),
		Array(100,999),
		Array(10000,99999),
		Array(1000000,9999999),
		Array(100000000,999999999),
		Array(10000000000,99999999999)
	      );
    $levParent=0;
    for ($L=0; $L<count($levels); $L++) {
	$entries = $col->find(array('_id' => array('$gte'=>$levels[$L][0],
						   '$lte'=>$levels[$L][1]
						  )
				   )
			     );

	if ($entries->count() == 0 ){
	    print "No entries for level $L";
	    break;

	}else{
    	    foreach ($entries as $r) {
		$id      = $r['_id'];
		$data    = Array(
			   'descrip'=>$r['description'],
			   'feature'=>$r['feature']
			   );
		
		if ($L == 0){
			$ontology[$id]=$data;
		}else{
			$punter=&$ontology;
			for ($i=0;$i<$L;$i++){
			    $p= substr($id,0,1+$i*2);
		 	    $punter=$punter[$p];
			    print "appending to parent [$p]\n";
			}
			$punter[$id]=$data;
			#array_merge()
			print "--- punter fin ---\n";
			var_dump($punter);
		}
	    }
	}
    }
    exit(0);
}



function printTrajDescription($data){
	?>
        <table style="background:none; margin:5px;padding:5px;">
           <colgroup>
            <col style="width:25%;">
            <col/>
           <colgroup/>

	   <tr>
	    <td>Reference experimental structure</td>
	    <td>PDB &nbsp; <input type="text" size=5 name="PDB" value="<?php echo $data['PDB']; ?>"/>
		NDB &nbsp; <input type="text" size=5 name="NDB" value="<?php echo $data['NDB']; ?>"/></td>
	   </tr>
	   <tr>
	    <td>Ligands (or modified nucleotides)</td>
	    <td><input type="text" name="ligandNames" value="<?php echo $data['ligandNames']; ?>" />
		<i>(IDs commma separated if more than one)</i></td>
	   </tr>
	   <tr>
	    <td>Additional Solvent</td>
	    <td><input type="text" name="additionalSolvent" value="<?php echo $data['additionalSolvent']; ?>" />
		<i>(commma separated if more than one)</i></td>
	   </tr>
	   <tr>
	    <td>Counterions</td>
	    <td><input type="text" name="counterions" value="<?php echo $data['counterions']; ?>" />
		<i>(commma separated if more than one)</i></td>
	   </tr>
           <tr>
           <td>Simulation length (*)</td>
           <td><input type="text" size="5" name="trajLength" value="<?php echo $data['trajLength'];?>" required/> (ns)</td>
           </tr>
	   <tr>
	    <td>Number of Frames (*)</td>
	    <td><input type="text" size="5" name="frames" value="<?php echo $data['frames']; ?>" required/></td>
	   </tr>
	   <tr>
	    <td>Frame Step (*)</td>
	    <td><input type="text" size="5" name="frameStep" value="<?php echo $data['frameStep']; ?>" required/> (ns)</td>
	   </tr>
           <tr>
           <td>Simulation temperature (*)</td>
           <td><input type="text" size="5" name="trajTemperature" value="<?php echo $data['trajTemperature'];?>" required/> (K)</td>
           </tr>
	   <tr>
	    <td>Comments</td>
	    <td><textarea rows="3" cols="40"  name="comments"><?php echo $data['comments']; ?> </textarea></td>
	   </tr>
	</table>
	<?php
}

function printOntoMongo($col,$data){
    $ontology=Array();
    $oblig=Array("L101","L102","L103","L104","L105","L106","L107","L20101");

    $entries  = $col->find();
    $onto=Array();
    foreach ($entries as $r) {
	$onto["L".$r['_id']]['feature']    =$r['feature'];
	$onto["L".$r['_id']]['description']=$r['description'];
	$onto["L".$r['_id']]['visible']    =$r['visible'];
    }
    ksort($onto);
    $ids = array_keys($onto);
    $n=0;
    foreach ($onto as $id => $r) {
	$num= preg_replace('/L/',"",$id);
	$idAnt= (isset($ids[$n-1]) ? $ids[$n-1] : 0);
	$idNex= (isset($ids[$n+1]) ? $ids[$n+1] : 0);
	$level   = strlen($id)/2;
	$levelAnt= ( (is_int($idAnt) && $idAnt == 0) ? NULL : strlen($idAnt)/2);
	$levelNex= ( (is_int($idNex) && $idNex == 0) ? NULL : strlen($idNex)/2);
	$checked = ( isset($data[$num])? "checked" : "");
	$checkedChild = ( preg_grep ('/^'.$num.'/i', array_keys($data)) ? 1 : 0 );
	$foundChild   = ( preg_grep ('/^'.$num.'/i', array_keys(Array())) ? 1 : 0 );
	$spacing = "";
	for ($c=0;$c<$level;$c++){
		$spacing.="    ";
	}
	if ($checkedChild || $foundChild){
		$visibility = "";
	}else{
		$visibility = "visibility:hidden;display:none;";
	}
	
	if ( isset($levelAnt) && $levelAnt > $level){
            $idParent = substr($idAnt,0,-2);

	    for($dif=1;$dif<=($levelAnt-$level);$dif++){
                if(preg_grep('/^'.$idParent.'$/',$oblig)){
		    $idNew      = $idParent."00";
		    $numNew     = preg_replace('/L/',"",$idNew);
		    $levelNew   = strlen($idNew)/2;
		    $paddingNew = 20*$levelNew."px";
		    $checkedNew = ( isset($data[$numNew])? "checked" : "");
                    print "<li><span style=\"padding-left:$paddingNew;\"><input type=\"checkbox\" name=\"".preg_replace('/^L/',"",$idNew)."\" value=\"Not specified\"/ $checkedNew> &nbsp;Not specified</span></li>";
                }
                $idParent = substr($idParent,0,-2);

		print $spacing."</div>\n";
	    }
	}

	$padding = 20*$level."px";
	$hasChild = ( preg_grep('/^'.$id.'01/', $ids)   ? 1 : 0);

	$isSimple=1;
	$idBrother= substr($id,0,-2);
	$brothers= preg_grep('/^'.$idBrother.'..$/',$ids);
	foreach ($brothers as $b){
	    if ( preg_grep('/^'.$b.'01/', $ids)) {
		$isSimple=0;
		break;
	    }
	}
	if ($level != 1){
  	    print $spacing."<li><span style=\"padding-left:$padding;\">";
	}else{
  	    print $spacing."<li style=\"padding-top:15px;\">$num .-<span style=\"padding-left:$padding;\">";
	}
	if ($hasChild == 0){
	   if ($isSimple){
		print "<input type=\"checkbox\" name=\"".preg_replace('/^L/',"",$id)."\" value=\"".$r['feature']."\"/ $checked> &nbsp;";
	   }else{
		print "<input type=\"checkbox\" name=\"".preg_replace('/^L/',"",$id)."\" value=\"".$r['feature']."\"/ $checked> &nbsp;";
	   }
	}
	print $r['feature'];
//	if ($num == "20102" || $num == "20103"){
//	    print "&nbsp;&nbsp; <input type=\"text\" name=\"".preg_replace('/^L/',"",$id)."\" value=\"".$data[$num]."\"/ >";
//	    if ($num == "20102")
//		print "&nbsp;(ns)";
//	    if ($num == "20103")
//		print "&nbsp;(K)";
//	}
	if (isset($levelNex) && $levelNex > $level){
		if ($level != 1)
		    print "&nbsp;<a href=\"javascript:void(0)\" onclick=\"toggleVisLink('$id',this);\">(+)</a>";
	}
	print "</span></li>\n";

	if (isset($levelNex) && $levelNex > $level){
		if ($level != 1){
		    print $spacing."<div id=\"$id\" style=\"$visibility\">\n";
		}else{
		    print $spacing."<div id=\"$id\" style=\"border:1px solid;background-color:rgba(255, 255, 255, 0.7);\">\n";
		}
	}
	$n++;
    }
    print "</div>";
}

function writeMetadataFromHash ($metaFile,$data){
     $metaFile   = absolutePathGSFile($metaFile);
#    $old = $GLOBALS['cassandra']->find(array('filename' => $metaFile));
#    if ( $old->count()  ){
#	return "ERROR: Metadata file ($metaFn) already exits in the database";
#    }
    $vars= Array("datasetName" => 1,
		"datasetDescription" =>1,
		"submissionID"=>1,
		"submissionState"=>1,
		"publDateSys" =>1,
		"publDate" =>1,
		"pubSys" =>1,
		"pubTitle" =>1,
		"pubAuth" =>1,
		"pubJourn" =>1,
		"pubYear" =>1,
		"pubVol" =>1,
		"pubDOI" =>1,
		"trajFile" =>1,
		"trajFormat" =>1,
		"topFile" =>1,
		"topFormat" =>1,
		"docFile" =>1,
		"docFormat" =>1,
		"metaFile" =>1,
		"metaFormat" =>1,
		"PDB" =>1,
		"NDB" =>1,
		"ligandNames" =>1,
		"additionalSolvent" =>1,
		"counterions" =>1,
		"trajLength" =>1,
		"frames" =>1,
		"frameStep" =>1,
		"trajTemperature" =>1,
		"comments" =>1,
		"rmsd" =>1,
		"rmsd_bp" =>1,
		"Rgyr" =>1,
		"lostWC" =>1,
		"lostContacts" =>1,
		"fraying" =>1,
		"avgTwist" =>1,
		"minorGrooveSize" =>1,
		"majorGrooveSize" =>1,
		"grooveSizeMethod" =>1
	   );

    $metaFn   = pathinfo($metaFile, PATHINFO_BASENAME );
    $metaTmp  = $GLOBALS['tmpDir']."/".$metaFn;

    $F  = fopen($metaTmp, "w") or die("Unable to create file $metaTmp");
    foreach ($data as $k => $v){
	if (!isset($v) || strlen($v) == 0)
	    continue;
	if (isset($vars[$k]) || is_int($k)){
	    if (is_array($v)){
		foreach ($v as $k1 => $v1){
		    if (is_array($v1))
			return "ERROR: Value of $k1 (within $k) is an array. Expected string.";
		}
		$v= join(", ",$v);
	    }
            if (!isset($v) || strlen($v) == 0 || preg_match('/^\s*$/',$v)){
		continue;
	    }
	    fwrite($F, str_replace(";",",",$k).";".preg_replace('/;/',",",$v)."\n");
	}
    }
    fclose($F);

    if (is_file($metaTmp)){
    	uploadGSFileBNS($metaFile, $metaTmp, Array('userUpload'=>1) );
        return 0;
    }else{
	return "ERROR: temporal metadata file $metaTmp not created.";
    }
}

function readMetadataToHash($metaFile){
	$data=Array();
	
	$file = $GLOBALS['cassandra']->findOne(array('filename' => $metaFile));
	if (! $file->file['_id']  ){
	   $data[] = "ERROR: Metadata file $metaFile not found";
	   return $data;
	}
	$tmpFile = $GLOBALS['tmpDir'].'/metadata.csv';
	$file->write($tmpFile);
	$F = fopen($tmpFile,"r") or die("Unable to open file $tmpFile");
	$n=1;
	while (($line = fgets($F)) !== false) {
	    $fields=explode(";",$line);
	    if (count($fields) != 2){
		$data[]= "&nbsp; ERROR: Found ".count($fields)." columns and expected 2 at metadata file $metaFile [row $n]";
		return $data;
	    }
	    $data[$fields[0]] = $fields[1];
	    $n++;
	}
	fclose($F);

	return $data;
}

function moveFilesToSubmission($files,$submissionID){
	print "_________________ moveFilesToSubm__";
	$filesNew=array();
	if (count($files) ==0){
		return $files;
	}

        foreach ($files as $f){
		$fo = $GLOBALS['cassandraIds']->findOne(array('_id' => $f));
		if (empty($fo)){
		    $_SESSION['errorData'][error][] = "Cannot move file $f into submission folder. File does not exist";
                    $filesNew[] = $f;
		    continue;
		}
                $new = $_SESSION['BNSId']."/submissions/$submissionID/".basename($f);
                
                if ( $GLOBALS['cassandraIds']->find(array('_id' => $new))->count() > 0 ){
                        foreach (range(1, 99) as $N) {
                            $newName= $new."_".$N;
                            if ($GLOBALS['cassandraIds']->find(array('_id' => $newName))->count() == 0){
                                $new = $newName;
                                break;
                            }
                        }
                }
                $r = moveGSFileBNS($f,$new);
                if ($r == 0){
                       $filesNew[] = $f;
		}else{
		    $r = modifyGSFileBNS($new, "permissions", "000");
		    $r = modifyGSFileBNS($new, "expiration", new MongoDate(strtotime("+365 day")));
		    $filesNew[] = $new;
                }
        }
	return $filesNew;
}
