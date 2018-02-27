<?php

require "../phplib/genlibraries.php";
redirectOutside();

?>

<?php require "../htmlib/header.inc.php"; ?>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-container-bg-solid page-sidebar-fixed">
  <div class="page-wrapper">

  <?php require "../htmlib/top.inc.php"; ?>
  <?php require "../htmlib/menu.inc.php"; ?>

<!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content" id="body-help">
                        <!-- BEGIN PAGE HEADER-->
                        <!-- BEGIN PAGE BAR -->
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
															<li>
																<a href="/home/">Home</a>
																<i class="fa fa-circle"></i>
															</li>
                              <li>
                                  <span>Help</span>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>Get Data</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Get Data
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->

												<div id="html-content-help">

												<ul>
													<li><a href="help/upload.php#step1">First Step: Upload Data</a>
														<ul>
															<li><a href="help/upload.php#files">Upload Files</a></li>
															<li><a href="help/upload.php#repository">From Repository</a></li>
															<li><a href="help/upload.php#sample">Import Sample Data</a></li>
														</ul>
													</li>
													<li><a href="help/upload.php#step2">Second Step: Edit File Metadata</a></li>
												</ul>

												<?php require "../help/inc/upload.php"; ?>
												

												</div>
				
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../htmlib/footer.inc.php"; 
require "../htmlib/js.inc.php";

?>
