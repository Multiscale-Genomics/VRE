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

if (! $_SESSION['User']['Uploader'] == "on"){
	print errorPage("Error", "User". $_SESSION['User']['id']." is not allowed to write into the workspace. Register a user with the right configuration");
	exit(0);
}

###
### store step info to session
###

if (isset($_REQUEST['curr'])){
	if (isset($_REQUEST['metaFile']) && strlen($_REQUEST['metaFile'])== 0 )
		unset($_REQUEST['metaFile']);
	$_SESSION[$_REQUEST['curr']]=$_REQUEST;
}
if ($_REQUEST['clean']){
	unset($_SESSION['step1']);
	unset($_SESSION['step2']);
}
###
### process current form before advancing step
###
switch ($_REQUEST['curr']) {

    case 'step1' :

	// check and save attached files
	if (!isset($_REQUEST['trajFile'])){
            $errStr = "No trajectory file selected.";
	    break;
	}else{
	    foreach($_REQUEST['trajFile'] as $i => $b){
		if ($_REQUEST['trajFormat'][$i] == "None" ){
        	    $errStr = "File type for ".$_REQUEST['trajFile'][$i]." needs to be specified";
	            break;
		}
		#$errStr = validate_format($_REQUEST['trajFile'][$i],$_REQUEST['trajFormat'][$i]);
		if ($errStr)
		    break;
	    }
	}
	if (!isset($_REQUEST['topFile'])){
            $errStr = "No topology file selected.";
	    break;
	}else{
	    foreach($_REQUEST['topFile'] as $i => $b){
		if ($_REQUEST['topFormat'][$i] == "None" ){
        	    $errStr = "File type for ".$_REQUEST['topFile'][$i]." needs to be specified";
	            break;
		}
		#$errStr = validate_format($_REQUEST['topFile'][$i],$_REQUEST['topFormat'][$i]);
		if ($errStr)
		    break;
	    }
	}
	if (isset($_REQUEST['docFile'])){
	    foreach($_REQUEST['docFile'] as $i => $b){
		if ($_REQUEST['docFormat'][$i] == "None" ){
        	    $errStr = "File type for ".$_REQUEST['docFile'][$i]." needs to be specified";
	            break;
		}
		if ($errStr)
		    break;
	    }
	}
	if (isset($_REQUEST['metaFile'])){
		if ($_REQUEST['metaFormat']== "None" ){
        	    $errStr = "File type for ".$_REQUEST['metaFile']." needs to be specified";
	            break;
		}
		$metaData = readMetadataToHash($_REQUEST['metaFile']);
                if (preg_grep("/ERROR/i", $metaData)){
                    $errStr = $metaData;
		    $errStr.= "<br/> Cannot load ".$_REQUEST['metaFile']." . Edit it <a href=\"BNSdatamanager/workspace.php?op=openPlainFile&fn=".urlencode($_REQUEST['metaFile'])."\" >manually</a> or unselect it from the drop menu to create a new one</a>";
	            break;
		}
	 }

	//set submissionID
	if (!isset($_REQUEST['datasetName'])){
            $errStr = "No dataset name selected.";
	}elseif (strlen($_REQUEST['datasetName'])<4 || strlen($_REQUEST['datasetName'])>7 || preg_match('/_| /',$_REQUEST['datasetName'])  ) {
            $errStr = "The dataset name should have between 4 and 7 alphanumeric characters";
	    break;
	}else{
	    $datasetNameClean = str_replace(' ', '', $_REQUEST['datasetName']);
	    $datasetNameClean = preg_replace('/[^A-Za-z0-9\-_]/', '', $datasetNameClean);
	    $_SESSION['step1']['datasetName']= $datasetNameClean; 

	    $submissionID  = "BNS_".$datasetNameClean."_0000";
	    for ($n=0; $n<=9999;$n++){
	    	$submissionID  = "BNS_".$datasetNameClean."_".sprintf('%04d',$n);
		$file= $GLOBALS['submissions']->findOne(array('identifier'=> $submissionID));
		if (empty($file))
			break;
	    }
	    $_SESSION['step1']['submissionID']= $submissionID;
	}
	break;


    case 'step2':
	// check inputs from step2
	if (! isset($_SESSION['step1']['trajFile']) ){
	    $errStr = "Cannot save metadata. Any trajectory file defined. Return to <a href=\"javascript:void(0);\" onclick=\"document.gesDeposition.op.value='step1';document.gesDeposition.submit();\" >step 1</a>";
	    break;
	}
	$metaFile="";
	// overwrite Metadata file
	if ( isset($_SESSION['step1']['metaFile']) ){
	    $metaFile = $_SESSION['step1']['metaFile'];
	    $_SESSION['step1']['metaFormat'] = "meta_csv";
	}else{
	// write new Metadata file
	    $metaFn   = pathinfo($_SESSION['step1']['trajFile'][0],PATHINFO_FILENAME).".csv";
	    $metaFile = (pathinfo($_SESSION['step1']['trajFile'][0],PATHINFO_DIRNAME) == "." ? $metaFn : pathinfo($_SESSION['step1']['trajFile'][0],PATHINFO_DIRNAME)."/".$metaFn );
	    $_SESSION['step1']['metaFile']   = $metaFile;
	    $_SESSION['step1']['metaFormat'] = "meta_csv";
	}
	$err = writeMetadataFromHash($metaFile,($_REQUEST+$_SESSION['step1']));
	if ($err){
	    $errStr= "Cannot create a valid metadata file from the given data<br/>";
	    $errStr.= $err;
	    break;
	}
	break;


    case 'step3':
	// check inputs
	if (! isset($_SESSION['step1']['submissionID']) ){
		$errStr=" Submission identifier not found";
		break;
	}
	if (! isset($_SESSION['step1']['metaFile']) ){
		$errStr.=" No metadata file found";
		break;
	}
        $metaData=readMetadataToHash($_SESSION['step1']['metaFile']);
        if (preg_grep("/ERROR/i", $metaData)){
		$errStr.= implode("<br/>",$metaData);
		break;
	}

	// create submission folder
	$r = createGSDirBNS($GLOBALS['cassandraIds'],$_SESSION['BNSId']."/submissions/".$_SESSION['step1']['submissionID']);
	if ($r == 0)
		break;
	

	// move files into submission folder
	$log     = $_SESSION['BNSId']."/submissions/".$_SESSION['step1']['submissionID']."/SUBMISSION.log"; 
	$logTemp = $GLOBALS['tmpDir']."/SUBMISSION_".$_SESSION['BNSId'].".log"; 
	$now = strtotime("now");
	$F = fopen($logTemp,"w");
	fwrite($F,"#### SUBMISSION LOG FILE ####\n");

	$_SESSION['step1']['topFile']  = moveFilesToSubmission($_SESSION['step1']['topFile'],$_SESSION['step1']['submissionID']);
	$_SESSION['step1']['trajFile'] = moveFilesToSubmission($_SESSION['step1']['trajFile'],$_SESSION['step1']['submissionID']);
	$_SESSION['step1']['docFile']  = moveFilesToSubmission($_SESSION['step1']['docFile'],$_SESSION['step1']['submissionID']);
	$_SESSION['step1']['metaFile'] = moveFilesToSubmission(array($_SESSION['step1']['metaFile']),$_SESSION['step1']['submissionID']);
	$_SESSION['step1']['metaFile'] = $_SESSION['step1']['metaFile'][0];

	if (isset($_SESSION['errorData']))
		break;

	$files = Array(
		   'topology'   => $_SESSION['step1']['topFile'],
		   'trajectory' => $_SESSION['step1']['trajFile'],
		   'metadata'   => $_SESSION['step1']['metaFile'],
		   'documentation' => $_SESSION['step1']['docFile']
		);

	fclose($F);	
	$file= $GLOBALS['submissions']->findOne(array('identifier'=> $_SESSION['step1']['submissionID']));
        if ( empty($file)){

	   // upload submission collection
	   $GLOBALS['submissions']->insert(
               array('identifier' => $_SESSION['step1']['submissionID'],
		 'dataset'=> $metaData['datasetName'],
		 'owner'  => $_SESSION['BNSId'],
		 'date'   => new MongoDate($now),
		 'status' => "COMMITTED",
		 'files'  => $files 
	         )
               );

	    //upload log file
            $insertData=array(
                        '_id'        => $log,
                        'owner'      => $_SESSION['BNSId'],
                        'size'       => filesize($logTemp),
                        'mtime'      => new MongoDate($now),
			'permissions'=> "000",
			'expiration' => new MongoDate(strtotime("+365 day"))
                        );
            $r = uploadGSFileBNS($log, $logTemp, $insertData);
	   
	    modifyGSFileBNS($_SESSION['BNSId']."/submissions/".$_SESSION['step1']['submissionID'], "permissions", "000");	

	}else{
	    if ($_REQUEST['op'] == "step3"){
		$errStr.= " Cannot save submission. Database already has an entry with submissionID=".$_SESSION['step1']['submissionID'];
		break;
	    }
	}
	break;
}

if ($errStr || $_SESSION['errorData']){
	$_REQUEST['op']   = $_REQUEST['curr'];
}

if ($_REQUEST['op'] == "step2" && !isset($_REQUEST['trajN']) ){
	$_REQUEST['trajN'] = 0;
}

###
###  print forms according to the selected op
###

print headerTP('');

?>

<div style="clear:both"></div>
<br/>
<br/>
    <p>
	We recommend that you take a moment to review the submission  <a href="help.php?id=submission" target="_blank">procedure</a> before you start a deposition for the first time
    <p/> 

<?php
if ($errStr) {
    print "<div class=\"notify\"><img src=\"BNSdatamanager/images/delete.png\" align=\"left\" style=\"height:20px;\">$errStr</div>";
    $errStr = '';
}?>

<form name="gesDeposition" id="gesDeposition" action="BNSdatamanager/deposition.php" method="post" enctype="multipart/form-data">


  <?php if (!isset($_REQUEST['op']) || $_REQUEST['op'] == "step1" ){ ?>

        <div id="step1">
	    <input type="hidden" name="curr" id="op"  value="step1"/>
	    <input type="hidden" name="op"  id="op" value="step2"/>
	    <hr>
	    <span style="font-weight:bold; text-align:left; font-size:14px;display:block;">
		Step1 &#65073; Submission
	    </span>
	    <div style="clear:both"></div>

	    <p style="text-align:left;margin: 30px 0 10px 5px;"><b>Dataset</b> - Group a collection of simulations into a dataset to better search and administrate your data</p>	
	    <table style="background:none; margin:5px;padding:5px;">
	     <colgroup>
	        <col style="width:13%;">
	        <col/>
	     </colgroup>
	    <tr>
		<td>Dataset name (<b>*</b>)</td>
		<td><input type="text" size="7" name="datasetName" value="<?php echo $_SESSION['step1']['datasetName'];?>" required/></td>
	    </tr>
	    <tr>
		<td>Dataset description</td>
		<td><textarea rows="2" name="datasetDescription" ><?php echo $_SESSION['step1']['datasetDescription'];?></textarea></td>
	    </tr>
	    <tr>
            	<td>Dataset publication (<b>*</b>)</td>
		<td>
		    <input type="radio" id="publDateSys" name="publDateSys" value="release" onclick="hiddenFromRadio(this,'release','date');" required <?php if ($_SESSION['step1']['publDateSys'] == "release") { echo "checked";}?>> Release once submission is accepted
		    <br/>
		    <input type="radio" id="publDateSys" name="publDateSys" value="hold"    onclick="hiddenFromRadio(this,'release','date');" required <?php if ($_SESSION['step1']['publDateSys'] == "hold") { echo "checked";}?>> Hold dataset until certain date
		    &nbsp; &nbsp;
		    <span id="date" style="visibility:hidden;display:none;">
		    <input type="text" id="publDate" name="publDate" size="10" value="<?php echo $_SESSION['step1']['publDate'];?>"/> Format (yyyy/mm/dd)
		    </span>
		</td>
	    </tr>
	    <tr>
            	<td style="vertical-align:top;">Publication/s (<b>*</b>)</td>
		<td>
		  <input type="radio" id="pubSys" name="pubSys" value="document" onclick="hiddenFromRadio(this,'document','pubsDiv');" required <?php if ($_SESSION['step1']['pubSys'] == "document") { echo "checked";}?>> The dataset has not a reference publication yet.
			<span style="font-size:0.8em;">(Project documentation will be uploaded on the following section)</span>
		  <br/>
		  <input type="radio" id="pubSys" name="pubSys" value="reference" onclick="hiddenFromRadio(this,'document','pubsDiv');" required <?php if ($_SESSION['step1']['pubSys'] == "reference") { echo "checked";}?> > Reference publication/s are the following
		  <div id="pubsDiv" style="visibility:hidden;display:none;">
    		    <table id="pubs" style="margin:10px 0; border:1px solid;background:none" >
		      <tr>
			<th>Title</th>
			<th>Authors</th>
			<th>Journal</th>
			<th>Year</th>
			<th>Num (Vol)</th>
			<th colspan="2">DOI</th>
		       <tr>
                    <?php
		    if (isset($_SESSION['step1']['pubTitle']) ){
		    	foreach($_SESSION['step1']['pubTitle'] as $i => $b){
			    $pub= $_SESSION['step1']['pubTitle'];
			    printRow_publication("pubs",$_SESSION['step1']['pubTitle'][$i],$_SESSION['step1']['pubAuth'][$i],$_SESSION['step1']['pubJourn'][$i],$_SESSION['step1']['pubYear'][$i],$_SESSION['step1']['pubVol'][$i],$_SESSION['step1']['pubDOI'][$i]);
			}
		    }else{
			printRow_publication("pubs","","","","","","");
		    }
		    ?>
		    </table>
		    <a href="javascript:void(0)" onclick="addRow('pubs')">[ Add new ]</a>
		  </div>
		</td>
	    </tr>
	    </table>

	    <p style="text-align:left;margin: 30px 0 10px 5px;"><b>Files to submit</b> - Select the files that will compose your submission. Only those files previously uploded to the <a href="BNSdatamanager/workspace.php">workspace</a> can be here selected</p>
	    <table style="background:none; margin:5px;padding:5px;">
	     <colgroup>
	        <col style="width:13%;">
	        <col/>
	     </colgroup>

	    <tr>
	    	<td>Trajectory File/s (<b>*</b>)</td>
		<td>
	    	<table id="tblTraj"  style="margin:10px 0; border:1px solid;" >
		    <tr>
			<th>File Name</th>
			<th colspan="2">File Format</th>
		    </tr>
		    <?php
		    $formatSelect = Array("traj_crd" => "MD trajectory AMBER CRD",
					  "traj_dcd"=>"MD trajectory CHARMM/NAMD DCD", 
					  "traj_cdf"=>"MD trajectory AMBER NetCDF",
					  "traj_gro"=>"MD trajectory AMBER BINPOS",
					  "traj_xtc"=>"MD trajectory Gromacs XTC"
					 );

		    if (isset($_SESSION['step1']['trajFile']) ){
			foreach($_SESSION['step1']['trajFile'] as $i => $b){
			     printRow_file("trajFile[]","trajFormat[]","tblTraj",$formatSelect,$_SESSION['step1']['trajFile'][$i],$_SESSION['step1']['trajFormat'][$i],"required");
			}
		    }else{
			     printRow_file("trajFile[]","trajFormat[]","tblTraj",$formatSelect,"None","None","required");
		    }
		    ?>
		</table>
		<a href="javascript:void(0)" onclick="addRow('tblTraj')">[ Add new ]</a>
		</td>
	    </tr>
	    <tr>
		<td>Topology File/s(<b>*</b>)</td>
		<td>
		<table id="tblTop"  style="margin:10px 0; border:1px solid;" >
                    <tr>
                        <th>File Name</th>
                        <th colspan="2">File Format</th>
                    </tr>
                   <?php
                    $formatSelect = Array("top_pdb" => "topology PDB",
                                          "top_prmtop"=>"topology AMBER PRMTOP",
                                          "top_psf"=>"topology NAMD PSF",
                                          "top_top"=>"topology GROMACS TOP",
                                          "top_itp"=>"topology GROMACS ITP",
                                          "top_rtp"=>"topology GROMACS RTP"
                                         );
                    if (isset($_SESSION['step1']['topFile']) ){
                        foreach($_SESSION['step1']['topFile'] as $i => $b){
                             printRow_file("topFile[]","topFormat[]","tblTop",$formatSelect,$_SESSION['step1']['topFile'][$i],$_SESSION['step1']['topFormat'][$i],"required");
                        }
                    }else{
                             printRow_file("topFile[]","topFormat[]","tblTop",$formatSelect,"None","None","required");
                    }
                    ?>
		</table>
                <a href="javascript:void(0)" onclick="addRow('tblTop')">[ Add new ]</a>
		</td>
	    </tr>
	    <tr>
		<td>Simulation metadata</td>
		<td>
		<table id="tblMeta"  style="margin:10px 0; border:1px solid;" >
                    <tr>
                        <th>File Name</th>
                        <th colspan="2">File Format</th>
                    </tr>
	            <tr>
	               <td>
	                <select name="metaFile">
	                    <option  value="" <?php if (!$_SESSION['step1']['metaFile']) { echo "selected";}?>  >Create a new one</option>
	                    <?php
			     $uploadsC = $GLOBALS['cassandraIds']->find(array('owner' => $_SESSION['BNSId'],
                                                                     'userUpload'=>1,
                                                                     'parentDir' => array('$not' => new MongoRegex('/'.$_SESSION['BNSId'].'/submissions\//i'))
                                                                    )); 
	                    if ($uploadsC->count() > 0 ){
	                            foreach ($uploadsC as $file) {
	                                $name = str_replace($_SESSION['BNSId']."/","",$file['_id']);
	                                if ($file['_id'] == $_SESSION['step1']['metaFile'] ){
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
	                <select name="metaFormat" style="width:240px;" >
	                    <option  value="meta_csv" <?php if ($_SESSION['step1']['metaFormat'] == "meta_csv") { echo "selected";}?> >metadata file CSV</option>
	                </select>
        	    </td>
		    <td>&nbsp;</td>
		    </tr>
		</table>
		</td>
	    </tr>
	    <tr>
		<td>Dataset documentation</td>
		<td>
		<table id="tblDoc"  style="margin:10px 0; border:1px solid;" >
                    <tr>
                        <th>File Name</th>
                        <th colspan="2">File Format</th>
                    </tr>
                   <?php
                    $formatSelect = Array("doc_pdf"   => "document PDF",
                                          "doc_other"=> "other document format"
                                         );
                    if (isset($_SESSION['step1']['docFile']) ){
                        foreach($_SESSION['step1']['docFile'] as $i => $b){
                             printRow_file("docFile[]","docFormat[]","tblDoc",$formatSelect,$_SESSION['step1']['docFile'][$i],$_SESSION['step1']['docFormat'][$i],"");
                        }
                    }else{
                             printRow_file("docFile[]","docFormat[]","tblDoc",$formatSelect,"None","None","");
                    }
                    ?>
		</table>
                <a href="javascript:void(0)" onclick="addRow('tblDoc')">[ Add new ]</a>
		</td>
	    </tr>
	    </table>
	    

	    <p>(*) Obligatory fields</p>
	    <br/>

	    <input type="submit" value=">> Submit files "/>
	    <br/><br/>
        </div>



  <?php }elseif ($_REQUEST['op'] == "step2"){?>

	<div id="step2" style="text-align:left;" >
	    <input type="hidden" name="curr" id="curr" value="step2"/>
	    <input type="hidden" name="op"   id="op"   value="step3"/>
            <hr>

             <div style="float:right;padding-right:100px;">
                <a href="javascript:void(0);" onclick="document.gesDeposition.op.value='step1';document.gesDeposition.submit();"> [ previous step ]</a>
            </div>

            <span style="font-weight:bold; text-align:left; font-size:14px;display:block;">
                Step 2 &#65073; Simulation Metadata
            </span>
	    <br/><br/>
            <div style="clear:both"></div>

	    <?php
	    $metaData=Array();
	    if (isset($_SESSION['step1']['metaFile'])){
		$metaData=readMetadataToHash($_SESSION['step1']['metaFile']);
                if (preg_grep("/ERROR/i", $metaData)){
                     $errStr.= $metaData;
		     $errStr.= "<br/> Ignoring the given metada file";
    		     print "<div class=\"notify\"><img src=\"BNSdatamanager/images/delete.png\" align=\"left\" style=\"height:20px;\">$errStr</div>";
    		     $errStr = '';
		     $metaData=Array();
                }else{
		    print "<h3 style=\"text-align:left;font-size:inherit\">  Loading metadata from file : ".$_SESSION['step1']['metaFile']."</h3>";
		}

	    }elseif (isset($_SESSION['step2'])){
		$metaData=$_SESSION['step2'];
	    }
	    ?>
	    <ul>
		<li><b>General description</b>: </li>
		<li>
	             <ul>
	  	     <?php printTrajDescription($metaData);?>
	             </ul>
		</li>

		</br>
		<li><b>Trajectory annotation</b>: Check the onotological terms that better describe the uploded trajectory</li>
			<li>
	             <ul>
	  	     <?php printOntoMongo($GLOBALS['ontology'],$metaData);?>
	             </ul>
		</li>

		</br>	</br>
		<li><b>Preliminar analyses</b>: Simulation quality control parameters required to validate the submitted trajectory.</li>

		<li>
		    <ul>
	            <table style="background:none; margin:5px;padding:5px;">	
	            <tr>
	                <td>RMSd <sup>&#8225;</sup>(*)</td>
	                <td><input type="text" name="rmsd" size="5" value="<?php echo $_SESSION['step2']['rmsd'];?>"/> (&#8491;)</td>
			<td>&nbsp;</td>
	                <td>RMSd/bp <sup>&#8225;</sup>(*)</td>
	                <td><input type="text" name="rmsd_bp" size="5" value="<?php echo $_SESSION['step2']['rmsd_bp'];?>"/> (&#8491;/bp) </td>
	             </tr>
	            <tr>
	                <td>R. Gyration Variation (*)</td>
	                <td><input type="text" name="Rgyr" size="5" value="<?php echo $_SESSION['step2']['Rgyr'];?>"/> (&#8491;/bp)</td>
			<td>&nbsp;</td>
	                <td>Lost of WC HBonds (*)</td>
	                <td><input type="text" name="lostWC" size="5" value="<?php echo $_SESSION['step2']['lostWC'];?>"/> (&#37;)</td>
	            </tr>
	            <tr>
	                <td>Lost of 3D contacts (*)</td>
	                <td><input type="text" name="lostContacts" size="5" value="<?php echo $_SESSION['step2']['lostContacts'];?>"/> (&#37;)</td>
			<td>&nbsp;</td>
	                <td>Presence of fraying (*)</td>
	                <td><input type="text" name="fraying" size="5" value="<?php echo $_SESSION['step2']['fraying'];?>"/></td>
	            </tr>
	            <tr>
	                <td>Global average Twist (*)</td>
	                <td><input type="text" name="avgTwist" size="5" value="<?php echo $_SESSION['step2']['avgTwist'];?>"/> (degrees)</td>
			<td>&nbsp;</td>
	                <td>Global average Roll (*)</td>
	                <td><input type="text" name="avgRoll" size="5" value="<?php echo $_SESSION['step2']['avgRoll'];?>"/> (degrees)</td>
	            </tr>
	            <tr>
	                <td>Major groove size (*)</td>
	                <td><input type="text" name="majorGrooveSize" size="10" value="<?php echo $_SESSION['step2']['majorGrooveSize'];?>" /> width x depth (&#8491;)</td>
			<td>&nbsp;</td>
	                <td>Groove size method (*)</td>
	                <td><input type="text" name="grooveSizeMethod" value="<?php echo $_SESSION['step2']['grooveSizeMethod'];?>" /></td>
	            </tr>
	            <tr>
	                <td>Minor groove size (*)</td>
	                <td><input type="text" name="minorGrooveSize" size="10" value="<?php echo $_SESSION['step2']['minorGrooveSize'];?>" /> width x depth (&#8491;)</td>
			<td>&nbsp;</td>
			<td colspan="2">&nbsp;</td>
	            </tr>
	            </table>
		    </ul>
		</li>
                <p>(*) Obligatory fields</br>
		   (&#8225;) Reference for RMSd should be the experimental structure when available.
		</p>
	    </ul>
            <br/>

	   <input type="submit" value=">> Save metadata "/>
	   <br/><br/>
	</div>


  <?php }elseif ($_REQUEST['op'] == "step3"){ ?>
	<div id="step3" style="text-align:left;" >

            <input type="hidden" name="curr"  id="curr"  value="step3"/>
	    <input type="hidden" name="op"    id="op"  value="step3"/>
	    <hr>
	    <span style="font-weight:bold; text-align:left; font-size:14px;display:block;">
		Step3 &#65073; Confirmation
	    </span>
	    <div style="clear:both"></div>

             <div style="float:right;padding-right:100px;">
                <a href="BNSdatamanager/deposition.php?op=step1"> [ step 1 ]</a>
		&nbsp;
                <a href="BNSdatamanager/deposition.php?op=step2"> [ step 2 ]</a>
            </div>

	    <?php
	    $submission="READY TO SEND";
	    $subDate   = "";
	    $subFiles  = Array();
	    $file= $GLOBALS['submissions']->findOne(array('identifier'=> $_SESSION['step1']['submissionID']));
	    if (! empty($file)){
		$submission= $file['status'];
		$subDate   = strftime('%d %b %G - %H:%M', $file['date']->sec);
		$subFiles  = $file['files'];
	    }else{
		if (isset($_SESSION['step1']['metaFile'])){
		    $metaData=readMetadataToHash($_SESSION['step1']['metaFile']);
		    if (preg_grep("/ERROR/i", $metaData)){
                    	$errStr.= $metaData[0];
                     	$errStr.= "<br/>Return to <a href=\"javascript:void(0);\" onclick=\"document.gesDeposition.op.value='step2';document.gesDeposition.submit();\" >step 2</a> to edit  simulation metadata";
		    }else{
			foreach( $metaData as $k => $v){
			    if (preg_match('/file/i',$k)){
				$subFiles[$k] = $v;
			    }
			}
		    }
		} else {
		    $errStr = "No simulation metadata found. Define it at <a href=\"javascript:void(0);\" onclick=\"document.gesDeposition.op.value='step2';document.gesDeposition.submit();\" >step 2</a>";
		}
            }
	    ?>

	     <p style="text-align:left;margin: 30px 0 10px 5px;"><b>Submission process</b></p>
	     <table style="background:none; margin:5px;padding:5px;">
		 <colgroup><col style="width:25%;"></col></colgroup>
		 <tr><td>Submission Identifier</td><td><?php echo $_SESSION['step1']['submissionID'];?></td></tr>
		 <tr><td>Status</td><td><?php echo $submission;?></td></tr>
		 <tr><td>Submission date</td><td><?php echo $subDate;?></td></tr>
	     </table>

	     <p style="text-align:left;margin: 30px 0 10px 5px;"><b>Files attached to the submission</b></p>
	     <table style="background:none; margin:5px;padding:5px;">
		<colgroup><col style="width:25%;"></col></colgroup>
	        <?php foreach( $subFiles as $k => $v){
		    if (is_array($v)){
			$v= implode("<br/>",$v);
		    }
		    $v=str_replace(",","<br/>",$v);
		    if ($k == "metaFile"){
			 $v = trim($v);
		         print "<tr><td> $k </td><td><a href=\"BNSdatamanager/workspace.php?op=openPlainFile&fn=".urlencode($v)."\" target=\"_blank\">$v</a></td><tr/>\n";
		    }else{
		         print "<tr><td> $k </td><td> $v</td><tr/>\n";
		    }
	        }?>
	     </table>
	</div>
	<?php if ($submission == "READY TO SEND"){ ?>
	    <input type="submit" value=">>Complete Submission"/>
	<?php }else{ ?>
	    <p><b>Submission successfully sent!</b></p>
	    <p style="margin:0 50px;">Your simulation data has been sent. During the following days the petition will be validated. Return to your personal <a href="BNSdatamanager/workspace.php">workspace</a> to keep track of your submissions status.</p>
	<?php }?>
        <br/><br/>

 <?php }else{?>
	<pre>OTHER OP set</pre>
	<pre><?php echo $_REQUEST['op']?></pre>
  <?php }?>

</form>

<script>
  hiddenFromRadio(document.getElementById('pubSys'),'document','pubsDiv');
  hiddenFromRadio(document.getElementById('publDateSys'),'release','date')
</script>

 <?php
if ($errStr) {
    print "<div class=\"notify\"><img src=\"BNSdatamanager/images/delete.png\" align=\"left\" style=\"height:20px;\">$errStr</div>";
    $errStr = '';
}

if (isset($_SESSION['errorData'])) {
    print printErrorData();
    unset($_SESSION['errorData']);
}
print footerTP();
