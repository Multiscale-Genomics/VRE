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
                                  <span>Workspace</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Workspace
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->

												<div id="html-content-help">

												<ul>
													<li><a href="help/ws.php#desc">General description</a></li>
													<li><a href="help/ws.php#list">List</a>
														<ul>
															<li><a href="help/ws.php#features">Features</a></li>
															<li><a href="help/ws.php#ordering">Ordering</a></li>
															<li><a href="help/ws.php#filtering">Filtering</a></li>
															<li><a href="help/ws.php#folders">Folders / Projects</a></li>
															<li><a href="help/ws.php#files">Files</a></li>
														</ul>
													</li>
													<li><a href="help/ws.php#manage">Manage Files</a></li>
													<li><a href="help/ws.php#help">Tools' Help</a></li>
													<li><a href="help/ws.php#jobs">Last Jobs</a></li>
													<li><a href="help/ws.php#disk">Disk Use</a></li>
												</ul>

												<?php require "../help/inc/ws.php"; ?>

												</div>

                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../htmlib/footer.inc.php"; 
require "../htmlib/js.inc.php";

?>
