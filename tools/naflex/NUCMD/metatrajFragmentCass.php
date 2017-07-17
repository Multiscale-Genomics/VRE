<?php
require "phplib/global.inc.php";
#

$idSession = $_REQUEST['idSession'];
$format = $_REQUEST['format'];
$frames = $_REQUEST['frames'];
$type = $_REQUEST['type'];

$input = $_SESSION['metaTrajList'];

#echo "$input <br/>$format<br/>$frames<br/>";

$jinput = json_encode($input);
#echo "$jinput <br/>";

#$url = "http://ms2/compose";
$url = "http://m002/compose";

#curl -i -H "Content-Type: application/json" -X GET -d '{"id_trajs":{"NAFlex_1d11": ["1"], "NAFlex_1dcw": ["3", "14", "25"], "NAFlex_1tro": ["0", "14"], "NAFlex_36merSPCE": ["16"], "NAFlex_36merTIP3P": ["16"]}, "motiff_length": "6"}' http://ms2:5002/compose

$data = array(
  'idSession' => $idSession,
  'id_trajs' => $input,
  'frames' => $frames,
  'format' => $format
);
$data_string = json_encode($data);

#echo "$data_string<br/>";

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

if($type == 'naflex'){
	$traj2 = str_replace(".crd",'',$trajName);
	$format = 'crd';
	redirect("../NAFlex2/metatraj2NAFlex.php?id=$traj2&ext=$format&mask=SelFragment&frames=$frames");
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

#$response = file_get_contents(http://localhost:5000/trajectoryroot);
# GEN_TRAJ_CASSANDRA ($traj, $mask, $frames, $format)

