<?php
require "phplib/global.inc.php";
require "phplib/aux.inc.php";
#

$traj = $_POST['idCode'];
$idSession = $_POST['idSession'];
$mask = $_POST['mask'];
$frames = $_POST['frames'];
$format = $_POST['format'];
$type = $_POST['type'];

$framesTxt = str_replace(":","_",$frames);
$fileId = uniqId("BIGNASim");
$name = "$fileId-$traj-$framesTxt";
$desc = "Subtrajectory of $traj with $framesTxt frames selected";

/*
$data = getNUCDBData($_REQUEST['idCode'], True);
if (!$_REQUEST['idCode'] or !$data['_id']) {
    print errorPageMMB("Error", "<h3>Unknown</h3>");
    exit;
}
$data['idCodelc'] = strtolower($data['_id']);
*/

#print headerMMB("PDB mirror", array(), False);
#print "<pre>";
#print "Call to Ramones-trajectory-generator with:";
#print "idTraj: $traj, Mask: $mask, Frames: $frames, Format: $format, Type: $type";
#print "idTraj: $traj, Mask: $mask, Frames: $frames, Format: $format, Type: $type";
#print "</pre>";

#$url = "http://localhost:5000/trajectory";
#$url = "http://ms2/trajectory";
#$url = "http://ms2/download";
$url = "http://m002/download";

if($type == 'naflex'){
	$url = "http://ms2/analysis";
	$format = "mdcrd";
}

$data = array(
  'idSession' => $idSession,
  'idTraj' => $traj,
  'name' => $name,
  'description' => $desc,
  'mask' => $mask,
  'frames' => $frames,
  'format' => $format
);
$data_string = json_encode($data);
#echo "$data_string";

# If NAFlex redirection, curl call is not asynchronous (by now)
if($type == 'naflex'){
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
	    'Content-Type: application/json',
	    'Content-Length: ' . strlen($data_string))
	);
	$json_response = curl_exec($curl);

	// Checking if an error occurred
	if(curl_errno($curl))
	{
	    #echo 'Curl error: ' . curl_error($curl);
	    $errorCurl = 'Curl error: ' . curl_error($curl);
	    redirect("error.php");
	    #print errorPageMMB("Error", $errorCurl);
	    #exit;
	}

	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

	if ( $status != 200 ) {
	    #print errorPageMMB("Error", $errorCurl);
	    redirect("error.php");
	    exit;
	}

	curl_close($curl);

	$response = json_decode($json_response, true);

	$topName = $response[top_name];
	$trajName = $response[traj_name];

	$pdbGFS = getGSFile($GLOBALS['cassandra'], $topName);
	$trajGFS = getGSFile($GLOBALS['cassandra'], $trajName);

	$top = "/tmp/$topName";
	$traj = "/tmp/$trajName";

	$dirname = dirname($top);
	if (!is_dir($dirname))
	    mkdir($dirname, 0755, true);

	$dirname = dirname($traj);
	if (!is_dir($dirname))
	    mkdir($dirname, 0755, true);

	$fout=fopen($top, "w");
	fwrite($fout, $pdbGFS);
	fclose($fout);

	$fout=fopen($traj, "w");
	fwrite($fout, $trajGFS);
	fclose($fout);

	if (!file_exists($top) || !file_exists($traj)) {
	        print "Uuups, we couldn't generate the trajectory... Please try it again later.";
	}

        $traj2 = str_replace(".crd",'',$trajName);
	$formatNAF = 'crd';
        redirect("../NAFlex2/metatraj2NAFlex.php?id=$traj2&ext=$formatNAF&mask=$mask&frames=$frames");
}

$workdir = $GLOBALS['tmpDir']."/".$idSession;
if (!file_exists("$workdir")) {
    mkdir("$workdir", 0777, true);
}
chdir($workdir);

# Call to Ramones-trajectory-generator with data_string
$cmd = queueCURL ($fileId, $data_string, $url);

# Queueing Curl execution.
#$pid = execCURL ($workdir,$cmd);
$pid = execCURL (".",$cmd);

# Saving the pid and the session
$_SESSION["pid"] = $pid;
$_SESSION["SGE"][$pid]["name"] = $name;
$_SESSION["SGE"][$pid]["desc"] = $desc;
$_SESSION["SGE"][$pid]["fileId"] = $fileId;

saveSession ($idSession);

# Redirect to waitResults
redirect("waitResults.php?idSession=" . $idSession);
?>

