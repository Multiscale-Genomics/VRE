<?php

function popChar(&$String) {
    $c = substr($String, 0, 1);
    $String = substr($String, 1);
    return $c;
}

function getFullDef (&$tree, $leaf) {
	if (!$tree[$leaf])
		return "";
	else if ($tree[$leaf]['parent'] != 'root')
		return getFullDef($tree,$tree[$leaf]['parent'])." > ".$tree[$leaf][Titol];
	else
		return $tree[$leaf]['Titol'];
}

function getLeaf (&$tree, &$req, $leaf,$ops='') {
	$txt = getLeafHTML ($tree, $leaf, $req, $ops);
	if ($tree[$leaf]['estat']) {
		foreach (array_values($tree[$leaf]['leaf']) as $l)
			$txt .= getLeaf($tree, $req, $l, $ops);
	}
	return $txt;
}

function getLeafABC (&$tree, &$req, $leaf,$ops='') {
	$txt = getLeafHTML_ABC ($tree, $leaf, $req, $ops);
	if ($tree[$leaf]['estat']) {
		foreach (array_values($tree[$leaf]['leaf']) as $l)
			$txt .= getLeafABC($tree, $req, $l, $ops);
	}
	return $txt;
}

function resalta ($txt,$resalt) {
#print "-$resalta-";
	if ($resalt) {
		foreach (split (' ', $resalt) as $k)
			$txt = eregi_replace("($k)","<b>\\1</b>",$txt);
	}
	return $txt;
}

function getXReference ($dir,$fname1) {
logger("Getting XReference!!");
    $fname = $dir."/".$fname1;
    $reference = "can't find file $fname";
    if (file_exists($fname)) {
        $fh = fopen($fname, 'r');
        $fname = fgets($fh);
        while(!feof($fh)) {
            $line = trim($fname);
            if (substr($line, 1, 4) == 'xref') {
                $fname = fgets($fh);
                break;
            }
            $fname = fgets($fh);
        }
        while(!feof($fh)) {
            $line = trim($fname);
            if (substr($line, 1, 2) == 'id') {
                break;
            }
            $fname = fgets($fh);
        }
        fclose($fh);
        $arr = explode (" ", $line);
        $reference = substr($arr[2], 1, -1);
    }

	// Creating soft link to be able to know the disk used with a du -L

	//$newRef = $reference;
	//$newRef = preg_replace('/\//','.',$newRef);
	//$cmd = "cd ".$_SESSION['projectData']['projDir'].";ln -s ".$GLOBALS['asyncDir']."/".$reference." ln_".$newRef; 
	$persDir = $GLOBALS['asyncDir'];
	if(preg_match("/\.\.\//",$reference)){
		$persDir = $GLOBALS['servicesTmpDir'];
		$reference = preg_replace("/^\.\.\//","",$reference);
	}
	$cmd = "cd ".$_SESSION['projectData']['projDir'].";ln -s ".$persDir."/".$reference." ln_".$fname1; 
	logger("Going to execute: $cmd");
	
	//if($newRef and !file_exists($_SESSION['projectData']['projDir']."/ln_".$newRef)){
	if($fname1 and !file_exists($_SESSION['projectData']['projDir']."/ln_".$fname1)){
		exec($cmd);
	}

    return $reference;
}

function getSize (&$tree, $leaf) {
    $data = $tree[$leaf];
    $filename = $data['fname'];
    $persist = $data['persistent'];
    $projdir = $_SESSION['projectData']['projDir'];
    $projid = $_SESSION['projectData']['idProject'];
    $login = $_SESSION['userData']['login'];
    $async = 0;
    $links = 0;
    $dirs = 0;
   
#    if (!$tree[$leaf]['size']) {
        if (file_exists("$projdir/$filename")) {
#            if (file_exists("$projdir/$filename.async")) {
#                $async = round(filesize("$projdir/$filename.async")/1024, 0);
#            }
            if (!$persist) {
                $tree[$leaf]['size'] = round(filesize("$projdir/$filename")/1024, 1);
		logger("getSize Project $projdir-$projid : $tree[$leaf][size] ( round(filesize($projdir/$filename)/1024, 1) )");
            }
            else {
                $sizeloc = round(filesize("$projdir/$filename")/1024, 1);
		$reference = getXReference($projdir,$filename);
	        $persDir = $GLOBALS['asyncDir'];
	        if(preg_match("/^MDWebTrajInput/",$reference)){
        	        $persDir = $GLOBALS['servicesTmpDir'];
		}
		if(file_exists("$persDir/$reference"))
	                $links = $sizeloc + round(filesize($persDir."/".$reference)/1024, 1);
		logger("getSize Project $projdir-$projid : $tree[$leaf][size]"); # (round(filesize($persDir."/".$reference) / 1024, 1))");
            }
        }

	# Leaf files
	$files = glob($projdir.'/'.$leaf.'.*');
	if(is_array($files) and count($files) > 0){
		foreach ($files as $file){
			$dd = exec("du -Lks $file | sed 's/\t/#/'");
			$du = split('#',$dd);
			$size = $du[0];
			$dirs += $size;
			#logger("File $file, Du: $du[0], Size: $size Sum: $dirs");
		}
        }
	$files = glob($projdir.'/*_'.$leaf);

	# Sub-folders
	if(is_array($files) and count($files) > 0){
		foreach ($files as $folder){
			$dd = exec("du -Lks $folder | sed 's/\t/#/'");
			$du = split('#',$dd);
			$size = $du[0];
			$dirs += $size;
			#logger("Folder $folder, Du: $du[0], Size: $size Sum: $dirs");
		}
        }

	$tree[$leaf]['size'] = $links;
	$tree[$leaf]['size'] += $async;
	$tree[$leaf]['size'] += $dirs;
#    }


    #$tree[$leaf]['size'] += $async;
    #$tree[$leaf]['size'] += $dirs;

    $size = $tree[$leaf]['size'];
    $suf = " kB";
    if ($size > 1024) {
        $size = round(($size / 1024),1);
        $suf = " MB";
    }
    if ($size > 1024) {
        $size = round(($size / 1024),1);
        $suf = " GB";
    }
    return $size.$suf;
}

function getLeafHTML (&$tree, $leaf, $req, $ops='') {
	include "htmlib/treeIcons.inc.htm";
#
        $data = $tree[$leaf];

		$title = $data['Titol'];
		if(preg_match('/^_/',$title)){
			$title = $data['keyOp'].$subtype.$title;
		}

        $subtype = '';
        if ($data['Subtype'])
            $subtype .= "_".$data['Subtype'];
	ob_start();?>
<tr><td valign=top class='toolboxtitle'><a href = '<?php echo $leaf ?>' name='<?php echo $leaf ?>'</a><?php echo indentat($tree, $leaf,1) ?>
<?php	if (count($data['leaf'])) { 
?>
		<a href=javascript:submitAs('project','t_<?php echo $leaf ?>')><?php echo $icon[$data['estat']] ?></a>
<?php
}	else {
?>
		<?php echo $punt ?>
	<?php 
}
?>
	<?php echo $typeIcon[$data['objectType']] ?><a href="javascript:setVis('<?php echo $leaf ?>')"><?php echo $title ?></a> <?php if ($data['Descript']) print '('.$data['Descript'].')' ?> <?php if ($data['config']) print '(MD Config Files)' ?> <?php if(!$data['config']) print '('.getSize($tree, $leaf).')' ?> <?php echo $statIcon[$data['fileok']] ?>
     <div id="<?php echo $leaf ?>Div" class="toolbox">
    <?php if ($data['fileok'] == 1) {
		$toolboxType = $GLOBALS['toolbox'][$data['objectType']];
		if ($data['config'])
			$toolboxType = $GLOBALS['toolbox']['Config'];
		if($_SESSION['projectData']['projType'] == "anal" and preg_match("/Trajectory/",$data['objectType'])){
			$toolboxType = $GLOBALS['toolbox'][$data['objectType']."_anal"];
		}

        if  ($toolboxType) {
			foreach ($toolboxType as $op) {
				if($op == "jmolTraj") {?>
			 		<a href= "javascript:alert('Visualize a trajectory could take some minutes. Please, be patient.');javascript:submitAs('project','<?php echo $op ?>_<?php echo $leaf ?>')"><?php echo $opsIcons[$op] ?></a>
				<?php 
} else { 
?>
					<a href=javascript:submitAs('project','<?php $op ?>_<?php $leaf ?>')><?php $opsIcons[$op] ?></a>
				<?php
				}
			}
		}
	} elseif ($data['fileok'] == 3) { ?>
		<a href=javascript:submitAs('project','readLog_<?php echo$leaf ?>')><?php echo $opsIcons['readLog'] ?></a> <a href=javascript:submitAs('project','trash_<?php echo $leaf ?>')><?php echo $opsIcons['trash'] ?></a>
	<?php  
	} else { 
	?>
    	<a href=javascript:submitAs('project','trash_<?php echo $leaf ?>')><?php echo $opsIcons['trash'] ?></a>
    	<?php 
	} 
	?>
    </div>
</td></tr>
<?php if ($ops['leaf']==$leaf)  { # cal obrir Ops
#	$newId = newFileOrder($_SESSION[projectData][projDir]);
	$newId = newObjectOrder($tree);
    ?>
	<tr><td> <div id="opsDialog">
		<input name="leaf" type="hidden" value="<?php echo $leaf ?>" />
		<input name="newId" type="hidden" value="<?php echo $newId ?>" />
       	<p>Select the desired operation.</p>
       	<p>Title: <input name="titol" size="25" value="_<?php echo $newId ?>" />
        Comment: <input name="comm" size="40" value="" /></p>
	</td></tr>
   	<tr><td> <div id="opsDialog"> <table><tr><td>
	<?php if(!empty($ops['ops'])){?>
	  <select name="idOperacio" onChange='setHelpForm();setInnerForm()'>  <option value="XX">List of Operations:</option> 
       	<?php  foreach ($ops['ops'] as $o) { ?> 
        	<option value="<?php echo $o['idOperacio']."-".$o['subtype']?>"><?php echo $o['nom']?></option> 
	  <?php } ?>
		</select>
		</td><td id="helpForm"></td>
		</tr></table>
		</div>
	</td></tr>
	<tr><td> 
		<div id="innerForm" > </div>
		<div id="opsDialog">
       	<input type="button" value="ok" onClick='checkParams(error,"project","doMoby_<?php echo $leaf ?>")'/>
        <input type="button" value="cancel" onClick='location.href="openProject.php"' />
		<br><br>
		</div>
	<?php 
	}else{
	?>
		<p>Sorry, there is no operation available from this input...</p>
		</td></tr></table></div>
	<?php 
	}
	?>
    </td></tr>
<?php
}
?>
<?php	$txt = ob_get_contents();
        ob_end_clean();
	return $txt;
}

function getLeafHTML_ABC (&$tree, $leaf, $req, $ops='') {
	include "htmlib/treeIcons.inc.htm";
#
        $data = $tree[$leaf];

        $title = $data['Titol'];
        if(preg_match('/^_/',$title)){
              $title = $data['keyOp'].$subtype.$title;
        }

        $subtype = '';
        if ($data['Subtype'])
            $subtype .= "_".$data['Subtype'];

	ob_start();?>
<tr><td valign=top class='toolboxtitle'><a href = '<?php echo $leaf ?>' name='<?php echo $leaf ?>'</a><?php echo indentat($tree, $leaf,1) ?>
<?php	if (count($data['leaf'])) { ?>
		<a href=javascript:submitAs('project','t_<?php echo $leaf ?>')><?php echo $icon[$data['estat']] ?></a>
	<?php
	}else {
	?>
		<?php echo $punt ?>
	<?php 
	}
	?>
	<?php echo $typeIcon[$data['objectType']] ?><a href="javascript:setVis('<?php echo $leaf ?>')"><?php echo $title ?></a> <?php if ($data['Descript']) print '('.$data['Descript'].')' ?><?php print '('.getSize($tree, $leaf).')' ?> <?php echo $statIcon[$data['fileok']] ?>
     <div id="<?php echo $leaf ?>Div" class="toolbox">
    <?php if ($data['fileok'] == 1) {

	if (preg_match("/AMBER/",$data['objectType'])) 
        	$toolboxType = $GLOBALS['toolbox']['ABC_res'];
	else
		$toolboxType = $GLOBALS['toolbox']['ABC'];

        if  ($toolboxType) {
			foreach ($toolboxType as $op) {
				?><a href=javascript:submitAs('project','<?php echo $op ?>_<?php echo $leaf ?>')><?php echo $opsIcons[$op] ?></a><?php
			}
		}
	} elseif ($data['fileok'] == 3) { ?>
		<a href=javascript:submitAs('project','readLog_<?php echo $leaf ?>')><?php echo $opsIcons['readLog'] ?></a> <a href=javascript:submitAs('project','trash_<?php echo $leaf ?>')><?php echo $opsIcons['trash'] ?></a>
	<?php  
	} else {
	?>
    	<a href=javascript:submitAs('project','trash_<?php echo $leaf ?>')><?php echo $opsIcons['trash'] ?></a>
    <?php 
	}
	?>
    </div>
</td></tr>
<?php if ($ops['leaf']==$leaf)  { # cal obrir Ops
#	$newId = newFileOrder($_SESSION[projectData][projDir]);
	$newId = newObjectOrder($tree);
    ?>
	<tr><td> <div id="opsDialog">
		<input name="leaf" type="hidden" value="<?php echo $leaf ?>" />
		<input name="newId" type="hidden" value="<?php echo $newId ?>" />
       	<p>Select the desired operation.</p>
       	<p>Title: <input name="titol" size="25" value="_<?php echo $newId ?>" />
        Comment: <input name="comm" size="40" value="" /></p>
	</td></tr>
   	<tr><td> <div id="opsDialog"> <table><tr><td>
	<?php if(!empty($ops['ops'])){ ?>
	  <select name="idOperacio" onChange='setHelpForm();setInnerForm()'>  <option value="XX">List of Operations:</option> 
       	<?php  foreach ($ops['ops'] as $o) { ?> 
        	<option value="<?php echo $o['idOperacio']."-".$o['subtype'] ?>"><?php echo $o['nom'] ?></option> 
	  <?php  } ?>
		</select>
		</td><td id="helpForm"></td>
		</tr></table>
		</div>
	</td></tr>
	<tr><td> 
		<div id="innerForm" > </div>
		<div id="opsDialog">
       	<input type="button" value="ok" onClick='checkParams(error,"project","doMoby_<?php echo $leaf ?>")'/>
        <input type="button" value="cancel" onClick='location.href="openProject.php"' />
		<br><br>
		</div>
	<?php 
	}
	else{
	?>
		<p>Sorry, there is no operation available from this input...</p>
		</td></tr></table></div>
	<?php 
	}
	?>
    </td></tr>
<?php
}
?>
<?php	$txt = ob_get_contents();
        ob_end_clean();
	return $txt;
}

function indentat (&$tree, $leaf,$hlin) {
	if (!$hlin)
		$hlin=1;
	$inic = '<img src="images/barra.png" border="0" align=absmiddle width="10" height="'.($hlin*20).'">';
	$sep = '<img src="images/spacer.gif" border="0" width="10" height="10">';
	$txt = str_repeat($inic.$sep,depth($tree,$leaf));
	return "<span style='font-family:courier; font-size:9pt'>$txt</span>";
}

function depth (&$tree,$leaf) {
	if ($tree[$leaf]['parent'])
		return (1 + depth($tree, $tree[$leaf]['parent']));
	else
		return 0;
}
function toggle (&$tree, $leaf) {
	if ($tree[$leaf]['estat'])
		colapse ($tree, $leaf);
	else
		expand ($tree, $leaf);
}
function colapse (&$tree, $leaf) {
	$tree[$leaf]['estat'] = 0;
 	foreach (array_values($tree[$leaf]['leaf']) as $l)
		colapse ($tree, $l);
}
function expand (&$tree, $leaf) {
#print "-$leaf-<br>";
	$tree[$leaf]['estat'] = 1;
	if ($tree[$leaf]['parent'] and ($tree[$leaf]['parent'] != 'root'))
		expand ($tree, $tree[$leaf]['parent']);
}

function expand_parent(&$tree, $leaf) {
	$parent=$tree[$leaf]['parent'];
	if ($parent != 'root')
		expand($tree, $parent);
}
function colapseAll(&$tree) {
	foreach (array_values($tree['root']['leaf']) as $l)
		colapse($tree,$l);
}

function expandAll(&$tree) {
	foreach (array_keys($tree) as $l) {
		if ($l != 'root')
			$tree[$l]['estat']=1;
	}
}

function deleteTreeNode (&$t, $leaf, $r=0) {
# R = 0 restringit a nodes terminals
	$leafData = $t[$leaf];
	if (count($leafData['leaf'])) {
		if ($r) {
			foreach ($leafData['leaf'] as $l)
					deleteTreeNode($t, $leafData['leaf'], $r);
		}
	}
	if (! count($leafData['leaf'])) {
		unset ($t[$leafData[parent]]['leaf'][$leafData['id']]);
		unset ($t[$leafData['id']]);
	}
}

function addNewLeaf (&$f, $leaf, &$req, $opData, $pid=0) {
    $newf='f'.$req['newId'];
    $pers = 0;
	$config = 0;
	$cg = 0;
// JL Modificada residueToMutate per considerar la cadena
	$title = $req['titol'];
	if ($req['idOperacio']== 'mutateResidueFromPDBText'){
		$resName = residueToMutate($f['projDir']."/".$f['fileTree'][$leaf]['fname'],$req['prm']['resid'],$req['prm']['chain']);
		$title = "Structure with mutation ".$resName."-".$req['prm']['chain'].":".$req['prm']['resid']." to ".$req['prm']['newRes']." ";
		logger("MutateResidue, changing title to $title!!");
	}
        if (preg_match("/GromacsWorkflow/",$req['idOperacio']) or preg_match("/GromacsGenTop/",$req['idOperacio'])){
		$txt_ff = $req['prm']['forcefield'];
                $gmx_ff = $GLOBALS['gmxff'][$txt_ff];
                $txt_wat = $req['prm']['waterType'];
                $gmx_wat = $GLOBALS['gmxWat'][$txt_wat];
                logger("GromacsWorkflow: changing title to $title + FF + Wat: $gmx_ff $gmx_wat (ff[$txt_ff]): $gmx_ff | (wat[$txt_wat]): $gmx_wat");

                $title .= " #$gmx_ff ff - $gmx_wat#";
        }
        if (preg_match("/AmberWorkflow/",$req['idOperacio']) or preg_match("/AmberGenTop/",$req['idOperacio'])){
                $txt_ff = $req['prm']['forcefield'];
                logger("AmberWorkflow: changing title to $title + FF: $txt_ff ");

                $title .= " #$txt_ff ff#";
        }
        if ( preg_match("/protonate/",$req['idOperacio']) ){
                $type = $req['prm']['type'];
                logger("Protonate Services: changing title to $title + Program: $type ");

                $title .= " #$type#";
	}
	# Next if necessary for posterior NA Flexibility Analysis, to discern between CG analysis types.
        if ( preg_match("/nucleicAcidAnalysis/",$req['idOperacio']) or preg_match("/^CG/",$req['idOperacio']) ){
                $type = $req['prm']['analysis'];
                logger("Nucleic Acid Analysis: changing title to $title + Type: $type ");

                $title .= " #$type#";
	}
        if ( preg_match("/CG_WLCAnalysis/",$req['idOperacio']) or preg_match("/CG_MontecarloAnalysis/",$req['idOperacio'])){
                $type = $req['prm']['global'];
		if($type){
			$anType = "Global Analysis";
		}
		else{
			$ini = $req['prm']['atomDistances']['base1'];
			$end = $req['prm']['atomDistances']['base2'];
			$offset = $req['prm']['atomDistances']['offset'];
			$anType = "Local Analysis (From $ini to $end every $offset bases)";
		}

                logger("Coarse-Grained Analysis: changing title to $title + Analysis Type: $anType ");

                $title .= " #$anType#";
	}
        if ( preg_match("/WLC/",$req['idOperacio']) || preg_match("/Montecarlo/",$req['idOperacio']) ){
		logger("Coarse Grained Analysis ADD NEW LEAF.");
		$cg = $req['cg'];
	}

    if ((substr($opData['ObjectOut'],0,13) == 'MD_Trajectory') || (substr($opData['ObjectOut'],0,24) == 'MD_Compressed_Trajectory'))
    #if (substr($opData['ObjectOut'],0,13) == 'MD_Trajectory')
        $pers = 1;

    if (preg_match("/WorkflowSolv/",$req['idOperacio']) )  
 	$pers = 1;

    if ($req['conf'])
        $config = 1;

    #print "pers = ".$pers;
    $fname = $newf.'.moby';
$p = $opData['ObjectOut'];
logger("AddNewLeaf, objectType: $p");
#    $objtype = getObjectType($f['projDir']."/".$fname);
#    if ($objtype == 'PDB__Text')
#        $objtype = 'PDB-Text';
	$objtype = $opData['ObjectOut'];
	$idOp = $req['idOperacio'].$req['subtype'];
    $f['fileTree'][$newf] = Array (
        'id' => $newf,
        'Titol' => $title,
        'Descript' => $req['comm'],
        'Op' => $req['idOperacio'],
        'keyOp' => $opData['keyOp'],
        'Subtype' => $req['subtype'],
        'script' => $opData['script'],
        'parent' => $leaf,
        'pid' => $pid,
		'config' => $config,
        'leaf' => Array(),
        'estat' => 1,
        'fname' => $fname,
        'fileok' => 2,
        'objectType' => $objtype,
	'cg' => $cg,
        'persistent' => $pers);
    $f['fileTree'][$leaf]['leaf'][$newf]=$newf;
	expand($f['fileTree'],$newf);
}

function checkFiles (&$f, $force=0) {
        global $pend;
        $pend_temp = $pend;
	$pending=0;
	foreach (array_keys($f['fileTree']) as $k) {
		$pid = $f['fileTree'][$k]['pid'];

		if($pid !=0){
			$process = new ProcessSGE();
			$process->setPid($pid);

			if ($process->status()){
				logger("Async process currently running!! ($pid).");
	            $f['fileTree'][$k]['fileok'] = "2";
				$pending = "1";
			}else{
				logger("No Async process running.");
				$size = getSize($f['fileTree'], $k);
				$file = $f['projDir']."/".$f['fileTree'][$k]['fname'];
		        
				$f['fileTree'][$k]['pid'] = 0;
	            $fname = $f['fileTree'][$k]['fname'];
				$f1 = $f['projDir']."/".$fname;
				$f2 = $f['fileTree'][$k]['persistent'];
				$s = getSize($f['fileTree'], $k);
logger("ProjDir: $f1, s>0? ($s > 0)");
				if ((file_exists($f1)) && (getSize($f['fileTree'], $k) > 0)) {
			
					# Case Abstract Object Type: We need to know the child name, in order to show the correct icon.
					$objType = $f['fileTree'][$k]['objectType'];
					logger("Case Abstract Object Type: $objType, need to know the child name.");
					if($objType == 'MD_Structure' or $objType == 'MD_Trajectory'){
						$objType = getObjectType($f['projDir']."/".$fname);
						$f['fileTree'][$k]['objectType'] = $objType;
						logger("Child name: $objType");
					}

#		            $objtype = getObjectType($f['projDir']."/".$fname);
#		            if ($objtype == 'PDB__Text')
#		                $objtype = 'PDB-Text';
#		            $f['fileTree'][$k]['objectType'] = $objtype;

					$cmd = $GLOBALS['scriptDir']."/checkObjectCorrectness.pl $f1 $f2";
					logger("exec $cmd");
					$correct = exec($cmd);
					if ($correct){
			            $f['fileTree'][$k]['fileok'] = "1";
					}
					else{
			            $f['fileTree'][$k]['fileok'] = "3";
					}
		        }
				else if ((file_exists($f1.".config"))) { #&& (getsize($f['fileTree'], $k) > 0)) {

					# MD Config File case.
					logger("MD Config File case. File: $f1.config ");

					# Getting WorkDir: Web Service Temporary Folder.
					$toBeParsed = exec ("grep WorkDir ".$f1.".config");
					$arrDir = explode(":", $toBeParsed);
					$workDir = trim($arrDir[1]);

					if(!is_empty_folder($workDir)){
						$f['fileTree'][$k]['fileok'] = "1";
						logger("NON Empty Folder $workDir");
					}
					else{
			            $f['fileTree'][$k]['fileok'] = "3";
						logger("Empty Folder $workDir");
					}			
				}
				else{
					logger("Process with pid $pid has finished, but without results in $file...");
		            $f['fileTree'][$k]['fileok'] = "3";
				}
			}
		}

#        if (($f['fileTree'][$k]['fileok'] != "1") or $force) {
#            $f['fileTree'][$k]['async'] = file_exists($f['projDir']."/".$f['fileTree'][$k]['fname'].".async");
#			if ($f['fileTree'][$k]['async']) {
#                $f['fileTree'][$k]['fileok'] = "2";
#                $pend = 0;
#                execAsyncWSScript ($f['fileTree'][$k]['script'], $f['projDir']."/".$f['fileTree'][$k]['fname'].".async", $f['projDir']."/".$f['fileTree'][$k]['fname'], $_SESSION['userData']['login'], $f['idProject']);
#                $pend = $pend_temp;
#            }
#            if ((file_exists($f['projDir']."/".$f['fileTree'][$k]['fname'])) && (getsize($f['fileTree'], $k) > 0)) {
#                $f['fileTree'][$k]['fileok'] = "1";
#                $fname = $f['fileTree'][$k]['fname'];
#                $objtype = getObjectType($f['projDir']."/".$fname);
#                if ($objtype == 'PDB__Text')
#                    $objtype = 'PDB-Text';
#                $f['fileTree'][$k]['objectType'] = $objtype;
#            }
            //print "$k, ".$f['fileTree'][$k]['fileok'].", ".$pending."/";
#		    if ($pending or ($f['fileTree'][$k]['fileok'] == "2"))
#		        $pending = "1";            
#        }
	}
	return $pending;
}

// JL 17/05 Modificada residueToMutate per considerar la cadena
function residueToMutate ($fname,$numRes,$chain=" ") {
#	$fname = $f['projDir']."/".$f['fileTree'][$k]['fname'].".moby";

	$resName = '?';
    $fh = fopen($fname, 'r');
    $fname = fgets($fh);
    while(!feof($fh)) {
		$line = trim($fname);
		if(strncmp($line, "ATOM", 4)==0) {
		    if ((trim(substr($line, 22, 4)) == $numRes) and (trim(substr($line,21,1)) == $chain)) {
		        $resName = trim(substr($line,17,3));
		        break;
		    }
		}
	    $fname = fgets($fh);
    }
    fclose($fh);

	return $resName;
}

function is_empty_folder($folder){
    $c=0;
    if(is_dir($folder) ){
        $files = opendir($folder);
        while ($file=readdir($files)){
            $c++;
            if ($c>2)
               return false; // dir contains something
        }
         return true; // empty dir
    }
    else return true; // not a dir
}

?>
