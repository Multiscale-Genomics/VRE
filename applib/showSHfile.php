<?php

require "../phplib/genlibraries.php";
require "../phplib/tools.inc.php";
redirectOutside();

print "<h2>Execution Summary</h2>";

$fn     =  $_REQUEST['fn'];
$toolId =  $_REQUEST['tool'];

$tool   = $GLOBALS['toolsCol']->findOne(array('_id' => $toolId));
if (empty($tool)){
	print "<p>The tool '$toolId' is not defined or is not registered in the database. Sorry, cannot show the details for the selected execution</p>";
	die(0);
}

#$fn   = getAttr_fromGSFileId($fn,'path');
if (preg_match('/^\//',$fn)){
	$rfn  = $fn;
	$fn  = str_replace($GLOBALS['dataDir']."/","",$fn);
}else{
	$rfn  = $GLOBALS['dataDir']."/".$fn;
}

$getPrefix = Array(
        'readBAM.R'     => 'PPread',
        'coverage.R'    => 'PPcov',
        'index'         => 'PPidx',
        'sort'          => 'PPsort',
        'nucleR.R'      => 'NR', 
        'nucleosomeDynamics.R ' => 'ND',
        'nfr.R'         => 'NFR',
        'periodicity.R' => 'P',
        'gauss_fit.R'   => 'STF',
        'tx_classes.R'  => 'TSS',
);


if (!is_file($rfn)){
        print "<p>Sorry, no information found for this execution. ".$rfn." not found</p>";
	die(0);
}

$cmdsParsed = Array();
switch($toolId){
	case 'pydock':
		$cmdsParsed = parseSHFile_pyDock($rfn);
		break;
	case 'nucldynwf':
		$cmdsParsed = parse_submissionFile_SGE($rfn);
		break;
	case 'BAMval':
		$cmdsParsed = parseSHFile_BAMval($rfn);
		break;
	default:
		//$cmdsParsed = parseSGFile_generic($rfn);
}


foreach ($cmdsParsed as $n => $cmd){
	?>
	<table class="table table-striped table-bordered">
	    <tr>
                <th><b>Analysis #<?php echo $n;?></b></th>
		<!--<th><a target="_blank" href="help.php?id=<?php echo $cmd['prgPrefix'];?>">-->
			<th><a href="javascript:;">
			<strong><?php echo $cmd['prgName'];?></strong></a>
		</th>
	    </tr>
	<?php
	foreach ( $cmd['params'] as $k => $v){
	    print "<tr><td>$k</td><td>";
	    if (in_array($k, Array("config","metadata","out_metadata")))
		print "<a target=\"_blank\" href=\"files/".$_SESSION['User']['id']."/$v\">$v</a>";
	    else
		print $v;
	    print "</td></tr>";
	}
	?>
        </table>


	<a href="javascript:toggleVis('raw<?php echo $n?>');"><i class="fa fa-code"></i> View raw comand</a>

	<div id="raw<?php echo $n;?>" class="display-hide">
		<table class="table table-striped table-bordered" style="border:2px solid #79a2c5;margin-top:10px;">	
		<tr><td>Raw command</td><td><?php echo $cmd['cmdRaw'];?></td></tr>
		<tr><td>Working directory</td><td><?php echo $cmd['cwd'];?></td></tr>
	    </table>
	</div>

	<?php 
}


?>
