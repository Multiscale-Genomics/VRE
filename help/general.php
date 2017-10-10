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
                                  <span>General Information</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> General Information
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->

														<p>
															<div class="note note-info">
                                            <h4 class="block">
The MuG Virtual Research Environment supports the expanding 3D/4D genomics community by developing tools to integrate the navigation in genomics data from sequence to 3D/4D chromatin dynamics data.</p>
</h4>
                                        </div>

														<p><img src="assets/layouts/layout/img/help/VRE2.png" style="width:800px;max-width:100%;" /></p>

														<p>MuG develops tools and services fitted for community needs as well as the computing infrastructure necessary to support the Virtual Research Environment services.

<ul><li>Infrastructure provisioning: user accounts, user certificates and assignment of a gateway for access to the European data and computing infrastructure to enable simulations on distributed supercomputing resources.</li>
<li>End-user interfaces: interactive and programmatic interfaces will be provided</li>
<li>Interoperability, adopting standards implementation to ensure alignment with the activities on e-infrastructures.</li>
<li>Deployment, maintenance and support to guarantee the quality of the platform.</li>
<li>Documentation and training to ensure the modules of this VRE can be correctly used by users and developers.</li></p>

                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../htmlib/footer.inc.php"; 
require "../htmlib/js.inc.php";

?>
