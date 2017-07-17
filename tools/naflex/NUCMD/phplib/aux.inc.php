<?php

function getListIds_orig ($res,$subseq,$flank) {

        $len = strlen($subseq);
        $line = "-$len- ";
        foreach ($res as $k => $value){
                $line .= "$k ";

                $seq = $value;
                $seqLen = strlen($seq);

                $lastPos = 0;
                $positions = array();

                while (($lastPos = strpos($seq, $subseq, $lastPos))!== false) {
		    if( ($lastPos >= $flank) and ($lastPos < ($seqLen-$flank)) ){
	                    $positions[] = $lastPos;
		    }
                    $lastPos = $lastPos + 1;
                }

                foreach ($positions as $value) {
                        $line .= "$value ";
                }
        }
        #print "$line<br/>";

	return $line;
}

function getListIds ($res,$subseq,$flank,$simData) {
        $len = strlen($subseq);
	$line = array();
	array_push($line,$len);

	if (empty($res))
		return $line;

        foreach ($res as $k => $value){
                $seq = $value;
                $seqLen = strlen($seq);

		# At that moment, we just offer fragment metatrajectories for standard DUPLEX structures
		$doc = $simData->findOne( array('_id' => $k ), array('Chains' => true));
		if($doc[Chains] != "duplex"){
			#echo "$k $doc[Chains]";
			continue;
		}

                $lastPos = 0;	
                $positions = array();

		if(!$flank)
			$flank = 1;	# Skipping first nucleotide (terminal)

                while (($lastPos = strpos($seq, $subseq, $lastPos))!== false) {
		    if( ($lastPos >= $flank) and ($lastPos < ($seqLen-$len-$flank)+1) ){

			    # Getting complementary nucleotides (duplex strand)
			    $pair = ( ($seqLen*2) - $lastPos);
			    $iniPair = ($pair - $len);

	                    $positions[] = $lastPos;
	                    $positions[] = $iniPair;
		    }
                    $lastPos = $lastPos + 1;
                }

		$a = array();
                foreach ($positions as $value) {
			array_push($a,$value);
                }
		if (! empty($a))
			array_push($line,array($k => $a));
        }

	return $line;
}

function highlightFragment_good($row,$idSubSeq,$flank) {

	if($flank){
		$reg_flank = "{".$flank."}";
		$reg = "/(\w*)(\w$reg_flank)($idSubSeq)(\w$reg_flank)(\w*)/";
		$row = preg_replace($reg,"$1<mark style='background-color: #FF9900;'>$2</mark><mark>$3</mark><mark style='background-color: #FF9900;'>$4</mark>$5",$row);
	}
	else {
		$reg = "/(\w*)($idSubSeq)(\w*)/";
		$row = preg_replace($reg,"$1<mark>$2</mark>$3",$row);
	}
	return $row;
}

function highlightFragmentQS($row,$idQS,$flank) {

	$idSubSeq = strtoupper($idQS);
	highlightFragment($row,$idSubSeq,$flank);

	$reg = "/($idQS)/i";
	$seq = preg_replace($reg,"<mark>$1</mark>",$row);

	# Remove marks from href link:
	$reg = "/getStruc.php\?idCode=([A-Za-z0-9_]*)<mark>([A-Za-z0-9_]*)<\/mark>/";
	$seq2 = preg_replace($reg,"getStruc.php?idCode=$1$2",$seq);

	# Remove marks from html tr id attribute:
	$reg = "/id=\"([A-Za-z0-9_]*)<mark>([A-Za-z0-9_]*)<\/mark>/";
	$seq3 = preg_replace($reg,"id=\"$1$2",$seq2);

	# Remove marks from html input name attribute:
	$reg = "/name=\"([A-Za-z0-9_]*)<mark>([A-Za-z0-9_]*)<\/mark>/";
	$seq4 = preg_replace($reg,"name=\"$1$2",$seq3);

	return $seq4;
}


function highlightFragment($row,$idSubSeq,$flank) {

	$reg = "/\<td style=\"word-break: break-all; -ms-word-break: break-all; max-width: 300px;\"\>([\w| ]*)\<\/td\>/";
	preg_match($reg,$row,$coin);
	$seq = $coin[1];
	$orig_seq = "/$seq/";
	if (preg_match("/|/",$orig_seq))
		$orig_seq = preg_replace("/\|/","\|",$orig_seq);

	if($flank){
		$reg_flank = "{".$flank."}";
		$reg = "/($idSubSeq)/";
		$seq = preg_replace($reg,"<mark>$1</mark>",$seq);
		$reg = "/(\w$reg_flank)(\<mark\>$idSubSeq\<\/mark\>)(\w$reg_flank)/";
		$seq = preg_replace($reg,"<mark style='background-color: #FF9900;'>$1</mark>$2<mark style='background-color: #FF9900;'>$3</mark>",$seq);
		$reg = "/(\w$reg_flank)(\<mark\>$idSubSeq\<\/mark\>)/";
		$seq = preg_replace($reg,"<mark style='background-color: #FF9900;'>$1</mark>$2",$seq);
		$reg = "/(\<mark\>$idSubSeq\<\/mark\>)(\w$reg_flank)/";
		$seq = preg_replace($reg,"$1<mark style='background-color: #FF9900;'>$2</mark>",$seq);
	}
	else {
		$reg = "/($idSubSeq)/";
		$seq = preg_replace($reg,"<mark>$1</mark>",$seq);
		#$reg = "/(\w*)($idSubSeq)(\w*)/";
		#$seq = preg_replace($reg,"$1<mark>$2</mark>$3",$seq);
	}
	$row = preg_replace($orig_seq,$seq,$row);
	return $row;
}

function QCplot ($title,$path,$avg,$stdev) {

	#print QCplot("RMSd");
	#print QCplot("Rgyr");
	#print QCplot("SASA");
	#print QCplot("RMSf");

	$units = "&Aring;"; # Angstroms
	if ($title == "SASA")
		$units = "&Aring;&sup2;"; # Square Angstroms

	$mtitle = strtolower($title);
	$file = "$path/INFO/structure.$mtitle.png";
	if(file_exists("$file") and filesize("$file")!=0 ){
		print "<tr>\n";
		if ($avg != 0) {
			print "<td><strong>$title</strong></td><td>$avg ($stdev) $units &nbsp;&nbsp;&nbsp;  <a href=\"$file\" class=\"jqueryImages\"><img class=\"rmsd\" style=\"width: 35px; height: 20px; vertical-align: middle; padding: 0px;\" src=\"$file\"></a></td>\n";
		}
		else{
			print "<td><strong>$title</strong></td><td><a href=\"$file\" class=\"jqueryImages\"><img class=\"rmsd\" style=\"width: 35px; height: 20px; vertical-align: middle; padding: 0px;\" src=\"$file\"></a></td>\n";
		}
		print "</tr>\n";
	}
	else {
		print "<tr>\n";
		print "<td><strong>$title</strong></td><td>-</td>\n";
		print "</tr>\n";
	}
}

# saveSession
# Saving Project Php Session Vars 
function saveSession ($sessId){

        #$workDir = $GLOBALS["tmpDir"]."/".$sessId;
        #chdir($workDir);

    $workDir = ".";
    $fs = fopen ($workDir."/projectData.bin","w");
    fwrite ($fs,serialize($_SESSION));
    fclose($fs);
}

# loadSession
# Loading Project Php Session Vars
function loadSession ($sessId){

        #$workDir = $GLOBALS["tmpDir"]."/".$sessId;
        #chdir($workDir);
        return unserialize(file_get_contents("projectData.bin"));
}


?>
