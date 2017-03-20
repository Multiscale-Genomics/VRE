</div>
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

</div>
            <!-- END CONTAINER -->
            <!-- BEGIN FOOTER -->
            <div class="page-footer">
				<div class="page-footer-inner"> &copy; 2017 MuG Virtual Research Environment</div>
                <div class="scroll-to-top">
                    <i class="icon-arrow-up"></i>
                </div>
            </div>
            <!-- END FOOTER -->
        </div>
<!--[if lt IE 9]>
<script src="/assets/global/plugins/respond.min.js"></script>
<script src="/assets/global/plugins/excanvas.min.js"></script>
<![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
										<script src="/assets/global/plugins/ngl.js" type="text/javascript"></script>
				<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
				<script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
				<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
				<script src="/tools/pydock/assets/js/output.js" type="text/javascript"></script>
							        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
					<script type="text/javascript" src="NUCMD/jsmol/JSmol.min.nojq.js"></script>

	<!-- JSMol auxiliar Scripts -->
	<script type="text/javascript" src="NUCMD/js/jmolScripts.js"></script>

	<!-- Image Preview -->
	<script type="text/javascript" src="NUCMD/js/imagePreview.js"></script>

	<!-- Image/Video visualization with jQuery -->
	<script type="text/javascript" src="NUCMD/js/jqueryImages/jqueryImages.js"></script>

	<script type="text/javascript">

		$(document).ready( function() {
<?php if($analysisType == 'PCAZIP') { ?> $("#insertJmol").html(Jmol.getAppletHtml("jmol",Info)); <?php } ?>

			imagePreview();
		});
	</script>

		        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        			<script src="/assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
			<script src="/assets/layouts/layout/scripts/main.js" type="text/javascript"></script>
					<!-- END THEME LAYOUT SCRIPTS -->
    </body>

</html>
