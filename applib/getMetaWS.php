<?php

require "../phplib/genlibraries.php";
require "../phplib/tools.inc.php";
redirectOutside();


if($_REQUEST["type"] != 2) {
    // EXTRACT FILE METADATA FROM DMP FILE
	$mt = $GLOBALS['filesMetaCol']->findOne(array('_id' => $_REQUEST["id"]));
	$tool = $GLOBALS['toolsCol']->findOne(array('_id' => $mt["tool"]));
}else{
    // EXTRACT JOB METADATA FROM USER JOBS
	$mt = getUserJobPid($_SESSION['User']['_id'],$_REQUEST["id"]);

}

?>


<h3>Item Details</h3>

<?php 
if($mt["description"] != "") {
?>
<table class="table table-striped table-bordered">
	<tbody><tr>
			<th><b>Description. Genis,  nom sencer a la capcalera? boto edit meta? </b></th>
	</tr>
				<tr>
				<td><?php echo nl2br ($mt["description"]); ?></td>
			</tr>
		</tbody>
</table>
<?php
}
?>

<?php 
if($mt["tool"] != "") {
?>
<table class="table table-striped table-bordered">
	<tbody><tr>
			<th><b>Tool</b></th>
	</tr>
			<tr>
				<td><?php echo $tool["name"]; ?></td>
			</tr>
	</tbody>
</table>
<?php
}
?>

<?php 
if($_REQUEST["type"] == 1) {
    // show data_type, file_type
    $dt = $GLOBALS['dataTypesCol']->findOne(array('_id' => $mt["data_type"]));
    // show taxon_id and assembly
    if(!isset($mt["taxon_id"])){
       $taxon = "Not applicable";
    }elseif($mt['taxon_id'] == 0){
        $taxon = $mt['taxon_id'];
    }else{
        $taxon = fromTaxonID2TaxonName($mt['taxon_id'])." (".$mt['taxon_id'].")";
    }
    if(!isset($mt["refGenome"])){$mt['refGenome'] = "Not applicable";}

    ?>
    <table class="table table-striped table-bordered">
        <tbody>
        <tr>
    	    <th style="width:50%;"><b>Data Type</b></th>
    	    <th><b>File Type</b></th>
    	</tr>
    	<tr>
   			<td><?php echo $dt["name"]; ?></td>
   			<td><?php echo $mt["format"]; ?></td>
    	</tr>
    	</tbody>
    </table>
    <table class="table table-striped table-bordered">
        <tbody>
        <tr>
    	    <th style="width:50%;"><b>Taxon</b></th>
    	    <th><b>Assembly</b></th>
    	</tr>
    	<tr>
   			<td><?php echo $taxon; ?></td>
   			<td><?php echo $mt["refGenome"]; ?></td>
    	</tr>
    	</tbody>
    </table>

<?php
}
?>

<?php
// FIXME   Temporal fix until cloudName is not stored in metadata. Hardcoded here
if($mt["cloudName"] == "")
    $mt['cloudName'] = $GLOBALS['cloud'];

if($mt["cloudName"] != "") {
?>
<table class="table table-striped table-bordered">
	<tbody><tr>
			<th><b>Execution cloud infrastructure</b></th>
	</tr>
			<tr>
				<td><?php echo $mt['cloudName'];?></td>
			</tr>
	</tbody>
</table>
<?php
}
?>


<?php
if ($_SESSION['User']['Type']== 0 || $_SESSION['User']['Type'] == 1){
?>
<table class="table table-striped table-bordered">
	<tbody><tr>
			<th><b>Full DMP entry (TODO)</b></th>
	</tr>
			<tr>
            <td><a target="_blank" href="<?php echo $GLOBALS['URL'].'mug/api/dmp/file_meta?file_id='.$_REQUEST['id'];?>"><?php echo $GLOBALS['URL'].'mug/api/dmp/file_meta?file_id='.$_REQUEST['id'];?></a>
			</tr>
	</tbody>
</table>
<?php
}
?>


<?php
/*if(isset($mt['shPath'])) {

    $fn     =  $mt['shPath'];
    $toolId =  $mt["tool"];

    // Find tool
    $tool  = getTool_fromId($toolId,1);
    if (empty($tool)){
    	print "<p>The tool '$toolId' is not defined or is not registered in the database. Sorry, cannot show the details for the selected execution</p>";
    	die(0);
    }

    //Find submission file
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
    $config_fn = str_replace(basename($fn),$GLOBALS['tool_config_file'],$fn);
    $config_rfn = $GLOBALS['dataDir']."/".$config_fn;


    //
    // Parsing config file
    if (is_file($config_rfn)){

        $configParsed = parse_configFile($config_rfn);

        print "<h3>Execution Summary</h3>";

        // Print config file Input files
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

        <?php
        // Print config file Arguments
        ?>
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
        				 <td rowspan="<?php echo $rowspan;?>">
        					<label class="control-label"><?php echo $arg_description;?> 
        					<?php if($arg_help != "") { ?>
        						<i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $arg_help;?></p>"></i>
        					<?php } ?>
        					</label>
        					</td>
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
        		}else{ 
        		?>
        		<tr>
        				<td>
    					<label class="control-label"><?php echo $arg_description;?> 
        						<?php if($arg_help != "") { ?>
        						<i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $arg_help;?></p>"></i>
        						<?php } ?>
        					</label>
        				</td>
        		    <td><?php echo $v;?></td>
        		</tr>
        	<?php }
        	}?>
        </table>
        <?php
    }

    //
    // Parsing and printing submission file

    if ($_SESSION['User']['Type']== 0 || $_SESSION['User']['Type'] == 1){

    	print "<h3>Submission Command</h3>";
    
        $SH_parsed = Array();
        $launcher = $tool['infrastructure']['clouds'][$mt['cloudName']]['launcher'];
        if (!$launcher){
            print "Sorry, can not show the submission details. Cannot infer where and how this job has been executed.";
        }else{
            if ($tool['external'] === false){
                $SH_parsed = parseSHFile_BAMval($rfn);
            }else{
                switch($launcher){
                    case 'PMES':
            			$SH_parsed = parseSHFile_PMES($rfn);
            			break;
            		case 'SGE':
            			$SH_parted = parse_submissionFile_SGE($rfn);
            			break;
                    default:
                        print "Sorry, can not show the submission details. Job executed via '$launcher', and this launcher is not implemented";
            	}
            }
        }
	    if (count($SH_parsed) == 0){
    		print "Sorry, can not show the submission details. The expected SH file named '$rfn' is an old version or is not correclty formatted.";
    	}
?>

        <table class="table table-striped table-bordered">
        <?php foreach($SH_parsed as $k => $v){ ?>
            <tr>
                <td><?php echo $k;?></td>
                <td><?php echo $v;?></td>
            </tr>
        <?php }?>
        </table>
    <?php
    }

}*/
?>

<?php 

// SHOWING AUXILIAR FILES ACCORDING DMP FILE
if($_REQUEST["type"] == 0) {

	$p = getGSFile_fromId($_REQUEST["id"])['path'];
	$path = __DIR__.'/../files/'.$p.'/';

?>

		<?php if(($_SESSION['User']['Type'] == 0) || ($_SESSION['User']['Type'] == 1)) { ?>
		<table class="table table-striped table-bordered">
			<tbody><tr>
					<th><b>Path</b></th>
			</tr>
					<tr>
								<td><?php echo dirname($p);?></td>
					</tr>
			</tbody>
		</table>
		<?php } ?>


		<div id="meta-log">

				<?php if(file_exists($mt['logPath'])) { ?>
				<a href="workspace/workspace.php?op=openPlainFileFromPath&fnPath=<?php echo urlencode($mt['logPath']); ?>" class="btn green" target="_blank"><i class="fa fa-file-text-o"></i> VIEW LOG FILE </a>
				<?php }else{ ?>
				<a href="javascript:;" class="btn grey tooltips" data-container="body" data-html="true" data-placement="bottom" data-original-title="<p align='left' style='margin:0'>Fie not available</p>"><i class="fa fa-exclamation-triangle"></i> VIEW LOG FILE </a>
				<?php } ?>

				<?php if(($_SESSION['User']['Type'] == 0) || ($_SESSION['User']['Type'] == 1)) { ?>

				<?php if(file_exists($path.'.submit')) { ?>
				<a href="workspace/workspace.php?op=openPlainFileFromPath&fnPath=<?php echo urlencode($p.'/.submit'); ?>" class="btn green" target="_blank"><i class="fa fa-paper-plane"></i> VIEW SUBMIT FILE </a>
				<?php }else{ ?>
				<a href="javascript:;" class="btn grey tooltips" data-container="body" data-html="true" data-placement="bottom" data-original-title="<p align='left' style='margin:0'>Fie not available</p>"><i class="fa fa-exclamation-triangle"></i> VIEW SUBMIT FILE </a>
				<?php } ?>
				<?php if(file_exists($path.'.config.json')) { ?>
				<a href="workspace/workspace.php?op=openPlainFileFromPath&fnPath=<?php echo urlencode($p.'/.config.json'); ?>" class="btn green" target="_blank"><i class="fa fa-cog"></i> VIEW CONFIG FILE </a>
				<?php }else{ ?>
				<a href="javascript:;" class="btn grey tooltips" data-container="body" data-html="true" data-placement="bottom" data-original-title="<p align='left' style='margin:0'>Fie not available</p>"><i class="fa fa-exclamation-triangle"></i> VIEW CONFIG FILE </a>
				<?php } ?>
				<?php if(file_exists($path.'.input_metadata.json')) { ?>
				<a href="workspace/workspace.php?op=openPlainFileFromPath&fnPath=<?php echo urlencode($p.'/.input_metadata.json'); ?>" class="btn green" target="_blank"><i class="fa fa-tags"></i> VIEW META FILE </a>
				<?php }else{ ?>
				<a href="javascript:;" class="btn grey tooltips" data-container="body" data-html="true" data-placement="bottom" data-original-title="<p align='left' style='margin:0'>Fie not available</p>"><i class="fa fa-exclamation-triangle"></i> VIEW META FILE </a>
				<?php } ?>
				<?php if(file_exists($path.'.results.json')) { ?>
				<a href="workspace/workspace.php?op=openPlainFileFromPath&fnPath=<?php echo urlencode($p.'/.results.json'); ?>" class="btn green" target="_blank"><i class="fa fa-line-chart"></i> VIEW RESULTS FILE </a>
				<?php }else{ ?>
				<a href="javascript:;" class="btn grey tooltips" data-container="body" data-html="true" data-placement="bottom" data-original-title="<p align='left' style='margin:0'>Fie not available</p>"><i class="fa fa-exclamation-triangle"></i> VIEW RESULTS FILE </a>
				<?php } ?>

				<?php } ?>

		</div>

<?php
}
?>

<?php 
// SHOWING AUXILIAR FILES ACCORDING USER JOB 

if($_REQUEST["type"] == 2) {
?>

		<div id="meta-log">

				<?php if(file_exists($mt[$_REQUEST['id']]['log_file'])) { ?>
				<a href="workspace/workspace.php?op=openPlainFileFromPath&fnPath=<?php echo urlencode(fromAbsPath_toPath($mt['log_file'])); ?>" class="btn green" target="_blank"><i class="fa fa-file-text-o"></i> VIEW LOG FILE </a>
				<?php }else{ ?>
				<a href="javascript:;" class="btn grey tooltips" data-container="body" data-html="true" data-placement="bottom" data-original-title="<p align='left' style='margin:0'>Fie not available</p>"><i class="fa fa-exclamation-triangle"></i> VIEW LOG FILE </a>
				<?php } ?>


				<?php if(($_SESSION['User']['Type'] == 0) || ($_SESSION['User']['Type'] == 1)) { ?>

				<?php if(file_exists($mt[$_REQUEST['id']]['submission_file'])) { ?>
				<a href="workspace/workspace.php?op=openPlainFileFromPath&fnPath=<?php echo urlencode($mt[$_REQUEST['id']]['submission_file']); ?>" class="btn green" target="_blank"><i class="fa fa-paper-plane"></i> VIEW SUBMIT FILE </a>
				<?php }else{ ?>
				<a href="javascript:;" class="btn grey tooltips" data-container="body" data-html="true" data-placement="bottom" data-original-title="<p align='left' style='margin:0'>Fie not available</p>"><i class="fa fa-exclamation-triangle"></i> VIEW SUBMIT FILE </a>
				<?php } ?>
				<?php if(file_exists($mt[$_REQUEST['id']]['config_file'])) { ?>
				<a href="workspace/workspace.php?op=openPlainFileFromPath&fnPath=<?php echo urlencode($mt[$_REQUEST['id']]['config_file']); ?>" class="btn green" target="_blank"><i class="fa fa-cog"></i> VIEW CONFIG FILE </a>
				<?php }else{ ?>
				<a href="javascript:;" class="btn grey tooltips" data-container="body" data-html="true" data-placement="bottom" data-original-title="<p align='left' style='margin:0'>Fie not available</p>"><i class="fa fa-exclamation-triangle"></i> VIEW CONFIG FILE </a>
				<?php } ?>
				<?php if(file_exists($mt[$_REQUEST['id']]['metadata_file'])) { ?>
				<a href="workspace/workspace.php?op=openPlainFileFromPath&fnPath=<?php echo urlencode($mt[$_REQUEST['id']]['metadata_file']); ?>" class="btn green" target="_blank"><i class="fa fa-tags"></i> VIEW META FILE </a>
				<?php }else{ ?>
				<a href="javascript:;" class="btn grey tooltips" data-container="body" data-html="true" data-placement="bottom" data-original-title="<p align='left' style='margin:0'>Fie not available</p>"><i class="fa fa-exclamation-triangle"></i> VIEW META FILE </a>
				<?php } ?>
				<?php if(file_exists($mt[$_REQUEST['id']]['stageout_file'])) { ?>
				<a href="workspace/workspace.php?op=openPlainFileFromPath&fnPath=<?php echo urlencode($mt[$_REQUEST['id']]['stageout_file']); ?>" class="btn green" target="_blank"><i class="fa fa-line-chart"></i> VIEW RESULTS FILE </a>
				<?php }else{ ?>
				<a href="javascript:;" class="btn grey tooltips" data-container="body" data-html="true" data-placement="bottom" data-original-title="<p align='left' style='margin:0'>Fie not available</p>"><i class="fa fa-exclamation-triangle"></i> VIEW RESULTS FILE </a>
				<?php } ?>

				<?php } ?>

		</div>

<?php
}
?>
