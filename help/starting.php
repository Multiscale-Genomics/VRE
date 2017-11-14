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
                                  <span>Getting Started</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Getting Started
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->

												<div id="html-content-help">
												<ul>
													<li><a href="help/starting.php#interface">MuG VRE Interface</a></li>
													<li><a href="help/starting.php#registration">User Registration</a></li>
												</ul>

												<p><span id="interface">&nbsp;</span></p>

                        <h2>MuG VRE Interface</h2>

												<p><strong>MuG VRE</strong> holds a personal workspace where input and output files are stored.</p>

												<p>Data is structured in <strong>projects</strong>. A project is opened selecting one or several files from the workspace, independently if these files are part of the uploads / repository folders or outputs of previously executed projects.</p>
		
												<p><img src="assets/layouts/layout/img/help/starting01.png" style="width:800px;max-width:100%;" /></p>

												<p><span id="registration">&nbsp;</span></p>

												<h2>User Registration</h2>

												<p><strong>MuG VRE</strong> gives the possibility to work as a registered user.</p>

												<p>Users can register directly giving personal data or via <strong>OAuth2</strong> using Google or LinkedIn.</p>

												<p><img src="assets/layouts/layout/img/help/starting02.png" style="width:800px;max-width:100%;" /></p>

												<p>Once users are registered, MuG VRE provides the ability of change personal data.</p>

												<p><img src="assets/layouts/layout/img/help/starting03.png" style="width:800px;max-width:100%;" /></p>
												
												</div>
				
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../htmlib/footer.inc.php"; 
require "../htmlib/js.inc.php";

?>
