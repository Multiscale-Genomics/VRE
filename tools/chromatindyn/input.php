<?php

require "../../phplib/genlibraries.php";
redirectOutside();

// check inputs
if (!isset($_REQUEST['fn']) && !isset($_REQUEST['rerunDir'])){
	$_SESSION['errorData']['Error'][]="Please, before running Chromatin Dynamics, select two TXT files with DNA sequences and / or a GFF3 file as input.";
	redirect('/workspace/');
}

if((count($_REQUEST['fn']) != 1) && (count($_REQUEST['fn']) != 2)) { 
	$_SESSION['errorData']['Error'][] = "Please, select two TXT files with DNA sequences or a GFF3 file.";
	redirect('/workspace/');
 }

$rerunParams  = Array();
$inPaths = Array();
$formats = Array();

if ($_REQUEST['rerunDir']){
	$dirMeta = $GLOBALS['filesMetaCol']->findOne(array('_id' => $_REQUEST['rerunDir'])); 
	if (!is_array($dirMeta['inPaths']) && !isset($dirMeta['raw_params'])){
		$_SESSION['errorData']['Error'][]="Cannot rerun job ".$_REQUEST['rerunDir'].". Some folder metadata is missing.";
		redirect('/workspace/');
	}
	if (is_array($dirMeta['inPaths'][0])){
		$_SESSION['errorData']['Internal'][]="Cannot rerun job ".$_REQUEST['rerunDir'].". New directory metadata not implemeted yet.";
		redirect('/workspace/');
	}
	foreach ($dirMeta['inPaths'] as $inPath){
		$file['path'] = $inPath;
		$file['fn'] = getGSFileId_fromPath($inPath);
		$file['format'] = getAttr_fromGSFileId($file['fn'],'format');
		array_push($formats,$file['format']);
		array_push($inPaths,$file);
	}
	$rerunParams = $dirMeta['raw_params'];
	//$inPaths=$dirMeta['inPaths']
	//$_REQUEST['fn']= array_map("getGSFileId_fromPath",$dirMeta['inPaths']);
}else{
	if (!is_array($_REQUEST['fn']))
		$_REQUEST['fn'][]=$_REQUEST['fn'];

	foreach($_REQUEST['fn'] as $fn){
		$file['path'] = getAttr_fromGSFileId($fn,'path');
		$file['fn'] = $fn;
		$file['format'] = getAttr_fromGSFileId($fn,'format');
		array_push($formats,$file['format']);
		array_push($inPaths,$file);
	}
	//array_push($inPaths,getAttr_fromGSFileId($fn,'path'));
}

$count_val = array_count_values($formats);

/*if((count($_REQUEST['fn']) == 2) && ($count_val['TXT'] < 2)) {
	$_SESSION['errorData']['Error'][] = "Please, select two TXT files with DNA sequences and a GFF3 file.";
	redirect('/workspace/');
}*/

/*if((count($_REQUEST['fn']) == 3) && (!(in_array("TXT", $formats) && in_array("GFF3", $formats)))) {
	$_SESSION['errorData']['Error'][] = "Please, select two TXT files with DNA sequences and a GFF3 file.";
	redirect('/workspace/');
}*/

//var_dump($count_val['TXT']);
//die();

//if((count($_REQUEST['fn']) == 2) && ($formats[0] != 'TXT') && (count($formats) > 1)/*(!in_array("TXT", $formats)) && (count($formats) > 1)*/) {
if(count($_REQUEST['fn']) == 2) {
	if(!in_array("TXT", $formats)) {
		$_SESSION['errorData']['Error'][] = "Please, select two TXT files with DNA sequences1.";
		redirect('/workspace/');
	} elseif(($count_val['TXT'] != 2) /*&& ($count_val['GFF3'] != 1)*/) {
		$_SESSION['errorData']['Error'][] = "Please, select two TXT files with DNA sequences2.";
		redirect('/workspace/');
		}
}

if((count($_REQUEST['fn']) == 1) && !in_array("GFF3", $formats)) {
	$_SESSION['errorData']['Error'][] = "Please, select a GFF3 file.";
	redirect('/workspace/');
}

$formats = array_unique($formats);

// default project dir
$dirNum="000";
$reObj = new MongoRegex("/^".$_SESSION['User']['id']."\\/run\d\d\d$/i");
$prevs  = $GLOBALS['filesCol']->find(array('path' => $reObj, 'owner' => $_SESSION['User']['id']));
if ($prevs->count() > 0){
        $prevs->sort(array('_id' => -1));
        $prevs->next();
        $previous = $prevs->current();
        if (preg_match('/(\d+)$/',$previous["path"],$m) ){
            $dirNum= sprintf("%03d",$m[1]+1);
        }
}
$dirName="run".$dirNum;
$prevs  = $GLOBALS['filesCol']->find(array('path' => $GLOBALS['dataDir']."/".$_SESSION['User']['dataDir']."/$dirName", 'owner' => $_SESSION['User']['id']));
if ($prevs->count() > 0){
    $dirName="run".rand(100, 999);
}



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
                                  <span>Chromatin Dynamics</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Chromatin Dynamics </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->
                        <div class="row">
				<div class="col-md-12">
				<?php if(isset($_SESSION['errorData'])) { ?>
					<div class="alert alert-warning">
					<?php foreach($_SESSION['errorData'] as $subTitle=>$txts){
						print "$subTitle<br/>";
						foreach($txts as $txt){
							print "<div style=\"margin-left:20px;\">$txt</div>";
						}
					}
					unset($_SESSION['errorData']);
					?>
					</div>
				<?php } ?>
															<!-- BEGIN PORTLET 0: INPUTS -->
                              <div class="portlet box blue-oleo">
                                  <div class="portlet-title">
                                      <div class="caption">
                                        <div style="float:left;margin-right:20px;"> <i class="fa fa-sign-in" ></i> Inputs</div>
                                      </div>
                                  </div>
                                  <div class="portlet-body">
																		<ul class="feeds" id="list-files-run-tools">
																		<?php foreach ($inPaths as $file) {
																			$path= $file['path'];
																			$p = explode("/", $path); 
																			?>
																			<li class="tool-122 tool-list-item">
																			<div class="col1">
																				<div class="cont">
																					<div class="cont-col1">
																						<div class="label label-sm label-info">
																							<i class="fa fa-file"></i>
																						</div>
																					</div>
																					<div class="cont-col2">
																					<div class="desc">
																					<span class="text-info" style="font-weight:bold;"><?php echo $p[1]; ?>  /</span> <?php echo $p[2]; ?> 
																						<!--
																						<?php if($file['format'] == 'PDB') { ?>
																
																							<a href="javascript:openNGL('<?php echo $file['fn']; ?>', '<?php echo $p[2]; ?> ');" style="margin-left:5px;">
																								<div class="label label-sm label-info tooltips" style="padding:4px 5px;" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Click here to preview this file with NGL.</p>">
																									<i class="fa fa-window-maximize font-white"></i>
																								</div>
																							</a>
																						
																						<?php } ?>								
																						-->
																					</div>
																					</div>
																				</div>
																			</div>
																			</li>
																		<?php } ?>
                                </ul>
                                  </div>
                              </div>
                              <!-- END PORTLET 0: INPUTS -->

														<form action="#" class="horizontal-form" id="chromdyn-form">
																<input type="hidden" name="tool" value="chromatindyn" />
																<input type="hidden" id="base-url"     value="<?php echo $GLOBALS['BASEURL']; ?>"/>
																				 
                              <!-- BEGIN PORTLET 1: ANALYZES -->
                              <div class="portlet box blue-oleo">
                                  <div class="portlet-title">
                                      <div class="caption">
                                        <div style="float:left;margin-right:20px;"> <i class="fa fa-check-square-o" ></i> Project</div>
                                      </div>
                                  </div>
                                  <div class="portlet-body form">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">Name</label>
                                                    <input type="text" name="project" id="dirName" class="form-control" value="<?php echo $dirName;?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">Description</label>
                                                    <textarea id="description" name="description" class="form-control" style="height:120px;" placeholder="Write a short description here..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                              </div>
															<!-- END PORTLET 1: ANALYZES -->
															<?php if((count($_REQUEST['fn']) > 1)) { ?>
															<!-- BEGIN PORTLET 2: OPTIONS -->
                              <div class="portlet box blue form-block-header" id="form-block-header1">
                                  <div class="portlet-title">
                                      <div class="caption">
                                        <i class="fa fa-cogs" ></i> Chromatin Dynamics from sequence and nucleosomes position
                                      </div>
                                  </div>
                                  <div class="portlet-body form form-block" id="form-block1">
                                      <div class="form-body">
																					<h4 class="form-section">File inputs</h4>
																					<div class="row">

                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label class="control-label">DNA sequence <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>DNA linker sequence.</p>"></i></label>
                                                      <select  name="input_files[sequence]" id="sequence" class="form-control form-field-enabled params_chromdyn_inputs">
																												<option selected value> -- select a file -- </option>
																													<?php foreach ($inPaths as $file) {  ?>
																													<?php //if($file['format'] == 'TXT') { ?>
																													<?php $p = explode("/", $file['path']); ?>
                                                      		<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																													<?php //} ?>
																													<?php } ?>
																											</select>		
                                                  </div>
                                              </div>
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label class="control-label">Positions of nucleosomes <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Put in position where the nucleosomes are positioned in the linker sequence. Between each number has to be a space: for example '5 15' creates a structure where one nucleosome is put after the 5th bp of the linker sequence and another one after the 15th bp of the linker sequence generating a linker of 10bp between the two nucleosomes. Before the first and after the last nucleosome there have to at least 5 base pairs. Between two nucleosomes there has to be at least 3 base pairs.</p>"></i></label>
                                                      <select  name="input_files[nuclPos]" id="nuclPos" class="form-control form-field-enabled params_chromdyn_inputs">
																												<option selected value> -- select a file -- </option>
																													<?php foreach ($inPaths as $file) {  ?>
																													<?php //if($file['format'] == 'GFF3') { ?>
																													<?php $p = explode("/", $file['path']); ?>
                                                      		<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																													<?php //} ?>
																													<?php } ?>
																											</select>		
                                                  </div>
                                              </div>


                                          </div>

																					<h4 class="form-section">Settings</h4>
	
																						<div class="row">
                                              <div class="col-md-6">
												  											<div class="form-group operations_select">
                                                      <label class="control-label">Operations <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Select the operation you want to execute.</p>"></i></label>
                                                      <select class="form-control form-field-enabled valid select2chromdyn" name="arguments[operations][]" id="operations" aria-invalid="false" multiple="multiple">
                                                          <option value=""></option>
                                                          <option value="createStructure" >Create Structure</option>
                                                          <option value="createTrajectory">Create Trajectory</option>
                                                      </select>
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group display-hide" id="fg_numStruct">
                                                      <label class="control-label">Number of structures <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Number of structures to generate (max 100).</p>"></i></label>
																											<input type="number" name="arguments[createTrajectory:numStruct]" id="numStruct" class="form-control form-field-enabled" min="1" max="100" value="10" disabled>
                                                  </div>
																							</div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 2: OPTIONS -->
															<?php } ?>
															<?php if((count($_REQUEST['fn']) != 2) && in_array("GFF3", $formats)) { ?>
															<!-- BEGIN PORTLET 4: Create 3D From NucleR -->
                              <div class="portlet box blue" id="form-block-header2">
                                  <!--<div class="portlet-title">
                                      <div class="caption">
																				<input type="checkbox" class="make-switch switch-block" name="arguments[create3DfromNucleaR]" id="switch-block2" data-size="mini" checked>
                                        <div style="float:right;margin-left:20px;">Create 3D From NucleR </div>
																			</div>
																			<div class="tools">
                                          <a href="javascript:;" class="collapse"></a>
                                      </div>
                                  </div>-->
																	<div class="portlet-title">
                                      <div class="caption">
                                        <i class="fa fa-cogs" ></i> Chromatin Dynamics from NucleR
                                      </div>
                                  </div>
                                  <div class="portlet-body form form-block" id="form-block2">
                                      <div class="form-body">
                                          <h4 class="form-section">File inputs</h4>
																					<div class="row">
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label class="control-label">NucleR output <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Create a 3D structure from an 'NR_' gff file from Nucleosome Dynamic.</p>"></i></label>
                                                      <select  name="input_files[gffNucleaR]" id="gffNucleaR" class="form-control form-field-enabled ">
																													<?php foreach ($inPaths as $file) {  ?>
																													<?php if($file['format'] == 'GFF3') { ?>
																													<?php $p = explode("/", $file['path']); ?>
                                                      		<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																													<?php } ?>
																													<?php } ?>
																											</select>		
                                                  </div>
                                              </div>
                                          </div>
                                          <h4 class="form-section">Settings</h4>
                                          <div class="row">
																							<div class="col-md-6">
												  											<div class="form-group operations_select">
                                                      <label class="control-label">Operations <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Select the operation you want to execute.</p>"></i></label>
                                                      <select class="form-control form-field-enabled valid select2chromdyn" name="arguments[operations][]" id="operations" aria-invalid="false" multiple="multiple" disabled>
                                                          <option value="create3DfromNucleaR" selected>Create 3D Structure</option>
                                                      </select>
																											<input type="hidden" name="arguments[operations][]" value="create3DfromNucleaR" />

                                                  </div>
																							</div>

                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">3D structure from Nucleosome Dynamics <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Obtain a structure from nucleosome positions within a genomic region calculated by nucleaR (within Nucleosome Dynamics). Use for example 'chrI:37415..39104'.</p>"></i></label>
																											<input type="text" name="arguments[create3DfromNucleaR:genRegion]" id="genRegion" class="form-control form-field-enabled" placeholder="format type: chrI:37415..39104">
                                                  </div>
																							</div>
																							
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 4: Create 3D From NucleR -->
															<?php } ?>

                              <div class="alert alert-danger err-nd display-hide">
                                  <strong>Error!</strong> You forgot to fill out some mandatory fields, please check them before submit the form.
                              </div>

                              <div class="alert alert-warning warn-nd display-hide">
                                  <strong>Warning!</strong> At least one analysis should be selected.
                              </div>

                              <div class="form-actions">
                                  <button type="submit" class="btn blue" style="float:right;">
                                      <i class="fa fa-check"></i> Compute</button>
                              </div>
                              </form>
                            </div>
                        </div>
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->
    
									<div class="modal fade bs-modal" id="modalNGL" tabindex="-1" role="basic" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title"></h4>
                            </div>
                            <div class="modal-body">
                              <div id="viewport" style="width:100%; height:500px;background:#ddd;"></div>
                             </div>
                            <div class="modal-footer">
                                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
				</div>

 
<?php 

require "../../htmlib/footer.inc.php"; 
require "../../htmlib/js.inc.php";

?>
