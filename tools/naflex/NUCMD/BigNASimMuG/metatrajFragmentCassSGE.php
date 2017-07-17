<?php
require "phplib/global.inc.php";
require "phplib/aux.inc.php";
#

$idSession = $_REQUEST['idSession'];
$format = $_REQUEST['format'];
$frames = $_REQUEST['frames'];
$type = $_REQUEST['type'];

$input = $_SESSION['metaTrajList'];
$subSeq = $_SESSION['idSubSeqCentral'];
$trajs = $_SESSION['trajs'];
$selTrajs = array();
foreach ($trajs as $k) {
	$trajsHash[$k] = 1;
	array_push($selTrajs,$k);
}
$numTrajs = count($selTrajs);
$selTrajsTxt = implode(',',$selTrajs);

$framesTxt = str_replace(":","_",$frames);

$fileId = uniqId("BIGNASim");
$name = "$fileId-$subSeq-$framesTxt-$numTrajs"."trajs";
$desc = "Metatrajectory of fragment $subSeq, with frame selection: $framesTxt, from $numTrajs trajectories: $selTrajsTxt";

#echo "$input <br/>$format<br/>$frames<br/>";

$jinput = json_encode($input);
#echo "$jinput <br/>";

#$url = "http://ms2/compose";
$url = "http://m002/compose";

#curl -i -H "Content-Type: application/json" -X GET -d '{"id_trajs":{"NAFlex_1d11": ["1"], "NAFlex_1dcw": ["3", "14", "25"], "NAFlex_1tro": ["0", "14"], "NAFlex_36merSPCE": ["16"], "NAFlex_36merTIP3P": ["16"]}, "motiff_length": "6"}' http://ms2:5002/compose

$data = array(
  'idSession' => $idSession,
  'name' => $name,
  'description' => $desc,
  'id_trajs' => $input,
  'frames' => $frames,
  'format' => $format
);
$data_string = json_encode($data);

#echo "$data_string<br/>";

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
	    print errorPageMMB("Error", $errorCurl);
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

	#echo "TopName: $topName, TrajName: $trajName";

	if ( $top == ">" or $traj == ">" ) {
		print "Uuups, we couldn't generate the trajectory... Please try it again later.";
		return;
	}

	if (!file_exists($top) || !file_exists($traj)) {
		if (0 == filesize( $top ) || 0 == filesize($traj) ) {
			print "Uuups, we couldn't generate the trajectory... Please try it again later.";
			return;
		}
	}

	$traj2 = str_replace(".dcd",'',$trajName);
	$format = 'dcd';
	redirect("../NAFlex2/metatraj2NAFlex.php?id=$traj2&ext=$format&mask=SelFragment&frames=$frames");
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
#$_SESSION['SGE'] = array();
$_SESSION["SGE"][$pid]["name"] = $name;
$_SESSION["SGE"][$pid]["desc"] = $desc;
$_SESSION["SGE"][$pid]["fileId"] = $fileId;

saveSession ($idSession);

# Redirect to waitResults
redirect("waitResults.php?idSession=" . $idSession);
?>

