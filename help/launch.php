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
                                  <span>Launch Job</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Launch Job
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->

												<div id="html-content-help">
												<ul>
													<li><a href="help/launch.php#ws">Launch job from Workspace</a>
														<ul>
															<li><a href="help/launch.php#selectfiles">Select files from Workspace</a></li>
															<li><a href="help/launch.php#inputform">Tools input form</a></li>
															<li><a href="help/launch.php#after">After launching job</a></li>
															<li><a href="help/launch.php#finish">Finished execution</a></li>
															<li><a href="help/launch.php#viewresults">View Results</a></li>
														</ul>
													</li>
													<li><a href="help/launch.php#tool">Launch job from tool</a></li>
												</ul>

												<p><span id="ws">&nbsp;</span></p>

												<h2>Launch job from Workspace</h2>

												<p><span id="selectfiles">&nbsp;</span></p>

												<h3>Select files from Workspace</h3>

												<p>The process of launching a job from the Workspace starts when users select the files they want to launch:</p>

												<p><img src="assets/layouts/layout/img/help/launch01.png" style="max-width:100%;" /></p>

												<p>Once the desired files are selected, users should go to the <em>Manage Files</em> block.</p>

												<p><img src="assets/layouts/layout/img/help/launch02.png" style="width:800px;max-width:100%;" /></p>

												<p>Clicking the <em>Available Tools</em> button, users should select the tool to execute, in this example pyDockDNA: </p>

												<p><img src="assets/layouts/layout/img/help/launch03.png" style="max-width:100%;" /></p>

												<p><span id="inputform">&nbsp;</span></p>

												<h3>Tools input form</h3>

												<p>After clicking the tools button, <strong>MuG VRE</strong> will redirect users to the input form:</p> 

												<p><img src="assets/layouts/layout/img/help/launch04.png" style="width:800px;max-width:100%;" /></p>

												<p>Although all the tools input form are different and customized for every tool, the main structure is similar:</p>

												<h4>Inputs</h4>

												<p><img src="assets/layouts/layout/img/help/launchxx02.png" style="width:800px;max-width:100%;" /></p>

												<p>This block is common for all the tools and consists of a list of the files selected in the Workspace. In some cases the files can be previsualized
												clicking the preview button <img src="assets/layouts/layout/img/help/launchxx01.png" />. In the example tool, the <strong>MuG VRE</strong> will open a 
												modal window with a 3D visualizer:</p>

												<p><img src="assets/layouts/layout/img/help/launch05.png" style="max-width:100%;" /></p>

												<h4>Projects</h4>

												<p><img src="assets/layouts/layout/img/help/launchxx03.png" style="width:800px;max-width:100%;" /></p>

												<p>This block is common for all the tools and consists of a couple of input text boxes where users must provide the name and the description for the job.</p>

												<h4>Tools settings</h4>

												<p><img src="assets/layouts/layout/img/help/launchxx04.png" style="width:800px;max-width:100%;" /></p>

												<p>This block is different for every tool. It can be just one block or several of them depending on the tool. In this block, users must fill in the arguments in order 
												to launch the job properly. Once all the required data is filled in, users must click the Compute button <img src="assets/layouts/layout/img/help/launch06.png" />
												and they will be redirected to the Workspace.</p>

												<p><span id="after">&nbsp;</span></p>
	
												<h3>After launching job</h3>

												<p>After launching the job, users will be redirected to the Workspace and they will found the job running in the Workspace:</p>

												<p><img src="assets/layouts/layout/img/help/launch07.png" style="width:800px;max-width:100%;" /></p>

												<p>The job can be cancelled clicking the <em>Cancel Jobs</em> button:</p>
		
												<p><img src="assets/layouts/layout/img/help/launch08.png" style="max-width:100%;" /></p>

												<p>Depending on the tool, the job can be in running state between a few minutes and a few days. As the Workspace's size can increase dramatically, <strong>MuG VRE</strong>
												provides the <em>Last Jobs</em> block where users can quickly take a look to the jobs state:</p>

												<p><img src="assets/layouts/layout/img/help/launch09.png" style="max-width:100%;" /></p>

												<p><span id="finish">&nbsp;</span></p>
	
												<h3>Finished execution</h3>

												<p>Once the execution has finished, the Workspace shows the results to the users in a form of a list of files inside the job folder:</p>

												<p><img src="assets/layouts/layout/img/help/launch10.png" style="width:800px;max-width:100%;" /></p>

												<p>Most of the tools have a customized <em>View Results</em> page, which is used to show the users the results of the tool properly. To view this special page
												users should click the <em>View Results</em> button:</p>

												<p><img src="assets/layouts/layout/img/help/launch11.png" style="max-width:100%;" /></p>

												<p><span id="viewresults">&nbsp;</span></p>
	
												<h3>View Results</h3>

												<p>Custom results page for the example tool pyDockDNA:</p>

												<p><img src="assets/layouts/layout/img/help/launch12.png" style="width:800px;max-width:100%;" /></p>

												<p><span id="tool">&nbsp;</span></p>

												<h2>Launch job from tool</h2>

												<p>This feature will be implemented shortly.</p>

												</div>
				
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../htmlib/footer.inc.php"; 
require "../htmlib/js.inc.php";

?>
