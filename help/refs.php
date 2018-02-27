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
                                  <span>References</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> References
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->

												<div id="html-content-help">

												<ul>
													<li><a href="help/refs.php#2017">2017</a></li>
													<li><a href="help/refs.php#2016">2016</a></li>
													<li><a href="help/refs.php#2015">2015</a></li>
													<li><a href="help/refs.php#2014">2014</a></li>
													<li><a href="help/refs.php#2013">2013</a></li>
												</ul>

												<?php require "../help/inc/refs.php"; ?>

												</div>
				
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../htmlib/footer.inc.php"; 
require "../htmlib/js.inc.php";

?>
