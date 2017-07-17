<?php
/* MDWeb
 * projects.inc.php
 * Project management
 */

function generateVMDImage ($fpdb, $fout, $coarseGrained) {
        $projDir = dirname($fout);
        $representation = "mol representation newCartoon 0.2 20 5";
	$ftmp = $projDir."/protein.tga";
        $dir = $GLOBALS['softDir'];
logger("COARSE_GRAINED VMD: $coarseGrained");
	$vmdScript =
	   "mol load pdb $fpdb
		display projection orthographic
		color Display Background white
		axes location off
		display nearclip set 0
		display height 4
		lappend auto_path $dir/vmd/la1.0
		lappend auto_path $dir/vmd/orient
		package require Orient
		namespace import Orient::orient
		set sel [atomselect top \"protein or nucleic\"]
		set I [draw principalaxes \$sel]
		set A [orient \$sel [lindex \$I 2] {1 0 0}]
		\$sel move \$A
		set I [draw principalaxes \$sel]
		set A [orient \$sel [lindex \$I 1] {0 1 0}]
		\$sel move \$A
		graphics top delete all
		mol delrep 0 0
		mol color structure
		mol material BrushedMetal
		$representation
		mol addrep top
		mol color colorid 4
		mol material BrushedMetal
		mol selection (within 5 of resname \\\"CY\\.\\\") and (resname \\\"CY\\.\\\")  and mass > 2
		mol representation Licorice 0.3 20 20
		mol addrep top
		mol representation NewRibbons 0.300000 6.000000 3.000000 0
		mol color Element
		mol selection {nucleic}
		mol addrep top
		#mol representation Tube 0.300000 6.000000
		#mol representation Licorice 0.300000 10.000000 10.000000
		mol representation Lines
		mol color Name
		mol selection {nucleic}
		mol addrep top
		render TachyonInternal $ftmp
		quit";

	if($coarseGrained){
	        $vmdScript ="mol load pdb $fpdb
                display projection orthographic
                color Display Background white
                axes location off
                display nearclip set 0
                display height 4
                lappend auto_path $dir/vmd/la1.0
                lappend auto_path $dir/vmd/orient
                package require Orient
                namespace import Orient::orient
                set sel [atomselect top all]
                set I [draw principalaxes \$sel]
                set A [orient \$sel [lindex \$I 2] {1 0 0}]
                \$sel move \$A
                set I [draw principalaxes \$sel]
                set A [orient \$sel [lindex \$I 1] {0 1 0}]
                \$sel move \$A
                graphics top delete all
                mol delrep 0 0
                mol representation Lines
                mol color Name
                mol addrep top
                render TachyonInternal $ftmp
                quit";
	}

	$vmdScriptFile = $projDir."/protein.vmdScript";
	$fvmd = fopen ($vmdScriptFile, "w");
	fwrite ($fvmd, $vmdScript);
	fclose ($fvmd);
    # Not-queued script for quick calculus
	$noqueueScriptFile = $projDir."/protein.noqueueScriptFile";
        $fq0 = fopen ($noqueueScriptFile, "w");
        fprintf ($fq0, "#!/bin/bash\n\n");
	fprintf ($fq0, "%s\n", "cd ".$projDir."\n");
	fprintf ($fq0, "%s\n", "export VMDDIR=".VMDDIR);
	fprintf ($fq0, "%s\n", "export STRIDE_BIN=".STRIDEBIN."\n");
	fprintf ($fq0, "%s\n", VMDBIN." -dispdev text -size 300 300 -e $vmdScriptFile > vmd.log");
	fprintf ($fq0, "%s\n", "tgatoppm $ftmp | pnmcrop -white | pnmtopng -transparent white > $fout");
	fprintf ($fq0, "\n");
        fclose ($fq0);
        exec ("/bin/sh $noqueueScriptFile\n");

	$stderr = exec("grep ERROR $projDir/vmd.log | grep -v Warning | grep -v Stride",$error);
	
	if ($stderr){
		logger("VMD Error: $stderr");
		return $stderr;
	}
	//unlink ($noqueueScriptFile);
	//unlink ($vmdScriptFile);
	//unlink ($ftmp);

	return 0;
}

function checkDiskUsage() {
    $diskusage = getDiskUsage();
    $pre = substr($diskusage, 0, -3);
    $suf = substr($diskusage, -3);

    if ($suf == " GB") {
        $pre = round(($pre * 1024), 1);
        $suf = " MB";
    }
    if ($suf == " MB") {
        $pre = round(($pre * 1024), 1);
        $suf = " kB";
    }

    $disklimit = $GLOBALS['disklimit'];

    logger("DiskLimit? $pre > $disklimit");
    if ($pre > $disklimit) {
        return (1); //error
    } else {
        return (0); //no error
    }
}

function getDiskUsage($idProject='') {
    $login =  $_SESSION['userData']['login'];

    if (!$idProject) {
        $projDir = $_SESSION['userData']['workDir'];
        }
    else {
        $projDir = $_SESSION['userData']['workDir']."/$idProject";
    }
    $dd = exec("du -Lks $projDir | sed 's/\t/#/'");
    $du = split('#',$dd);

    $size = $du[0];
    $suf = " kB";

    if ($size > 1024) {
        $size = round(($size / 1024),1);
        $suf = " MB";
    }
    if ($size > 1024) {
        $size = round(($size / 1024),1);
        $suf = " GB";
    }
logger("getDiskUsage. Project:$idProject, ProjDir: $projDir");
logger("Du: $du[0], Size: $size $suf");

    return $size.$suf;
}

function createProject () {
    $idProject = uniqId('NAFlex');
    $projDir = $_SESSION['userData']['workDir']."/$idProject";
    $userDir = "userData/".$_SESSION['userData']['login'];
    $baseURL = "$userDir/$idProject";
    #print $projDir;
    $prj=$_SESSION['inputData'];
    $prj['projDir']=$projDir;
    $prj['baseURL']=$baseURL;
    $prj['idProject']=$idProject;
$type = $_SESSION['inputData']['projType'];
logger("createProject: $projDir, $baseURL, $idProject, $type");
    $old = umask(0);
	if (! file_exists($userDir)) mkdir($userDir, 0777, TRUE);
    mkdir ($projDir, 0777, TRUE);
// Index.htm blanc per evitar directori a web
    system ("touch $projDir/index.htm");
    umask($old);
    if ($_SESSION['inputData']['pdbcode']) {
        $pdb = $_SESSION['inputData']['pdbcode'];
        $rs = getRecordSet("SELECT * FROM uniprot.uniprot WHERE acNum='$pdb' OR swpId='$pdb'");
        $rsF = mysql_fetch_array($rs);
        $seq = $rsF['seq'];
        if ($seq) {
            $seq = preg_replace('/[[:space:]]/', '', $seq);
            $pdb = searchSimilarProtein ($seq);
		logger("Search similar protein: $pdb ($seq)");
		if(!$pdb){
		 $_SESSION['errorData']['nopdbforSWP'] = 1;
		 redirect ("newProject.php");
		}
        }
    } elseif ($_FILES['structure']['tmp_name']) {
        move_uploaded_file($_FILES['structure']['tmp_name'],"$projDir/structure.pdb");
        //$pdb = searchSimilarProtein ("", "$projDir/structure.pdb");
        //if (!$pdb) {
        $pdb = "$projDir/structure.pdb";
        //}
    }
    
    if ($pdb) {

	$result = execScript ("checkPDB.pl", $pdb);
	logger("execScript checkPDB.pl $pdb : $result[0] ($result)");

	# If input pdb has no chains, try to add them... It's a recurrent ERROR!!!
	if(preg_match("/NO_CHAIN/",$result[0])){
		copy ($pdb, "$projDir/structure_tmp.pdb");
		execScript("addChain.pl","$projDir/structure_tmp.pdb $projDir/structure.pdb");
		$result = execScript ("checkPDB.pl", $pdb);
	}

	if(preg_match("/REMEDIATED/",$result[0])){
		$_SESSION['errorData']['remediated'] = 1;
		redirect ("newProject.php");
	}
	else if($result[0] > $GLOBALS['maxAtoms']) {
		$_SESSION['errorData']['pdbTooBig'] = 1;
		redirect ("newProject.php");
	}
	else if ($result[0] < 0){
		$_SESSION['errorData']['nopdb'] = 1;
		redirect ("newProject.php");
	}
        execScript ("getPDBStructure.pl", $pdb, "$projDir/root.moby");
    }
    $cg = 0; # Coarse-Grained Case, VMD representation has to be changed.
    switch ($_SESSION['inputData']['projType']) {
        case "sim":
            # Check DNA
            $fname = "$projDir/structure.pdb";
            $onlydna = checkOnlyDNA($fname);

            $prj['fileTree']['root']=Array (
                'id' => "root",
                'Titol' => "Base structure",
                'Descript' => '',
                'Op' => '',
                'parent' => '',
                'leaf' => Array(),
                'estat' => 1,
                'cg' => 0,
                'objectType' => 'PDB-Text',
                'fname' => 'root.moby',
                'fileok' => 1,
		'molType' => $onlydna,
                'persistent' => 0);
            break;
	case "seq":

	    $titol = "Base DNA/RNA structure From Sequence";

	    move_uploaded_file($_FILES['FASTAsequence']['tmp_name'],"$projDir/sequence.fas");

	    $seq = $_SESSION['inputData']['sequence'];
	    if(!$seq) $seq = "$projDir/sequence.fas";
	    $type = $_SESSION['inputData']['nucType']; 

	    if($type == "DNAuser" or $type == "RNAuser"){
		$ntype = strtolower(substr($type,0,3)); 
		$xoffset = $_SESSION['inputData']['xoffset']; 
		$inclination = $_SESSION['inputData']['inclination']; 
		$rise = $_SESSION['inputData']['rise']; 
		$twist = $_SESSION['inputData']['twist']; 

		#Usage: perl runBuildStructureWithHelicalParametersFromSeq.pl <id> <Sequence> <outPDB> Opt PARAMS: [<type>: dna|rna], <xoffset>, <inclination>, <rise>, <twist>]
		execScript("runBuildStructureWithHelicalParametersFromSeq.pl", "MDWeb_seq $seq $projDir/structure_tmp.pdb $ntype $xoffset $inclination $rise $twist");

		execScript("addChain.pl","$projDir/structure_tmp.pdb $projDir/structure.pdb");
	    }
	    else if($type == "ABC_DNA"){
		execScript("runNab.pl","$projDir $seq dna structure ABC");
	    }
	    else if($type == "X-ray_DNA"){
		execScript("runNab.pl","$projDir $seq dna structure XRAY");
	    }
	    else if($type == "DNAlive"){
		execScript("genMontecarloCGModel.pl","$projDir $seq structure.pdb");
		$cg = "CG-DNAlive";
		$titol = "Elastic Mesoscopic Coarse-Grained DNA/RNA structure From Sequence # Resolution: Nucleotide Base #";
	    }
	    else if($type == "WLC"){
		$nbeads = $_SESSION['inputData']['nbeads']; 
		execScript("genWLCCGModel.pl","$projDir $seq structure.pdb $nbeads");
		$cg = "CG-WLC";
		$titol = "WLC Coarse-Grained DNA/RNA structure From Sequence # Resolution: $nbeads Base Pairs x Bead #";
	    }
	    else{
		#Usage: perl scripts/runBuildStructureFromSeq.pl <id> <Sequence> <outPDB> [<type>: adna|arna|aprna|lbdna|abdna|sbdna]
		execScript("runBuildStructureFromSeq.pl", "MDWeb_seq $seq $projDir/structure_tmp.pdb $type");
		logger("Input Sequence: $seq");

		execScript("addChain.pl","$projDir/structure_tmp.pdb $projDir/structure.pdb");
	    }

	    if (!file_exists("$projDir/structure.pdb")) {
                $_SESSION['errorData']['noseq2pdb'] = 1;
                redirect ("newProjectDiff.php");
	    }

	    execScript ("getPDBStructure.pl", "$projDir/structure.pdb", "$projDir/root.moby");

            # Check DNA or RNA
            $fname = "$projDir/structure.pdb";
            $onlydna = checkOnlyDNA($fname);


            $prj['fileTree']['root']=Array (
                'id' => "root",
                'Titol' => $titol,
                'Descript' => '',
                'Op' => '',
                'parent' => '',
                'leaf' => Array(),
                'estat' => 1,
                'cg' => $cg,
                'objectType' => 'PDB-Text',
                'fname' => 'root.moby',
                'fileok' => 1,
		'molType' => $onlydna,
                'persistent' => 0);

		break;

        case "anal":
            move_uploaded_file($_FILES['coordinates']['tmp_name'],"$projDir/trajectory.coor");
            $topfile=".";
            if ($_FILES['topology']['tmp_name']) {
                move_uploaded_file($_FILES['topology']['tmp_name'],"$projDir/topology.top");
                $topfile="$projDir/topology.top";
            }
            //if GROMACS
            if ($_SESSION['inputData']['ffForm'] == 'gromacs') {
                $grofile=".";
                if ($_FILES['gro']['tmp_name']) {
                    move_uploaded_file($_FILES['gro']['tmp_name'],"$projDir/topology.gro");
                    $grofile="$projDir/topology.gro";
                }
                
                $count=0;
                $itpfilenames="";
                foreach($_FILES as $itp) {
                    if (($itp != $_FILES['coordinates']) && ($itp != $_FILES['topology']) && ($itp != $_FILES['gro'])) {
                            move_uploaded_file($itp['tmp_name'],"$projDir/".$itp['name']);
                            $itpfilenames.=$itp['name']." ";
                            $count++;
                    }
                }
                execScript("loadGromacsTraj.pl", "$projDir/trajectory.coor $grofile $topfile $projDir/rootTraj.moby $itpfilenames");
            } else {
                //if AMBER/NAMD
                execScript("loadTrajectFile.pl", "$projDir/topology.top $projDir/trajectory.coor $projDir/structure.pdb ".$_SESSION['inputData']['trajForm']." ".$_SESSION['inputData']['topForm'], "$projDir/rootTraj.moby");
            }

            if (!file_exists("$projDir/structure.pdb")) {
                $_SESSION['errorData']['badtraj'] = 1;
                redirect ("newProjectDiff.php");
            }

                # Check DNA
                $fname = "$projDir/structure.pdb";
                $onlydna = checkOnlyDNA($fname);

		logger("CREATING PROJECT, OnlyDNA: $onlydna");

            $prj['fileTree']['root']=Array (
                'id' => "rootTraj",
                'Titol' => "Base trajectory",
                'Descript' => '',
                'Format' => $prj['ffForm'],
                'Op' => '',
                'parent' => '',
                'leaf' => Array(),
                'estat' => 1,
                'fname' => 'rootTraj.moby',
                'fileok' => 1,
		'molType' => $onlydna,
                'persistent' => 1);
            switch ($_SESSION['inputData']['trajForm']) {
                case "crd":
                    $prj['fileTree']['root']['objectType'] = 'MD_TrajectoryCRD';
                    break;
                case "binpos":
                    $prj['fileTree']['root']['objectType'] = 'MD_TrajectoryBINPOS';
                    break;
                case "dcd":
                    $prj['fileTree']['root']['objectType'] = 'MD_TrajectoryDCD';
                    break;
                case "netcdf":
                    $prj['fileTree']['root']['objectType'] = 'MD_TrajectoryNetCDF';
                    break;
                case "xtc":
                    $prj['fileTree']['root']['objectType'] = 'MD_TrajectoryXTC';
                    break;
                case "pcazip":
                    $prj['fileTree']['root']['objectType'] = 'MD_Compressed_Trajectory';
                    break;
            }
          //  print $prj['fileTree']['root']['objectType'];
            break;
        default:
            print errorPage("Error","Unknown");
        }
        $error = generateVMDImage("$projDir/structure.pdb", "$projDir/protein_vmd.png",$cg);
	logger("Error: $error");

	#VMD Error: ERROR) Tubes may be incomplete.
	#VMD Error: ERROR) BaseMolecule: Cannot bond atom 0 to itself.
	#VMD Error: ERROR) No molecules loaded.

	#if(preg_match("/No molecules loaded/",$error)){
	if($error){
 		$_SESSION['errorData']['badtraj'] = $error;
		$_SESSION['errorData']['noseq2pdb'] = $error;
	}
        
        if (file_exists("$projDir/topology.top"))
            unlink("$projDir/topology.top");
        if (file_exists("$projDir/topology.gro"))
            unlink("$projDir/topology.gro");
        if (file_exists("$projDir/trajectory.coor"))
            unlink("$projDir/trajectory.coor");
        /*if (file_exists("$projDir/structure.pdb"))
            unlink("$projDir/structure.pdb");*/
        return $prj;
    }

     function saveNewProjectDB ($time) {
    	//CF fixed SQL injection
    	$idProject = mysql_real_escape_string($_SESSION['projectData']['idProject']);
    	$titol = mysql_real_escape_string($_SESSION['projectData']['titol']);
    	$descripcio = mysql_real_escape_string($_SESSION['projectData']['descripcio']);
    	$idUser = mysql_real_escape_string($_SESSION['userData']['idUser']);
    	
        execSql("INSERT INTO projects (idProject,titol,descripcio,lastmodif,idUser) VALUES (
    '".$idProject."',
    '".$titol."',
    '".$descripcio."',
    '".$time."',
    ".$idUser.")");
    }

	function saveProjectDB ($idProject, $time) {
    	//CF fixed SQL injection
    	$idP = mysql_real_escape_string($idProject);
    	$t = mysql_real_escape_string($time);
        execSql("UPDATE projects set lastmodif = '$t' where idProject = '$idP'");
    }

    function saveProject($new=False) {
        $time = moment();
        $_SESSION['projectData']['lastmodif'] = $time;
        $fs = fopen ($_SESSION['projectData']['projDir']."/projectData.bin","w");
        fwrite ($fs,serialize($_SESSION['projectData']));
        fclose($fs);
        if ($new) {
            saveNewProjectDB($time);
            addLog($_SESSION['userData']['login'], $_SESSION['projectData']['idProject'], "New project created");
        }
        else {
            saveProjectDB($_SESSION['projectData']['idProject'], $time);
            garbageCollector();
        }
    }

    function loadProject($projDir) {
	if(file_exists($_SESSION['userData']['workDir']."/$projDir/projectData.bin"))
	        return unserialize(file_get_contents($_SESSION['userData']['workDir']."/$projDir/projectData.bin"));
	else
		return 0;
    }
	
	function dumpProject ($idProject) {
		garbageCollector();
		$projDir = $_SESSION['userData']['workDir']."/$idProject";
		$fs = fopen ("$projDir/projId.txt", "w");
		// Id user i projecte verure que passa amb anonims!!
		fwrite ($fs, "User=".$_SESSION['userData']['login']."\n");
		fwrite ($fs, "Project=".$idProject."\n");
		fwrite ($fs, "DumpDate=".moment()."\m");
		fclose($fs);
		// DB Dump
		exec ("mysqldump -u gelpi -pjl12gb --extended-insert=false  MDWeb projects | grep ".$idProject." > $projDir/DBDump.sql");
		if (!file_exists("$projDir/PersistentFiles")) 
			mkdir ("$projDir/PersistentFiles",0750);
		$dir = opendir($projDir);
		while ($ff = readdir($dir)) {
			if (is_link($projDir."/".$ff)) {
				$origF = readLink($projDir."/".$ff);

				// Analysis Uploaded Trajectories
				$persFile = $GLOBALS['asyncDir'];
				if(preg_match("/MDWebTrajInput/",dirname($origF))){
					$persFile = $GLOBALS['persTrajDir']."/".$_SESSION['userData']['login'];
				}
				$path=str_replace ($persFile,'',dirname($origF));				
				$fname=basename($origF);
				copy ($origF, "$projDir/PersistentFiles/$path"."%".$fname);
				logger("OrigF: $origF, $projDir/PersistentFiles/$path % $fname");
			}
		};
		$tarfile = $GLOBALS['projectDumpDir']."/".$_SESSION['userData']['login']."_$idProject.NAFlexDump.tgz";
		chdir ($_SESSION['userData']['workDir']);
		exec ("tar -czvf $tarfile $idProject");
		exec ("rm -r $projDir/PersistentFiles");
		return $_SESSION['userData']['login']."_$idProject.NAFlexDump.tgz";
	}

	function restoreProject ($fn) {
		$dumpFile = $GLOBALS['projectDumpDir']."/$fn";
		chdir ($GLOBALS['projectDumpDir']);
		$files=Array();
		exec ("tar -tzvf $fn", $files);
 		$line = split (' ',$files[0]);
		$tmpprojDir = $line[5];
		exec ("tar -xvf $fn");
		$projId = file("$tmpprojDir/projId.txt", FILE_IGNORE_NEW_LINES);
		foreach (array_values($projId) as $v) {
			list($f,$v)= split('=',$v);
			$projData[$f]=$v;
		}
		// Checking Errors
		if ((!preg_match('/^MDWeb/', $projData['User']) or (!preg_match('/^NAFlex/', $projData['User']))) and ($projData['User'] != $_SESSION['userData']['login'])) {
			print "error no permis";
			exit;
		}
		$newprojDir = $_SESSION['userData']['workDir']."/".$projData['Project'];
		if (file_exists($newprojDir)) {
			print "error project existeix";
			exit;
		} else {
		// despleguem
			exec ("mv $tmpprojDir ".$_SESSION['userData']['workDir']);
		// BD
			$sql = file("$newprojDir/DBDump.sql");
			foreach (array_values($sql) as $s)
				execSql($s);
		// Persistent
			$pDir = opendir("$newprojDir/PersistentFiles");
			while ($ff=readdir($pDir)) {
				list($d,$f) = split('%',$ff);
                			logger("FF: $ff");

				// Analysis Uploaded Trajectories
				if(preg_match("/^MDWeb/",$d) or preg_match("/^NAFlex/",$d)){
                			logger("FF: $ff");
					if (!file_exists($GLOBALS['persTrajDir']."/".$_SESSION['userData']['login']))
						mkdir ($GLOBALS['persTrajDir']."/".$_SESSION['userData']['login']);
		                        if (!file_exists($GLOBALS['persTrajDir']."/".$_SESSION['userData']['login']."/".$d))
                                                mkdir ($GLOBALS['persTrajDir']."/".$_SESSION['userData']['login']."/".$d);
		
                                        exec ("mv $newprojDir/PersistentFiles/$ff ".$GLOBALS['persTrajDir']."/".$_SESSION['userData']['login']."/$d/$f");
				}
				else{	// Persistence Setup Services
					if (!file_exists($GLOBALS['asyncDir']."/".$d)) 
						mkdir ($GLOBALS['asyncDir']."/".$d);
					exec ("mv $newprojDir/PersistentFiles/$ff ".$GLOBALS['asyncDir']."/$d/$f");
				}
			}
			rmdir ("$newprojDir/PersistentFiles");
		}
		unlink ($dumpFile);
		unlink ("$newprojDir/DBDump.sql");
		unlink ("$newprojDir/projId.txt");
	}

    function projectHeader ($f) {
#        $projDir=$_SESSION['projectData']['projDir'];
        $projDir=$f['projDir'];
        $left = "<h4>$f[titol] ($f[idProject])</h4><p>$f[descripcio]</p><p>Last modification on: ".prdata('an',$f['lastmodif'])."</p><p>Disk Usage: ".getDiskUsage($f['idProject'])."</p>";
        $right = "<img src=\"getFile.php?fileloc=$projDir/protein_vmd.png\" align=\"right\" alt=\"$projDir/protein_vmd.png\" title=\"$projDir/protein_vmd.png\" width=\"140\">";
        $whole = "<table width=\"100%\" border=\"0\"><tr><td>$left</td><td width=\"22%\">$right</td></tr></table>";
        return $whole;
    }

    function deleteProject ($idProject) {
    	//CF fixed security issues
    	//$idProject = escapeshellarg($idProject);
    	//$workDir = escapeshellarg($_SESSION['userData']['workDir']);
    	//$login = escapeshellarg($_SESSION['userData']['login']);

	$workDir = $_SESSION['userData']['workDir'];
	$login = $_SESSION['userData']['login'];
	$folder = "$workDir/$idProject";
    	
	$idProject = escapeshellarg($idProject);

        if ($idProject) {
	logger("WARNING: Deleting project $idProject ($workDir/$idProject)");

		// Delete soft links in Project Folder
		$files = opendir($folder);
	        while ($file=readdir($files)){
			if ( preg_match("/^ln_/",$file) ){
				$fullPersFile = "$folder/$file";
		                logger("Delete Project: Persistent File: $fullPersFile");
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
			}
        	}

		// Now Removing Project Folder.
            	system ("rm -rf $workDir/$idProject");		
        }
        //CF escaped the string 
		//AH des-escaped the string... It's already escaped with escapeshellarg 9 lines above.
//        $idProject = mysql_real_escape_string($idProject);
        execSQL ("DELETE from projects WHERE idProject = $idProject");
        addLog($login, $idProject, "Project deleted");
    }

    function newFileOrder ($projDir) {
        $n=0;
        while (file_exists("$projDir/f".substr("00$n",-2).".moby")) $n++;
        return substr("00$n",-2);
    }

    function newObjectOrder ($proj) {
        $n=0;
        while ($proj["f".substr("00$n",-2)])
        $n++;
        return substr("00$n",-2);
    }

    function checkExistsPDB ($pdb) {
        //here we will check also for the Swiss-Prot code
        $rs = getRecordSet("SELECT count(*) FROM uniprot.uniprot WHERE acNum='$pdb' OR swpId='$pdb'");
        $rsF = mysql_fetch_array($rs);

	if(!$rsF[0]){
	        $result = execScript ("checkPDB.pl", $pdb);
		return $result[0];
	}
	else{
	        return $rsF[0];
	}
    }

    function garbageCollector() {
        cleanGarbage($_SESSION['projectData']['projDir']);

        //clean expired anonymous users

        $rs = getRecordSet('SELECT * FROM users WHERE surname="Anonymous"');
	while ($rsF = mysql_fetch_array($rs))  {
            $time=$rsF['lastlogin'];
            $userId=$rsF['idUser'];
            $login=$rsF['login'];
            $now=moment();
            if (dif_days($now, $time) > 2)
                cleanAnonymousUser($userId, $login);
        }

        //clean expired projects

	# NAFlex2, we are not interested in cleaning any project
        #$rs = getRecordSet('SELECT * FROM projects');
        #while ($rsF = mysql_fetch_array($rs)) {
        #    $time=$rsF['lastmodif'];
        #    $projId=$rsF['idProject'];
        #    $userId=$rsF['idUser'];
        #    $now=moment();
	#	
	#    # UserID=846 ==> Demo user, it can not be removed!
	#    # UserID=835 ==> Interactive Demo user, it can not be removed!
        #    if (dif_days($now, $time) > 40 && $userId != 846 && $userId != 835) {
        #        $line=getRecord("users", "idUser", $userId, "T");
        #        $login = $line['login'];
        #        $dir = escapeshellarg($GLOBALS['baseDir'])."/$login";
        #        //CF fixed injection
        #        $projId = mysql_real_escape_string($projId);
        #        if ($projId) {
        #            execSql ("delete from projects where idProject = '".$projId."'");
        #            $projId = escapeshellarg($projId);
	#		logger("WARNING: Cleanning expired project $projId ($dir, $dir2)");
        #            system ("cd ".$dir."; rm -r $projId");
        #        }
        #    }
        #}

    }

    function cleanGarbage($dir) {
        $files = glob($dir."/*");
        foreach($files as $filename) {
            if (!(endsWith($filename, ".bin")) && !(endsWith($filename, ".moby")) && !(endsWith($filename, ".png")) && !(endsWith($filename, ".async")) && !(endsWith($filename, ".asyncLog")) && !(endsWith($filename, ".lib")) && !(endsWith($filename, ".frcmod")) && !(endsWith($filename, ".pdb")) && !(endsWith($filename, ".config")) && !(endsWith($filename, ".gro")) && !(endsWith($filename, ".top")) && !(endsWith($filename, ".dat")) && !(endsWith($filename, "input.in")) && !(is_link($filename)) && ($dir)) {

		logger("WARNING: cleanGarbage, removing $filename!!");
	        system ("cd ".$dir."; rm $filename");
            }

	    if( endsWith($filename,".jmol.pdb") ){
		logger("WARNING: cleanGarbage, removing $filename!!");
	        system ("cd ".$dir."; rm $filename");
	    }
        }
    }

    function cleanAnonymousUser($id, $login) {
    	//CF fixed injection
    	$login   = mysql_real_escape_string($login);
    	$id      = mysql_real_escape_string($id);
    	$basedir = escapeshellarg($GLOBALS['baseDir']);
    	
        execSql ("delete from users where login = '".$login."'");
        execSql ("delete from projects where idUser = '".$id."'");
        $dirname = "$basedir/$login";
        if ($login && file_exists($dirname)) 
            delete_directory($dirname);
    }
    
	function printIntegrityWarnings($id) {
        $projDir = $_SESSION['userData']['workDir'];
		$filePath = "$projDir/$id/root.moby.integrity";
		if (file_exists ($filePath)){
			if ($file = fopen ($filePath, "r")){
				while(!feof($file)){     
					$buffer = fgets($file,4096); 
					echo str_replace("\n", "<br>", $buffer); 
				}
				fclose($file);
			} else {
				echo "The warnings file cannot be opened";
			}
		}

	}

	function printLigands($id) {
   	     $projDir = $_SESSION['userData']['workDir'];
		$filePath = "$projDir/$id/root.moby.ligands";
		if (file_exists ($filePath)){
			if ($file = fopen ($filePath, "r")){
				while(!feof($file)){     
					$buffer = fgets($file,4096);
					echo str_replace("\n", "<br>", $buffer); 
				}
				fclose($file);
			} else {
				echo "The ligands file cannot be opened";
			}
		} 

	}

	function checkStiffnessMatrices($file) {

		# Needed (and valid) Tetramers.
		$tetramers['AATT'] = 1; $tetramers['ACGT'] = 1; $tetramers['AGCT'] = 1; $tetramers['ATAT'] = 1;
		$tetramers['CATG'] = 1; $tetramers['CCGG'] = 1; $tetramers['CGCG'] = 1; $tetramers['CTAG'] = 1;
		$tetramers['GATC'] = 1; $tetramers['GCGC'] = 1; $tetramers['GGCC'] = 1; $tetramers['GTAC'] = 1;
		$tetramers['TATA'] = 1; $tetramers['TCGA'] = 1; $tetramers['TGCA'] = 1; $tetramers['TTAA'] = 1;

		# Read Tetramers:
		$readTets;

		if (file_exists ($file)){
			if ($f = fopen ($file, "r")){
				while(!feof($f)){     
					$line = fgets($f);

					$line = str_replace("\n", '', $line); // remove new lines
					$line = str_replace("\r", '', $line); // remove carriage returns

					if(preg_match("/^#/",$line)) continue;
					if(!$line) continue;

					if(preg_match('/^[ACGT]{4}$/',$line)){
						if(!$tetramers[$line]){
							#echo "SEQ INVALID line: $line <br>"; 
							return 1;
						}
						else{
							$readTets[$line]=1;
						}
					}
					else if(preg_match("/\d+/",$line)){
						#echo "Values line: $line <br>";
						$array = preg_split('/\s+/',$line);
						$cont = 0;
						foreach ($array as $k => $v){
							if(!$v) continue;
							if(preg_match("/\d\.\d{5}/",$v)){
								#echo "Valid Value: $v <br>";
								$cont++;
							}
							else{
								#echo "INValid Value: $v <br>"; 
							}
						}
						if($cont != 6) {
							#echo "INValid Values Line: $line<br>";
							return 1;
						}
					}
					else{
						#echo "Invalid line: $line<br>";
						return 1;
					}
				}
				fclose($f);
				
				$l = count($readTets);
				if ($l != 16){
					#echo "INValid Tetramers Length: $l <br>";
					return 1;
				}

				#foreach ($readTets as $k => $v){
				#	echo "READ TETS: $k <br>";
				#}

			} else {
				return 1;
				#echo "The Stiffness Matrices file cannot be opened";
			}
		}
		return 0; 
	}

?>
