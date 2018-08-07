<?php

require "../../phplib/genlibraries.php";
redirectOutside();

InputTool_checkRequest($_REQUEST);

$from = InputTool_getOrigin($_REQUEST);

list($rerunParams,$inPaths) = InputTool_getPathsAndRerun($_REQUEST);

$dirName = InputTool_getDefExName();

// get tool details
$toolId = "chromatindyn";
$tool   = getTool_fromId($toolId,1);

// CHROMATIN DYNAMICS TOOL OPERATIONS:
// op = 0 || count(fn) = 2 -> TXT + TXT
// op = 1 || count(fn) = 1 -> GFF

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
                                  <span><?php echo $tool['name']; ?></span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> <?php echo $tool['title']; ?> </h1>
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
														
														<?php if($from == "tool") { ?>			

														<div class="row">
															<div class="col-md-12">
																		
																<div class="mt-element-step">
																	<div class="row step-line">
																			<div class="col-md-6 mt-step-col first active">
																					<div class="mt-step-number bg-white">1</div>
																					<div class="mt-step-title uppercase font-grey-cascade">Select tool</div>
																			</div>
																			<div class="col-md-6 mt-step-col last active">
																					<div class="mt-step-number bg-white">2</div>
																					<div class="mt-step-title uppercase font-grey-cascade">Configure tool</div>
																			</div>
																	</div>
																</div>

															</div>
														</div>

														<?php } ?>	

														<form action="#" class="horizontal-form" id="tool-input-form">
																<input type="hidden" name="tool" value="<?php echo $toolId;?>" />
																<input type="hidden" id="base-url"     value="<?php echo $GLOBALS['BASEURL']; ?>"/>
																				 
                              <!-- BEGIN PORTLET 1: PROJECT -->
                              <div class="portlet box blue-oleo">
                                  <div class="portlet-title">
                                      <div class="caption">
                                        <div style="float:left;margin-right:20px;"> <i class="fa fa-check-square-o" ></i> Project</div>
                                      </div>
                                  </div>
                                  <div class="portlet-body form">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Select Project</label>
																										<?php InputTool_getSelectProjects(); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Execution Name</label>
                                                    <input type="text" name="execution" id="dirName" class="form-control" value="<?php echo $dirName;?>">
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
                              <!-- END PORTLET 1: PROJECT -->


															<?php if(($_REQUEST["op"] == 0 && isset($_REQUEST["op"])) || (count($_REQUEST['fn']) == 2 && !isset($_REQUEST["op"]))) { ?>
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
                                                  <!--<div class="form-group">
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
                                                  </div>-->
																									<?php $ff = matchFormat_File($tool['input_files']['sequence']['file_type'], $inPaths); ?>
																									<?php InputTool_printSelectFile($tool['input_files']['sequence'], $rerunParams['loc'], $ff[0], false, true); ?>
                                              </div>
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
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
                                                  </div>-->
																									<?php $ff = matchFormat_File($tool['input_files']['nuclPos']['file_type'], $inPaths); ?>
																									<?php InputTool_printSelectFile($tool['input_files']['nuclPos'], $rerunParams['loc'], $ff[0], false, true); ?>
                                              </div>


                                          </div>

																					<h4 class="form-section">Settings</h4>
	
																						<div class="row">
                                              <div class="col-md-6">
												  											<!--<div class="form-group operations_select">
                                                      <label class="control-label">Operations <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Select the operation you want to execute.</p>"></i></label>
                                                      <select class="form-control form-field-enabled valid select2chromdyn" name="arguments[operations][]" id="operations" aria-invalid="false" multiple="multiple">
                                                          <option value=""></option>
                                                          <option value="createStructure" >Create Structure</option>
                                                          <option value="createTrajectory">Create Trajectory</option>
                                                      </select>
                                                  </div>-->
																								<?php echo InputTool_printField($tool['arguments']['operations'], $rerunParams['operations']); ?>
																							</div>
																							<div class="col-md-6">
																									<div class="form-group display-hide" id="fg_numStruct">
                                                      <label class="control-label"><?php echo $tool['arguments']['createTrajectory:numStruct']['description']; ?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['createTrajectory:numStruct']['help']; ?></p>"></i></label>
																											<input type="number" name="arguments[<?php echo $tool['arguments']['createTrajectory:numStruct']['name']; ?>]" id="createTrajectory_numStruct" class="form-control form-field-enabled" min="<?php echo $tool['arguments']['createTrajectory:numStruct']['minimum']; ?>" max="<?php echo $tool['arguments']['createTrajectory:numStruct']['maximum']; ?>" value="<?php echo $tool['arguments']['createTrajectory:numStruct']['default']; ?>" disabled>
                                                  </div>
																							</div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 2: OPTIONS -->
															<?php } else { ?>
															<!-- BEGIN PORTLET 4: Create 3D From NucleR -->
                              <div class="portlet box blue" id="form-block-header2">
                                  
																	<div class="portlet-title">
                                      <div class="caption">
                                        <i class="fa fa-cogs" ></i> Tool settings
                                      </div>
                                  </div>
                                  <div class="portlet-body form form-block" id="form-block2">
                                      <div class="form-body">
                                          <h4 class="form-section">File inputs</h4>
																					<div class="row">
                                              <div class="col-md-6">
                                                  <!--<div class="form-group">
                                                      <label class="control-label">NucleR output <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Create a 3D structure from an 'NR_' gff file from Nucleosome Dynamic.</p>"></i></label>
                                                      <select  name="input_files[gffNucleaR]" id="gffNucleaR" class="form-control form-field-enabled ">
																													<?php foreach ($inPaths as $file) {  ?>
																													<?php if($file['format'] == 'GFF3') { ?>
																													<?php $p = explode("/", $file['path']); ?>
                                                      		<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																													<?php } ?>
																													<?php } ?>
																											</select>		
                                                  </div>-->
																									<?php $ff = matchFormat_File($tool['input_files']['gffNucleaR']['file_type'], $inPaths); ?>
																									<?php InputTool_printSelectFile($tool['input_files']['gffNucleaR'], $rerunParams['loc'], $ff[0], false, true); ?>
                                              </div>
                                          </div>
                                          <h4 class="form-section">Settings</h4>
                                          <div class="row">
																							<div class="col-md-6">
												  											<div class="form-group operations_select">
                                                      <label class="control-label"><?php echo $tool['arguments']['operations']['description']; ?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['arguments']['operations']['help']; ?></p>"></i></label>
                                                      <select class="form-control form-field-enabled valid" name="arguments[<?php echo $tool['arguments']['operations']['name']; ?>][]" id="operations" aria-invalid="false" multiple="multiple" disabled>
                                                          <option value="create3DfromNucleaR" selected>Create 3D Structure</option>
                                                      </select>
																											<input type="hidden" name="arguments[<?php echo $tool['arguments']['operations']['name']; ?>][]" value="create3DfromNucleaR" />

                                                  </div>
																							</div>

                                              <div class="col-md-6">
																									<!--<div class="form-group">
                                                      <label class="control-label">3D structure from Nucleosome Dynamics <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'>Obtain a structure from nucleosome positions within a genomic region calculated by nucleaR (within Nucleosome Dynamics). Use for example 'chrI:37415..39104'.</p>"></i></label>
																											<input type="text" name="arguments[create3DfromNucleaR:genRegion]" id="genRegion" class="form-control form-field-enabled" placeholder="format type: chrI:37415..39104">
                                                  </div>-->
																								<?php echo InputTool_printField($tool['arguments']['create3DfromNucleaR:genRegion'], $rerunParams['create3DfromNucleaR:genRegion']); ?>
																							</div>
																							
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <!-- END PORTLET 4: Create 3D From NucleR -->
															<?php } ?>

                              <div class="alert alert-danger err-tool display-hide">
                                  <strong>Error!</strong> You forgot to fill out some mandatory fields, please check them before submit the form.
                              </div>

                              <div class="alert alert-warning warn-tool display-hide">
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
    
									<div class="modal fade bs-modal-lg" id="modalDTStep2" tabindex="-1" role="basic" aria-hidden="true">
      	<div class="modal-dialog modal-lg">
    			<div class="modal-content">
        		<div class="modal-header">
      				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
     	 				<h4 class="modal-title">Select file(s)</h4>
        		</div>
        		<div class="modal-body"><div id="loading-datatable"><div id="loading-spinner">LOADING</div></div></div>
        		<div class="modal-footer">
      				<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
      				<button type="button" class="btn green btn-modal-dts2-ok" disabled>Accept</button>
        		</div>
    			</div>
    			<!-- /.modal-content -->
      	</div>
      	<!-- /.modal-dialog -->
  		</div>

 
<?php 

require "../../htmlib/footer.inc.php"; 
require "../../htmlib/js.inc.php";

?>
