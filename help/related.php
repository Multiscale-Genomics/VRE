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
                                  <span>Related Links</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Related Links
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->

												<div id="html-content-help">

													<div class="list-group">
														<a href="http://www.multiscalegenomics.eu/MuGVRE/modules/BigNASimMuG/" target="_blank" class="list-group-item list-group-item-action">BigNASim</a>
														<a href="http://mmb.irbbarcelona.org/NucleosomeDynamics/" target="_blank" class="list-group-item list-group-item-action">Nucleosome Dynamics</a>
														<a href="http://www.multiscalegenomics.eu/MuGVRE/flexibility-browser/" target="_blank" class="list-group-item list-group-item-action">Flexibility Browser</a>
														<a href="http://www.multiscalegenomics.eu/MuGVRE/modules/ConnectivityBrowser/" target="_blank" class="list-group-item list-group-item-action">MuG Information Network</a>
													</div>
		
												</div>
				
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../htmlib/footer.inc.php"; 
require "../htmlib/js.inc.php";

?>
