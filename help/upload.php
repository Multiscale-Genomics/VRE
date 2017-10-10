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
                                  <span>Upload Data</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Upload Data
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->

												<div id="html-content-help">

												<ul>
													<li><a href="help/upload.php#step1">First Step: Upload Data</a>
														<ul>
															<li><a href="help/upload.php#files">Upload Files</a></li>
															<li><a href="help/upload.php#repository">From Repository</a></li>
															<li><a href="help/upload.php#id">From ID</a></li>
														</ul>
													</li>
													<li><a href="help/upload.php#step2">Second Step: Edit File Metadata</a></li>
												</ul>

												<p><span id="step1">&nbsp;</span></p>
	
												<h2>First Step: Upload Data</h2>

												<p><strong>MuG VRE</strong> provides three ways fot getting data:</p>

												<p><span id="files">&nbsp;</span></p>

												<h3>Upload Files</h3>

												<p><img src="assets/layouts/layout/img/help/upload00.png" style="width:800px;max-width:100%;" /></p>

												<p>Likewise, there are three ways of uploading files:</p>

												<h4> Upload files from your local computer </h4>

												<p><img src="assets/layouts/layout/img/help/upload01.png" style="width:800px;max-width:100%;" /></p>

												<p>To upload a file from the computer, users just have to drag and drop the files to the specified area or click on it.</p>

												<h4> Create new file from text </h4>

												<p><img src="assets/layouts/layout/img/help/upload02.png" style="width:800px;max-width:100%;" /></p>

												<p>To create a new file from text, users just have to insert the file name and the text data (i.g. a DNA sequence) and click the button <em>SEND DATA</em>.</p>

												<h4> Load file from an external URL </h4>

												<p><img src="assets/layouts/layout/img/help/upload03.png" style="width:800px;max-width:100%;" /></p>

												<p>To load a file from an external URL, users just have to insert the URL in the input file and click the button <em>SEND DATA</em></p>

												<p><span id="repository">&nbsp;</span></p>

												<h3>From Repository</h3>

												<p><img src="assets/layouts/layout/img/help/upload04.png" style="width:800px;max-width:100%;" /></p>

												<p><strong>MuG VRE</strong> provides the users with a repository with thousands of experiments ready to load to the Workspace.</p>

												<p><img src="assets/layouts/layout/img/help/upload05.png" style="width:800px;max-width:100%;" /></p>

												<p>Clicking on any of the experiments of the list, users access the experiment detail. Here, just clicking the button 
												<img src="assets/layouts/layout/img/help/upload06.png" /> the experiment is automatically loaded to the user's <em>Repository</em> folder of the Workspace. 
												As the uploading process of this kind of files is asyncron because of the huge size of some of them, users are redirected directly to the Workspace 
												instead of the second step of the process (File Metadata Edition)</p>

												<p><span id="id">&nbsp;</span></p>

												<h3>From ID</h3>

												<p><img src="assets/layouts/layout/img/help/upload07.png" style="width:800px;max-width:100%;" /></p>

												<p><strong>MuG VRE</strong> provides the users the feature of loading files directly inserting the ID of a Data Bank. Currently the <strong>MuG VRE</strong>
												just implements the Protein Data Bank (PDB) option. Users just have to start writting the first characters of the code ID and the system will autocomplete the code.</p>

												<p><span id="step2">&nbsp;</span></p>

												<h2>Second Step: Edit File Metadata</h2>

												<p>After uploading a file in some of the several ways explained above, the <strong>MuG VRE</strong> will redirect the users to the Edit File Metadata
												page, the second step of the Getting Data process.</p>

												<p><img src="assets/layouts/layout/img/help/upload08.png" style="width:800px;max-width:100%;" /></p>

												<p>Once the file is uploaded, users must fill in the metadata of the file:</p>

												<ul>
													<li>File Format: Format of the specified file.</li>
													<li>Data Type: Type of data of the specified file.</li>
													<li>Taxon: Taxon Name or ID of the specified file.</li>
													<li>Assembly: Assembly of the specified file.</li>
													<li>Assembly: Description for the metadata of the specified file (optional).</li>
												</ul>

												<p><img src="assets/layouts/layout/img/help/upload09.png" style="width:800px;max-width:100%;" /></p>

												<p>After clicking the button <em>SEND METADATA</em> there are three possible answers:</p>

												<h4>VALIDATED</h4>

												<p>File has successfully passed the meta-data checking as well as the file processing procedure that ensures the resource can be visualized and used as input for tools' platform. This file processing varies depending on the file format, but includes format validation, genomic region naming checking, sorting and indexing.</p>

												<p><img src="assets/layouts/layout/img/help/upload10.png" style="width:800px;max-width:100%;" /></p>

												<h4>READY</h4>

												<p>File has successfully passed the meta-data checking, but still, some file processing is required in order to obtain a validated file. Changes to be carried out are listed and ready to by applied as soon as the user accepts them.</p>

												<p><img src="assets/layouts/layout/img/help/upload11.png" style="width:800px;max-width:100%;" /></p>

												<h4>ERROR</h4>

												<p>File may have passed the meta-data checking, but not the file processing, meaning that the user needs to manually amend the file and upload it once corrected. Meanwhile, the file will be listed in the user workspace, but not eligible as input for tools nor visualizers.</p>

												<p><img src="assets/layouts/layout/img/help/upload12.png" style="width:800px;max-width:100%;" /></p>

												<p>A non-validated file can be edited clicking the button <img src="assets/layouts/layout/img/help/upload13.png" /> in the Workspace:</p>

												<p><img src="assets/layouts/layout/img/help/upload14.png" style="width:800px;max-width:100%;" /></p>

												<p>Once a file is validated, it will be displayed as follows:</p>

												<p><img src="assets/layouts/layout/img/help/upload15.png" style="max-width:100%;" /></p>

												<p>At any time, users will be able to modify the metadata of the file:</p>

												<p><img src="assets/layouts/layout/img/help/upload16.png" style="width:800px;max-width:100%;" /></p>

												</div>
				
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<?php 

require "../htmlib/footer.inc.php"; 
require "../htmlib/js.inc.php";

?>
