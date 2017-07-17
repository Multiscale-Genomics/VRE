<?php
require "phplib/global.inc.php";
#

print headerMMB("BIGNASim database structure and analysis portal for nucleic acids simulation data");

if ( $_SESSION['User'] )
        $idSession = $_SESSION['User']['id'];
else
        $idSession = $_SESSION['BNSId'];

# Getting Trajectory ids to look at (coming from Search or Browse)

$selTrajs = array();
if ($_SESSION['trajs']){
	$trajs = $_SESSION['trajs'];
	foreach ($trajs as $k) {
		$trajsHash[$k] = 1;
		array_push($selTrajs,$k);
	}
}
else{
	$trajs = array();
	$trajsHash = array();
	foreach (array_keys($_REQUEST) as $k) {
        	if (preg_match('/NAFlex/',$k)) {
			#echo "$k<br/>";
	                array_push($trajs,$k);
			$trajsHash[$k] = 1;
			array_push($selTrajs,$k);
	        }
	}
}
$_SESSION['trajs'] = $trajs;
$trajsLen = count($trajs);

# Converting php array to Json to use it in jQuery Ajax (mongo queries)
$trajsJson = json_encode($trajs);

# String with all trajectories id, to be shown in the header of the page.
$selection = implode(', ',$selTrajs);

# Subsequence queried, to be emphasized in Sequences shown.
$idSubSeqCentral = ($_SESSION['subSeq'])?$_SESSION['subSeq']:$_REQUEST['idSubSeq'];
$idSubSeq = $idSubSeqCentral;
$_SESSION['idSubSeqCentral'] = $idSubSeq;

# Adding flanking region to the sequence queried.
$flank = ($_SESSION['flank'])?$_SESSION['flank']:$_REQUEST['flank'];
$flankUp = str_repeat('X',$flank);
$flankDown = str_repeat('X',$flank);
$idSubSeqFull = $flankUp.$idSubSeqCentral.$flankDown;
if(empty($idSubSeqFull)) {
        $idSubSeqFull = "ALL_NUCS";
}

$filter = "<mark style='background-color: #FF9900;'>$flankUp</mark><mark>$idSubSeqCentral</mark><mark style='background-color: #FF9900;'>$flankDown</mark>";

$input = $_SESSION['metaTrajList'];

$fragmentLen = $input[0];

# Cutoff for NAFlex redirection:
$naflexLen = $fragmentLen * $trajsLen;

$newInput = array();
$newInput[0] = $fragmentLen;

for($i=1;$i<count($input);$i++){
	$value = $input[$i];
	foreach ($value as $sim => $indexes){
		if ($trajsHash[$sim])
			array_push($newInput,$value);
	}
}

$jinput = json_encode($input);
#echo "$jinput <br/>";

$jnewinput = json_encode($newInput);
#echo "$jnewinput <br/>";

//$_SESSION['metaTrajList'] = $jnewinput;
$_SESSION['metaTrajList'] = $newInput;

?>

<div class="metaImageSection">
    <form id="metatrajFragment" method="post" target="_blank" action="metatrajFragmentCassSGE.php" onsubmit="javascript: submitType();">
    <input type="hidden" name="idCode" value="<?=$data['_id']?>" />
    <input type="hidden" name="idSession" value="<?=$idSession?>" />
    <input type="hidden" name="type" value="download" />
      <div class="metaImage">
        <hr/>
                <h4><a>Meta-Trajectory Generation</a></h4>
        <hr/>
        <div>
                <strong>Fragment</strong> <?=$filter?>
	<br/>
        </div>
        <div>
                <strong>Selected Simulations* :</strong> <?=$selection?>
        </div>
        <div class="inputSelection">
                <strong>Frame</strong> (start:stop:step)
                <input name='frames' id='frames' value='1:5000:100' size='30' />
        </div>
        <div class="metaImage" style="border: none;">
        <div class="trajSelection">
                <strong>Download</strong><img class="metaLogo" alt="" src="images/Download.png"><br/><br/>
                <strong>Format: &nbsp; &nbsp; &nbsp; &nbsp;</strong>
                <select name="format" id="formatSel">
                  <!--<option selected="selected" value="pcz">PCZ-Compressed</option>-->
		  <!--<option value="mdcrd">ASCII CRD</option>-->
                  <option selected="selected" value="dcd">DCD binary</option>
                  <option value="netcdf">NetCDF binary</option>
                  <option value="xtc">Gromacs XTC binary</option>
                  <option value="trr">Gromacs TRR binary</option>
                  <option value="pdb">PDB (Models)</option>
                  <option value="gro">Gromacs GRO</option>
                </select>
                <br/><br/>
                <a class="submitMetatraj" href="javascript: submitformMetaTraj('download',0);">
                <img id="metaDownload" alt="" src="images/Download2.png">
                </a>
        </div>
        <div class="trajSelection">
                <strong>Analysis</strong><img class="metaLogo" alt="" src="images/Magnifier.png"><br/><br/>
                <strong>Analyse the generated meta-trajectory using NAFlex server. &nbsp; &nbsp; &nbsp; &nbsp;</strong>
                <br/><br/>
                <a class="submitMetatraj" href="javascript:submitformMetaTraj('naflex',<?=$naflexLen?>);">
                <img id="metaNAFlex" alt="" src="images/NA_Flex_Logo.png">
                </a>
        </div>
      </div>
<p><i>(*) Please note that non-duplex structures and fragments placed in terminal strand sites are not included in meta-trajectories</i></p>
      </div>
    </form>

</div>

<?php
/*
$input = $_SESSION['metaTrajList'];
#echo "$input <br/>";

$jinput = json_encode($input);
#echo "$jinput <br/>";

#JL ?? $url = "http://ms2:5002/compose";
#$url = "http://ms2/compose";
$url = "http://m002/compose";

#curl -i -H "Content-Type: application/json" -X GET -d '{"id_trajs":{"NAFlex_1d11": ["1"], "NAFlex_1dcw": ["3", "14", "25"], "NAFlex_1tro": ["0", "14"], "NAFlex_36merSPCE": ["16"], "NAFlex_36merTIP3P": ["16"]}, "motiff_length": "6"}' http://ms2:5002/compose

$data = array(
  'id_trajs' => $traj,
  'mask' => $mask,
  'frames' => $frames,
  'format' => $format
);
$data_string = json_encode($data);

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

$top = $response[top_path];
$traj = $response[traj_path];
$id = preg_replace("/\.pdb/","",preg_replace("/\/tmp\//","",$top));
#print "Top: $top";
#print "Traj: $traj";

#$_SESSION['top_path'] = $top;
#$_SESSION['traj_path'] = $traj;
#print "--$top-- --$_SESSION[top_path]-- --$traj-- --$_SESSION[traj_path]--";

if($type == 'naflex'){
	redirect("../metatraj2NAFlex.php?id=$id&ext=$format");
}

$top2 = str_replace('/tmp/','',$top);
$traj2 = str_replace('/tmp/','',$traj);
system ("cd /tmp; tar -czf metatraj.tgz $top2 $traj2");

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
*/
print footerMMB();
?>

