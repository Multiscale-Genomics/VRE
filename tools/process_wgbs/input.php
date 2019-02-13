<?php

require "../../phplib/genlibraries.php";
redirectOutside();


InputTool_checkRequest($_REQUEST);

$from = InputTool_getOrigin($_REQUEST);

list($rerunParams,$inPaths) = InputTool_getPathsAndRerun($_REQUEST);

$dirName = InputTool_getDefExName();

// get tool details
$toolId = "process_wgbs";
$tool   = getTool_fromId($toolId,1);

// PROCESS WGBS TOOL OPERATIONS:
// op = 0 || count(fn) = 3  -> FASTQ + FASTQ  +FASTA

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


                              <!-- BEGIN PORTLET 2: SECTION 1 -->
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
                                                <label class="control-label"><?php echo $tool['input_files']['fastq1']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['input_files']['fastq1']['help']?></p>"></i></label>
                                                <select  name="input_files[<?php echo $tool['input_files']['fastq1']['name']?>]" class="form-control form-field-enabled params_wgbs_inputs">
																								<option selected="" value=""> -- select a file -- </option>
 																								<?php foreach ($inPaths as $file) {  ?>
																									<?php if($file['format'] == 'FASTQ') { ?>
																									<?php $p = explode("/", $file['path']); ?>
																									<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																									<?php } ?>
																								<?php } ?>
																								</select>		
																								 </div>-->
																								<?php $ff = matchFormat_File($tool['input_files']['fastq1']['file_type'], $inPaths); ?>
																									<?php InputTool_printSelectFile($tool['input_files']['fastq1'], $rerunParams['fastq1'], $ff[0], false, true); ?>
                                             </div>

																							<div class="col-md-6">
            																	<!--<div class="form-group">
                                                <label class="control-label"><?php echo $tool['input_files']['fastq2']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['input_files']['fastq2']['help']?></p>"></i></label>
                                                <select  name="input_files[<?php echo $tool['input_files']['fastq2']['name']?>]" class="form-control form-field-enabled params_wgbs_inputs">
																								<option selected="" value=""> -- select a file -- </option>
 																								<?php foreach ($inPaths as $file) {  ?>
																									<?php if($file['format'] == 'FASTQ') { ?>
																									<?php $p = explode("/", $file['path']); ?>
																									<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																									<?php } ?>
																								<?php } ?>
																								</select>		
																								 </div>-->
																								<?php $ff = matchFormat_File($tool['input_files']['fastq2']['file_type'], $inPaths); ?>
																									<?php InputTool_printSelectFile($tool['input_files']['fastq2'], $rerunParams['fastq2'], $ff[1], false, false); ?>

                                             </div>
																							
                                          </div>

																					<div class="row">

																						<?php if(($_REQUEST["op"] == 1) 
																						|| (count($_REQUEST['fn']) == 3 && !isset($_REQUEST["op"]))
																						|| (count($_REQUEST['fn']) == 2 && !isset($_REQUEST["op"]))) { ?>

																						<!-- input_file: genome -->
                                             <div class="col-md-6">
            																	<!--<div class="form-group">
                                                <label class="control-label"><?php echo $tool['input_files']['genome']['description']?> <i class="icon-question tooltips" data-container="body" data-html="true" data-placement="right" data-original-title="<p align='left' style='margin:0'><?php echo $tool['input_files']['genome']['help']?></p>"></i></label>
                                                <select  name="input_files[<?php echo $tool['input_files']['genome']['name']?>]" class="form-control form-field-enabled  ">
																								<?php foreach ($inPaths as $file) {  ?>
																									<?php if($file['format'] == 'FASTA') { ?>
																									<?php $p = explode("/", $file['path']); ?>
																									<option value="<?php echo $file['fn']; ?>"><?php echo $p[1]; ?> / <?php echo $p[2]; ?></option>
																									<?php } ?>
																								<?php } ?>
																								</select>	
																								 </div>-->
																								<?php $ff = matchFormat_File($tool['input_files']['genome']['file_type'], $inPaths); ?>
																									<?php InputTool_printSelectFile($tool['input_files']['genome'], $rerunParams['fastq2'], $ff[0], false, true); ?>
																						 </div>

																							<?php } else { ?>

																							<div class="col-md-6">
																								<?php InputTool_printListOfFiles($tool['input_files_public_dir']['genome_public'], $rerunParams['genome_public'], true); ?>
																							</div>

																							<?php } ?>

																						
																							
																					</div>
																						<?php echo InputTool_printField($tool['arguments']['aligner'], $rerunParams['aligner']); ?>
																						<?php echo InputTool_printField($tool['arguments']['aligner_path'], $rerunParams['aligner_path']); ?>
																						<?php echo InputTool_printField($tool['arguments']['bss_path'], $rerunParams['bss_path']); ?>
																	</div>
																</div>
															</div>
									
                              <!-- END PORTLET 2: SECTION 1 -->



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
