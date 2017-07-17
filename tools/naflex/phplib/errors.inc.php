<?php
/* 
 * MDWeb
 * Error messages
 */

require "phplib/globalVars.inc.php";

$GLOBALS['errors']['formErrors'] = Array (
    'errorTitol' => 'Project title is mandatory',
    'errorStructure'=> 'A Structure must be provided in simulation projects',
    'nopdb' => 'Unknown PDB or Swiss-Prot code',
    'nopdbforSWP' => 'Sorry, we could not find a protein structure similar to your Swiss-Prot code.',
    'notop' => 'Topology file is mandatory in analysis projects',
    'nogro' => 'GRO file is mandatory in GROMACS analysis projects',
    'noseq' => 'Nucleotide Sequence is mandatory in DNA/RNA From Sequence projects',
    'noupload' => 'MDWeb Project Dump file is required',
    'noseq2pdb' => 'Could not generate a structure from these input parameters, please check them',
    'nocoord' => 'Coordinates file is mandatory in analysis projects',
    'coordfilesize' => 'Coordinates file exceeds maximum file size',
    'coordemptyfile' => 'Coordinates file is empty',
    'topfilesize' => 'Topology file exceeds maximum file size',
    'pdbTooBig' => 'PDB file exceeds maximum number of Atoms ('.$GLOBALS['maxAtoms'].')',
    'topemptyfile' => 'Topology file is empty',
    'badtraj' => 'Error loading trajectory, check topology and trajectory matching and accordance with formats selected.',
    'remediated' => 'Error loading PDB structure, check matching and accordance with PDB Remediated Format (e.g. Chain Ids, Hetatms).',
    'seqtoolong' => 'Sequence too long, maximum number of bases exceeded ('.$GLOBALS['MaxSeqLength'].').', 
    'seqtoolongCG' => 'Sequence too long, maximum number of bases exceeded ('.$GLOBALS['MaxCGSeqLength'].').',
    'WLCbeads' => 'Error in Worm-like chain resolution, number of beads must be between 1 and 10'
);

function nonExistReq ($field) {
        return !$_REQUEST[$field];
}
function nonExistUploadFile ($field) {
        return !$_FILES[$field]['size'];
}

function fileSizeExceeded($file) {
//	foreach($file as $f) {

	$limitFileSize = $GLOBALS['limitFileSize']; // 100MB = 100000KB
	//$limitFileSize = 10000; // 10MB = 10000KB

	$size = $file['size'];
	$sizeK = $size;
	if($size > 0 ){
		$sizeK = $size / 1024;
	}
	logger("File Size: $sizeK KB");
	if($sizeK > $limitFileSize){
		logger("File Size Exceeded: $sizeK KB > $limitFileSize KB");
		return true;
	}
//	foreach ($file as $k => $v){
//	logger("FileSizeExceeded: $k - $v");
//	}
	return false;
}

function emptyFile($file) {

        $size = $file['size'];
	$f = $file['tmp_dir'];
        if($size > 0 ){
        	logger("Empty File: $f");
		return false;
        }
        return true;
}

?>
