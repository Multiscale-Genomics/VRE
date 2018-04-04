<?php

require "../../phplib/genlibraries.php";
redirectOutside();

// check inputs
if (!isset($_REQUEST['fn']) && !isset($_REQUEST['rerunDir'])){
	$_SESSION['errorData']['Error'][]="Please, before running TADbit, please select the input files for running this tool.";
	redirect('/workspace/');
}

if(count($_REQUEST['fn']) > 1) {
	$_SESSION['errorData']['Error'][] = "Please, select just one BAM file.";
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

/*$count_val = array_count_values($formats);

if(!(in_array("FASTQ", $formats))) {
	$_SESSION['errorData']['Error'][] = "Please, select at least two FASTQ files.";
	redirect('/workspace/');
} elseif($count_val['FASTQ'] != 2) {
	$_SESSION['errorData']['Error'][] = "Please, select at least two FASTQ files.";
	redirect('/workspace/');
}

if(in_array("FASTA", $formats) && !in_array("GEM", $formats)) {
	$_SESSION['errorData']['Error'][] = "If you provide a FASTA file, you must provide a GEM file too.";
	redirect('/workspace/');
}*/

$formats = array_unique($formats);

if($formats[0] != "BAM"){
	$_SESSION['errorData']['Error'][] = "Please, select one BAM file for running this tool";
	redirect('/workspace/');
}

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
                                  <span>TADbit Normalize</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> TADbit Normalize</h1>
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

			 <form action="#" class="horizontal-form" id="tadbit_normalize-form">
				  <input type="hidden" name="tool" value="tadbit_normalize" />
                    <input type="hidden" id="base-url"     value="<?php echo $GLOBALS['BASEURL']; ?>"/>
                    <input type="hidden" name="input_files_public_dir[refGenomes_folder]" value="refGenomes/" />

				 
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
                              <!-- BEGIN PORTLET 5: Normalization by ICE -->
                              <div class="portlet box blue" id="form-block-header4">
                                  <div class="portlet-title">
                                      <div class="caption">
																				 Normalization by ICE
		                                  </div>
                                  </div>
                                  <div class="portlet-body form" id="form-block4">
																			<div class="form-body">

																				<h4 class="form-section">File inputs</h4>
																					<div class="row">

                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label class="control-label">TADbit-generated BAM file <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Path to a TADbit-generated BAM file with filtered reads</p>"></i></label>
                                                      <select  name="input_files[bamin]" class="form-control">
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php if($file['format'] == 'BAM') { ?>
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
																									<div class="form-group">
																											<label class="control-label">Normalization method <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Normalization method to apply.</p>"></i></label>
																											<select  name="arguments[normalization]" class="form-control" id="normalization">
																												<option></option>
																												<option value="Vanilla" selected>Vanilla</option>
																												<option value="oneD">oneD</option>
																											</select>
                                                  </div>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Resolution of the normalization <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Resolution of the normalization (should input a number between 10000 (10 kb) and 10000000 (10 Mb)).</p>"></i></label>
																											<input type="number" min="10000" max="10000000" name="arguments[resolution]" id="resolution" class="form-control" value="100000">
                                                  </div>
																							</div>
																					</div>
																					<div class="row">
																							<div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Minimum percentage <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Lower percentile from which consider bins as good.</p>"></i></label>
																											<input type="number" min="0" max="100" step="0.1" name="arguments[min_perc]" id="min_perc" class="form-control" value="7">
                                                  </div>
																							</div>
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label class="control-label">Maximum percentage <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Upper percentile until which consider bins as good.</p>"></i></label>
																											<input type="number" min="0" max="100" step="0.1" name="arguments[max_perc]" id="max_perc" class="form-control" value="99.8">
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 5: Normalization by ICE -->



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
