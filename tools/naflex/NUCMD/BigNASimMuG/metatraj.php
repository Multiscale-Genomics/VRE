<?php
require "phplib/global.inc.php";
#

$traj = $_POST['idCode'];
$idSession = $_POST['idSession'];
$mask = $_POST['mask'];
$frames = $_POST['frames'];
$format = $_POST['format'];
$type = $_POST['type'];

/*
$data = getNUCDBData($_REQUEST['idCode'], True);
if (!$_REQUEST['idCode'] or !$data['_id']) {
    print errorPageMMB("Error", "<h3>Unknown</h3>");
    exit;
}
$data['idCodelc'] = strtolower($data['_id']);
*/

#print headerMMB("BIGNASim database structure and analysis portal for nucleic acids simulation data");
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
	#$url = "http://ms2/analysis";
	$url = "http://m002/analysis";
	$format = "crd";
}

$data = array(
  'idSession' => $idSession,
  'idTraj' => $traj,
  'mask' => $mask,
  'frames' => $frames,
  'format' => $format
);
$data_string = json_encode($data);
#echo "$data_string";

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
    print errorPageMMB("Error", $errorCurl);
    exit;
}

$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
 
if ( $status != 200 ) {
    #print errorPageMMB("Error", $errorCurl);
    redirect("error.php");
    exit;
    #   die("Error: call to Ramones-trajectory-generator failed with status $status, curl_errno() "
    #           .curl_errno($curl).", curl_error() ".curl_error($curl).", response $json_response");
}

curl_close($curl);

$response = json_decode($json_response, true);

#print "<pre>";
#print "Call to Ramones-trajectory-generator:";
#print "Response: $response";
#print "idTraj: $traj, Mask: $mask, Frames: $frames, Format: $format, Type: $type";
#print "</pre>";
#exit;
#$top = $response[top_path];
#$traj = $response[traj_path];

$topName = $response[top_name];
$trajName = $response[traj_name];

$pdbGFS = getGSFile($GLOBALS['cassandra'], $topName);
$trajGFS = getGSFile($GLOBALS['cassandra'], $trajName);

$top = "/tmp/$topName";
$traj = "/tmp/$trajName";

$fout=fopen($top, "w");
fwrite($fout, $pdbGFS);
fclose($fout);

$fout=fopen($traj, "w");
fwrite($fout, $trajGFS);
fclose($fout);

if (!file_exists($top) || !file_exists($traj)) {
	print "Uuups, we couldn't generate the trajectory... Please try it again later.";
}

if($type == 'naflex'){
	$traj2 = str_replace(".crd",'',$trajName);
	redirect("../NAFlex2/metatraj2NAFlex.php?id=$traj2&ext=$format&mask=$mask&frames=$frames");
}

system ("cd /tmp; tar -czf metatraj.tgz $topName $trajName");

$name = "metatraj.tgz";
$tarfile = "/tmp/$name";
$mimeType="application/x-compressed";
if (file_exists($tarfile) && is_readable($tarfile)) {
        header("Content-Disposition: attachment; filename=$name");
        header("Content-Type: $mimeType");
        header("Content-Length: " . filesize($tarfile));
        readfile($tarfile);
}

##$response = file_get_contents(http://localhost:5000/trajectoryroot);
## GEN_TRAJ_CASSANDRA ($traj, $mask, $frames, $format)

print footerMMB();
?>

