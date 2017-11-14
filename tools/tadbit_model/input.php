<?php

require "../../phplib/genlibraries.php";
redirectOutside();

// check inputs
if (!isset($_REQUEST['fn']) && !isset($_REQUEST['rerunDir'])){
	$_SESSION['errorData']['Error'][]="Please, before running TADbit, please select the input files for running this tool.";
	redirect('/workspace/');
}

if((count($_REQUEST['fn']) != 1)/* && (count($_REQUEST['fn']) != 2)*/) { 
	$_SESSION['errorData']['Error'][] = "Please, select one HiC contact matrix (TXT) file";
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

//var_dump($formats);

/*if(count($_REQUEST['fn']) == 2) {
	if(!in_array("BAM", $formats) || !in_array("PICKLE", $formats)) {
		$_SESSION['errorData']['Error'][] = "Please, select one BAM file (mandatory) and / or one PICKLE file (optional)";
		redirect('/workspace/');
	} 
}*/

if((count($_REQUEST['fn']) == 1) && !in_array("TXT", $formats)) {
	$_SESSION['errorData']['Error'][] = "Please, select one HiC contact matrix (TXT) file";
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
                                  <span>TADbit Model</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> TADbit Model</h1>
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

																					<?php if($file['format'] == 'BAM') { ?>
							
																						<a target="_blank" href="visualizers/jbrowse/index.php/?user=<?php echo $_SESSION['User']['id']; ?>&fn[]=<?php echo $file['fn']; ?>">
																							<div class="label label-sm label-info tooltips" style="padding:4px 5px;" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Click here to preview this file with JBrowse.</p>">
																								<i class="fa fa-align-right font-white"></i>
																							</div>
																						</a>
																					
																					<?php } ?>

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

			 <form action="#" class="horizontal-form" id="tadbit_model-form">
				  <input type="hidden" name="tool" value="tadbit_model" />
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
                              <!-- BEGIN PORTLET: Modeling -->
                              <div class="portlet box blue" id="form-block-header1">
                                  <div class="portlet-title">
                                      <div class="caption">
																				 Modeling
		                                  </div>
                                  </div>
                                  <div class="portlet-body form" id="form-block1">
																			<div class="form-body">

																				<h4 class="form-section">File inputs</h4>
																					<div class="row">

                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label class="control-label">HiC contact matrix normalized <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Path to a tab-separated normalized contact matrix</p>"></i></label>
                                                      <select  name="input_files[hic_contacts_matrix_norm]" class="form-control">
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php if($file['format'] == 'TXT') { ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																												<?php } ?>
																											</select>		
                                                  </div>
																							</div>
																							<?php if(count($_REQUEST['fn']) == 2) { ?>
																							<div class="col-md-6">
                                                  <div class="form-group">
                                                      <label class="control-label">TADbit-generated PICKLE file <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Path to a TADbit-generated PICKLE file with HiC biases</p>"></i></label>
                                                      <select  name="input_files[hic_biases]" class="form-control">
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php if($file['format'] == 'PICKLE') { ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																												<?php } ?>
																											</select>		
                                                  </div>
																							</div>
																							<?php } ?>
                                          </div>
				
                                        <h4 class="form-section">Common Settings</h4>
                                          <div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Resolution <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Resolution of the binning (should input a number between 10000 (10 kb) and 10000000 (10 Mb)).</p>"></i></label>
																											<input type="number" min="10000" max="10000000" name="arguments[resolution]" id="resolution" class="form-control" value="100000">
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label"> Chromosome name. <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Chromosome name (chromosome name i.e. chr1)</p>"></i></label>
																											<input type="text" name="arguments[gen_pos_chrom_name]" id="gen_pos_chrom_name" class="form-control form-field-enabled" >
                                                  </div>
																							</div>
																					</div>
																					<div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Genomic position. Begin. <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Genomic coordinate from which to start modeling.</p>"></i></label>
																											<input type="number" name="arguments[gen_pos_begin]" id="gen_pos_begin" class="form-control form-field-enabled" >
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Genomic position. End. <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Genomic coordinate where to end modeling.</p>"></i></label>
																											<input type="number" name="arguments[gen_pos_end]" id="gen_pos_end" class="form-control form-field-enabled" >
                                                  </div>
																							</div>
																					</div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET: Modeling -->
															<!-- BEGIN PORTLET: Modeling - parameter optimization -->
                              <div class="portlet box blue" id="form-block-header2">
																	<div class="portlet-title">
																			<div class="caption">
																				<input type="checkbox" class="make-switch switch-block" name="optimization" id="switch-block2" data-size="mini" checked>
                                        <div style="float:right;margin-left:20px;">Modeling - parameter optimization </div>
																			</div>
																			<div class="tools">
                                          <a href="javascript:;" class="collapse"></a>
                                      </div>
                                  </div>
                                  <div class="portlet-body form form-block" id="form-block2">
                                      <div class="form-body">
																					<div class="row">
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Number of models to compute <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Number of models to compute for each optimization step.</p>"></i></label>
																											<input type="number" name="arguments[optimization:num_mod_comp]" id="num_mod_comp1" class="form-control form-field-enabled" value="50">
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Number of models to keep <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Number of models to keep from the total of models computed, for the comparison with original Hi-C matrix.</p>"></i></label>
																											<input type="number" name="arguments[optimization:num_mod_keep]" id="num_mod_keep1" class="form-control form-field-enabled" value="20">
                                                  </div>
																							</div>
                                          </div>
																					<div class="row">
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Maximum distance <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Range of numbers for optimal maxdist parameter, i.e. 400:1000:100; or just a single number e.g. 800; or a list of numbers e.g. 400 600 800 1000.</p>"></i></label>
																											<input type="text" name="arguments[optimization:max_dist]" id="max_dist1" class="form-control form-field-enabled" value="400:1000:200">
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Upper bound for Z-scored frequencies of interaction <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Range of numbers for optimal upfreq parameter, i.e. 0:1.2:0.3; or just a single number e.g. 0.8; or a list of numbers e.g. 0.1 0.3 0.5 0.9.</p>"></i></label>
																											<input type="text" name="arguments[optimization:upper_bound]" id="upper_bound1" class="form-control form-field-enabled" value="0:1.2:0.3" >
                                                  </div>
																							</div>
                                          </div>
																					<div class="row">
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Lower bound for Z-scored frequencies of interaction <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Range of numbers for optimal low parameter, i.e. -1.2:0:0.3; or just a single number e.g. -0.8; or a list of numbers e.g. -0.1 -0.3 -0.5 -0.9.</p>"></i></label>
																											<input type="text" name="arguments[optimization:lower_bound]" id="lower_bound1" class="form-control form-field-enabled" value="-1.2:0:0.3">
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Cutoff distance to consider an interaction between 2 particles <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Range of numbers for optimal cutoff distance. Cutoff is computed based on the resolution. This cutoff distance is calculated taking as reference the diameter of a modeled particle in the 3D model. i.e. 1.5:2.5:0.5; or just a single number e.g. 2; or a list of numbers e.g. 2 2.5.</p>"></i></label>
																											<input type="text" name="arguments[optimization:cutoff]" id="cutoff1" class="form-control form-field-enabled" value="2" >
                                                  </div>
																							</div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET: Modeling - parameter optimization -->
															<!-- BEGIN PORTLET: Modeling - generation of models -->
                              <div class="portlet box blue" id="form-block-header3">
																	<div class="portlet-title">
																			<div class="caption">
																				<input type="checkbox" class="make-switch switch-block" name="generation" id="switch-block3" data-size="mini">
                                        <div style="float:right;margin-left:20px;">Modeling - generation of models </div>
																			</div>
																			<div class="tools">
                                          <a href="javascript:;" class="collapse"></a>
                                      </div>
                                  </div>
                                  <div class="portlet-body form form-block" id="form-block3">
                                      <div class="form-body">
																					<div class="row">
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Number of models to compute <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Number of models to compute for each optimization step.</p>"></i></label>
																											<input type="number" name="arguments[generation:num_mod_comp]" id="num_mod_comp2" class="form-control form-field-enabled">
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Number of models to keep <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Number of models to keep from the total of models computed, for the comparison with original Hi-C matrix.</p>"></i></label>
																											<input type="number" name="arguments[generation:num_mod_keep]" id="num_mod_keep2" class="form-control form-field-enabled">
                                                  </div>
																							</div>
                                          </div>
																					<div class="row">
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Maximum distance <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Range of numbers for optimal maxdist parameter, i.e. 400:1000:100; or just a single number e.g. 800; or a list of numbers e.g. 400 600 800 1000.</p>"></i></label>
																											<input type="text" name="arguments[generation:max_dist]" id="max_dist2" class="form-control form-field-enabled">
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Upper bound for Z-scored frequencies of interaction <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Range of numbers for optimal upfreq parameter, i.e. 0:1.2:0.3; or just a single number e.g. 0.8; or a list of numbers e.g. 0.1 0.3 0.5 0.9.</p>"></i></label>
																											<input type="text" name="arguments[generation:upper_bound]" id="upper_bound2" class="form-control form-field-enabled">
                                                  </div>
																							</div>
                                          </div>
																					<div class="row">
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Lower bound for Z-scored frequencies of interaction <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Range of numbers for optimal low parameter, i.e. -1.2:0:0.3; or just a single number e.g. -0.8; or a list of numbers e.g. -0.1 -0.3 -0.5 -0.9.</p>"></i></label>
																											<input type="text" name="arguments[generation:lower_bound]" id="lower_bound2" class="form-control form-field-enabled">
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Cutoff distance to consider an interaction between 2 particles <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Range of numbers for optimal cutoff distance. Cutoff is computed based on the resolution. This cutoff distance is calculated taking as reference the diameter of a modeled particle in the 3D model. i.e. 1.5:2.5:0.5; or just a single number e.g. 2; or a list of numbers e.g. 2 2.5.</p>"></i></label>
																											<input type="text" name="arguments[generation:cutoff]" id="cutoff2" class="form-control form-field-enabled">
                                                  </div>
																							</div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET: Modeling - generation of models -->


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
