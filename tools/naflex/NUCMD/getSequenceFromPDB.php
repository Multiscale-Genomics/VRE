<?php

require "phplib/global.inc.php";

$idPdb = strtoupper($_REQUEST['idPdb']);

if (strlen($idPdb) < 4) {
	print "Please type a proper PDB code (ex: 1naj, 1naj_A)";
	exit;
}

$rex = new MongoRegex("/^$idPdb/");
$seqc = $sequencesCol->find(array(
	'_id' => $rex,
	'origin' => 'pdb',
	'type' => 'na'), array('sequence'=>1)
);

$seqc->timeout(-1);

$seqs = Array();
foreach ($seqc as $k) {
	$seq = $k['sequence'];
	#echo "$seq";
	$seqs[] = $seq;
}

#foreach (array_values(iterator_to_array($seqc)) as $sq) {
#	echo "Seq: $sq";
#	$seqs[$sq] = 1;
#}

if (count($seqs)) {
	print "$seqs[0]";
}
else {
	print "No Nucleic Acid Sequence found for pdbCode $idPdb...";
}

#if (!count($seqs)) {
#	print "<h3>No records found (SEQ)</h3>";
#	exit;
#}
#else{
#	foreach (array_keys($ids) as $id)
#	  print "$id<br/>";
#}

?>
