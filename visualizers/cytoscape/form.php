<?php

require "../../phplib/genlibraries.php";
redirectOutside();

?>

<?php require "../../htmlib/header.inc.php"; ?>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-container-bg-solid page-sidebar-fixed">
  <div class="page-wrapper">

	<?php require "../../htmlib/top.inc.php"; ?>
	<?php require "../../htmlib/nomenu.inc.php"; ?>

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
                                  <span>Visualizers</span>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>Cytoscape</span>
                              </li> 
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Form that does something 
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->

												<p>You must fill this field because it's very important</p>

												<form name="" id="" action="" method="post">

													<div class="row">
												
														<div class="col-md-6">	
															<div class="form-group " id="">
																<label>Amazing field</label>
																<input type="text" maxlength="" name="" id="" class="form-control" placeholder="Placeholder">
															</div>
														</div>

													</div>

													<div class="form-actions btn-send-data">
				  									<input type="submit" class="btn green snd-metadata-btn" id="" value="SUBMIT">
				  								</div>

												</form>
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../../htmlib/footer.inc.php"; 
require "../../htmlib/js.inc.php";

?>
