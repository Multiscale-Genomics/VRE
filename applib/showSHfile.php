<?php

require "../phplib/genlibraries.php";
require "../phplib/tools.inc.php";
redirectOutside();


print "<h2>Execution Summary</h2>";


$fn     =  $_REQUEST['fn'];
$toolId =  $_REQUEST['tool'];

// Find tool
//$tool  = $GLOBALS['toolsCol']->findOne(array('_id' => $toolId));
$tool  = getTool_fromId($toolId,1);
if (empty($tool)){
	print "<p>The tool '$toolId' is not defined or is not registered in the database. Sorry, cannot show the details for the selected execution</p>";
	die(0);
}

//Find submission SH file
if (preg_match('/^\//',$fn)){
	$rfn  = $fn;
	$fn  = str_replace($GLOBALS['dataDir']."/","",$fn);
}else{
	$rfn  = $GLOBALS['dataDir']."/".$fn;
}
if (!is_file($rfn)){
        print "<p>Sorry, no information found for this execution. ".$rfn." not found</p>";
	die(0);
}

//Find config file
$config_fn = str_replace(basename($fn),".config.json",$fn);
$config_rfn = $GLOBALS['dataDir']."/".$config_fn;
if (!is_file($config_rfn)){
        print "<p>Sorry, no configuration file found for this execution. ".$config_rfn." not found</p>";
	die(0);
}

// Parsing config file
$configParsed = parse_configFile($config_rfn);

// Print config file
?>
<table class="table table-striped table-bordered">
	<tr>
	    <th colspan="2"><b>Input files</b></th>
	</tr>
	<?php
	foreach ( $configParsed['input_files'] as $input_name => $vs){
	    $input_description = (isset($tool['input_files'][$input_name]["description"])?$tool['input_files'][$input_name]["description"]:$input_name);
	    $input_help        = (isset($tool['input_files'][$input_name]["help"])?$tool['input_files'][$input_name]["help"]:"");
	    $rowspan = count($vs);
	    for ($i=0;$i<count($vs);$i++){
		if ($i==0){
		  ?>
		  <tr>
		    <td rowspan="<?php echo $rowspan;?>"><label class="control-label"><?php echo $input_description;?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $input_help;?></p>"></i></label></td>
		    <td><?php echo $vs[$i];?></td>
		  </tr>
		<?php
		}else{ ?>
		  <tr>
		    <td><?php echo $vs[$i];?></td>
		  </tr>
		<?php
		}
	     }
	}?>
</table>

<table class="table table-striped table-bordered">
	<tr>
	    <th colspan="2"><b>Arguments</b></th>
	</tr>
	<?php
	foreach ( $configParsed['arguments'] as $arg_name => $v){
	    $arg_description = (isset($tool['arguments'][$arg_name]["description"])?$tool['arguments'][$arg_name]["description"]:$arg_name);
	    $arg_help        = (isset($tool['arguments'][$arg_name]["help"])?$tool['arguments'][$arg_name]["help"]:"");


	    if (is_array($v)){
		$rowspan = count($v);
		for ($i=0;$i<count($v);$i++){
		    if ($i==0){
			?>
			<tr>
			   <td rowspan="<?php echo $rowspan;?>"><label class="control-label"><?php echo $arg_description;?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $arg_help;?></p>"></i></label></td>
			    <td><?php echo $v[$i];?></td>
			</tr>
	   	<?php
		   }else{ ?>
			<tr>
			    <td><?php echo $v[$i];?></td>
			</tr>

		<?php
		   }
	 	}
	 }else{ ?>
		<tr>
		    <td><label class="control-label"><?php echo $arg_description;?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $arg_help;?></p>"></i></label></td>
		    <td><?php echo $v;?></td>
		</tr>
	<?php }
	}?>
</table>


<?php


//
// Parsing and printing submission command

if ($_SESSION['User']['Type']== 0 || $_SESSION['User']['Type'] == 1){

	print "<h2>Submission Command</h2>";

	$cmdsParsed = Array();
	switch($toolId){
		case 'pydock':
			$cmdsParsed = parseSHFile_pyDock($rfn);
			break;
		case 'BAMval':
			$cmdsParsed = parseSHFile_BAMval($rfn);
			break;
		default:
			$cmdsParsed = parse_submissionFile_SGE($rfn);
	}
	if (count($cmdsParsed) == 0){
		print "Sorry, can not show the submission details. The expected SH file named '$rfn' is an old version or is not correclty formatted.";

	}
	
	foreach ($cmdsParsed as $n => $cmd){
		?>
		<table class="table table-striped table-bordered">
		    <tr>
	                <th><b>Analysis #<?php echo $n;?></b></th>
			<th><a href="javascript:;"><strong><?php echo $cmd['prgName'];?></strong></a></th>
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
}
	
	
?>
