<?php

require "../phplib/genlibraries.php";
require "../phplib/admin.inc.php";

redirectToolDevOutside();


?>

<?php require "../htmlib/header.inc.php"; ?>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-container-bg-solid page-sidebar-fixed">
  <div class="page-wrapper">

  <?php require "../htmlib/top.inc.php"; ?>
  <?php require "../htmlib/menu.inc.php"; ?>


<!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        <!-- BEGIN PAGE HEADER-->
                        <!-- BEGIN PAGE BAR -->
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                              <li>
                                  <span>Admin</span>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>JSON Validator</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> JSON Validator
                            <small>for tool developers</small>
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->
                        <div class="row">
                            <div class="col-md-12">
																<p style="margin-top:0;">Paste or write your JSON code in the text area below. Once the JSON is correct, 
you can validate it against our <a href="https://raw.githubusercontent.com/Multiscale-Genomics/VRE_tool_jsons/master/tool_specification/tool_schema_dev.json" target="_blank">JSON Schema</a>.</p>
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                <div class="portlet light portlet-fit bordered">
																	
                                    <div class="portlet-body" id="portlet-json">
                                    
																			<textarea id="code_editor"  placeholder="Please, paste or write your JSON code here...">{}</textarea>
    
																		</div>
                                
                            </div>
														<!-- END EXAMPLE TABLE PORTLET-->
																<input type="hidden" id="base-url" value="<?php echo $GLOBALS['BASEURL']; ?>" />
																<div class="form-actions">
																				<input type="button" class="btn green snd-metadata-btn" id="json-val-but" value="VALIDATE JSON" disabled>
																			</div>
                        </div>
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

								<div class="modal fade bs-modal" id="modalJSONSchema" tabindex="-1" role="basic" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title">JSON Schema Validation</h4>
                            </div>
														<div class="modal-body"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

<?php 

require "../htmlib/footer.inc.php"; 
require "../htmlib/js.inc.php";

?>
