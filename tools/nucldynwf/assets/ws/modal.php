<div class="modal fade bs-modal" id="modalTool" tabindex="-1" role="basic" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
								<h4 class="modal-title">
									<span class="fa-stack fa-sm">
										<i class="fa fa-circle-o"></i>
										<i class="fa fa-circle-thin fa-stack-1x" style="position: absolute;top: 7px;"></i>
									</span>
									MuGVRE - Nucleosome Dynamics
								</h4>
							</div>
							<div class="modal-body table-responsive">
									<p>Welcome to the MuGVRE. For executing the Nucleosome Dynamics tool on the MuGVRE you must follow the next steps: </p>

									<form id="import-sample"  action="applib/getData.php" method="post">
										<input type="hidden"  name="uploadType" value="sampleData" />
										<input type="hidden"  name="sampleData" value="nucldynwf" />
									</form>

									<ul>
										<li>
											<p>First of all, you need to have some input data. You can bring your own data or you can use the example dataset provided by the MuGVRE:</p>
											<p><a class="btn green" href="<?php echo $GLOBALS['BASEURL']; ?>getdata/uploadForm.php"><i class="fa fa-upload"></i> Upload my own data</a></p>
											<p><button class="btn green" id="btn-sample"><i class="fa fa-database"></i> Import example dataset</button></p>
											<p><a class="btn green" href="javascript:closeModalTool();"><i class="fa fa-times"></i> No thanks, I want a clean workspace</a></p>
										</li>
										<li>
											<p>Once the data is in the MuGVRE workspace, you need to launch the Nucleosome Dynamics form. Please, click the button below to launch the tool:</p>
											<p><a class="btn green" href="<?php echo $GLOBALS['BASEURL']; ?>tools/nucldynwf/input.php?op=0"><i class="fa fa-rocket"></i> Launch Nucleosome Dynamics</a></p>
										</li>
									</ul>
									<?php if($_SESSION["User"]["Type"] == 3) { ?>
									<p><strong>NOTE:</strong> remember that your workspace is not persistent, for saving your data you need to keep safe the URL shown in the <em>Restore Link</em> box.</p>
									<?php } ?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
