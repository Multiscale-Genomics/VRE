<?php

require "../../phplib/genlibraries.php";
redirectOutside();

// check inputs
if (!isset($_REQUEST['fn']) && !isset($_REQUEST['rerunDir'])){
	$_SESSION['errorData']['Error'][]="Please, before running NAFlex, select either a PDB file or a PDB file, a TOP file and a CRD file. ";
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

if((count($_REQUEST['fn']) != 1) && (count($_REQUEST['fn']) != 3)){
	$_SESSION['errorData']['Error'][] = "NAFlex only can be run with one (PDB) or three (PDB, TOP and CRD / DCD) input files.";
	redirect('/workspace/');
}

$count_val = array_count_values($formats);

if(count($_REQUEST['fn']) == 3) {

	if(($count_val['PDB'] != 1) && ($count_val['TOP'] != 1) || ((!in_array("MDCRD", $formats)) && (!in_array("DCD", $formats)))) {
		$_SESSION['errorData']['Error'][] = "NAFlex only can be run with one (PDB) or three (PDB, TOP and CRD / DCD) input files.";
		redirect('/workspace/');
	}

}else {

	if($count_val['PDB'] != 1) {
		$_SESSION['errorData']['Error'][] = "NAFlex only can be run with one (PDB) or three (PDB, TOP and CRD / DCD) input files.";
		redirect('/workspace/');
	}


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

// set default values
/*$def = Array(
    'pyDock'=> Array(
	'receptor'   => $_REQUEST['fn'][0],
	'ligand'     => $_REQUEST['fn'][1],
	'models'     => 5,
	'scoring'    => "PyDockDNA",
	'description'=> "",
	'project'    => $dirName
    )
);


if (count($rerunParams)){
	$def_tmp=array_merge($def,$rerunParams);
	$def = $def_tmp;
}*/


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
                                  <a href="workspace/">User Workspace</a></span>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>Tools</span>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>NAFlex analyses</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Nucleic Acids Flexibility Analyses
                        </h1>
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
				
								<?php if($file['format'] == 'PDB') { ?>
		
									<a href="javascript:openNGL('<?php echo $file['fn']; ?>', '<?php echo $p[2]; ?>', 'pdb');" style="margin-left:5px;">
										<div class="label label-sm label-info tooltips" style="padding:4px 5px;" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Click here to preview this file with NGL.</p>">
											<i class="fa fa-window-maximize font-white"></i>
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
			 <form action="#" class="horizontal-form" id="naflex-form">
					<input type="hidden" name="tool" value="naflex" />
					<input type="hidden" id="base-url"     value="<?php echo $GLOBALS['BASEURL']; ?>"/>
				  <!--<input type="hidden" id="file-input1" name="fn[]" value="<?php echo $_REQUEST['fn'][0]; ?>" />
				  <input type="hidden" id="file-input2" name="fn[]" value="<?php echo $_REQUEST['fn'][1]; ?>" />-->
				 
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
                              <!-- BEGIN PORTLET 2: OPTIONS -->
                              <div class="portlet box blue form-block-header" id="form-block-header1">
                                  <div class="portlet-title">
                                      <div class="caption">
                                        <i class="fa fa-cogs" ></i> Tool settings
                                      </div>
                                  </div>
                                  <div class="portlet-body form form-block" id="form-block1">
                                      <div class="form-body">
																					<h4 class="form-section">File inputs</h4>
																					<div class="row">
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label class="control-label">PDB File <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Input nucleic acid (or protein-na complex) representative structure used in the MD simulation.</p>"></i></label>
                                                      <select  name="input_files[pdb]" class="form-control form-field-enabled params_naflex_inputs">
																												<?php if(count($_REQUEST['fn']) != 1) { ?><option selected value> -- select a file -- </option> <?php } ?>
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>" <?php if(count($_REQUEST['fn']) == 1) echo 'selected' ?>><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																											</select>		
                                                  </div>
                                              </div>
																							<?php if(count($_REQUEST['fn']) == 3) { ?>

                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Top File <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Input nucleic acid (or protein-na complex) topology used in the MD simulation.</p>"></i></label>
                                                      <select  name="input_files[top]" class="form-control form-field-enabled params_naflex_inputs">
																												<option selected value> -- select a file -- </option>
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																											</select>		
                                                  </div>
                                              </div>

																							<?php } ?>
                                          </div>
	
																					<?php if(count($_REQUEST['fn']) == 3) { ?>
																						<div class="row">
                                              <div class="col-md-6">
																									<div class="form-group">
                                                      <label class="control-label">Trajectory File <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Input nucleic acid (or protein-na complex) trajectory obtained from a MD simulation.</p>"></i></label>
                                                      <select  name="input_files[crd]" class="form-control form-field-enabled params_naflex_inputs">
																												<option selected value> -- select a file -- </option>
																												<?php foreach ($inPaths as $file) {  ?>
																												<?php $p = explode("/", $file['path']); ?>
																												<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																												<?php } ?>
																											</select>		
                                                  </div>
                                              </div>
																						</div>
																					<?php } ?>
	

																					<h4 class="form-section">Settings</h4>
	
																						<div class="row">
                                              <div class="col-md-12">
												  											<div class="form-group operations_select">
                                                      <label class="control-label">Operations <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Operations you want to apply to this tool. You can choose more than one field.</p>"></i></label>
																											<?php if(count($_REQUEST['fn']) == 1) { ?>
																											<select class="form-control form-field-enabled valid select2naf" name="arguments[operations][]" id="operations" aria-invalid="false" multiple="multiple" disabled>
                                                          <option value="Curves" selected>Curves</option>
																											</select>
																											<input type="hidden" name="arguments[operations][]" value="Curves" />
																											<?php } else { ?>
                                                      <select class="form-control form-field-enabled valid select2naf" name="arguments[operations][]" id="operations" aria-invalid="false" multiple="multiple">
                                                          <option value=""></option>
                                                          <option value="ALL">ALL</option>
                                                          <option value="Curves">Curves</option>
																													<option value="Stiffness">Stiffness</option>
																													<option value="Pcazip">Pcazip</option>
                                                          <option value="Nmr_Jcouplings">Nmr_Jcouplings</option>
																													<option value="Nmr_NOEs">Nmr_NOEs</option>
																													<option value="HBs">HBs</option>
                                                          <option value="Stacking">Stacking</option>
																													<option value="DistanceContactMaps">DistanceContactMaps</option>
                                                      </select>
																											<?php } ?>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 2: OPTIONS -->
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
															<div id="loading-viewport" style="position:absolute;left:42%; top:200px;"><img src="assets/layouts/layout/img/ring-alt.gif" /></div>
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