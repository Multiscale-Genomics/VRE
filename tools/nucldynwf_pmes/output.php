<?php

require "../../phplib/genlibraries.php";
redirectOutside();

if(!isset($_REQUEST['execution'])){

	$_SESSION['errorData']['Error'][]="You should select an execution to view results";
	redirect('/workspace/');

}

// setting custom visualizer working_dir
//

/*$wd  = $GLOBALS['dataDir']."/".$_SESSION['User']['id']."/.tmp/outputs_".$_REQUEST['execution'];
$indexFile = $wd.'/index';

$results =Array();
if(is_dir($wd)) {

	// check if content uncompressed

	if(file_exists($indexFile)) {
	
		$results = file($indexFile);
		//var_dump($results);

	}

}else{

	// create $wd

	mkdir($wd);
	touch($indexFile);

}


// Get internal results
//

if(!count($results)) {

	$files = $GLOBALS['filesCol']->findOne(array('_id' => $_REQUEST['execution']), array('files' => 1, '_id' => 0));

	foreach($files["files"] as $id) {

		$fMeta = iterator_to_array($GLOBALS['filesMetaCol']->find(array('_id' => $id,
																																		'data_type'  => "tool_statistics",
																																		'format'     =>'TAR',
																																		'compressed' =>"gzip")));
		if(count($fMeta) ) {
			$path = $GLOBALS['dataDir']."/".getAttr_fromGSFileId($id,'path');
			exec("tar --touch -xzf \"$path\" -C \"$wd\" 2>&1", $err);

			if(!count($err)) {

				$fp = fopen($indexFile, 'a');
				fwrite($fp, $id);
				fclose($fp);

			} else { echo "error!!!!"; }
		}
	}

	$results = file($indexFile);

}*/


$wd  = $GLOBALS['dataDir'].$_SESSION['User']['id']."/".$_SESSION['User']['activeProject']."/".$GLOBALS['tmpUser_dir']."/outputs_".$_REQUEST['execution'];
$indexFile = $wd.'/index';

$results = file($indexFile);

$dir = basename(getAttr_fromGSFileId($_REQUEST['execution'],'path'));

$pathTemp = 'files/'.$_SESSION['User']['id']."/".$_SESSION['User']['activeProject']."/.tmp/outputs_".$_REQUEST['execution'];
$pathGff = $GLOBALS['dataDir'].$_SESSION['User']['id']."/".$_SESSION['User']['activeProject']."/".$dir;

$gffFiles = glob("$pathGff/*gff");

?>

<?php require "../../htmlib/header.inc.php"; ?>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-container-bg-solid page-sidebar-fixed">
  <div class="page-wrapper">

  <?php require "../../htmlib/top.inc.php"; ?>
  <?php require "../../htmlib/menu.inc.php"; ?>

<!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        <!-- BEGIN PAGE HEADER-->
                        <!-- BEGIN PAGE BAR -->
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                              <li>
                                  <a href="home/">Home</a>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <a href="workspace/">User Workspace</a>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>Tools</span>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>Nucleosome Dynamics</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Results
                            <small>Nucleosome Dynamics</small>
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->
                        <div class="row">
                  			    <div class="col-md-12">
                        				<p style="margin-top:0;">
																General Statistics for <strong><?php echo basename($pathGff); ?></strong> execution.
                                </p>
														</div>
														<div class="col-md-12">
														<div class="note note-info" style="padding-bottom:7px;">
																<h4><a href="workspace/workspace.php?op=downloadFile&fn=<?php echo $results[0]; ?>" style="text-decoration:none;"><i class="fa fa-download"></i> Download all in a compressed tar.gz file </a></h4>
															</div>
														</div>
												</div>

												<div class="portlet light bordered">

												<input type="hidden" id="tmpf" value="<?php echo $_REQUEST['execution']; ?>" />

													<div class="portlet-title">
															<div class="caption">
																<i class="icon-share font-dark hide"></i>
																<span class="caption-subject font-dark bold uppercase">Statistics per gene</span>
																<small style="font-size:75%;"></small>
															</div>
														</div>

														<div class="portlet-body">

															<div class="row display-hide" id="nd-toggle-table"></div>

															<div class="row display-hide" id="nd-filters-table"></div>

															<div class="row">
															<div class="col-md-12 col-sm-12" id="ndtable-portlet">
																	<div id="loading-datatable"><div id="loading-spinner">LOADING</div></div>
															</div>
															</div>


														</div>
													</div>

                        <div class="row">
												<div class="col-md-12">

													<div class="portlet light bordered">

													<div class="portlet-title">
															<div class="caption">
																<i class="icon-share font-dark hide"></i>
																<span class="caption-subject font-dark bold uppercase">Genome-wide Statistics</span>
																<small style="font-size:75%;"></small>
															</div>
														</div>

														<div class="portlet-body">

													<div class="panel-group accordion">
														
														<?php 
														$i = 1;
														foreach($gffFiles as $file) { 
							
															$fname = pathinfo($file, PATHINFO_FILENAME);

															$t = explode("_", $fname)[0];
	
															switch($t) {
																case 'ND': $type = "Nucleosome Dynamics"; break;
																case 'NFR': $type = "Nucleosome Free Regions"; break;
																case 'NR': $type = "NucleR"; break;
																case 'P': $type = "Periodicity"; break;
																case 'STF': $type = "Stiffness"; break;
																case 'TSS': $type = "TSS Classification"; break;
															}

															$pngFiles = glob("../../".$pathTemp."/".$fname."_stats*png");
															$csvFiles = glob($wd."/".$fname."_stats*csv");
															$genesFiles = glob($wd."/".$fname."_genes_stats*csv");

														?>

														<div class="panel panel-default">
																<div class="panel-heading">
																		<h4 class="panel-title">
																				<a class="accordion-toggle accordion-toggle-styled <?php echo ($i > 1) ? "collapsed" : "" ?>" data-toggle="collapse" href="#collapse_3_<?php echo $i; ?>"> <?php echo basename($file); ?> &nbsp;&nbsp;&nbsp; <span style="font-weight:400;"><?php echo $type; ?></span> </a>
																		</h4>
																</div>
																<div id="collapse_3_<?php echo $i; ?>" class="panel-collapse <?php echo ($i == 1) ? "in" : "collapse" ?>">
																		<div class="panel-body nd-accordion">
																			<div class="row">
																				<?php if((!count($pngFiles)) && (!count($csvFiles))) { ?>
																				<div class="col-md-12">
																					The file <strong><?php echo basename($file); ?></strong> has not statistics associated.
																				</div>
																				<?php } else { ?>
																				
																					<?php if((count($pngFiles)) && (!count($csvFiles))) { ?>
																					<!-- 2 PNGs -->
																					<?php foreach($pngFiles as $png) { ?>

																						<div class="col-md-6">
																							<img src="<?php echo $png; ?>" />
																						</div>

																					<?php } ?>
																					<?php } elseif((count($pngFiles)) && (count($csvFiles))) { ?>
																					<!-- 1 PNG + 1 CSV -->
																					
																						<?php foreach($csvFiles as $csv) { ?>
	
																							<div class="col-md-6">
																								<?php
																								$row = 1;
																								if (($handle = fopen($csv, "r")) !== FALSE) {
																									$htmlval = '<table class="table table-striped ">';
																									while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE)) {
																										$num = count($data);
																										if($row == 1) {
																											$htmlval = $htmlval."<thead>";
																											$col = 'th';
																										}else{ 
																											if($row == 2) $htmlval = $htmlval."<tbody>";
																											$col = 'td';
																										}
																										$htmlval = $htmlval."<tr>";
																										for ($c=0; $c < $num; $c++) {
																											if($data[$c] != "" || trim($data[$c]) != " "){
																													$htmlval = $htmlval. '<'.$col.'>'.$data[$c].'</'.$col.'>';
																											}else{
																													$htmlval = $htmlval. '<'.$col.'>&nbsp;</'.$col.'>';
																											}
																										}
																										if($row == 1) $htmlval = $htmlval."</tr></thead>";
																										else $htmlval = $htmlval."</tr>";
																										$row++;
																									}
																									$htmlval = $htmlval."</tbody></table>";
																									fclose($handle);
																								}else {
																									$htmlval = "Error reading data file";
																								}

																								echo $htmlval;
																								?>
																							</div>
														
																						<?php } ?>
																						<?php foreach($pngFiles as $png) { ?>

																							<div class="col-md-6">
																								<img src="<?php echo $png; ?>" />
																							</div>

																						<?php } ?>

																					<?php } elseif((!count($pngFiles)) && (count($csvFiles))) { ?>
																					<!-- 1 CSV -->

																						<?php foreach($csvFiles as $csv) { ?>
	
																							<div class="col-md-6">
																								<?php
																								$row = 1;
																								if (($handle = fopen($csv, "r")) !== FALSE) {
																									$htmlval = '<table class="table table-striped ">';
																									while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE)) {
																										$num = count($data);
																										if($row == 1) {
																											$htmlval = $htmlval."<thead>";
																											$col = 'th';
																										}else{ 
																											if($row == 2) $htmlval = $htmlval."<tbody>";
																											$col = 'td';
																										}
																										$htmlval = $htmlval."<tr>";
																										for ($c=0; $c < $num; $c++) {
																											if($data[$c] != "" || trim($data[$c]) != " "){
																													$htmlval = $htmlval. '<'.$col.'>'.$data[$c].'</'.$col.'>';
																											}else{
																													$htmlval = $htmlval. '<'.$col.'>&nbsp;</'.$col.'>';
																											}
																										}
																										if($row == 1) $htmlval = $htmlval."</tr></thead>";
																										else $htmlval = $htmlval."</tr>";
																										$row++;
																									}
																									$htmlval = $htmlval."</tbody></table>";
																									fclose($handle);
																								}else {
																									$htmlval = "Error reading data file";
																								}

																								echo $htmlval;
																								?>
																							</div>
														
																						<?php } ?>

																					<?php } ?>

																					

																				<?php } ?>
																			</div>
																		</div>
																</div>
														</div>

														<?php 
														$i ++;
														} 
														?>
														
												</div>

												</div>
												</div>	

												</div>
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../../htmlib/footer.inc.php"; 
require "../../htmlib/js.inc.php";

?>
