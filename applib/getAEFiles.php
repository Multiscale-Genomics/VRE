<?php

require "../phplib/genlibraries.php";

redirectOutside();

/*if($_POST){

	$dt = getDataTypeFromFileType($_REQUEST['filetype']);

	echo json_encode($dt);

}else{
	redirect($GLOBALS['URL']);
}*/

$files = $GLOBALS['sdrfCol']->find(array("_id.DBId" => $_REQUEST["id"]));

$id = explode(":", $_REQUEST["id"])[1];

$taxon = "";

foreach($files as $f) {

	$urls = $f["urls"];

	if(isset($urls) && ($taxon == "")) {

		foreach($urls as $a) {

			if($taxon == "") {
	
				foreach($a as $k => $v) {

					preg_match('/Taxon/', $v, $matches, PREG_OFFSET_CAPTURE);

					if(!empty($matches)) {
						$t = explode("_", $v);
						$taxon = $t[sizeof($t) - 1];
						break;
					}
		
				}

			}
		
		}

	}

}



echo '<table class="table table-striped table-bordered">
	<tbody>
		<tr>
			<th>File Name</th>
			<th>Click here to load file to Workspace</th>
			<th>Click here to download file</th>
    </tr>';

$c = 0;

foreach($files as $f) {

	$urls = $f["urls"];

	if(isset($urls)) {

		foreach($urls as $a) {

			echo '<tr>';

			foreach($a as $k => $v) {

				preg_match('/Taxon/', $v, $matches, PREG_OFFSET_CAPTURE);

				if(empty($matches)) {
					
					$type = explode("|", $k);

					$zipName = explode("/", $v);
					$zipName = $zipName[sizeof($zipName) - 1];

					$fileName = $f[$type[0]]["id"];
					//$fileName = $v;
					if(gettype($fileName) == "array") {
						$fileName = implode(", ", $fileName);
						// ARRAYS NOT SHOWN!!!!
					}

					$isFQ = false;

					if (strpos($k, 'FASTQ_URI') !== false) $isFQ = true;

					$ext = strtoupper(pathinfo($fileName, PATHINFO_EXTENSION));
					$e = strtoupper(pathinfo($v, PATHINFO_EXTENSION));

					//var_dump($e);

					$file_type = $GLOBALS['fileTypesCol']->findOne(array("_id" => $ext));

					if(isset($file_type) || ($ext == "")) {

						if($isFQ)	{
							$urlFile = $f["Scan_Name"]["Comment"]["FASTQ_URI"];
						} else {
							if($e == "ZIP") $urlFile = "https://www.ebi.ac.uk/arrayexpress/files/$id/$zipName/$fileName";
							else $urlFile = $v;
						}

						if($fileName == "")  {
							$fileName = end(explode('/',$v));
							$urlFile = $v;
						}

						$repID = $_REQUEST["id"];
							
						echo '<td>';
						echo /*$f["_id"]["record"].' '.*/$fileName;
						//if($taxon != "") echo " ($taxon)";
						echo '</td>';

						echo '<td>';
						echo "<a href=\"".$GLOBALS['URL']."/applib/getData.php?uploadType=repository&url=$urlFile&repo=AE&taxon=$taxon&id=$repID\" 
						class=\"btn green\"><i class=\"fa fa-cloud-upload\"></i> IMPORT FILE TO WORKSPACE </a>";
						echo '</td>';

						echo '<td>';
						echo "<a href=\"$urlFile\" 
						target=\"_blank\" class=\"btn green\"><i class=\"fa fa-download\"></i> DOWNLOAD FILE </a>";
						echo '</td>';

						$c ++;

					}
					
				}

			}
			
			echo '</tr>';

		}

	}

}

if($c == 0) echo "<td colspan=\"3\">No available files compatible with this experiment.</td>";

echo '</tbody>
</table>';


