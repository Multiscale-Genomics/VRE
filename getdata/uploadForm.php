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
                    <div class="page-content">
                        <!-- BEGIN PAGE HEADER-->
                        <!-- BEGIN PAGE BAR -->
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
															<li>
																<a href="/home/">Home</a>
																<i class="fa fa-circle"></i>
															</li>
                             	<li>
			       										<span>Get Data</span>
			       										<i class="fa fa-circle"></i>
			   											</li>
			   											<li>
			       										<span>Upload Files</span>
			   											</li>
                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Upload Files
                            <small>upload files to your data table</small>
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mt-element-step">
                                    <div class="row step-line">
                                        <div class="mt-step-desc">
										  <p>You can <a href="javascript:$('.nav-tabs a[href=\'#tab_1_1_1\']').click();">upload multiple files</a> to your workspace just drag and dropping 
											them over the area below. You can also 
											<a href="javascript:$('.nav-tabs a[href=\'#tab_1_1_2\']').click();">create a text file</a> from 
											a sequence or <a href="javascript:$('.nav-tabs a[href=\'#tab_1_1_3\']').click();">load a file</a> 
											to your workspace from an external URL.</p>

											<!--<p>List of actions:</p>

											<div class="list-group" style="width:50%;">
												<a class="list-group-item font-green" href="javascript:$('.nav-tabs a[href=\'#tab_1_1_1\']').click();"><i class="fa fa-upload"></i> Upload files from your local computer</a>
												<a class="list-group-item font-green" href="javascript:$('.nav-tabs a[href=\'#tab_1_1_2\']').click();"><i class="fa fa-pencil-square-o"></i> Create new file from text</a>
												<a class="list-group-item font-green" href="javascript:$('.nav-tabs a[href=\'#tab_1_1_3\']').click();"><i class="fa fa-cloud-download"></i> Upload file from an external URL</a>
											</div>-->
											
                                        </div>

										<?php require "../htmlib/stepsup.inc.php"; ?>	
										
                                    </div>
                                </div>
								
								<div class="alert alert-danger display-hide alert-error-uploading">
                                  					Error, you tried to upload a wrong file.
								</div>

								<!-- BEGIN TAB PORTLET-->
								<div class="portlet light bordered">
										<!--<div class="portlet-title tabbable-line">
												<div class="caption">
														<i class="icon-share font-dark"></i>
														<span class="caption-subject font-dark bold uppercase">Portlet Tabs</span>
												</div>
										</div>-->
										<div class="portlet-body">
												<div class="tabbable-custom nav-justified">
														<ul class="nav nav-tabs nav-justified">
																<li class="active uppercase">
																		<a href="#tab_1_1_1" data-toggle="tab"> Upload files from your local computer </a>
																</li>
																<li class="uppercase">
																		<a href="#tab_1_1_2" data-toggle="tab"> Create new file from text </a>
																</li>
																<li class="uppercase">
																		<a href="#tab_1_1_3" data-toggle="tab"> Load file from an external URL </a>
																</li>
														</ul>
														<div class="tab-content">
																<div class="tab-pane active" id="tab_1_1_1">
																<p> Just drag & drop your files over the area below or click it to open your browser (as a BETA version, the maximum upload size is <strong><?php echo $GLOBALS['MAXSIZEUPLOAD']; ?>M</strong>) </p>
																		<form action="applib/getData.php" class="dropzone dropzone-file-area" id="my-dropzone" style="/*width: 500px;*/ font-size:24px; font-weight:600; margin: 10px 0;">
																			<input type="hidden" name="baseURL" id="base-url" value="<?php echo $GLOBALS['BASEURL']; ?>" />
																			<input type="hidden" name="uploadType" value="file" />
																		</form>
																</div>
																<div class="tab-pane" id="tab_1_1_2">
																		<p> Insert below the text you want to convert into a file </p>
																		<form name="uploadFromTxt" id="uploadFromTxt" action="javascript:;" method="post">
																			<div class="alert alert-danger display-hide" id="alert-down-form">
																			Error downloading file, please, try again.
																			</div>

																			<input type="hidden" name="uploadType" value="txt" />									
																			<div class="form-group " id="">
																				<label>File Name</label>
																				<input type="text" name="filename" id="filename" class="form-control" placeholder="Insert your file name here">
																			</div>

																			<div class="form-group " id="">
																				<label>Text Data</label>
																				<textarea name="txtdata" id="txtdata" class="form-control" rows="6" placeholder="Insert your text data here"></textarea>
																			</div>

																			<div class="form-actions btn-send-data">
																				<input type="submit" class="btn green snd-metadata-btn" value="SEND DATA" style="position:relative;z-index:20;" >
																			</div>

																			<div class="progress-bar-file progress display-hide" style="margin-top:20px;">
																				<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
																					<span class="sr-only"> 20% Complete </span>
																				</div>
																			</div>	
																	</form>
																</div>
																<div class="tab-pane" id="tab_1_1_3">
																		<p> Insert the URL from which you want to get the data </p>
																		<form class="down-form" action="javascript:;" method="post">
										<div class="alert alert-danger display-hide" id="alert-down-form2">
																			Error downloading file, please, try again.
										</div>
																		<div class="form-group">
											<label>External URL</label>

												<div class="input-icon">
													<i class="fa-li fa fa-cloud-download font-green" style="line-height:10px;"></i>
													<input type="url" class="form-control" name="url"  placeholder="http://public/path/to/file">
													<input type="hidden" name="uploadType" value="url" />
												</div>
											<button class="btn green" type="submit" id="btn-down-remote" style="margin-top:20px;">SEND DATA</button>

											<div class="progress-bar-down progress display-hide" style="margin-top:20px;">
																						<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
																							<span class="sr-only"> 20% Complete </span>
																						</div>
																				</div>	

																		</div>

										</form>
																</div>
														</div>
												</div>
										</div>
								</div>
								<!-- END TAB PORTLET-->

								<!--
								<div class="portlet light">
									<div class="portlet-title">
											<div class="caption">
													<span class="caption-subject uppercase">Upload file from your local computer</span>
													<span class="caption-helper">Just drag & drop your files over the area below or click it to open your browser</span>
											</div>
									</div>
									<div class="portlet-body">

										<form action="applib/getData.php" class="dropzone dropzone-file-area" id="my-dropzone" style="/*width: 500px;*/ font-size:24px; font-weight:600; margin: 10px 0;">
											<input type="hidden" name="baseURL" id="base-url" value="<?php echo $GLOBALS['BASEURL']; ?>" />
											<input type="hidden" name="uploadType" value="file" />
										</form>
								</div>
								</div>


								<div class="portlet light">
									<div class="portlet-title">
											<div class="caption">
													<span class="caption-subject uppercase"> Upload file from an external URL</span>
													<span class="caption-helper">Insert the URL from which you want to get the data</span>
											</div>
									</div>
									<div class="portlet-body">

										<form class="down-form" action="javascript:;" method="post">
										<div class="alert alert-danger display-hide" id="alert-down-form">
																			Error downloading file, please, try again.
										</div>
																		<div class="form-group">
											<label>Upload from web by URL</label>

												<div class="input-icon">
													<i class="fa fa-download font-green"></i>
													<input type="url" class="form-control" name="url"  placeholder="http://public/path/to/file">
													<input type="hidden" name="uploadType" value="url" />
												</div>
											<button class="btn green" type="submit" id="btn-down-remote" style="margin-top:20px;">SEND DATA</button>

											<div class="progress-bar-down progress display-hide" style="margin-top:20px;">
																						<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
																							<span class="sr-only"> 20% Complete </span>
																						</div>
																				</div>	

																		</div>

										</form>
									</div>
								</div>

								<div class="portlet light">
									<div class="portlet-title">
											<div class="caption">
													<span class="caption-subject uppercase"> Create new file from text</span>
													<span class="caption-helper">Insert below the text you want to convert into a file</span>
											</div>
									</div>
									<div class="portlet-body">

										<form name="uploadFromTxt" id="uploadFromTxt" action="javascript:;" method="post">
											<div class="alert alert-danger display-hide" id="alert-down-form">
																			Error downloading file, please, try again.
										</div>

										 	<input type="hidden" name="uploadType" value="txt" />									
											<div class="form-group " id="">
												<label>File Name</label>
												<input type="text" name="filename" id="filename" class="form-control" placeholder="Insert your file name here">
											</div>

											<div class="form-group " id="">
												<label>Text Data</label>
												<textarea name="txtdata" id="txtdata" class="form-control" rows="6" placeholder="Insert your text data here"></textarea>
											</div>

											<div class="form-actions btn-send-data">
												<input type="submit" class="btn green snd-metadata-btn" value="SEND DATA" style="position:relative;z-index:20;" >
											</div>

											<div class="progress-bar-file progress display-hide" style="margin-top:20px;">
																						<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
																							<span class="sr-only"> 20% Complete </span>
																						</div>
																				</div>	

																		</div>


										</form>
									</div>
								</div>-->



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
