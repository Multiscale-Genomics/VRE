<?php

require "phplib/genlibraries.php";
redirectOutside();
?>

<?php require "htmlib/header.inc.php"; ?>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-container-bg-solid">
  <div class="page-wrapper">

  <?php require "htmlib/top.inc.php"; ?>
  <?php require "htmlib/menu.inc.php"; ?>

  <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        <!-- BEGIN PAGE HEADER-->
                        <!-- BEGIN PAGE BAR -->
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                              <li>
                                  <span>User Workspace</span>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>Data Table</span>
                              </li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> User Workspace
                            <small>manage data through the data table</small>
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->

                        <div class="row">
                            <div class="col-md-12">
								<p style="margin-top:0;">
                                	If you want apply a Tool to a file, please select it from the dropdown menu on the Tools column. If you need to apply a Tool
									to more than one file, check the selected files and they will be loaded in the <i>Run Tools</i> list at the bottom of the table.
								</p>
								<?php if($_SESSION['User']['Type'] == 100) { ?>
								<div class="alert alert-warning">
                                  Your request for a premium user account is being processed. In the meantime, you can use the platform as a common user.
								</div>	
								<?php }else if($_SESSION['User']['Type'] == 3) { ?>
								<div class="alert alert-info">
                                  As a guest user you have reduced functionalities in the platform.
                                </div>
								<?php } ?>
                                <!-- BEGIN EXAMPLE TABLE PORTLET -->
                                <div class="portlet">
                                    <div class="portlet-body">
									  <form name="gesdir" action="datamanager/workspace.php" method="post" enctype="multipart/form-data">
                                      <table id="workspace" class="display" cellspacing="0" width="100%">
                                          <thead>
                                              <tr class="heading">
                                                  <th>
                                                    <a href="home.php" style="float:left;text-decoration:none;" title="refresh table">
                                                      <span class="refresh-table"></span>
                                                    </a>
                                                  </th>
                                                  <th>File
                                                    <div id="mock_button1" class="mock_button"></div>
                                                  </th>
                                                  <th>Format
                                                    <div id="mock_button2" class="mock_button"></div>
                                                  </th>
                                                  <th>Project
                                                    <div id="mock_button3" class="mock_button"></div>
                                                  </th>
                                                  <th>Date
                                                    <div id="mock_button4" class="mock_button"></div>
                                                  </th>
                                                  <th>Size
                                                    <div id="mock_button5" class="mock_button"></div>
                                                  </th>
                                                  <th>Tools</th>
                                                  <th>Actions</th>
                                                  <!-- COLUMNES AUXILIARS D'ORDENACIÓ -->
                                                  <th>OrderNAME</th>
                                                  <th>OrderDATE</th>
                                                  <th>OrderSIZE</th>
                                              </tr>
                                              <tr id="headerSearch">
                                            			<th style="background-color: #eee;padding:3px;">
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline" style="margin-left:7px;">
                                                        <input type="checkbox" class="group-checkable" data-set="#workspace .checkboxes" />
                                                        <span style="background-color:#fff;"></span>
                                                    </label>
                                                  </th>
                                            			<th style="background-color: #eee;padding:3px;" class="inputSearch">File</th>
                                            			<th style="background-color: #eee;padding:3px;" class="selector">Format</th>
                                            			<th style="background-color: #eee;padding:3px;" class="selector">Project</th>
                                            			<th style="background-color: #eee;"></th>
                                            			<th style="background-color: #eee;"></th>
                                            			<th style="background-color: #eee;"></th>
                                            			<th style="background-color: #eee;">
                                            				<a class="clearState" data-column="0" style="text-decoration:none;margin-left:-8px">
                                                      <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                        <i class="fa fa-times-circle"></i> Clear filters
                                                      </button>
                                            				</a>
                                            			</th>
                                            	</tr>
                                          </thead>

                                          <tbody>
                                              <tr data-tt-id="1">
                                                <td>
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes foldercheck" value="1" />
                                                        <span></span>
                                                    </label>
                                                </td>
                                                  <td>uploads</td>
                                                  <td>&nbsp;</td>
                                                  <td>uploads</td>
                                                  <td>2016/05/31 13:30</td>
                                                  <td>165.1 M</td>
                                                  <td>&nbsp;</td>
                                                  <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-trash-o"></i> Delete folder </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-file-zip-o"></i> Compress</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                  </td>
                                                  <td>-1000</td>
                                                  <td>2</td>
                                                  <td>3</td>
                                              </tr>
                                              <tr data-tt-id="1.1" data-tt-parent-id="1">
                                                <td>
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes" value="1" onchange="changeCheckbox(this)" />
                                                        <span></span>
                                                    </label>
                                                </td>
                                                  <td>V126.CpG_calls.bw <span class="expand_info" onclick="expandInfo(this)"></span>
													  <div class="extra_info">
														<table>
	  														<tr>
																<td>Description:</td>
																<td>BAM file for DNA methylation study</td>
		  													</tr>
		  													<tr>
																<td>Reference genome:</td>
																<td>R64-1-1</td>
															</tr>
															<tr>
																<td>BAM properties:</td>
																<td>paired sorted</td>
		  													</tr>
														 </table>
                                                    	</div>
                                                  </td>
                                                  <td>BW</td>
                                                  <td>uploads</td>
                                                  <td>2016/05/31 13:30</td>
                                                  <td>617.71 K</td>
												  <td>
													<div class="btn-group">
                                                        <button class="btn btn-xs blue-oleo dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Tools
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-motorcycle"></i> Nucleosome Dynamics</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-database"></i> BigNASim</a>
															</li>
															<li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-puzzle-piece"></i> Protein-DNA docking</a>
                                                            </li>
                                                        </ul>
                                                    </div>
												  </td>
                                                  <td>
                                                    <div class="btn-group">
                                                          <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                              <i class="fa fa-angle-down"></i>
                                                          </button>
                                                          <ul class="dropdown-menu pull-center" role="menu">
                                                              <li>
                                                                  <a href="javascript:;">
                                                                      <i class="fa fa-pencil"></i> Edit file metadata </a>
                                                              </li>
                                                              <li>
                                                                  <a href="javascript:;">
                                                                      <i class="fa fa-download"></i> Download file </a>
                                                              </li>
                                                              <li>
                                                                  <a href="javascript:;">
                                                                      <i class="fa fa-trash-o"></i> Delete file </a>
                                                              </li>
                                                              <li>
                                                                  <a href="javascript:;">
                                                                      <i class="fa fa-file-zip-o"></i> Compress</a>
                                                              </li>
                                                          </ul>
                                                      </div>
                                                    </td>

                                                  <td>1</td>
                                                  <td>2</td>
                                                  <td>3</td>
                                              </tr>
                                              <tr data-tt-id="1.2" data-tt-parent-id="1">
                                                <td>
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes" value="1" onchange="changeCheckbox(this)" />
                                                        <span></span>
                                                    </label>
                                                </td>
                                                  <td>PP_120502_34.bam.log</td>
                                                  <td>BAM</td>
                                                  <td>uploads</td>
                                                  <td>2016/04/27 21:31</td>
                                                  <td>3.37 K</td>
                                                  <td>
													<div class="btn-group">
                                                        <button class="btn btn-xs blue-oleo dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Tools
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-motorcycle"></i> Nucleosome Dynamics</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-database"></i> BigNASim</a>
                                                            </li>
															<li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-puzzle-piece"></i> Protein-DNA docking</a>
                                                            </li>

                                                        </ul>
                                                    </div>
												  </td>
                                                  <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-pencil"></i> Edit file metadata </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-download"></i> Download file </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-trash-o"></i> Delete file </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-file-zip-o"></i> Compress</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                  </td>
                                                  <td>1</td>
                                                  <td>2</td>
                                                  <td>3</td>
                                              </tr>
                                              <tr data-tt-id="1.3" data-tt-parent-id="1">
                                                <td>
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes" value="1" onchange="changeCheckbox(this)" />
                                                        <span></span>
                                                    </label>
                                                </td>
                                                  <td>PP_120502_SN365_B_L002_GGM-34.bam.log</td>
                                                  <td>LOG</td>
                                                  <td>uploads</td>
                                                  <td>2016/04/26 16:55</td>
                                                  <td>4.01 K</td>
                                                  <td>
													<div class="btn-group">
                                                        <button class="btn btn-xs blue-oleo dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Tools
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-motorcycle"></i> Nucleosome Dynamics</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-database"></i> BigNASim</a>
                                                            </li>
															<li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-puzzle-piece"></i> Protein-DNA docking</a>
                                                            </li>

                                                        </ul>
                                                    </div>
												  </td>
                                                  <td><div class="btn-group">
                                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-pencil"></i> Edit file metadata </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-download"></i> Download file </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-trash-o"></i> Delete file </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-file-zip-o"></i> Compress</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                  </td>
                                                  <td>1</td>
                                                  <td>2</td>
                                                  <td>3</td>
                                              </tr>
                                              <tr data-tt-id="1.4" data-tt-parent-id="1">
                                                <td>
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes" value="1" onchange="changeCheckbox(this)" />
                                                        <span></span>
                                                    </label>
                                                </td>
                                                  <td>120502_35.bam</td>
                                                  <td>BAM</td>
                                                  <td>uploads</td>
                                                  <td>2016/04/05 09:55</td>
                                                  <td>74.34 M</td>
                                                  <td>
													<div class="btn-group">
                                                        <button class="btn btn-xs blue-oleo dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Tools
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-motorcycle"></i> Nucleosome Dynamics</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-database"></i> BigNASim</a>
                                                            </li>
															<li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-puzzle-piece"></i> Protein-DNA docking</a>
                                                            </li>

                                                        </ul>
                                                    </div>
												  </td>
                                                  <td>
                                                    <div class="btn-group">
                                                      <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                          <i class="fa fa-angle-down"></i>
                                                      </button>
                                                      <ul class="dropdown-menu pull-center" role="menu">
                                                          <li>
                                                              <a href="javascript:;">
                                                                  <i class="fa fa-pencil"></i> Edit file metadata </a>
                                                          </li>
                                                          <li>
                                                              <a href="javascript:;">
                                                                  <i class="fa fa-download"></i> Download file </a>
                                                          </li>
                                                          <li>
                                                              <a href="javascript:;">
                                                                  <i class="fa fa-trash-o"></i> Delete file </a>
                                                          </li>
                                                          <li>
                                                              <a href="javascript:;">
                                                                  <i class="fa fa-file-zip-o"></i> Compress</a>
                                                          </li>
                                                      </ul>
                                                    </div>
                                                  </td>
                                                  <td>1</td>
                                                  <td>2</td>
                                                  <td>3</td>
                                              </tr>
                                              <tr data-tt-id="1.5" data-tt-parent-id="1">
                                                <td>
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes" value="1" onchange="changeCheckbox(this)" />
                                                        <span></span>
                                                    </label>
                                                </td>
                                                  <td>120502_SN365_B_L002_GGM-35_sort_PART.bam.gz</td>
                                                  <td>BAM</td>
                                                  <td>uploads</td>
                                                  <td>2016/02/10 12:11</td>
                                                  <td>75.73 K</td>
                                                  <td>
												    <div class="btn-group">
                                                        <button class="btn btn-xs blue-oleo dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Tools
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-motorcycle"></i> Nucleosome Dynamics</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-database"></i> BigNASim</a>
                                                            </li>
															<li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-puzzle-piece"></i> Protein-DNA docking</a>
                                                            </li>

                                                        </ul>
                                                    </div>
												  </td>
                                                  <td>
                                                    <div class="btn-group">
                                                      <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                          <i class="fa fa-angle-down"></i>
                                                      </button>
                                                      <ul class="dropdown-menu pull-center" role="menu">
                                                          <li>
                                                              <a href="javascript:;">
                                                                  <i class="fa fa-pencil"></i> Edit file metadata </a>
                                                          </li>
                                                          <li>
                                                              <a href="javascript:;">
                                                                  <i class="fa fa-download"></i> Download file </a>
                                                          </li>
                                                          <li>
                                                              <a href="javascript:;">
                                                                  <i class="fa fa-trash-o"></i> Delete file </a>
                                                          </li>
                                                          <li>
                                                              <a href="javascript:;">
                                                                  <i class="fa fa-file-zip-o"></i> Compress</a>
                                                          </li>
                                                      </ul>
                                                    </div>
                                                  </td>
                                                  <td>1</td>
                                                  <td>2</td>
                                                  <td>3</td>
                                              </tr>
                                              <tr data-tt-id="1.6" data-tt-parent-id="1">
                                                <td>
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes" value="1" onchange="changeCheckbox(this)" />
                                                        <span></span>
                                                    </label>
                                                </td>
                                                  <td>120502_SN365_B_L002_GGM-35_sort_PART.bam</td>
                                                  <td>BAM</td>
                                                  <td>uploads</td>
                                                  <td>2016/02/10 12:11</td>
                                                  <td>75.73 K</td>
                                                  <td>
												 	<div class="btn-group">
                                                        <button class="btn btn-xs blue-oleo dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Tools
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-motorcycle"></i> Nucleosome Dynamics</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-database"></i> BigNASim</a>
                                                            </li>
															<li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-puzzle-piece"></i> Protein-DNA docking</a>
                                                            </li>

                                                        </ul>
                                                    </div>
												  </td>
                                                  <td>
                                                    <div class="btn-group">
                                                      <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                          <i class="fa fa-angle-down"></i>
                                                      </button>
                                                      <ul class="dropdown-menu pull-center" role="menu">
                                                          <li>
                                                              <a href="javascript:;">
                                                                  <i class="fa fa-pencil"></i> Edit file metadata </a>
                                                          </li>
                                                          <li>
                                                              <a href="javascript:;">
                                                                  <i class="fa fa-download"></i> Download file </a>
                                                          </li>
                                                          <li>
                                                              <a href="javascript:;">
                                                                  <i class="fa fa-trash-o"></i> Delete file </a>
                                                          </li>
                                                          <li>
                                                              <a href="javascript:;">
                                                                  <i class="fa fa-file-zip-o"></i> Compress</a>
                                                          </li>
                                                      </ul>
                                                    </div>
                                                  </td>
                                                  <td>1</td>
                                                  <td>2</td>
                                                  <td>3</td>
                                              </tr>
                                              <tr data-tt-id="1.7" data-tt-parent-id="1">
                                                <td>
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes" value="1" onchange="changeCheckbox(this)" />
                                                        <span></span>
                                                    </label>
                                                </td>
                                                  <td>120502_SN365_B_L002_GGM-35_sort_PART.bam</td>
                                                  <td>BAM</td>
                                                  <td>uploads</td>
                                                  <td>2016/04/22 15:20</td>
                                                  <td>75.65 K</td>
                                                  <td>
												    <div class="btn-group">
                                                        <button class="btn btn-xs blue-oleo dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Tools
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-motorcycle"></i> Nucleosome Dynamics</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-database"></i> BigNASim</a>
                                                            </li>
															<li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-puzzle-piece"></i> Protein-DNA docking</a>
                                                            </li>

                                                        </ul>
                                                    </div>
												  </td>
                                                  <td>
                                                    <div class="btn-group">
                                                          <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                              <i class="fa fa-angle-down"></i>
                                                          </button>
                                                          <ul class="dropdown-menu pull-center" role="menu">
                                                              <li>
                                                                  <a href="javascript:;">
                                                                      <i class="fa fa-pencil"></i> Edit file metadata </a>
                                                              </li>
                                                              <li>
                                                                  <a href="javascript:;">
                                                                      <i class="fa fa-download"></i> Download file </a>
                                                              </li>
                                                              <li>
                                                                  <a href="javascript:;">
                                                                      <i class="fa fa-trash-o"></i> Delete file </a>
                                                              </li>
                                                              <li>
                                                                  <a href="javascript:;">
                                                                      <i class="fa fa-file-zip-o"></i> Compress</a>
                                                              </li>
                                                          </ul>
                                                      </div>
                                                  </td>
                                                  <td>1</td>
                                                  <td>2</td>
                                                  <td>3</td>
                                              </tr>
                                              <tr data-tt-id="1.8" data-tt-parent-id="1">
                                                <td>
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes" value="1" onchange="changeCheckbox(this)" />
                                                        <span></span>
                                                    </label>
                                                </td>
                                                  <td>120502_34.bam</td>
                                                  <td>BAM</td>
                                                  <td>uploads</td>
                                                  <td>2016/02/10 12:11</td>
                                                  <td>90.01 M</td>
                                                  <td>
												    <div class="btn-group">
                                                        <button class="btn btn-xs blue-oleo dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Tools
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-motorcycle"></i> Nucleosome Dynamics</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-database"></i> BigNASim</a>
                                                            </li>
															<li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-puzzle-piece"></i> Protein-DNA docking</a>
                                                            </li>

                                                        </ul>
                                                    </div>
												  </td>
                                                  <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-pencil"></i> Edit file metadata </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-download"></i> Download file </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-trash-o"></i> Delete file </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-file-zip-o"></i> Compress</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                  </td>
                                                  <td>1</td>
                                                  <td>2</td>
                                                  <td>3</td>
                                              </tr>
                                              <tr data-tt-id="2">
                                                <td>
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes foldercheck" value="1" />
                                                        <span></span>
                                                    </label>
                                                </td>
                                                  <td>run001</td>
                                                  <td>&nbsp;</td>
                                                  <td>run001</td>
                                                  <td>2017/05/31 13:30</td>
                                                  <td>10.47 M</td>
                                                  <td>&nbsp;</td>
                                                  <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">

                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-trash-o"></i> Delete folder </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-file-zip-o"></i> Compress</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                  </td>
                                                  <td>-1000</td>
                                                  <td>3</td>
                                                  <td>1</td>
                                              </tr>
                                              <tr data-tt-id="2.1" data-tt-parent-id="2">
                                                <td>
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes" value="1" onchange="changeCheckbox(this)" />
                                                        <span></span>
                                                    </label>
                                                </td>
                                                  <td>ND_120502_35-120502M_34.gff</td>
                                                  <td>GFF</td>
                                                  <td>run001</td>
                                                  <td>2017/04/27 15:16</td>
                                                  <td>9.64 M</td>
                                                  <td>
													<div class="btn-group">
                                                        <button class="btn btn-xs blue-oleo dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Tools
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-motorcycle"></i> Nucleosome Dynamics</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-database"></i> BigNASim</a>
                                                            </li>
															<li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-puzzle-piece"></i> Protein-DNA docking</a>
                                                            </li>

                                                        </ul>
                                                    </div>
												  </td>
                                                  <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-pencil"></i> Edit file metadata </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-download"></i> Download file </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-trash-o"></i> Delete file </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-file-zip-o"></i> Compress</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                  </td>
                                                  <td>1</td>
                                                  <td>3</td>
                                                  <td>1</td>
                                              </tr>
                                              <tr data-tt-id="2.2" data-tt-parent-id="2">
                                                <td>
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes" value="1" onchange="changeCheckbox(this)" />
                                                        <span></span>
                                                    </label>
                                                </td>
                                                  <td>P_120502M_34.gff<span class="expand_info" onclick="expandInfo(this)"></span>
													  <div class="extra_info">
														<table>
	  														<tr>
																<td>Description:</td>
																<td>BAM file for DNA methylation study</td>
		  													</tr>
		  													<tr>
																<td>Reference genome:</td>
																<td>R64-1-1</td>
															</tr>
															<tr>
																<td>BAM properties:</td>
																<td>paired sorted</td>
		  													</tr>
														 </table>
                                                    	</div>
</td>
                                                  <td>GFF</td>
                                                  <td>run001</td>
                                                  <td>2017/04/27 09:43</td>
                                                  <td>831.09 K</td>
                                                  <td>
													<div class="btn-group">
                                                        <button class="btn btn-xs blue-oleo dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Tools
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-motorcycle"></i> Nucleosome Dynamics</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-database"></i> BigNASim</a>
                                                            </li>
															<li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-puzzle-piece"></i> Protein-DNA docking</a>
                                                            </li>

                                                        </ul>
                                                    </div>
												  </td>
                                                  <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-pencil"></i> Edit file metadata </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-download"></i> Download file </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-trash-o"></i> Delete file </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-file-zip-o"></i> Compress</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                  </td>
                                                  <td>1</td>
                                                  <td>3</td>
                                                  <td>1</td>
                                              </tr>
                                              <tr data-tt-id="3">
                                                <td>
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes foldercheck" value="1" />
                                                        <span></span>
                                                    </label>
                                                </td>
                                                  <td>cellcycle</td>
                                                  <td>&nbsp;</td>
                                                  <td>cellcycle</td>
                                                  <td>2015/05/31 13:30</td>
                                                  <td>14.47 M</td>
                                                  <td>&nbsp;</td>
                                                  <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-trash-o"></i> Delete folder </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-file-zip-o"></i> Compress</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                  </td>
                                                  <td>-1000</td>
                                                  <td>1</td>
                                                  <td>2</td>
                                              </tr>
                                              <tr data-tt-id="3.1" data-tt-parent-id="3">
                                                <td>
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes" value="1" onchange="changeCheckbox(this)" />
                                                        <span></span>
                                                    </label>
                                                </td>
                                                  <td>A_120502_35-120502M_34.gff</td>
                                                  <td>ZIP</td>
                                                  <td>cellcycle</td>
                                                  <td>2015/04/27 15:16</td>
                                                  <td>13.64 M</td>
                                                  <td>
													<div class="btn-group">
                                                        <button class="btn btn-xs blue-oleo dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Tools
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:runTool(1, 1, 'A_120502_35-120502M_34.gff');">
                                                                    <i class="fa fa-motorcycle"></i> Nucleosome Dynamics</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:runTool(2, 1, 'A_120502_35-120502M_34.gff');">
                                                                    <i class="fa fa-database"></i> BigNASim</a>
                                                            </li>
															<li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-puzzle-piece"></i> Protein-DNA docking</a>
                                                            </li>

                                                        </ul>
                                                    </div>
												  </td>
                                                  <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-pencil"></i> Edit file metadata </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-download"></i> Download file </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-trash-o"></i> Delete file </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-file-zip-o"></i> Compress</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                  </td>
                                                  <td>1</td>
                                                  <td>1</td>
                                                  <td>2</td>
                                              </tr>
                                              <tr data-tt-id="3.2" data-tt-parent-id="3">
                                                <td>
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes" value="1" onchange="changeCheckbox(this)" />
                                                        <span></span>
                                                    </label>
                                                </td>
												  <td>Z_120502M_34.gff <span class="expand_info" onclick="expandInfo(this)"></span>
													  <div class="extra_info">
														<table>
	  														<tr>
																<td>Description:</td>
																<td>BAM file for DNA methylation study</td>
		  													</tr>
		  													<tr>
																<td>Reference genome:</td>
																<td>R64-1-1</td>
															</tr>
															<tr>
																<td>BAM properties:</td>
																<td>paired sorted</td>
		  													</tr>
														 </table>
                                                    	</div>
												  </td>
                                                  <td>GFF</td>
                                                  <td>cellcycle</td>
                                                  <td>2015/04/27 09:43</td>
                                                  <td>831.09 K</td>
                                                  <td>
													<div class="btn-group">
                                                        <button class="btn btn-xs blue-oleo dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Tools
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-motorcycle"></i> Nucleosome Dynamics</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-database"></i> BigNASim</a>
                                                            </li>
															<li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-puzzle-piece"></i> Protein-DNA docking</a>
                                                            </li>

                                                        </ul>
                                                    </div>
												  </td>
                                                  <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-center" role="menu">
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-pencil"></i> Edit file metadata </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-download"></i> Download file </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-trash-o"></i> Delete file </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-file-zip-o"></i> Compress</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                  </td>
                                                  <td>1</td>
                                                  <td>1</td>
                                                  <td>2</td>
                                              </tr>
                                          </tbody>
                                      </table>
									  </form>
										<!--<button class="btn green" type="submit" id="btn-run-files" style="margin-top:20px;" >Run Selected Files</button>-->
                                    </div>
                                </div>
                                <!-- END EXAMPLE TABLE PORTLET-->
                            </div>
						</div>

						<div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="icon-share font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Run Tools</span>
                                        </div>
                                        <div class="actions" style="display:none!important;" id="btn-av-tools">
                                            <div class="btn-group">
												<a class="btn btn-sm green" href="javascript:;" data-toggle="dropdown">
                                                	<i class="fa fa-wrench"></i> Available Tools
                                                    <i class="fa fa-angle-down"></i>
                                                </a>	
                                                <ul class="dropdown-menu pull-right" role="menu">
                                                	<li>
                                                    	<a href="javascript:;"><i class="fa fa-motorcycle"></i> Nucleosome Dynamics</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;"><i class="fa fa-database"></i> BigNASim</a>
                                                    </li>
													<li>
                                                                <a href="javascript:;">
                                                                    <i class="fa fa-puzzle-piece"></i> Protein-DNA docking</a>
                                                            </li>

                                                 </u>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="" data-always-visible="1" data-rail-visible="0">
                                            <ul class="feeds" id="list-files-run-tools"></ul>
											<div id="desc-run-tools">In order to run the tools on the files, please select them clicking on the checkboxes from the table above.</div>
                                        </div>
                                        <div class="scroller-footer">
											<a class="btn btn-sm red pull-right display-hide" id="btn-rmv-all"  href="javascript:;">
                                               	<i class="fa fa-trash"></i> Remove all files
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
						</div>

						<!-- SUMMARY AND DISK QUOTA ROW -->
						<div class="row">
                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="icon-share font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">SUMMARY</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="scroller" style="height: 204px;" data-always-visible="1" data-rail-visible="0">
                                            <ul class="feeds">
                                                <li>
                                                    <div class="col1">
                                                        <div class="cont">
                                                            <div class="cont-col1">
                                                              <div class="label label-sm label-danger">
                                                                  <i class="fa fa-database"></i>
                                                              </div>
                                                            </div>
                                                            <div class="cont-col2">
                                                                <div class="desc text-danger"> You are about to running out your disk space. </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col2">
                                                        <div class="date"> Just now </div>
                                                    </div>
                                                </li>
												<li>
                                                    <div class="col1">
                                                        <div class="cont">
                                                            <div class="cont-col1">
                                                              <div class="label label-sm label-warning">
                                                                  <i class="fa fa-history"></i>
                                                              </div>
                                                            </div>
                                                            <div class="cont-col2">
                                                                <div class="desc"> The file <span class="text-warning">Something.bam</span> is currently running. </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col2">
                                                        <div class="date"> Just now </div>
                                                    </div>
                                                </li>

                                                <li>
                                                    <a href="javascript:;" class="text-danger">
                                                        <div class="col1">
                                                            <div class="cont">
                                                                <div class="cont-col1">
                                                                  <div class="label label-sm label-danger">
                                                                      <i class="fa fa-exclamation-circle"></i>
                                                                  </div>
                                                                </div>
                                                                <div class="cont-col2">
                                                                    <div class="desc"> You must fill in the metadata of the file Something.txt. </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col2">
                                                            <div class="date"> 20 mins </div>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <div class="col1">
                                                        <div class="cont">
                                                            <div class="cont-col1">
                                                              <div class="label label-sm label-info">
                                                                  <i class="fa fa-check"></i>
                                                              </div>
                                                            </div>
                                                            <div class="cont-col2">
                                                                <div class="desc"> The file <span class="text-info">Something.bam</span> has finished processing. </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col2">
                                                        <div class="date"> 24 mins </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="col1">
                                                        <div class="cont">
                                                            <div class="cont-col1">
                                                              <div class="label label-sm label-warning">
                                                                  <i class="fa fa-history"></i>
                                                              </div>
                                                            </div>
                                                            <div class="cont-col2">
                                                                <div class="desc"> The file <span class="text-warning">Something.bam</span> is currently running. </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col2">
                                                        <div class="date"> Just now </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <a href="javascript:;" class="text-danger">
                                                        <div class="col1">
                                                            <div class="cont">
                                                                <div class="cont-col1">
                                                                  <div class="label label-sm label-danger">
                                                                      <i class="fa fa-exclamation-circle"></i>
                                                                  </div>
                                                                </div>
                                                                <div class="cont-col2">
                                                                    <div class="desc"> You must fill in the metadata of the file Something.txt. </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col2">
                                                            <div class="date"> 20 mins </div>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <div class="col1">
                                                        <div class="cont">
                                                            <div class="cont-col1">
                                                              <div class="label label-sm label-info">
                                                                  <i class="fa fa-check"></i>
                                                              </div>
                                                            </div>
                                                            <div class="cont-col2">
                                                                <div class="desc"> The file <span class="text-info">Something.bam</span> has finished processing. </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col2">
                                                        <div class="date"> 24 mins </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="col1">
                                                        <div class="cont">
                                                            <div class="cont-col1">
                                                              <div class="label label-sm label-warning">
                                                                  <i class="fa fa-history"></i>
                                                              </div>
                                                            </div>
                                                            <div class="cont-col2">
                                                                <div class="desc"> The file <span class="text-warning">Something.bam</span> is currently running. </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col2">
                                                        <div class="date"> Just now </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <a href="javascript:;" class="text-danger">
                                                        <div class="col1">
                                                            <div class="cont">
                                                                <div class="cont-col1">
                                                                  <div class="label label-sm label-danger">
                                                                      <i class="fa fa-exclamation-circle"></i>
                                                                  </div>
                                                                </div>
                                                                <div class="cont-col2">
                                                                    <div class="desc"> You must fill in the metadata of the file Something.txt. </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col2">
                                                            <div class="date"> 20 mins </div>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <div class="col1">
                                                        <div class="cont">
                                                            <div class="cont-col1">
                                                              <div class="label label-sm label-info">
                                                                  <i class="fa fa-check"></i>
                                                              </div>
                                                            </div>
                                                            <div class="cont-col2">
                                                                <div class="desc"> The file <span class="text-info">Something.bam</span> has finished processing. </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col2">
                                                        <div class="date"> 24 mins </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="col1">
                                                        <div class="cont">
                                                            <div class="cont-col1">
                                                              <div class="label label-sm label-warning">
                                                                  <i class="fa fa-history"></i>
                                                              </div>
                                                            </div>
                                                            <div class="cont-col2">
                                                                <div class="desc"> The file <span class="text-warning">Something.bam</span> is currently running. </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col2">
                                                        <div class="date"> Just now </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <a href="javascript:;" class="text-danger">
                                                        <div class="col1">
                                                            <div class="cont">
                                                                <div class="cont-col1">
                                                                  <div class="label label-sm label-danger">
                                                                      <i class="fa fa-exclamation-circle"></i>
                                                                  </div>
                                                                </div>
                                                                <div class="cont-col2">
                                                                    <div class="desc"> You must fill in the metadata of the file Something.txt. </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col2">
                                                            <div class="date"> 20 mins </div>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <div class="col1">
                                                        <div class="cont">
                                                            <div class="cont-col1">
                                                              <div class="label label-sm label-info">
                                                                  <i class="fa fa-check"></i>
                                                              </div>
                                                            </div>
                                                            <div class="cont-col2">
                                                                <div class="desc"> The file <span class="text-info">Something.bam</span> has finished processing. </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col2">
                                                        <div class="date"> 24 mins </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                <div class="portlet light tasks-widget bordered">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="icon-share font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">DISK USE</span>
                                        </div>

                                    </div>
                                    <div class="portlet-body">

                                        <input class="knob" data-fgColor="#006b8f" data-bgColor="#eeeeee" readonly value="47">

                                       <div style="position: absolute;top: 80px;right: 30px;width: 40%;height: 200px;font-size:16px;">
                                         You are using <strong>9.4GB</strong> from your <strong>20GB</strong> of disk quota.
                                      </div>

                                    </div>
                                </div>
                            </div>
                        </div>
						<!-- END SUMMARY AND DISK QUOTA ROW -->					

                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

				<div class="modal fade bs-modal-sm" id="myModal1" tabindex="-1" role="basic" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title">Warning!</h4>
                            </div>
                            <div class="modal-body"> You have more than one file selected. If you go ahead, this tool will just be applied to the selected file. </div>
                            <div class="modal-footer">
                                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cancel</button>
								<button type="button" class="btn green btn-modal-ok">Accept</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

<?php 

require "htmlib/footer.inc.php"; 
require "htmlib/js.inc.php";

?>
