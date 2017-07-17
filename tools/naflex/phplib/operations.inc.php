<?php
/* MDWeb
 * operations.inc.php
 * Operations and external script management
 */

require "phplib/ProcessSGE.php";
require "phplib/Process.php";

function sendFile (&$f,$leaf, $mime) {
	$data = $f[fileTree];
	system ("$GLOBALS[scriptDir]/extractPDBFromPDBText.pl $f[projDir] ".$data[$leaf][fname]." $mime");
	if ($mime == "zip") 
		$fileExt = "pdb.gz";
	else
		$fileExt = "pdb";
        logger("Sending file: ".$data[$leaf]['fname'].".$fileExt");
?>
<script>
newWin = window.open('<?php echo $f[baseURL]."/".$data[$leaf][fname].".$fileExt" ?>','<?php echo $leaf ?>')
</script>
<?php
}

function sendJmol (&$f,$leaf,$traj='0') {

        $data = $f['fileTree'];
	$objType = $data[$leaf]['objectType'];
	logger("OBJTYPE: $objType");

	$fname = $f['projDir']."/structure.pdb";
	$onlydna = checkOnlyDNA($fname);
	logger("sendJMol, $onlydna case");

#    if ($traj == '0') {
     if($objType == 'PDB_Collection' or $objType == 'PDB-Text' or preg_match("/MD_Structure/",$objType)) {

	$cmd = "perl $GLOBALS[scriptDir]/isPDBCollection.pl $f[projDir]/".$data[$leaf]['fname'];
	$snapshots = exec($cmd);
	logger("isPDBCollection?? $snapshots snapshots");

	if($snapshots > 0){
                logger("Viewing PDB_Collection in Jmol: $GLOBALS[scriptDir]/fromPDBCollectionToModels.pl $f[projDir] ".$data[$leaf]['fname']." ".$data[$leaf]['fname'].".jmol");
                system ("$GLOBALS[scriptDir]/fromPDBCollectionToModels.pl $f[projDir] ".$data[$leaf]['fname']." ".$data[$leaf]['fname'].".jmol");
	}
	else{
		logger("Viewing structure in Jmol: $GLOBALS[scriptDir]/extractPDBFromPDBText.pl $f[projDir] ".$data[$leaf]['fname']." ".$data[$leaf]['fname'].".jmol");
        	system ("$GLOBALS[scriptDir]/extractPDBFromPDBText.pl $f[projDir] ".$data[$leaf]['fname']." ".$data[$leaf]['fname'].".jmol");
	}
    } else {

#       $parent = $f['fileTree'][$leaf]['parent'];
#       $parentOp = $f['fileTree'][$parent]['Op'];
        $op = $f['fileTree'][$leaf]['Op'];
        $size = $f['fileTree'][$leaf]['size'];
	if($size > 0){
		$size = $size/1024; // Size in MB.
		$size = round($size,0);
	}

        logger("Trying to view a trajectory in Jmol: Size: $size MB");
        if( $size > $GLOBALS['maxTrajSize'] ){		// maxTrajSize is the biggest trajectory size allowed.
          $_SESSION['window'] = 'Error';
          $_SESSION['error'] = 9;
          $_SESSION['errorMessage'] = $size;
          return;
        }

        logger("Viewing trajectory in Jmol: Previous Operation: $op, Size: $size MB");
        if( preg_match("/FULL/",$op) or preg_match("/runMD/",$op)){
	  $_SESSION['window'] = 'Error';
	  $_SESSION['error'] = 1;
          return;
        }

	$trajName = $data[$leaf]['fname'];
	$offset = 1;
	if($objType == 'MD_Compressed_Trajectory'){
	        logger("Viewing trajectory in Jmol: MD_Compressed_Trajectory, First of all, uncompress it!");
		#logger("$GLOBALS[scriptDir]/pcazip.pl $f[projDir]/".$data[$leaf]['fname']." $f[projDir]/".$data[$leaf]['fname'].".crd");
		#system ("$GLOBALS[scriptDir]/pcazip.pl $f[projDir]/".$data[$leaf]['fname']." $f[projDir]/".$data[$leaf]['fname'].".crd");
		logger("Viewing trajectory in Jmol: $GLOBALS[scriptDir]/pcz2pdbs.pl ".$f['projDir']." $f[projDir]/".$data[$leaf]['fname']."  $f[projDir]/".$data[$leaf]['fname'].".jmol.pdb  >& ".$f[projDir]."/".$trajName.".jmol.log");
		system ("$GLOBALS[scriptDir]/pcz2pdbs.pl ".$f['projDir']." $f[projDir]/".$data[$leaf]['fname']."  $f[projDir]/".$data[$leaf]['fname'].".jmol.pdb >& ".$f[projDir]."/".$trajName.".jmol.log");
	}
	else{

		logger("Viewing trajectory in Jmol: $GLOBALS[scriptDir]/getPDBCollectionFromMD_Trajectory.pl $f[projDir] ".$trajName." ".$data[$leaf]['fname'].".jmol ".$offset." >& ".$f[projDir]."/".$trajName.".jmol.log");
		system ("$GLOBALS[scriptDir]/getPDBCollectionFromMD_Trajectory.pl $f[projDir] ".$trajName." ".$data[$leaf]['fname'].".jmol ".$offset." >& ".$f[projDir]."/".$trajName.".jmol.log");
    	}
    }

	logger("Trajectory prepared to be launched in JMol!");

        $t = $data[$leaf]['Titol'];
        if(preg_match('/^_/',$t)){
            $t = $data[$leaf]['keyOp'].$t;
        }

	$cg = $data[$leaf]['cg'];
	if($cg){
		$include = "htmlib/jmol.DNA_CG.inc.htm";
	}
	else if($onlydna == 'Nuc'){
		$include = "htmlib/jmol.DNA.inc.htm";
	}
	else {
		$include = "htmlib/jmol.inc.htm";
	}

    	ob_start();
	print headerNA("NAFlex: Nucleic Acids Flexibility",0);
    	#print headerMMB("MDWeb. Molecular Dynamics on Web. ".$data[$leaf]['fname'],0);?>
    	<h4>JMol view. <?php echo $t?><?php if ($data[$leaf]['Descript']) print "(".$data[$leaf]['Descript'].")"?></h4>
	<?php
    	include "$include";
    	print footerNA();
	$txt =ob_get_contents();
	ob_end_clean();
	$j = fopen ($f['projDir']."/".$data[$leaf]['fname'].".jmol.htm","w");
	fwrite ($j, $txt);
	fclose($j);

	$_SESSION['window'] = 'JMol';
	$fname = $data[$leaf]['fname'];
	$_SESSION['filename'] = $fname;
}

function sendPlot (&$f, $leaf, $op) {
	$data = $f['fileTree'];
        logger ("Plotting: $GLOBALS[scriptDir]/plotFAASeq.pl $f[projDir] ".$data[$leaf]['fname']." $op");

	$t = $data[$leaf]['Titol'];
        if(preg_match('/^_/',$t)){
            $t = $data[$leaf]['keyOp'].$t;
        }

	$gmxUnits = ($data['root']['objectType']=="MD_TrajectoryXTC");

        if(preg_match('/Bfactor/',$t)){
		$units = "Units in &#8491;&sup2;"; # Square Angstroms.
		if($gmxUnits) {
			$units = "Units in nm&sup2;";  # Gromacs -> Square nm
		}
	}
	else{
		$units = "Units in &#8491;"; # Angstroms.
		if($gmxUnits) {
			$units = "Units in nm";  # Gromacs -> nm
		}
	}

	system ("$GLOBALS[scriptDir]/plotFAASeq.pl $f[projDir] ".$data[$leaf]['fname']." $op");
	ob_start();
	print headerNA("NAFlex: Nucleic Acids Flexibility",0);
	#print headerMMB("MDWeb. Molecular Dynamics on Web. ".$data[$leaf]['fname'], 0);?>
    <h4><?php echo $t; echo "\t( $units )" ?><?php if ($data[$leaf]['Descript']) print "(".$data[$leaf]['Descript'].")" ?></h4>
    <p align="center"><img border="1" src="<?php echo $data[$leaf]['fname'].".png" ?>"></p>
    <?php print footerNA();
	$txt =ob_get_contents();
	ob_end_clean();
	$j = fopen ($f['projDir']."/".$data[$leaf]['fname'].".png.htm","w");
	fwrite ($j, $txt);
	fclose($j);

        $_SESSION['window'] = 'Plot';
        $fname = $data[$leaf]['fname'];
        $_SESSION['filename'] = $fname;
}

function sendNucleicAcidAnalysis (&$f, $leaf, &$req) {
        $data = $f['fileTree'];
        logger ("NucleicAcidAnalysis: ".$data[$leaf]['fname']);
        $t = $data[$leaf]['Titol'];
        if(preg_match('/^_/',$t)){
            $t = $data[$leaf]['keyOp'].$t;
        }
        if(preg_match('/#(\w+)#/',$t,$match)){
		$type = preg_replace("/#/","",$match[0]);
            logger("Match: $match[0] ($type)");
        }

	$login = $_SESSION['userData']['login'];
	$proj=$f['projDir'];
	$l = preg_split ("/\//",$proj);
	$project = $l[sizeof($l)-1];

	logger("NUCACIDANALYSIS: login:$login,leaf:$leaf,project:$project,type:$type");

	$script = "<script>\n newWin =  window.open('nucleicAcidAnalysis.php?user=".$login."&proj=".$project."&op=".$leaf."&type=".$type."','".$leaf."_Nucleic_Acid_Analysis');\n if(newWin == null || typeof(newWin) == 'undefined') \n alert('Sorry, the server could not open a new window. Please, try to turn off your internet browser pop-up blocker. Thank you very much.') </script>";

	return $script;
}

function download (&$f, $leaf, $pattern="", $mime="", $zipped=false) {
    $data = $f['fileTree'];
    $op = $data[$leaf]['Op'];

	if (($data[$leaf]['objectType'] == "FeatureAASequence") || ($data[$leaf]['objectType'] == "ArrayFloat")) {
          $fname = $data[$leaf]['fname'];
          logger("Downloading plot: $GLOBALS[scriptDir]/valuesArrFl.pl ".$f['projDir']." $fname");
          system("$GLOBALS[scriptDir]/valuesArrFl.pl ".$f['projDir']." $fname");
          $ext = ".out";
	}
	else if ($op == "nucleicAcidAnalysis" or preg_match("/^CG/",$op) ){
		logger("Download NucleicAcidAnalysis: $op");

    		$title = $data[$leaf]['Titol'];

		$files = glob($f['projDir'].'/*_'.$leaf,GLOB_ONLYDIR);

		if(is_array($files) and count($files) > 0)
			$dir = $files[0];

		if (preg_match("/Stiffness/",$title) )
			$dir = $dir.'/FORCE_CTES';

		logger("Title: $title, Dir: $dir");

		$fnames = buildNucleicAcidOutput($dir,$title,$leaf);

		$filesToCompress = join(" ",$fnames);
		logger("Files To Compress: $filesToCompress");

		system ("cd $dir; tar -czf ".$data[$leaf][fname].".tgz ".join (" ",$fnames));
		logger("system (cd $dir; tar -czf $data[$leaf][fname].tgz join (' ',$fnames))");
		$source = $dir."/".$data[$leaf]['fname'].".tgz";
		$dest = $f['baseURL']."/".$data[$leaf][fname].".tgz";
		copy($source,$dest);
		logger("copy($source,$dest)");

		$ext = ".tgz";
	}
	else if($data[$leaf]['config']){

		# MD Config File case: Building Config Output.
		logger("Download Config: INTO config");

		$fname = $f['fileTree'][$leaf]['fname'];
		$f1 = $f['projDir']."/".$fname;
		$programToBeParsed = exec ("grep Program ".$f1.".config");
                $arrProgram = explode(":", $programToBeParsed);
                $program = trim($arrProgram[1]);
logger("Config File Program Type: $program");

		$toBeParsed = exec ("grep WorkDir ".$f1.".config");
		$arrDir = explode(":", $toBeParsed);
		$dir = trim($arrDir[1]);

		$fnames = buildConfigOutput($dir,$program);

		$filesToCompress = join(" ",$fnames);
		logger("Files To Compress: $filesToCompress");

		system ("cd $dir; tar -czf ".$data[$leaf][fname].".tgz ".join (" ",$fnames));
		logger("system (cd $dir; tar -czf $data[$leaf][fname].tgz join (' ',$fnames))");
		$source = $dir."/".$data[$leaf]['fname'].".tgz";
		$dest = $f['baseURL']."/".$data[$leaf][fname].".tgz";
		copy($source,$dest);
		logger("copy($source,$dest)");

		$ext = ".tgz";
	}
	else {
                # Amber Workflow/op, changing psf to top.
                $op = $data[$leaf]['Op'];
		$amber = 0;
                if(preg_match("/Amber/",$op)){
			logger("Amber = 1");
                        $amber = 1;
                }

        	$arts=Array();
		$dir = $f['projDir'];
	        $query="SELECT * FROM objectDetails WHERE objectName='".$data[$leaf]['objectType']."'";
        	if (!(strpos($mime, "x-pdb") === false)) {
	            $query .= " and fileExt='pdb'";
        	}
	        $rs1 = getRecordSet($query);
        	while ($rsobj = mysql_fetch_array($rs1))  {
	            if ((!$pattern) || (substr_count($rsobj[articleName], $pattern) > 0)) {
        	        $articleName = $rsobj[articleName];
                	$fileExt = $rsobj[fileExt];

			# Amber Workflow/op, changing psf to top, removing Namd files.
			if($amber){
				if (preg_match("/psf/",$fileExt) )
					$fileExt = "top";
				if (preg_match("/coor/",$fileExt) or preg_match("/vel/",$fileExt) or preg_match("/xsc/",$fileExt) ) 
					continue;
			}
	                $type = $rsobj[type];
        	        $arts[] = Array('artName' => $articleName, 'ext' => $fileExt, 'type' => $type);
	            }
        	}

		logger("Download Op: $op");
	        $fnames=Array();
        	foreach ($arts as $a) {
	            $fname = $data[$leaf]['fname'];
        	    logger("Downloading files: $GLOBALS[scriptDir]/extractArticle.pl $dir $fname $a[artName] $a[ext] $a[type] $mime");
	            system("$GLOBALS[scriptDir]/extractArticle.pl $dir $fname $a[artName] $a[ext] $a[type] $mime");
        	    $namesfile = "filenames.tmp";
	            if (file_exists($dir."/".$namesfile)) {
        	        $fh = fopen($dir."/".$namesfile, 'r');
                	$fname = fgets($fh);
	                while(!feof($fh)) {
        	            $fnames[] = trim($fname).".".$a[ext];
                	    $fname = fgets($fh);
	                }
        	        fclose($fh);
                	unlink($dir."/".$namesfile);
	            }
        	}
	        if ($zipped == "true") {
        	    system ("cd $dir; tar -czf ".$data[$leaf][fname].".tgz ".join (" ",$fnames));
		    if (preg_match("/ABC/",$op) ){
			system("cd $dir; mv ".$data[$leaf][fname].".tgz ABC.setup.tgz");
		    }
	            $ext = ".tgz";
        	} else {
	            $ext = ".$a[ext]";
        	}
	}
	if (file_exists($f['baseURL']."/".$data[$leaf][fname].$ext)) {
		$file = $f['baseURL']."/".$data[$leaf][fname].$ext;
		logger("Open LOG file $file ");

	        $_SESSION['window'] = 'Download';
        	$fname = $data[$leaf]['fname'].$ext;
	        $_SESSION['filename'] = $fname;
	}
	else if (file_exists($f['baseURL']."/".$data[$leaf]['fname'].".asyncLog")) {
		$file = $f['baseURL']."/".$data[$leaf]['fname'].".asyncLog";
		logger("Open LOG file $file ");
		$cleanedFile =  $f['baseURL']."/".$data[$leaf]['fname'].".mdWeb.asyncLog";
		buildLogFile($file,$cleanedFile);
		logger("Open LOG file $cleanedFile $f[baseURL]");

                $_SESSION['window'] = 'Download';
                $fname = $data[$leaf]['fname'].".mdWeb.asyncLog";
                $_SESSION['filename'] = $fname;
	}
}

function sendFormLog (&$f, $leaf) {

	$data = $f['fileTree'];
	$file = $f['baseURL']."/".$data[$leaf]['fname'].".asyncLog";
	logger("Open LOG file $file --$f[baseURL]-- ");
	$cleanedFile =  $f['baseURL']."/".$data[$leaf]['fname'].".mdWeb.asyncLog";
	logger("CleanedFile: $cleanedFile");
	buildLogFileHTML($file,$cleanedFile);
	logger("Open LOG file $cleanedFile $f[baseURL]");

	$_SESSION['window'] = 'Download';
	$fname = $data[$leaf]['fname'].".mdWeb.asyncLog";
	$_SESSION['filename'] = $fname;
	$fnamepath = $f['baseURL']."/".$fname;

	logger("FNAME: $fnamepath");

        ob_start();
	print headerNA("NAFlex: Nucleic Acids Flexibility",0);
        #print headerMMB("MDWeb. Molecular Dynamics on Web. ".$data[$leaf]['fname'], 0);
	#print "<h4>Workflow Errors: <i>$name</i></h4><br/><br/>";
	passthru("cat $fnamepath"); 
	print footerNA();
        $txt =ob_get_contents();
        ob_end_clean();
        $j = fopen ($f['projDir']."/".$data[$leaf]['fname'].".mdWebLog.htm","w");
        fwrite ($j, $txt);
        fclose($j);

        $_SESSION['window'] = 'FormLog';
        $fname = $data[$leaf]['fname'];
        $_SESSION['filename'] = $fname;
}

function buildLogFile($file,$cleanedFile) {

	$fout = fopen($cleanedFile, 'w');

	if (file_exists($file)) {
                $fh = fopen($file, 'r');
                $line = fgets($fh);
                while(!feof($fh)) {
                    	if (!( preg_match("/redefined/",$line) or preg_match("/called/",$line) or preg_match("/uninitialized/",$line) or preg_match("/filehandle/",$line) or preg_match("/Argument/",$line) or preg_match("/MDWEBWF/",$line) or preg_match("/TmpDir:/",$line))){
				fwrite($fout,$line);

				// Query ID: 13 (runMDFromNAMD_MD_Structure13_130262654621589)
				if ( preg_match("/Query ID/",$line) ){
					$l = preg_split ("/\s+/",$line);
					$dir = $l[3];
					$dir = preg_replace ("/\(/","",$dir);
					$dir = preg_replace ("/\)/","",$dir);

					preg_match("/\d+_\d+/",$dir,$num);
					$mdrun_log = $num[0]."_mdrun.log";
					$grompp_log = $num[0]."_grompp.log";
					$cmip_log = $num[0]."_titration.log";
					$gmx_log = $num[0].".ret.log";

					$dir = $GLOBALS['servicesTmpDir']."/".$dir;
					logger("Dir: $dir ($gmx_log - $cmip_log)");

					if (file_exists($dir)){
						if (file_exists("$dir/namd.log")){
							$logAppend[] = "$dir/namd.log";
							$logTXT[] = "\n\n##########\nnamd.log\n##########\n\n";
							logger("There's a namd.log file!!");
						}
                                                if (file_exists("$dir/leap.log")){
							$logTXT[] = "\n\n##########\nleap.log\n##########\n\n";
							$logAppend[] = "$dir/leap.log";
                                                        logger("There's a leap.log file!!");
                                                }
                                                if (file_exists("$dir/$grompp_log")){
                                                        $logTXT[] = "\n\n##########\ngrompp.log\n##########\n\n";
                                                        $logAppend[] = "$dir/$grompp_log";
                                                        logger("There's an grompp.log file!!");
                                                }
                                                if (file_exists("$dir/$mdrun_log")){
							$logTXT[] = "\n\n##########\nmdrun.log\n##########\n\n";
							$logAppend[] = "$dir/$mdrun_log";
                                                        logger("There's an mdrun.log file!!");
						}
                                                if (file_exists("$dir/$gmx_log")){
       	                                                $logTXT[] = "\n\n##########\ngromacs.log\n##########\n\n";
               	                                        $logAppend[] = "$dir/$gmx_log";
                       	                                logger("There's an gmx.ret.log file!!");
                               	                }
                                                if (file_exists("$dir/$cmip_log")){
                                                        $logTXT[] = "\n\n##########\nCMIP.log\n##########\n\n";
                                                        $logAppend[] = "$dir/$cmip_log";
                                                        logger("There's a CMIP.titration.log file!!");
                                                }
					}
				}
			}
			$line = fgets($fh);
                }
                fclose($fh);

		for ( $i = 0; $i < count($logAppend); $i++ ){
//		for ( reset($logAppend);current($logAppend);next($logAppend) ) {
//			$logAppendFile = current($logAppend);
			$logAppendFile = $logAppend[$i];
			$logTxtFile = $logTXT[$i];
 
			if(file_exists($logAppendFile)){
				fwrite($fout,$logTxtFile);
				$logApp = fopen($logAppendFile, 'r');
        	        	$line2App = fgets($logApp);
	                	while(!feof($logApp)) {
        	        		fwrite($fout,$line2App);
					$line2App = fgets($logApp);
		                }
        		        fclose($logApp);
			}
		}
	}
	fclose($fout);
 }

function buildLogFileHTML($file,$cleanedFile) {

	$toWrite = '';
	$headerToWrite = "<ul> <li><a href='#ErrorFile0'>Service/Workflow Progress Log</a></li>";
	$done = Array();
	$count = 1;

        if (file_exists($file)) {
                $fh = fopen($file, 'r');
                $line = fgets($fh);
                while(!feof($fh)) {
                        if (!( preg_match("/redefined/",$line) or preg_match("/substr outside/",$line) or preg_match("/called/",$line) or preg_match("/uninitialized/",$line) or preg_match("/filehandle/",$line) or preg_match("/Argument/",$line) or preg_match("/MDWEBWF/",$line) or preg_match("/TmpDir:/",$line) or preg_match("/Destroying/",$line) or preg_match("/mmb.pcb.ub/",$line) or preg_match("/[pP]olling/",$line) or preg_match("/WS-Resource/",$line) or preg_match("/Workflow Code/",$line) or preg_match("/Input element/",$line) or preg_match("/Readable message/",$line) or preg_match("/Exception Code/",$line) or preg_match("/Service Failure/",$line) or preg_match("/Retrieving/",$line) or preg_match("/Finished/",$line) or preg_match("/^</",$line))){

				$line = preg_replace("/\\n\\n/","<br/>",$line);
				$line = preg_replace("/\\n/","<br/>",$line);
	

                                // Query ID: 13 (runMDFromNAMD_MD_Structure13_130262654621589)
                                if ( preg_match("/Query ID/",$line) ){

                                        $l = preg_split ("/\s+/",$line);
                                        $dir = $l[3];
                                        $dir = preg_replace ("/\(/","",$dir);
                                        $dir = preg_replace ("/\)/","",$dir);
                                        $dir = preg_replace ("/<br\/>/","",$dir);

                                        preg_match("/\d+_\d+/",$dir,$num);
                                        $mdrun_log = $num[0]."_mdrun.log";
                                        $grompp_log = $num[0]."_grompp.log";
                                        $gmx_log = $num[0].".ret.log";

                                        $dir = $GLOBALS['servicesTmpDir']."/".$dir;
                                        logger("Dir: $dir ($gmx_log)");
					if ($done[$dir]){
						$line = fgets($fh);
						continue;
					}
					$done[$dir] = 1;

					$toWrite.="Service Failure.<br/>Workflow Stopped, see program log files.";
	
                                        if (file_exists($dir)){
                                                if (file_exists("$dir/namd.log")){
                                                        $logAppend[] = "$dir/namd.log";
                                                        $logTXT[] = " <br/> <h4>## NAMD log File ##</h4> <br/> ";
							$headerToWrite.="<li><a href='#ErrorFile".$count."'>NAMD log File</a></li>";
							$count++;
                                                        logger("There's a namd.log file!!");
                                                }
                                                if (file_exists("$dir/leap.log")){
                                                        $logTXT[] = " <br/> <h4>## LEAP log File ##</h4> <br/> ";
                                                        $headerToWrite.="<li><a href='#ErrorFile".$count."'>LEAP log File</a></li>";
                                                        $count++;
                                                        $logAppend[] = "$dir/leap.log";
                                                        logger("There's a leap.log file!!");
                                                }
                                                if (file_exists("$dir/$grompp_log")){
                                                        $logTXT[] = " <br/> <h4>## GROMACS grompp log File ##</h4> <br/> ";
                                                        $headerToWrite.="<li><a href='#ErrorFile".$count."'>GROMACS grompp log File</a></li>";
                                                        $count++;
                                                        $logAppend[] = "$dir/$grompp_log";
                                                        logger("There's an grompp.log file!!");
                                                }
                                                if (file_exists("$dir/$mdrun_log")){
                                                        $logTXT[] = " <br/> <h4>## GROMACS mdrun log File ##</h4> <br/> ";
                                                        $headerToWrite.="<li><a href='#ErrorFile".$count."'>GROMACS mdrun log File</a></li>";
                                                        $count++;
                                                        $logAppend[] = "$dir/$mdrun_log";
                                                        logger("There's an mdrun.log file!!");
                                                }
                                                if (file_exists("$dir/$gmx_log")){
                                                        $logTXT[] = " <br/> <h4>## GROMACS log File ##</h4> <br/> ";
                                                        $headerToWrite.="<li><a href='#ErrorFile".$count."'>GROMACS log File</a></li>";
                                                        $count++;
                                                        $logAppend[] = "$dir/$gmx_log";
                                                        logger("There's an gmx.ret.log file!!");
                                                }
                                        }
                                }
				else{
					$toWrite.="$line";
				}
                        }
                        $line = fgets($fh);
                }
                fclose($fh);

		
                for ( $i = 0; $i < count($logAppend); $i++ ){
			$j = $i + 1;
                        $logAppendFile = $logAppend[$i];
                        $logTxtFile = $logTXT[$i];
logger("Into AppendFile ($logAppend[$i])");
                        if(file_exists($logAppendFile)){
				$toWrite.="<a name='ErrorFile".$j."' ></a>";
				$toWrite.=$logTxtFile;
                                $logApp = fopen($logAppendFile, 'r');
                                $line2App = fgets($logApp);
                                while(!feof($logApp)) {
					$line2App = preg_replace("/\\n/","<br/>",$line2App);
                                        //fwrite($fout,$line2App);
					$toWrite.="$line2App";
                                        $line2App = fgets($logApp);
                                }
                                fclose($logApp);
                        }
                }
        }

	$p = `pwd`;
	logger("FOPEN $cleanedFile PWD: $p");
        $fout = fopen($cleanedFile, 'w');
	if($count > 1){
        	fwrite($fout,"<h4>NAFlex Operation/Workflow Error State</h4><br/>");
		$headerToWrite = preg_replace("/<br\/><br\/>/","<br/>",$headerToWrite);
		$headerToWrite.="</ul>";
		fwrite($fout,$headerToWrite);
	}
	$toWrite = preg_replace("/<br\/><br\/><br\/>/","<br/>",$toWrite);
	$toWrite = preg_replace("/<br\/><br\/><br\/><br\/>/","<br/><br/>",$toWrite);
	$toWrite = preg_replace("/<br\/><br\/><br\/><br\/><br\/>/","<br/><br/>",$toWrite);
        fwrite($fout,"<br/><br/><a name='ErrorFile0'></a><h4>NAFlex Operation/Workflow Progress Log</h4><br/><br/>");
	fwrite($fout,$toWrite);
	fclose($fout);
 }



 function delete_directory($dirname) {
    //CF fixed escaping
	$dirname = escapeshellarg($dirname);
 	
 	if (is_dir($dirname))
         $dir_handle = opendir($dirname);
     if (!$dir_handle)
         return false;
     while($file = readdir($dir_handle)) {
         if ($file != "." && $file != "..") {
             if (!is_dir($dirname."/".$file))
                 unlink($dirname."/".$file);
             else
                 delete_directory($dirname.'/'.$file);
         }
     }
     closedir($dir_handle);
     rmdir($dirname);
     return true;
 }

function deleteFile (&$f,$leaf) {
	$data = $f[fileTree];
	$leafData = $f[fileTree][$leaf];
        garbageCollector();
	if ( ! count($leafData[leaf]) && (strpos($leafData[id], "root") === false) ) {
            if ($leafData['fileok'] == "2") {

				$pid = $leafData['pid'];
				if($pid != 0){
					logger("Killing background process with pid: $pid.");

		                        $process = new ProcessSGE();
		                        $process->setPid($pid);
	
        		                if ($process->stop()){
						logger("Process $pid killed: $kill.");
					}
					else{
						logger("Ups... we had problems removing process $pid from the queues...");
					}
				}

            }
            system ("rm $f[projDir]/$leafData[id]*");
            logger("File deleted: rm $f[projDir]/$leafData[id]*");

	    $files = glob($f[projDir].'/*_'.$leafData[id],GLOB_ONLYDIR);
	    if(is_array($files) && count($files) > 0){
		    logger("Directory Analysis Found: $f[projDir]/*_$leafData[id], Removing!");
	            system ("rm -r $f[projDir]/*_$leafData[id]");
	    }

		$persFile = "ln_".$leafData[id].".moby";
		$fullPersFile = $f[projDir]."/".$persFile;
		logger("Persistent File: $persFile");
		if(file_exists($fullPersFile)){
			$linkPointer = readlink($fullPersFile);
			if(!is_dir($linkPointer)){
            			system ("rm $linkPointer");
				logger("Link Pointer deleted: $linkPointer");
				$dirLink = dirname($linkPointer);
				if(is_empty_folder($dirLink)){
            				system ("rmdir $dirLink");
					logger("Link Pointer Folder deleted: $dirLink");
				}
			}
            		system ("rm $fullPersFile");
			logger("Persistence Link deleted: $fullPersFile");
		}

            deleteTreeNode($f['fileTree'], $leaf);
            saveProject(0);
            logger("Node ".$leafData['id']." deleted");
	} else {

		$n = $leafData['id'];
	            logger ("Can't delete root or father node ".$n."!");

		$_SESSION['window'] = 'Error';
		$_SESSION['error'] = 12;
		$_SESSION['errorMessage'] = $n;
		return;
        }
}

function getWSFromOp($idOp) {
    $idOp = preg_replace ("/-.*/","",$idOp);
    $opData = getOpData($idOp);
    $WS = $opData['WS'];
    return $WS;
}

function retrieveOpsABC (&$f, $leaf, $type=1) {

    $ops=Array('leaf'=>$leaf);

    $ops['ops']=Array();

    // ABC AMBER Setup Workflow
    $rs = getRecordSet("SELECT o.* FROM Workflows o WHERE o.idOperacio='ABCWorkflow'");
    while ($rsF = mysql_fetch_array($rs))  {

        logger("Previous Op: $previousOp, New Possible Op: $rsF[idOperacio], IncMolType: -$rsF[incMolType]-");
            $opId = $rsF['idOperacio'].$rsF['subtype'];
            $ops['ops'][$opId]=$rsF;
            $ops['ops'][$opId]['typeOp']= "WF";
            $ops['ops'][$opId]['input']=Array();
            $ops['ops'][$opId]['output']=Array();
            $ops['ops'][$opId]['secondary']=Array();
            $ops['ops'][$opId]['ObjectOut']=$rsF['ObjectOut'];
   }

   return $ops;

}

function retrieveOps (&$f, $leaf, $type=1) {

    $ops=Array('leaf'=>$leaf);

    $previousOp = $f['fileTree'][$leaf]['Op'];

// Now we are only interested in the particular moby-object, we don't want ISAs objects.
	$object = $f[fileTree][$leaf][objectType];
logger ("SELECT OBJECT: $object");
	if ($object == 'AMBER_MD_Structure' or $object == 'NAMD_MD_Structure' or $object == 'GROMACS_MD_Structure'){
		$cond = "MD_Structure' or v.ObjectType = '$object";
	}
	else if ($object == 'MD_TrajectoryCRD' or $object == 'MD_TrajectoryBINPOS' or $object == 'MD_TrajectoryDCD' or $object == 'MD_TrajectoryNetCDF' or $object == 'MD_TrajectoryXTC')  {
		$cond = "MD_Trajectory' or v.ObjectType = '$object";
	}
	else{
		$cond = $object;
	}

//    $glue = "' OR v.ObjectType = '";
// 	$cond = join ($glue, getISAList($f[fileTree][$leaf][objectType]));

	$ops['ops']=Array();

    //Ops Incompatibilities
    $rs = getRecordSet("SELECT * FROM incompatibleOps");
    while ($rsF = mysql_fetch_array($rs))  {
	$actual = $rsF['actualOp'];
	$previous = $rsF['previousOp'];
	$incompatible[$actual][$previous] = 1;
    }

    // Molecule type is checked only at the root file. In this MDWeb version it's not possible to add new structures to the root one.
    $onlydna = $f[fileTree][root][molType];

    // Nucleic Acids Coarse-Grained Special case: Only accepting CG-algorithms (incompatibilityMolType=Prot+Nuc).
    $cg = $f['fileTree'][$leaf]['cg'];
    if($cg) {
	//$onlydna = "Prot' and incMolType!= 'Nuc' and incMolType!='";
	$onlydna = "' and incMolType regexp '^$cg";
    }

    //Web services
    $rs = getRecordSet("SELECT o.* FROM operacions o , ValidInput v WHERE o.idOperacio=v.idOperacio and (v.ObjectType = '".$cond."') and tipus=$type and incMolType!= '".$onlydna."' order by nom");
//      $rs = getRecordSet("SELECT o.* FROM operacions o , ValidInput v WHERE o.idOperacio=v.idOperacio and (v.ObjectType = $cond) and tipus=$type order by nom");
		logger("SELECT o.* FROM operacions o , ValidInput v WHERE o.idOperacio=v.idOperacio and (v.ObjectType = '$cond') and tipus=$type and incMolType!= '".$onlydna."' order by nom");    
    while ($rsF = mysql_fetch_array($rs))  {

logger("Previous Op: $previousOp, New Possible Op: $rsF[idOperacio], IncMolType: -$rsF[incMolType]-");
	  if($rsF['idOperacio'] != $previousOp and !$incompatible[$rsF['idOperacio']][$previousOp]){
            $opId = $rsF['idOperacio'].$rsF['subtype'];
	    $ops['ops'][$opId]=$rsF;
            $ops['ops'][$opId]['typeOp']= "WS";
            $ops['ops'][$opId]['input']=Array();
            $ops['ops'][$opId]['output']=Array();
            $ops['ops'][$opId]['secondary']=Array();

            $rsprm = getRecordSet ("SELECT * FROM WSParamMDWeb where idOperacio = '".$rsF['idOperacio']."'");
            while ($rsFP = mysql_fetch_array($rsprm))
            	$ops['ops'][$opId][$rsFP['type']][]=$rsFP;
            $ops['ops'][$opId]['ObjectOut']=$ops['ops'][$opId]['output'][0]['objectName'];
            $rsprm=nothing;
	  }
    }

    if($cg) {
	//$onlydna = "Prot$|^Nuc' and incMolType!= '";
	$onlydna = "' and incMolType regexp '^$cg";
    }
    else{
	$onlydna .="' and incMolType not regexp '^CG";
    }

    if($onlydna==''){
	$onlydna = ' ';
    }

    // Workflows
    $rs = getRecordSet("SELECT o.* FROM Workflows o , ValidInput v WHERE o.idOperacio=v.idOperacio and (v.ObjectType = '".$cond."') and tipus=$type and incMolType!='".$onlydna."' order by nom");
    logger("SELECT o.* FROM Workflows o , ValidInput v WHERE o.idOperacio=v.idOperacio and (v.ObjectType = '".$cond."') and tipus=$type and incMolType !='".$onlydna."' order by nom");
//	$rs = getRecordSet("SELECT o.* FROM Workflows o , ValidInput v WHERE o.idOperacio=v.idOperacio and (v.ObjectType = '$object') and tipus=$type order by nom");
//logger("SELECT o.* FROM Workflows o , ValidInput v WHERE o.idOperacio=v.idOperacio and (v.ObjectType = '$object') and tipus=$type order by nom");
	while ($rsF = mysql_fetch_array($rs))  {

	logger("Previous Op: $previousOp, New Possible Op: $rsF[idOperacio], IncMolType: -$rsF[incMolType]-");
	  if($rsF['idOperacio'] != $previousOp and !$incompatible[$rsF['idOperacio']][$previousOp]){
            $opId = $rsF['idOperacio'].$rsF['subtype'];
			$ops['ops'][$opId]=$rsF;
            $ops['ops'][$opId]['typeOp']= "WF";
            $ops['ops'][$opId]['input']=Array();
            $ops['ops'][$opId]['output']=Array();
            $ops['ops'][$opId]['secondary']=Array();
            $ops['ops'][$opId]['ObjectOut']=$rsF['ObjectOut'];
	  }
	}

    return $ops;
}

function getOpData ($idOperacio) {
    $opData = getRecord('operacions', 'idOperacio', $idOperacio,'T');
    if ($opData) {
        $opData['typeOp']= "WS";
        $opData['input']=Array();
        $opData['output']=Array();
        $opData['secondary']=Array();
        $rsprm = getRecordSet ("SELECT * FROM WSParamMDWeb where idOperacio = '".$idOperacio."'");
        while ($rsFP = mysql_fetch_array($rsprm))
            	$opData[$rsFP['type']][]=$rsFP;
        $opData['ObjectOut']=$opData['output'][0]['objectName'];
    } else {
        $opData = getRecord('Workflows', 'idOperacio', $idOperacio,'T');
        $opData['typeOp']="WF";
    }
    return $opData;
}

function executeMoby(&$f, $leaf, &$req) {
        $noLeaf=false;
	$operacio = $req['idOperacio'];

	$opData = getOpData($operacio);
	logger("opData = $opData");

        $projID = $req['idProject'];
        $login = $_SESSION['userData']['login'];
        if (checkDiskUsage()) {
		$_SESSION['window'] = 'Error';
		$_SESSION['error'] = 2;
		return;
        }

	if($login == "demo" && $req['idOperacio']!= "flexAnalysis"){
		logger("DEMO user, not allowing operations!!");
		?>
			<script>alert("User demo is not allowed to run operations, it is just a read-only tutorial demo.\nSorry for the inconveniences.");</script> 
		<?php
		return;
	}

    //var_dump($opData);
	logger("OPERACIO: $req[idOperacio]");
    switch ($req['idOperacio']) {

        case 'flexAnalysis':

	$ftype = $f[fileTree][$leaf][objectType];
	$fname = $f[fileTree][$leaf][fname];
	$structname = $fname.".pdb";
	$fullstructpath = $persdir."/".$structname;

	$pdbfname = $f['projDir']."/structure.pdb";
	$onlydna = checkOnlyDNA($pdbfname);

	$traj = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
	if($onlydna == 'Prot-Nuc'){			
		$outTraj = $f[projDir]."/f".$req['newId'].".NA.moby";
		logger("Prot-Nuc complex, trying to strip protein from nucleic acid...");
		logger("$GLOBALS[scriptDir]/stripDNA.pl $traj $outTraj 1");
		$out = exec("$GLOBALS[scriptDir]/stripDNA.pl $traj $outTraj 1");
		$traj = $outTraj;
		$ftype = "MD_TrajectoryCRD";
		$fullstructpath = $outTraj.".pdb";
		$fname = "/f".$req['newId'].".NA.moby";
	}

            $noLeaf=true;
            $objID=$leaf;
            $projID=$req['idProject'];
            $dir = $f['projDir'];
	    $persdir = $dir;
#            $persdir = $GLOBALS['persDir']."/".$login."/".$projID;
#            logger("Flexibility analysis: $GLOBALS[scriptDir]/extractArticle.pl $persdir ".$dir."/".$fname." struct.content pdb String application/x-pdb");
#            exec("$GLOBALS[scriptDir]/extractArticle.pl $persdir ".$dir."/".$fname." struct.content pdb String application/x-pdb");
            logger("Flexibility analysis: $GLOBALS[scriptDir]/extractArticle.pl $persdir ".$traj." struct.content pdb String application/x-pdb");
            exec("$GLOBALS[scriptDir]/extractArticle.pl $persdir ".$traj." struct.content pdb String application/x-pdb");
            if ($ftype == 'MD_TrajectoryDCD' or $ftype == 'MD_TrajectoryNetCDF') {
		$req['persistent'] = 1;
                #execWSScript ('fromMD_TrajectoryToMD_TrajectoryBINPOS', $dir."/".$fname, $dir."/$fname.tmp", listParams($opData, $req));
                execWSScript ('fromMD_TrajectoryToMD_TrajectoryBINPOS', $traj, $dir."/$fname.tmp", listParams($opData, $req));
                $ftype = "MD_TrajectoryBINPOS";
                #$fname = "$fname.tmp";
            }
		logger("Going to select objectDetails  WHERE (objectName= $ftype ) and (articleName= coordinates)");
            $rs1 = getRecordSet("SELECT * FROM objectDetails WHERE (objectName='".$ftype."') and (articleName='coordinates')");
            $rsobj = mysql_fetch_array($rs1);
            $ext=$rsobj['fileExt'];
            $type=$rsobj['type'];
            #logger ("Flexibility analysis: $GLOBALS[scriptDir]/extractArticle.pl $persdir ".$dir."/".$fname." coordinates ".$ext." ".$type);
            #system("$GLOBALS[scriptDir]/extractArticle.pl $persdir ".$dir."/".$fname." coordinates ".$ext." ".$type);
            logger ("Flexibility analysis: $GLOBALS[scriptDir]/extractArticle.pl $persdir ".$traj." coordinates ".$ext." ".$type);
            system("$GLOBALS[scriptDir]/extractArticle.pl $persdir ".$traj." coordinates ".$ext." ".$type);
            $trajname = $fname.".".$ext;
            $fulltrajpath = $persdir."/".$trajname;
            if ($ftype == 'MD_TrajectoryXTC') {
                logger ("Converting XTC to CRD: ".$GLOBALS['softDir']."/g_xtc2crd -s $fullstructpath -f $fulltrajpath > $persdir/$fname.crd");
                system($GLOBALS['softDir']."/g_xtc2crd -s $fullstructpath -f $fulltrajpath > $persdir/$fname.crd");
                unlink($fulltrajpath);
                $trajname = $fname.".crd";
                $fulltrajpath = $persdir."/".$trajname;
            }
		logger("File exists? $fullstructpath $fulltrajpath");
           if (file_exists($fullstructpath) && file_exists($fulltrajpath)) {
#                $result = execScript("checkSnapshot.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname']." 16");

		$parmFname = $f['fileTree'][$leaf]['fname'];
		$out = exec("$GLOBALS[scriptDir]/numberOfSnapshots.pl $f[projDir] $parmFname");
		logger("exec: $GLOBALS[scriptDir]/numberOfSnapshots.pl $f[projDir] $parmFname");
                # out = NumSnapshots: 25
                $l = preg_split ("/:/",$out);
                $n = $l[1];
		logger("Number of Snapshots: $n");

                if ($n < "16") {
                    logger("Flexibility analysis: Not enough snapshots to start flexibility analysis - at least 16 snapshots needed");
                    $_SESSION['window'] = 'Error';
                    $_SESSION['error'] = 3;
                    return;
                } else if ($n >= "16") {

			logger("Copying flexServ input files to shared directory...");
			$pdir1 = $GLOBALS['flexServDir']."/".$login;
			$pdir2 = $pdir1."/".$projID;
			if (! file_exists($pdir1)) mkdir($pdir1, 0777, TRUE);
			if (! file_exists($pdir2)) mkdir($pdir2, 0777, TRUE);
			$destFile1 = "$pdir2/$structname";
			$destFile2 = "$pdir2/$trajname";
			#$fullstructpath = "/mmb/homes/tmp/patata";
			#exec("echo \"cp $fullstructpath $destFile1\" > /mmb/homes/tmp/cp.txt; cp $fullstructpath $destFile1 >& /mmb/homes/tmp/cp.log");
			#exec("cp $fullstructpath /mmb/homes/tmp/destFile1");
			$b1 = copy ($fullstructpath,$destFile1);
			logger("Copying file $fullstructpath to $destFile1");
			#exec("cp $fulltrajpath $destFile2");
			#exec("cp $fulltrajpath /mmb/homes/tmp/destFile2");
			$b2 = copy ($fulltrajpath,$destFile2);
			logger("Copying file $fulltrajpath to $destFile2");
			logger("Copied flexServ input files to shared directory: $destFile1, $destFile2");

			logger("http://mmb.pcb.ub.es/FlexServ/analysis2.php?analysisType=MDWeb&user=$login&project=$projID&structure=$structname&trajectory=$trajname");
                ?>
                <script>
                    newWin = window.open('<?php echo "http://mmb.pcb.ub.es/FlexServ/analysis2.php?analysisType=MDWeb&user=$login&project=$projID&structure=$structname&trajectory=$trajname" ?>','<?php echo $leaf ?>flexanal')
                </script>
                <?php
                addLog($_SESSION['userData']['login'], $_SESSION['projectData']['idProject'], "FlexServ analysis requested: ", $f['fileTree'][$leaf]['fname'], 'NULL');
                } else {
                    logger("Flexibility analysis error: ".$result);
                }
            }
            if (file_exists($persdir."/filenames.tmp")) {
#                unlink($persdir."/filenames.tmp");
            }

            break;

	case 'runDNA_MontecarloFromPDBText':

		# Check DNA

		$fname = $f['projDir']."/structure.pdb";
		$onlydna = checkOnlyDNA($fname);

		logger("Montecarlo, DNA: $onlydna");

		$cmd = "perl $GLOBALS[scriptDir]/getSequence.pl $fname";
		$len = exec($cmd);
		$l = preg_split ("/ /",$len);
		$n = $l[1];
		$seq = $l[2];
		if (! (preg_match("/^\([ACGT]+\)$/",$seq))){
			logger("ERROR: Montecarlo with non-standard nucleic bases ($seq) (maybe RNA?)");
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 14;
                        $_SESSION['errorMessage'] = $seq;
                        return;
		}

		logger("SEQUENCE: $n");
		$nsnaps = $req['prm']['nsnaps'];
		if (! (preg_match("/\d+/",$nsnaps))){
			logger("ERROR: Montecarlo with non-decimal input nof snapshots ($nsnaps)");
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 21;
                        $_SESSION['errorMessage'] = $nsnaps;
                        return;
		}
		if ($nsnaps < 1 or $nsnaps > 1000){
			logger("ERROR: Too many snapshots in CG Montecarlo Dynamics.");
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 19;
                        $_SESSION['errorMessage'] = $nsnaps;
                        return;
		}

		if ($_FILES['StiffnessMATS']['tmp_name']) {
			$stfile = $_FILES['StiffnessMATS']['tmp_name'];
			$outfile = "$f[projDir]/uploadedStiffnessMatrices.dat";
			move_uploaded_file( $stfile,$outfile);
			$error = checkStiffnessMatrices("$f[projDir]/uploadedStiffnessMatrices.dat");
			if($error){
				logger("Error Stiffness Matrices: $error");
				$_SESSION['window'] = 'Error';
				$_SESSION['error'] = 26;
				return;
			}
		}

		$storestep = 10;
		$dinmov = $n - 1;
		$stepratio = $storestep * $nsnaps / $dinmov;

		if($stfile){
			$pid = execAsyncScript("montecarloRun.pl", $fname, $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog","nsnaps ".$stepratio." stiffnessMATS ".$f['projDir']."/uploadedStiffnessMatrices.dat");
		}
		else{
			$pid = execAsyncScript("montecarloRun.pl", $fname, $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog","nsnaps ".$stepratio);
		}
	
		$req['cg'] = "CG-DNAlive";

		break;

	case 'runDNA_WLCFromPDBText':

		# Check DNA
		$fname = $f['projDir']."/structure.pdb";
		$onlydna = checkOnlyDNA($fname);

		# Getting WLC Resolution (Number of Base Pairs x Bead)
		$finput = $f['projDir']."/input.in";
		$cmd = "sed -n 10p $finput";
		$line = exec($cmd);
		$l = preg_split ("/ /",$line);
		$nbeads = $l[3];
		logger("WLC Model, DNA: $onlydna, Nbeads: $nbeads");

		$nsnaps = $req['prm']['nsnaps'];
		if (! (preg_match("/\d+/",$nsnaps))){
			logger("ERROR: WLC with non-decimal input nof snapshots ($nsnaps)");
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 21;
                        $_SESSION['errorMessage'] = "WLC Number of Snapshots Parameter: $nsnaps";
                        return;
		}
		if ($nsnaps < 1 or $nsnaps > 10000){
			logger("ERROR: Too many snapshots in CG WLC Dynamics.");
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 20;
                        $_SESSION['errorMessage'] = $nsnaps;
                        return;
		}

		$temp = $req['prm']['temp'];
		if (! (preg_match("/\d+/",$temp))){
			logger("ERROR: WLC with non-decimal input temp ($temp)");
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 21;
                        $_SESSION['errorMessage'] = "WLC Temperature Parameter: $temp";
                        return;
		}

		$salt = $req['prm']['salt'];
		if (! (preg_match("/\d+/",$salt))){
			logger("ERROR: WLC with non-decimal input salt concentration ($salt)");
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 21;
                        $_SESSION['errorMessage'] = "WLC Salt Concentration Parameter: $salt";
                        return;
		}

		$autoCharge = $req['prm']['autoCharge'];

		if($autoCharge == 'auto'){
			$charge = 'auto';
		}
		else{
			$charge = $req['prm']['charge'];
			if (! (preg_match("/\d+/",$charge))){
				logger("ERROR: WLC with non-decimal input charge ($charge)");
                        	$_SESSION['window'] = 'Error';
	                        $_SESSION['error'] = 21;
        	                $_SESSION['errorMessage'] = "WLC Charge Parameter: $charge";
                	        return;
			}
			else if ($charge > 0 or $charge < -10){
				logger("ERROR: WLC Charge with invalid number ($charge)");
                        	$_SESSION['window'] = 'Error';
	                        $_SESSION['error'] = 21;
        	                $_SESSION['errorMessage'] = "WLC Charge Parameter: $charge";
                	        return;
			}
			# Charge given is for Base Pair. Getting BEAD charge:
			$charge = $nbeads * $charge;
		}

		$bend = $req['prm']['bend'];
		if (! (preg_match("/\d+/",$bend))){
			logger("ERROR: WLC with non-decimal input bending persistence ($bend)");
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 21;
                        $_SESSION['errorMessage'] = "WLC Bend Parameter: $bend";
                        return;
		}
		else if ($bend < 0 or $bend > 1000){
			logger("ERROR: WLC Bend with invalid number ($bend)");
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 21;
                        $_SESSION['errorMessage'] = "WLC Bend Parameter: $bend";
                        return;
		}

		$torsion = $req['prm']['torsion'];
		if (! (preg_match("/\d+/",$torsion))){
			logger("ERROR: WLC with non-decimal input torsion persistence ($torsion)");
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 21;
                        $_SESSION['errorMessage'] = "WLC Torsion Parameter: $torsion";
                        return;
		}
		else if ($torsion < 0 or $torsion > 1000){
			logger("ERROR: WLC Bend with invalid number ($torsion)");
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 21;
                        $_SESSION['errorMessage'] = "WLC Torsion Parameter: $torsion";
                        return;
		}

		$pid = execAsyncScript("WLCRun.pl", $fname, $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog","nsnaps ".$nsnaps." temp ".$temp." salt ".$salt." charge ".$charge." bend ".$bend." torsion ".$torsion);

		$req['cg'] = "CG-WLC";
	
		break;

	case 'CG_WLCAnalysis':
	case 'CG_MontecarloAnalysis':

		logger("Coarse-Grained Nucleic Acid Analysis: $req[idOperacio]");

		$traj = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
		$global = $req['prm']['global'];

		$fname = $f['projDir']."/structure.pdb";
		$codes = getAtRes($fname);
		$distAtomPairs = '';

		if(!$global){

			$distAtomPairs = implode(" ",$req['prm']['atomDistances']);

			# Check existence of Base numbers and length of the Segment. 
			#$res1 = $distAtomPairs[0];
			#$res2 = $distAtomPairs[1];
			$res1 = $req['prm']['atomDistances']['base1'];
			$res2 = $req['prm']['atomDistances']['base2'];
			$offset = $req['prm']['atomDistances']['offset'];
			$res1 = preg_replace("/\@\s+/","",$res1);
			$res2 = preg_replace("/\@\s+/","",$res2);

			logger("Checking Segment -$res1 to $res2- in CG Flexibility Analysis");

			foreach (array_keys ($codes) as $code){
				$arr = preg_split("/\@/",$code);
				$code = $arr[0];
				$resCodes[$code]=1;
			}
			if(!$resCodes[$res1]){
				logger("$res1 not found...");
                        	$_SESSION['window'] = 'Error';
	      		        $_SESSION['error'] = 17;
        	      		$_SESSION['errorMessage'] = $res1;
	                        return;
			}
			if(!$resCodes[$res2]){
				logger("$res2 not found...");
                	        $_SESSION['window'] = 'Error';
      		        	$_SESSION['error'] = 17;
	              		$_SESSION['errorMessage'] = $res2;
        	                return;
			}

			# Checking Local Analysis Length. Do we really need a limit?
			# Segment Length
			#$segLeng = ($res2 - $res1) / $offset;
			#$maxLen = 100;
			#if ($req['idOperacio'] == 'CG_WLCAnalysis'){
			#	$maxLen = $maxLen * 4;	# WLC -> 1 bead every 4 base pairs.
			#}
			#if($segLeng > $maxLen){
        	        #        $_SESSION['window'] = 'Error';
      			#        $_SESSION['error'] = 18;
              		#	$_SESSION['errorMessage'] = "From $res1 to $res2 (with $offset offset) there is $segLeng bases";
	                #        return;
			#}
		}

		$pid = execAsyncScript("nucleicAcidsRun.pl", $traj, $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog",$req['idOperacio']." ".$distAtomPairs);

		break;

	case 'nucleicAcidAnalysis':

		# Check number of Snapshots
		$parmFname = $f['fileTree'][$leaf]['fname'];
		$out = exec("$GLOBALS[scriptDir]/numberOfSnapshots.pl $f[projDir] $parmFname");
		logger("exec: $GLOBALS[scriptDir]/numberOfSnapshots.pl $f[projDir] $parmFname");
                # out = NumSnapshots: 25
                $l = preg_split ("/:/",$out);
                $n = $l[1];
		logger("Number of Snapshots: $n");

                if ($n < "10") {
                    logger("Flexibility analysis: Not enough snapshots to start flexibility analysis - at least 10 snapshots needed");
                    $_SESSION['window'] = 'Error';
                    $_SESSION['error'] = 3;
		    $_SESSION['errorMessage'] = $n;
                    return;
		}

                if ($n < "51" && $req['prm']['analysis'] == 'Curves'){
                        ?>
                        <script>
                        alert("Sorry, NAFlex needs at least 51 snapshots to compute helical parameter analysis.");
                        </script>
                        <?php
                        return;
                }

		# Check DNA

		#$fname = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
		$fname = $f['projDir']."/structure.pdb";
		$onlydna = checkOnlyDNA($fname);

		logger("Nucleic Acid Analysis, DNA: $onlydna");

		$traj = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
		$cmd = "perl $GLOBALS[scriptDir]/checkWatsInTraj.pl $traj";
		$wats = exec($cmd);
		if($wats){
			logger("Nucleic Acid Analysis of a Solvated Trajectory!! -$wats-");
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 15;
                        return;
		}

		if($onlydna == 'Prot-Nuc'){			
			$outTraj = $f[projDir]."/f".$req['newId'].".NA.moby";
			logger("Prot-Nuc complex, trying to strip nucleic acid from protein...");
			logger("$GLOBALS[scriptDir]/stripDNA.pl $traj $outTraj");
			$out = exec("$GLOBALS[scriptDir]/stripDNA.pl $traj $outTraj");
			$traj = $outTraj;
		}
                else if ($onlydna == 'Prot') {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 13;
                        return;
                }

		# Checking DNA Sequence pairing
		$cmd = "perl $GLOBALS[scriptDir]/runCheckPDB_DNA.pl $fname";
		$outSeqs = exec($cmd);
                $seqs = explode(' ',$outSeqs);
		$seq1 = $seqs[1];
		$seq2 = $seqs[3];
		$l1 = strlen($seq1);
		$l2 = strlen($seq2);
		logger("DNA Check SEQS: SEQ1: $seqs[1] ($l1), SEQ2: $seqs[3] ($l2)");
                if ($l1 != $l2) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 22;
                        $_SESSION['errorMessage'] = "Strands length differ ($l1 vs $l2)";
                        return;
                }
		$unpaired = unpairedDNA($seq1,$seq2);
		if ($unpaired){
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 22;
                        $_SESSION['errorMessage'] = "Unpaired Strand Sequences ($seq1 vs $seq2)";
                        return;
		}		

		# Check existence of Atom Pairs (in Distance Analysis)
		if($req['prm']['analysis'] == 'Distances'){
			$distAtomPairs = implode(" ",$req['prm']['atomDistances']);
			$codes = getAtRes($fname);

			foreach (array_values ($req['prm']['atomDistances']) as $code){
				$code = preg_replace("/\\\'/","'",$code);
				logger("Checking Code $code in Distances Analysis");
				if(!$codes[$code]){
					logger("$code not found...");
		                        $_SESSION['window'] = 'Error';
                		        $_SESSION['error'] = 16;
                        		$_SESSION['errorMessage'] = $code;
		                        return;
				}
			}
			$params = $distAtomPairs;
		}

		# Check NOE intensities parameters (in NOE Analysis)
		if($req['prm']['analysis'] == 'Nmr_NOEs'){
			$w = $req['prm']['noeIntParams']['w'];
			$tc = $req['prm']['noeIntParams']['tc'];
			$tm = $req['prm']['noeIntParams']['tm'];

			logger("Input NOE Intensities Parameters: w: $w, tc: $tc, tm: $tm");
			if($tc < 2 || $tc > 4){
	                        $_SESSION['window'] = 'Error';
               		        $_SESSION['error'] = 22;
                       		$_SESSION['errorMessage'] = $tc;
	                        return;
			}
			if($tm < 50 || $tm > 300){
	                        $_SESSION['window'] = 'Error';
               		        $_SESSION['error'] = 23;
                       		$_SESSION['errorMessage'] = $tm;
	                        return;
			}
			$params = $w." ".$tc." ".$tm;
		}

		# Check modified nucleotides in Stacking  (in Distance Analysis)
		$modDNA = (preg_match("/X/",$seq1) or preg_match("/X/",$seq2));
		if($req['prm']['analysis'] == 'Stacking' and $modDNA){
			$_SESSION['window'] = 'Error';
                	$_SESSION['error'] = 23;
                        $_SESSION['errorMessage'] = "$seq1";
		        return;
		}

		$pid = execAsyncScript("nucleicAcidsRun.pl", $traj, $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog",$req['prm']['analysis']." ".$params);
		break;

        case 'zipTrajectory':

                $fname = $f['projDir']."/structure.pdb";
                $onlydna = checkOnlyDNA($fname);
		$mask = $req['prm']['mask'];

		if($onlydna == "Nuc" && $mask == 1){
			logger("ERROR: C-alpha RMSd with Nucleic Acid trajectory");

                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 24;
                        return;
		}

                logger("Zip Trajectory, DNA: $onlydna");

			$pid = execAsyncScript("pcazip.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", listParams($opData, $req));  //mdtype = 1 -> zip

# 	WARNING: Now there is NO checking about the size of the input traj, pcazip.pl just explodes if the traj has more than 1000 atoms, but we don't show any alert!
#
#            if (substr($res[0], 0, 3) == "BIG") {
#                $noLeaf=true;
/*                ?>
                <script>
                    alert("Error: compressing trajectories with more than 1000 atoms/frame is not permitted\n\nYou can try reducing your structure by applying a mask");
                </script>
                <?
*/
#            }
            break;
        case 'unzipTrajectory':
#            execScript("pcazip.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", 0);  //mdtype = 0 -> unzip
            $pid = execAsyncScript("pcazip.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby",$f['projDir']."/".$f['fileTree'][$leaf]['fname'].".moby.asyncLog", listParams($opData, $req));  //mdtype = 0 -> unzip
            break;

#        case 'optimizeStructureFromGROMACS_1':
#        case 'optimizeStructureFromGROMACS_2':
#        case 'optimizeStructureFromGROMACS_3':
#        case 'optimizeStructureFromAMBER_1':
#        case 'optimizeStructureFromAMBER_2':
#        case 'optimizeStructureFromNAMD_1':
#        case 'optimizeStructureFromNAMD_2':
#		case (preg_match("optimizeStructureFrom(GROMACS|AMBER|NAMD)_[1-3]",$req['idOperacio'])):
		case (preg_match("/^optimizeStructureFrom/",$req['idOperacio'])? true : false) :
            $pid = execAsyncScript("optimizeStructureAsync.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", listParams($opData, $req));
            break;

#        case 'runMDFromGROMACS1':
#        case 'runMDFromGROMACS2':
#        case 'runMDFromGROMACS3':
#        case 'runMDFromGROMACS4':
#        case 'runMDFromGROMACS5':
#        case 'runMDFromGROMACS6':
#        case 'runMDFromAMBER1':
#        case 'runMDFromAMBER2':
#        case 'runMDFromAMBER3':
#        case 'runMDFromAMBER4':
#        case 'runMDFromAMBER5':
#        case 'runMDFromAMBER6':
#        case 'runMDFromNAMD1':
#        case 'runMDFromNAMD2':
#        case 'runMDFromNAMD3':
#        case 'runMDFromNAMD4':
#        case 'runMDFromNAMD5':
#        case 'runMDFromNAMD6':
#		case (preg_match("runMDFrom(GROMACS|AMBER|NAMD)[1-6]",$req['idOperacio'])):
		case (preg_match("/^runMDFrom/",$req['idOperacio'])? true : false) :
			if($req['conf']){
				logger("Running runMDConfig.pl...");
	            $pid = execAsyncScript("runMDConfig.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby.config", $f[projDir]."/f".$req['newId'].".moby.asyncLog", listParams($opData, $req));
			}
			else{

 				$time = $req['prm']['time'];

        		        if ( $time > 500 ) {
		                        $_SESSION['window'] = 'Error';
		                        $_SESSION['error'] = 7;
		                        $_SESSION['errorMessage'] = $time;
		                        return;
		                }

				logger("Running runMDAsync.pl...");
	            $pid = execAsyncScript("runMDAsync.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", listParams($opData, $req));
			}
            break;

#	case 'convertToPDBs':
#	It doesn't work because persistence X-ref is not designed to work with Moby Collections...
#
#		$req['prm']['persistent'] = 'true';
#
#		$ws = $opData['WS']."_async";
#                logger("Asynchronous Service: $ws");
#
#		$pid = execWSScriptAsync ($ws, $f['projDir']."/".$f['fileTree'][$leaf]['fname'],
#                	$f['projDir']."/f".$req['newId'].".moby", $f['projDir']."/f".$req['newId'].".asyncLog", listParams($opData, $req));
#
#		break;

         case 'mutateResidueFromPDBText':
	
		$fname = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
		$res = $req['prm']['resid'];
		$chain = $req['prm']['chain'];
		$ok = checkResidueToMutate ($fname,$res,$chain);
		$txt = "$res $chain";

		if(!$ok){
		  	$_SESSION['window'] = 'Error';
		  	$_SESSION['error'] = 4;
			$_SESSION['errorMessage'] = $txt;
                        return;
		}

		$ws = $opData['WS'];

                $pid = execWSScriptAsync ($ws, $f['projDir']."/".$f['fileTree'][$leaf]['fname'],
                    $f['projDir']."/f".$req['newId'].".moby", $f['projDir']."/f".$req['newId'].".asyncLog", listParams($opData, $req));

                break;

         case 'runBrownianMDFromPDBText':

		$fname = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
		$onlydna = checkOnlyDNA($fname);

		logger("runBrownian: DNA: $onlydna");

                #if ($onlydna) {
                if ($onlydna == 'Nuc') {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 5;
			$_SESSION['errorMessage'] = "Brownian Molecular Dynamics";
                        return;
                }

                $numResidues = checkNumResidues($fname);
                logger("runBrownian: $numResidues residues");
                if ($numResidues > $GLOBALS[maxResCG]) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 11;
                        $_SESSION['errorMessage'] = "Brownian Molecular Dynamics with $numResidues residues";
                        return;
                }

//                $time = $req['prm']['time'];            # in ps
//                $timestep = $req['prm']['timestep'];                # in ps

//                $steps = $time/$timestep;

//                $req['prm']['time'] = $steps;           # steps

//                logger("Coarse-Grained Brownian, Time: $time ps, DT: $dt ps, Steps: $steps");

                $old_dt = $req['prm']['dt'];            # in ps
                $new_dt = $old_dt / 1000000000000;      # in seconds
                $req['prm']['dt'] = $new_dt;
                logger("Coarse-Grained MD DeltaT: $old_dt (old), $new_dt (new)");
	
		// Persistent output: CRD MD_Trajectory.
		$req['prm']['persistent'] = 1;

                $ws = $opData['WS']."_async";
                logger("Asynchronous Service: $ws");

                $pid = execWSScriptAsync ($ws, $f['projDir']."/".$f['fileTree'][$leaf]['fname'],
                    $f['projDir']."/f".$req['newId'].".moby", $f['projDir']."/f".$req['newId'].".asyncLog", listParams($opData, $req));

                break;

         case 'runDiscreteMDFromPDBText':

                $fname = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
                $onlydna = checkOnlyDNA($fname);

                logger("runDiscrete: DNA: $onlydna");

                #if ($onlydna) {
                if ($onlydna == 'Nuc') {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 5;
                        $_SESSION['errorMessage'] = "Discrete Molecular Dynamics";
                        return;
                }
		
		$numResidues = checkNumResidues($fname);
                logger("runDiscrete: $numResidues residues");
                if ($numResidues > $GLOBALS[maxResCG]) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 11;
                        $_SESSION['errorMessage'] = "Discrete Molecular Dynamics with $numResidues residues";
                        return;
                }

		$time = $req['prm']['time'];		# in ps
		$dt = $req['prm']['dt'];		# in ps

		$steps = $time/$dt;

		$req['prm']['time'] = $steps;		# steps

		// Persistent output: CRD MD_Trajectory.
		$req['prm']['persistent'] = 1;

		logger("Coarse-Grained Discrete, Time: $time ps, DT: $dt ps, Steps: $steps");

		$old_dt = $req['prm']['dt'];		# in ps
		$new_dt = $old_dt / 1000000000000;   	# in seconds
		$req['prm']['dt'] = $new_dt;
		logger("Coarse-Grained MD DeltaT: $old_dt (old), $new_dt (new)");

		$ws = $opData['WS']."_async";
		logger("Asynchronous Service: $ws");

                $pid = execWSScriptAsync ($ws, $f['projDir']."/".$f['fileTree'][$leaf]['fname'],$f['projDir']."/f".$req['newId'].".moby", $f['projDir']."/f".$req['newId'].".asyncLog", listParams($opData, $req));

		break;
         case 'runDiscreteMD_AA_FromPDBText':

                $fname = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
                $onlydna = checkOnlyDNA($fname);

                logger("runDiscrete AllAtoms: DNA: $onlydna");

                #if ($onlydna) {
                if ($onlydna == 'Nuc') {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 5;
                        $_SESSION['errorMessage'] = "Discrete Molecular Dynamics";
                        return;
                }

                $numResidues = checkNumResidues($fname);
                logger("runDiscrete: $numResidues residues");
                if ($numResidues > $GLOBALS[maxResCG]) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 11;
                        $_SESSION['errorMessage'] = "Discrete Molecular Dynamics with $numResidues residues";
                        return;
                }

                $time = $req['prm']['time'];            # Total time in fs.
		unset($req['prm']['time']);

                $dt = $req['prm']['tsnap'];             # Period between each time the program writes the structure (in fs).

                $nblocs = $time/$dt;

                $req['prm']['nbloc'] = $nblocs;           # nblocs * tsnap = Total Time

                // Persistent output: CRD MD_Trajectory.
                $req['prm']['persistent'] = 1;

                logger("Coarse-Grained Discrete_AA, Time: $time fs, Tsnap: $tsnap fs, Nbloc: $nbloc");

                $pid = execAsyncScript("DMDWorkflow.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']." ".listParams($opData,$req));

                break;

         case 'runDiscreteMD_DIMS_FromPDBText':

                $fname = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
                $onlydna = checkOnlyDNA($fname);

                logger("runDiscreteDIMS: DNA: $onlydna");

                #if ($onlydna) {
                if ($onlydna == 'Nuc') {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 5;
                        $_SESSION['errorMessage'] = "Discrete Molecular Dynamics";
                        return;
                }

                $numResidues = checkNumResidues($fname);
                logger("runDiscreteDIMS: $numResidues residues");
                if ($numResidues > $GLOBALS[maxResCG]) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 11;
                        $_SESSION['errorMessage'] = "Discrete Molecular Dynamics with $numResidues residues";
                        return;
                }

                $time = $req['prm']['time'];            # Total time in fs.
                unset($req['prm']['time']);

                $dt = $req['prm']['tsnap'];             # Period between each time the program writes the structure (in fs).

                $nblocs = $time/$dt;

                $req['prm']['nbloc'] = $nblocs;           # nblocs * tsnap = Total Time

                // Persistent output: CRD MD_Trajectory.
                $req['prm']['persistent'] = 1;

                logger("Coarse-Grained Discrete_DIMS, Time: $time fs, Tsnap: $tsnap fs, Nbloc: $nbloc");

                $pid = execAsyncScript("DIMSWorkflow.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']." ".listParams($opData,$req));

                break;

#        case 'runBrownianMDFromPDBText':
#            $cg_mode = 0;
#        case 'runDiscreteMDFromPDBText':
#            if (! isset($cg_mode))
#                $cg_mode = 1;

        case 'runNormalModeAnalysisFromPDBText':

                $fname = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
                $onlydna = checkOnlyDNA($fname);
                logger("runNMA: DNA: $onlydna");

                #if ($onlydna) {
                if ($onlydna == 'Nuc') {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 5;
                        $_SESSION['errorMessage'] = "Normal Mode Analysis";
                        return;
                }

                $numResidues = checkNumResidues($fname);
                logger("runDiscrete: $numResidues residues");
                if ($numResidues > $GLOBALS[maxResCG]) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 11;
                        $_SESSION['errorMessage'] = "Normal Mode Analysis with $numResidues residues";
                        return;
                }

		// Persistent output: CRD MD_Trajectory.
		$req['prm']['persistent'] = 1;

                $ws = $opData['WS']."_async";

                $pid = execWSScriptAsync ($ws, $f['projDir']."/".$f['fileTree'][$leaf]['fname'],
                    $f['projDir']."/f".$req['newId'].".moby", $f['projDir']."/f".$req['newId'].".asyncLog", listParams($opData, $req));

		break;

#            if (! isset($cg_mode))
#                $cg_mode = 2;
#            $cg = $cg_mode;
#            unset($cg_mode);
#            execAsyncWSScript("runCGMD.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $login, $projID, $cg, listParams($opData, $req));
#            break;

        case 'protonateHistidines':
            $pid = execAsyncScript("protonateHistidinesAsync.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", listParams($opData, $req));
            break;

        case 'titrateStructure':
            $pid = execAsyncScript("titrateStructureAsync.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", listParams($opData, $req));
            break;

     case 'solvateStructureFromAMBER_MD_Structure':
            $pid = execAsyncScript("solvateWithLigands.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']." ".listParams($opData, $req));
            break;

        case 'protonateIonizableResidues':
            $pid = execAsyncScript("protonateIonizableResAsync.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", listParams($opData, $req));
            break;

        case 'GromacsWorkflow':
		$gmxFF = $req['prm']['forcefield'];
		$errFF = $GLOBALS['gmxff'][$gmxFF];

		$water = $req['prm']['waterType'];

                if (checkIfLigands($f['projDir']) && ($gmxFF>7 && $gmxFF!=16) ) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 6;
                        $_SESSION['errorMessage'] = $errFF;
                        return;
                }

                $fname = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
		$dna = checkIfDNA($fname);
                if (checkIfDNA($fname) && ($gmxFF>9 && $gmxFF!=16) ) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 10;
                        $_SESSION['errorMessage'] = $errFF;
                        return;
                }

		$gmx_script =  "GROMACSWorkflow.pl";
		if ($dna) $gmx_script =  "GROMACSWorkflow_DNA.pl";

            $pid = execAsyncScript($gmx_script, $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']." ".$req['prm']['forcefield']." ".$water);
            break;

       case 'fixSideChains':
            $pid = execAsyncScript("fixSideChains.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']);
            break;

        case 'AmberWorkflow':
            $ambFF = $req['prm']['forcefield'];

                $fname = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
		$dna = checkIfDNA($fname);

            $pid = execAsyncScript("NAMDWorkflow.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']." amber ".$ambFF." ".$dna);
            break;

        case 'ABCWorkflow':

                $fname = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
		$onlydna = checkOnlyDNA($fname);

		$dna = 1;
                if ($onlydna != 'Nuc') {
			$dna = 0;
                }

            $pid = execAsyncScript("ABCWorkflow.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']." amber ".$dna);
            break;

        case 'NamdWorkflow':

		if (checkIfLigands($f['projDir'])) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 6;
                        $_SESSION['errorMessage'] = 'Charmm';
                        return;
        	}

	

            $pid = execAsyncScript("NAMDWorkflow.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']." namd");
            break;

        case 'GromacsWorkflowFULL':

		$gmxFF = $req['prm']['forcefield'];
		$errFF = $GLOBALS['gmxff'][$gmxFF];

                $water = $req['prm']['waterType'];

                if (checkIfLigands($f['projDir']) && ($gmxFF>7 && $gmxFF!=16) ) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 6;
                        $_SESSION['errorMessage'] = $errFF;
                        return;
                }
                $fname = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
		$dna = checkIfDNA($fname);
                if (checkIfDNA($fname) && ($gmxFF>9 && $gmxFF!=16) ) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 10;
                        $_SESSION['errorMessage'] = $errFF;
                        return;
                }

		$gmx_script =  "GROMACSWorkflowFull.pl";
		if ($dna) $gmx_script =  "GROMACSWorkflowFull_DNA.pl";

            $pid = execAsyncScript($gmx_script, $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']." ".$req['prm']['forcefield']." ".$water);
            break;

        case 'AmberWorkflowFULL':
            $ambFF = $req['prm']['forcefield'];

                $fname = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
		$dna = checkIfDNA($fname);

            $pid = execAsyncScript("NAMDWorkflowFull.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']." amber ".$ambFF." ".$dna);
            break;

        case 'NamdWorkflowFULL':

                if (checkIfLigands($f['projDir'])) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 6;
                        $_SESSION['errorMessage'] = 'Charmm';
                        return;
                }

            $pid = execAsyncScript("NAMDWorkflowFull.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']." namd");
            break;

        case 'GromacsWorkflowSolv':

		$gmxFF = $req['prm']['forcefield'];
		$errFF = $GLOBALS['gmxff'][$gmxFF];

                $water = $req['prm']['waterType'];

                if (checkIfLigands($f['projDir']) && ($gmxFF>7 && $gmxFF!=16) ) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 6;
                        $_SESSION['errorMessage'] = $errFF;
                        return;
                }
                $fname = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
		$dna = checkIfDNA($fname);
                if (checkIfDNA($fname) && ($gmxFF>9 && $gmxFF!=16) ) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 10;
                        $_SESSION['errorMessage'] = $errFF;
                        return;
                }

		$gmx_script =  "GROMACSWorkflowSolv.pl";
		if ($dna) $gmx_script =  "GROMACSWorkflowSolv_DNA.pl";

            $pid = execAsyncScript($gmx_script, $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']." ".$req['prm']['forcefield']." ".$water);
            break;

        case 'AmberWorkflowSolv':
            $ambFF = $req['prm']['forcefield'];

                $fname = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
		$dna = checkIfDNA($fname);

            $pid = execAsyncScript("NAMDWorkflowSolv.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']." amber ".$ambFF." ".$dna);
            break;

        case 'NamdWorkflowSolv':

                if (checkIfLigands($f['projDir'])) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 6;
                        $_SESSION['errorMessage'] = 'Charmm';
                        return;
                }

            $pid = execAsyncScript("NAMDWorkflowSolv.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog",  $f['projDir']." namd");
            break;

        case 'GromacsAdvancedEq':
            $pid = execAsyncScript("GROMACS_AdvancedEq.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog");
            break;

        case 'NamdAdvancedEq':
            $pid = execAsyncScript("NAMD_AdvancedEq.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", "namd");
            break;

        case 'AmberAdvancedEq':
            $pid = execAsyncScript("NAMD_AdvancedEq.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", "amber");
            break;

        case 'NamdGenTop':

                if (checkIfLigands($f['projDir'])) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 6;
                        $_SESSION['errorMessage'] = 'Charmm';
                        return;
                }

            $pid = execAsyncScript("NAMDGenTop.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']." namd");
            break;

		case 'AmberGenTop':
                $ambFF = $req['prm']['forcefield'];

            $pid = execAsyncScript("NAMDGenTop.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']." amber ".$ambFF);
            break;

        case 'GromacsGenTop':

		$gmxFF = $req['prm']['forcefield'];
		$errFF = $GLOBALS['gmxff'][$gmxFF];

                $water = $req['prm']['waterType'];

                if (checkIfLigands($f['projDir']) && ($gmxFF>7 && $gmxFF!=16) ) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 6;
                        $_SESSION['errorMessage'] = $errFF;
                        return;
                }
                $fname = $f['projDir']."/".$f['fileTree'][$leaf]['fname'];
		$dna = checkIfDNA($fname);
                if (checkIfDNA($fname) && ($gmxFF>9 && $gmxFF!=16) ) {
                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 10;
                        $_SESSION['errorMessage'] = $errFF;
                        return;
                }

		$gmx_script =  "GROMACSGenTop.pl";
		if ($dna) $gmx_script =  "GROMACSGenTop_DNA.pl";

            $pid = execAsyncScript($gmx_script, $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", $f['projDir']." ".$req['prm']['forcefield']." ".$water);
            break;

         case( preg_match("/^convert/",$req['idOperacio'])? true : false ):

		$ws = $opData['WS'];
$p = $req['idOperacio'];
logger("Convert TO: $ws, $p");

		if($req['idOperacio'] != "convertToPDBs")
			$req['prm']['persistent'] = 1;

		$pid = execWSScriptAsync ($ws, $f['projDir']."/".$f['fileTree'][$leaf]['fname'],
                    $f['projDir']."/f".$req['newId'].".moby", $f['projDir']."/f".$req['newId'].".asyncLog", listParams($opData, $req));

            	break;

         case 'cleanPDB':

                $ws = $opData['WS'];

                if(!$req['prm']['hydrogens'])
                        $req['prm']['hydrogens'] = 'false';
                if(!$req['prm']['ligands'])
                        $req['prm']['ligands'] = 'false';
                if(!$req['prm']['waters'])
                        $req['prm']['waters'] = 'false';

                $pid = execWSScriptAsync ($ws, $f['projDir']."/".$f['fileTree'][$leaf]['fname'],
                    $f['projDir']."/f".$req['newId'].".moby", $f['projDir']."/f".$req['newId'].".asyncLog", listParams($opData, $req));

                break;

#        case 'getDryTrajectory':
	 case( preg_match("/^getS/",$req['idOperacio'])? true : false ):

                $ws = $opData['WS']."_async";

		$snp = $req['prm']['snapshotNumber'];
		$snpStop = $req['prm']['snapshotStop'];

		$file = $f['fileTree'][$leaf]['fname'];
		logger("$GLOBALS[scriptDir]/numberOfSnapshots.pl $f[projDir] $file");
		$out = exec("$GLOBALS[scriptDir]/numberOfSnapshots.pl $f[projDir] $file");
		# out = NumSnapshots: 25
		$l = preg_split ("/:/",$out);
		$n = $l[1];

		logger("Number of snapshots: $n ( < $snp ?? )");

		if($n < $snp || $n < $snpStop){
			logger("ERROR: Number of snapshots: $n < $snp");

                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 8;
                        $_SESSION['errorMessage'] = $n;
                        return;
		}

		if( preg_match ("/^getSlice/",$req['idOperacio']) )
                        $req['prm']['persistent'] = 1;

            $pid = execWSScriptAsync ($ws, $f['projDir']."/".$f['fileTree'][$leaf]['fname'],
                    $f['projDir']."/f".$req['newId'].".moby", $f['projDir']."/f".$req['newId'].".asyncLog", listParams($opData, $req));

            break;

        case 'RMSd':

		# Check DNA

		$fname = $f['projDir']."/structure.pdb";
		$onlydna = checkOnlyDNA($fname);
		$mask = $req['prm']['mask'];

		logger("RMSd Analysis, DNA: $onlydna");

		if($onlydna == "Nuc" && $mask == 1){
			logger("ERROR: C-alpha RMSd with Nucleic Acid trajectory");

                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 24;
                        return;
		}

            $pid = execAsyncScript("getRMSd.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", listParams($opData, $req));
            break;

        case 'RMSdPerResidue':
            $pid = execAsyncScript("getRMSdPerResidue.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f[projDir]."/f".$req['newId'].".moby", $f[projDir]."/f".$req['newId'].".moby.asyncLog", listParams($opData, $req));
            break;

        case 'addPDB':
            if ($req['pdbcode']) {
                $pdb = $req['pdbcode'];
                $rs = getRecordSet("SELECT * FROM uniprot.uniprot WHERE acNum='$pdb' OR swpId='$pdb'");
                $rsF = mysql_fetch_array($rs);
                $seq = $rsF['seq'];
                if ($seq) {
                    $seq = preg_replace('/[[:space:]]/', '', $seq);
                    $pdb = searchSimilarProtein ($seq);
                }
            } else {
                move_uploaded_file($_FILES['structure']['tmp_name'],$f['projDir']."/tmp.pdb");
                //$pdb = searchSimilarProtein ("", $f['projDir']."/tmp.pdb");
                //if (!$pdb) {
                $pdb = $f['projDir']."/tmp.pdb";
                //}
            }
            execScript ("addPDBFromId.pl", $f['projDir']."/".$f['fileTree'][$leaf]['fname'], $f['projDir']."/f".$req['newId'].".moby", $pdb);
            unlink($f['projDir']."/tmp.pdb");
            break;
        
        case 'getBfactor':

		# Check DNA

		$fname = $f['projDir']."/structure.pdb";
		$onlydna = checkOnlyDNA($fname);
		$mask = $req['prm']['mask'];

		logger("Bfactor Analysis, DNA: $onlydna");

		if($onlydna == "Nuc" && $mask == 1){
			logger("ERROR: C-alpha Bfactor with Nucleic Acid trajectory");

                        $_SESSION['window'] = 'Error';
                        $_SESSION['error'] = 24;
                        return;
		}

        default:
#            execWSScript ($opData['WS'], $f['projDir']."/".$f['fileTree'][$leaf]['fname'],
#                $f['projDir']."/f".$req['newId'].".moby", listParams($opData, $req));

			# Finding out if the WS have an asynchronous implementation.
            $serviceID=getWSIdFromName($opData['WS']);
			$rs = getRecord('MobyLiteDB.Service','idService',$serviceID, 'T');
		    $async = $rs['async'];

			$ws = $opData['WS'];
			if ($async){
				$ws = $opData['WS']."_async";
				logger("Asynchronous Service: $ws");
			}

	        $pid = execWSScriptAsync ($ws, $f['projDir']."/".$f['fileTree'][$leaf]['fname'],
	            $f['projDir']."/f".$req['newId'].".moby", $f['projDir']."/f".$req['newId'].".asyncLog", listParams($opData, $req));


    }//end switch
    if ($noLeaf==false) {
        if (checkDiskUsage()) {
            $_SESSION['window'] = 'Error';
            $_SESSION['error'] = 2;
            return;
        } else {
            addNewLeaf ($f, $leaf, $req, $opData, $pid);
        }
    }
}

function listParams ($opData, $req) {
	$txt = "";
    $f = $req['prm'];
    $nprm=1;
    $projDir = $_SESSION['projectData']['projDir'];
    foreach ($opData['input'] as $k) {
        $artname = $k['articleName'];
        $object = $k['objectName'];
		logger("OpData: $artname $object");

        if ($_FILES['inputfiles']['name'][$artname]) {
                move_uploaded_file($_FILES['inputfiles']['tmp_name'][$artname],"$projDir/uploaded.$nprm.moby");
                $txt .= " ".$artname.".$nprm ".'"&'."$projDir/uploaded.$nprm.moby".'&'.$object.'"';
                $nprm++;
        } else {
            $txt .= " ".$artname.".$nprm input";
            $nprm++;
        }
    }
    foreach ($opData['output'] as $k) {
        $txt .= " ".$k['articleName'].".$nprm output";
        $nprm++;
    }
    if ($f) {
        foreach (array_keys($f) as $k) {
          if (strlen($f[$k]) > 0) {
            if ($f[$k]=='on')
                $f[$k]='true';
		if($f[$k] == '*'){
			$f[$k] = preg_replace("/\*/","\\\*",$f[$k]);
		}
	        else if(preg_match("/!/",$f[$k])){
                	$f[$k] = preg_replace("/\!/","\\\!",$f[$k]);
        	}
            	$txt .= " ".$k.".$nprm $f[$k]";
            	$nprm++;
           }
        }
    }
    return $txt;
}

function execWSScript ($WS, $inputf, $outputf='', $param='') {
    logger("Calling webservice: ".$GLOBALS['scriptDir']."/callSimpleWS.pl $WS $inputf $outputf $param 2>&1");
	$errors = exec ($GLOBALS['scriptDir']."/callSimpleWS.pl $WS $inputf $outputf $param 2>&1", $result);
    if ($errors) {
        logger ("Script output stream: ".$errors);
        addLog($_SESSION['userData']['login'], $_SESSION['projectData']['idProject'], "$WS error: ".$errors, $inputf, $outputf);
    }
    else
        addLog($_SESSION['userData']['login'], $_SESSION['projectData']['idProject'], "WSScript executed: ".$WS, $inputf, $outputf);
    return $result;
}

function execWSScriptAsync ($WS, $inputf, $outputf='', $logfile='', $param='') {

	$cmd = $GLOBALS['scriptDir']."/callSimpleAsyncWS.pl $WS $inputf $outputf $param >& $logfile"; 
    logger("Executing script: $cmd");
	$process = new ProcessSGE('fast',$cmd);
	$pid = $process->getPid();

	if ($process->status()){
        logger("The process $cmd is currently running ($pid).");
    }else{
        logger("The process is not running.");
    }
    return $pid;
}

function execScript ($script, $inputf, $outputf='', $param='') {

        logger("Executing script: ".$GLOBALS['scriptDir']."/$script $inputf $outputf $param");
	$errors = exec ($GLOBALS['scriptDir']."/$script $inputf $outputf $param 2>&1", $result);
    if ($errors) {
        logger("Script output stream: ".$errors);
        addLog($_SESSION['userData']['login'], $_SESSION['projectData']['idProject'], "$script error: ".$errors, $inputf, $outputf);
    }
    else
        addLog($_SESSION['userData']['login'], $_SESSION['projectData']['idProject'], "Script executed: ".$script, $inputf, $outputf);
    return $result;
}

function execAsyncScript ($script, $inputf, $outputf='', $logfile='', $param='') {

	$cmd = $GLOBALS['scriptDir']."/$script $inputf $outputf $param >& $logfile"; 
    logger("Executing script: $cmd");
	$process = new ProcessSGE('slow',$cmd);
	$pid = $process->getPid();

	if ($process->status()){
        logger("The process $script is currently running ($pid).");
    }else{
        logger("The process is not running.");
    }
    return $pid;
}

function getOpsSecondaryHTML($o) { ## Possible formulari automatic???
	ob_start();?>
    <p>Warning: AutoForm!<br/><br/>
    <?php
    if ($o['subtype']) {
    ?>
    <input type="hidden" name="prm[md_type]" value="<?php echo $o['subtype'] ?>" />
    <?php
	}
//print_r($o);
    foreach (array_values($o['secondary']) as $prm) {
        $default = getParameterDefaultValue($o['WS'], $prm['articleName']);
        $max = getParameterMaximumValue($o['WS'], $prm['articleName']);
        $min = getParameterMinimumValue($o['WS'], $prm['articleName']);
        if ($prm['type'] == "secondary") {
          if ($prm['articleName'] == "persistent") {
              $outObject = $o['output'][0]['objectName'];
              if (isa($outObject, 'MD_Trajectory')) {
            ?>
            	<input type="hidden" value="true" name="prm[<?php echo $prm['articleName'] ?>]" />
            <?php
              } 
          } else if ($prm['articleName'] != "md_type") {
            switch ($prm['objectName']) { 
                case 'Integer':
                    case 'Float':?>
                        <?php echo $prm['articleName'] ?> <input size="10" name="prm[<?php echo $prm['articleName'] ?>]" value="<?php echo $default ?>" />
        <?php               break;
                    case 'String':
                        $artname=$prm['articleName'];
                        $serviceID=getWSIdFromName($o['WS']);
                        $sql = "SELECT enum FROM MobyLiteDB.Parameter WHERE idService='$serviceID' and type='secondary' and articleName='$artname'";
                        $result=getRecordset($sql);
                        if (mysql_num_rows ($result))
                            $fetched=mysql_fetch_array($result);
                        mysql_free_result ($result);
                        if ($fetched)
                            $enum=$fetched[0];
                        if ($enum && ($enum != 'NULL')) {
                            $enumarr = explode(',',$enum);
                            echo $prm['articleName']." ";
                            
                             ?><select name="prm[<?php echo $prm['articleName'] ?>]">
                             <?php foreach ($enumarr as $k=>$v) {
                                 if ($v == $default) {
                                    ?> <option value="<?php echo $v ?>" selected="selected" ><?php echo $v ?></option>
                              <?php } else { 
                                    ?> <option value="<?php echo $v ?>"><?php echo $v ?></option>

                              <?php }
                             }
                             ?>
                             </select>
                             <?php
                        } else {
                        ?>
                        <?php echo $prm['articleName'] ?> <input size="30" name="prm[<?php echo $prm['articleName'] ?>]" value="<?php echo $default ?>" />
                        <?php
                        }
                        break;
                    case 'Boolean':
                        if (strtolower($default) == 'true') {
                    ?>
                    <?php echo $prm['articleName'] ?> <input type="checkbox" checked="checked" name="prm[<?php echo $prm['articleName'] ?>]" />
                    <?php }
                    else {
                        ?>
                        <?php echo $prm['articleName'] ?> <input type="checkbox" name="prm[<?php echo $prm['articleName'] ?>]" />
                    <?php
			}
             }
          }
        }
      }?>
    </p>  
    
   	<?php $txt = ob_get_contents();
	ob_end_clean();
    return $txt;
}

function buildNucleicAcidOutput($dir,$title,$leaf) {

	$filesRet = Array();

	if (!is_empty_folder($dir)){

		if (preg_match("/NOE/",$title) ){
			logger("BuildNucleicAcidOutput: NOEs ($dir)");
			$files = opendir($dir);
			while ($file=readdir($files)){
			     if (filesize($dir.'/'.$file) > 0)
	 			 if( endsWith($file,'avg.dat') or endsWith($file,'.stats') or endsWith($file,'.png') or (is_dir($dir.'/'.$file) and $file != '.' and $file != '..'))
					$filesRet[] = trim($file);
			}
		}
		else if (preg_match("/Jcoupling/",$title) ){
			logger("BuildNucleicAcidOutput: Jcouplings");
			$files = opendir($dir);
			while ($file=readdir($files)){
			     if (filesize($dir.'/'.$file) > 0)
	 			 if( endsWith($file,'avg.dat') or endsWith($file,'.stats') or endsWith($file,'.png') or (is_dir($dir.'/'.$file) and $file != '.' and $file != '..'))
					$filesRet[] = trim($file);
			}
		}
		else if (preg_match("/Curves/",$title) ){
			logger("BuildNucleicAcidOutput: Curves");
			$files = opendir($dir);
			while ($file=readdir($files)){
			     if (filesize($dir.'/'.$file) > 0)
	 			 if( is_dir($dir.'/'.$file) and $file != '.' and $file != '..' and $file != 'FORCE_CTES')
					$filesRet[] = trim($file);
			}
		}
		else if (preg_match("/Pcazip/",$title) ){
			logger("BuildNucleicAcidOutput: Pcazip");
			$files = opendir($dir);
			while ($file=readdir($files)){
			     if (filesize($dir.'/'.$file) > 0)
	 			 if( endsWith($file,'.dat') or endsWith($file,'.pdb') or endsWith($file,'.png') or endsWith($file,'.stats') or endsWith($file,'.evals') or endsWith($file,'.bfactors') or endsWith($file,'.collectivity') or endsWith($file,'.info'))
					$filesRet[] = trim($file);
			}
		}
		else if (preg_match("/Stiffness/",$title) ){
			logger("BuildNucleicAcidOutput: Stiffness");
			$files = opendir($dir);
			while ($file=readdir($files)){
			     if (filesize($dir.'/'.$file) > 0)
	 			 if( endsWith($file,'avg.dat') or endsWith($file,'.png') or (is_dir($dir.'/'.$file) and $file != '.' and $file != '..'))
					$filesRet[] = trim($file);
			}
		}
		else if (preg_match("/Distance/",$title) ){
			logger("BuildNucleicAcidOutput: Atom Pairs Distances Analysis");
			$files = opendir($dir);
			while ($file=readdir($files)){
			     if (filesize($dir.'/'.$file) > 0)
	 			 if( endsWith($file,'.dat') or endsWith($file,'.stats') or endsWith($file,'.png') or (is_dir($dir.'/'.$file) and $file != '.' and $file != '..'))
					$filesRet[] = trim($file);
			}
		}
		else if (preg_match("/HBs/",$title) ){
			logger("BuildNucleicAcidOutput: Canonical Hydrogen Bond Analysis");
			$files = opendir($dir);
			while ($file=readdir($files)){
			     if (filesize($dir.'/'.$file) > 0)
	 			 if( endsWith($file,'avg.dat') or endsWith($file,'.stats') or endsWith($file,'.png') or (is_dir($dir.'/'.$file) and $file != '.' and $file != '..'))
					$filesRet[] = trim($file);
			}
		}
		else if (preg_match("/Stacking/",$title) ){
			logger("BuildNucleicAcidOutput: Stacking Energies Analysis");
			$files = opendir($dir);
			while ($file=readdir($files)){
			     if (filesize($dir.'/'.$file) > 0)
	 			 if( (is_dir($dir.'/'.$file) and $file != '.' and $file != '..'))
					$filesRet[] = trim($file);
			}
		}
		else if (preg_match("/CG_/",$title) ){
			logger("BuildNucleicAcidOutput: Coarse Grained Flexibility Analysis");
			$files = opendir($dir);
			while ($file=readdir($files)){
			     if (filesize($dir.'/'.$file) > 0)
	 			 if( endsWith($file,'.dat') or endsWith($file,'.png') and $file != '.' and $file != '..' )
					$filesRet[] = trim($file);
			}
		}
	}
	return $filesRet;
}

function buildConfigOutput ($confDir,$program) {

	$filesRet = Array();
	if (!is_empty_folder($confDir)){

		$origDir = getcwd();
		chdir($confDir);
		if (preg_match("/GROMACS/",$confDir) ){
			logger("BuildConfigOutput: GROMACS Config File");

			$files = opendir($confDir);
			while ($file=readdir($files)){
				if( endsWith($file,'.itp') or endsWith($file,'.top') or endsWith($file,'.gro') or endsWith($file,'.tpr') or endsWith($file,'.trr') or endsWith($file,'gromacs.mdp') or endsWith($file,'.ndx') or endsWith($file,'.pdb') or endsWith($file,'.edr') or endsWith($file,'.sh'))
					$filesRet[] = trim($file);
			}

			# GROMACS Readme File:
			$readme = $GLOBALS[scriptDir]."/README.gromacs";
			copy($readme,"./README.gromacs");
			$filesRet[] = "README.gromacs";
		}
		else if (preg_match("/NAMD/",$confDir) ){
			logger("BuildConfigOutput: NAMD/AMBER Config File");

			if($program == "NAMD"){
				$files = opendir($confDir);
				while ($file=readdir($files)){
					logger("File: $file");
					if( endsWith($file,'namdProt-md.in') or endsWith($file,'MD.psf') or endsWith($file,'.vel') or endsWith($file,'.xsc') or endsWith($file,'MD.pdb') or endsWith($file,'namdMD.sh')) 
						$filesRet[] = trim($file);
				}

				# NAMD Readme File:
				$readme = $GLOBALS[scriptDir]."/README.namd";
				copy($readme,"./README.namd");
				$filesRet[] = "README.namd";
			}
			else if ($program == "AMBER"){
				logger("BuildConfigOutput: AMBER Config File");

                                $files = opendir($confDir);
                                if (!file_exists("NAMD")) mkdir("NAMD");
				if (!file_exists("AMBER")) mkdir("AMBER");
                                while ($file=readdir($files)){
                                        logger("File: $file");
                                        if( endsWith($file,'namdProt-md.in') or endsWith($file,'MD.psf') or endsWith($file,'.vel') or endsWith($file,'.xsc') or endsWith($file,'MD.pdb') or endsWith($file,'namdMD.sh')){
                                                $file = trim($file);
                                                copy($file,"./NAMD/$file");
                                                $filesRet[] = "NAMD/$file";
					}
					else if( endsWith($file,'amberProt-md.in') or endsWith($file,'MD.top') or endsWith($file,'MD.pdb') or endsWith($file,'amberMD.sh') or endsWith($file,'.crd')){
                                                $file = trim($file);
                                                copy($file,"./AMBER/$file");
                                                $filesRet[] = "AMBER/$file";
                                        }
                                }

				# AMBER/NAMD Readme Files:
				$readme = $GLOBALS[scriptDir]."/README.amber";
				copy($readme,"./AMBER/README.amber");
				$filesRet[] = "AMBER/README.amber";
				$readme = $GLOBALS[scriptDir]."/README.namd";
				copy($readme,"./NAMD/README.namd");
				$filesRet[] = "NAMD/README.namd";
                        }
		}
		else {
			logger("No program in BuildConfigOutput ($confDir)...");
		}
	}

	chdir($origDir);

	return $filesRet;
}

function checkIfLigands ($dirname) {

     if (is_dir($dirname))
	     $dir_handle = opendir($dirname);

     if (!$dir_handle)
         return false;

     while($file = readdir($dir_handle)) {
         if ($file != "." && $file != "..") {
             if (preg_match("/\.lib$/",$file))
                 	return true;
         }
     }
     closedir($dir_handle);

    return false;
}

function checkIfDNA ($fname) {

    $fh = fopen($fname, 'r');
    $fname = fgets($fh);
    while(!feof($fh)) {
                $line = trim($fname);
                if(strncmp($line, "ATOM", 4)==0) {

                        $mon = trim(substr($line,17,3));

                         if($GLOBALS['nucleic_codes_std'][$mon]){
                                $type = 'DNA';
				return 1;
                         }
                         else{
                                $type = 'Prot';
                         }
                }

            $fname = fgets($fh);
    }
    fclose($fh);

    return 0;
}

function checkOnlyDNA ($fname) {

    $type = '';
    $nuc = 0;
    $prot = 0;
    $fh = fopen($fname, 'r');
    $fname = fgets($fh);
    while(!feof($fh)) {
                $line = trim($fname);
                if(strncmp($line, "ATOM", 4)==0) {

			$mon = trim(substr($line,17,3));

                         if($GLOBALS['residue_codes_std'][$mon]){
				$prot = 1;
                         }
                         else if($GLOBALS['nucleic_codes_std'][$mon]){
				$nuc = 1;
                         }

#                 if ((/^ATOM/) && ($at eq "O2'" or $at eq "O2*"))
#                         $chType{$ch} = 'Rna';

                }

            $fname = fgets($fh);
    }
    fclose($fh);

    if($nuc and $prot) {$type = 'Prot-Nuc';}
    else if($nuc) {$type = 'Nuc';}
    else if($prot) {$type = 'Prot';}

logger("CheckOnlyDNA: $type");

    return $type;
#	if(strncmp($type,"DNA",3) == 0 ){ 
#		return 1;
#	}
}

function checkNumResidues ($fname) {

    $num = 0;
    $fh = fopen($fname, 'r');
    $fname = fgets($fh);
    while(!feof($fh)) {
                $line = trim($fname);
                if(strncmp($line, "ATOM", 4)==0) {

			$atomName=trim(substr($line, 12, 4));
			if($atomName=="CA") {
              			$num++;
                	}
		}
		$fname = fgets($fh);
    }
    fclose($fh);

    return $num;
}

function checkResidueToMutate ($fname,$numRes,$chain=" ") {
#	$fname = $f['projDir']."/".$f['fileTree'][$k]['fname'].".moby";

	$resName = '?';
    $fh = fopen($fname, 'r');
    $fname = fgets($fh);
    while(!feof($fh)) {
		$line = trim($fname);
		if(strncmp($line, "ATOM", 4)==0) {
		    if ((trim(substr($line, 22, 4)) == $numRes) and ( (trim(substr($line,21,1)) == $chain) or ($chain == '*') ) ) {
			logger("Mutate res Found! $line");
		        $resName = trim(substr($line,17,3));
		        break;
		    }
		}
	    $fname = fgets($fh);
    }
    fclose($fh);

   if($resName == '?')
	return 0;
   else
	return 1;
}

function getAtRes ($fname) {

    $fh = fopen($fname, 'r');
    $fname = fgets($fh);
    while(!feof($fh)) {
                $line = trim($fname);

		if (preg_match("/WAT/",$line)) {
			$fname = fgets($fh);
			continue;
		}

                if(strncmp($line, "ATOM", 4)==0) {

			$atomName=trim(substr($line, 12, 4));
			#$resName = trim(substr($line,17,3));
			$resNumber = substr($line, 22, 4) + 0;

			$code = $resNumber."@".$atomName;
			$codes[$code] = 1;
			#logger("Distances Code: -$code- saved!");
		}
		$fname = fgets($fh);
    }
    fclose($fh);

    return $codes;
}

function unpairedDNA ($seq1,$seq2) {

	$l1 = strlen($seq1);

	for ($i=0;$i<=$l1-1;$i++){
		$nuc1 = $seq1[$i];
		$nuc2 = $seq2[$i];

		if( ($nuc1 == "G" and ($nuc2 != "C" and $nuc2 != "X")) or ($nuc1 == "C" and ($nuc2 != "G" and $nuc2 != "X")) ) {
			return 1; 
		}
		else if( ($nuc1 == "A" and ($nuc2 != "T" and $nuc2 != "U" and $nuc2 != "X")) or (($nuc1 == "T" or $nuc1 == "U") and ($nuc2 != "A" and $nuc2 != "X")) ){
			return 1; 
		}
	}
	return 0;
}
