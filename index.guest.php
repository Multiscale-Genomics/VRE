
<?php

require "phplib/genlibraries.php";
checkIfSessionUser(basename($_SERVER['PHP_SELF']));

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="login-pf">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="robots" content="noindex, nofollow">

            <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>        Log in to Multiscale Genomics
</title>
    <link rel="icon" href="assets/layouts/layout/img/icon.png" />

        <link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
	<link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
	<link href="assets/pages/css/login.min.css" rel="stylesheet" type="text/css" />
	<link href="assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
	<link href="/assets/layouts/layout/css/custom.min.css?v=<?php echo rand(); ?>" rel="stylesheet" type="text/css" />
        

</head>


<body class=" login">
        <!-- BEGIN LOGO -->
        <div class="logo">
				<a href="">
								<img src="assets/layouts/layout/img/logo-big.png" alt="" /> </a>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
				<div class="content">

<p>some description?</p>

<form action="applib/loginAnonymous.php" method="post">
    <input class="btn green btn-block" type="submit" style="margin-top:20px;" value="Guest"/>
</form>

<form action="applib/loginToken.php" method="post">
    <input class="btn green btn-block" type="submit" style="margin-top:20px;" value ="Sign in"/>
</form>

	</div>


<div class="modal fade bs-modal" id="modalTerms" tabindex="-1" role="basic" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>-->
                                <h4 class="modal-title">MuG Virtual research environment terms of use</h4>
                            </div>
														<div class="modal-body table-responsive">
															<div class="container-terms" style="max-height: calc(100vh - 255px);"></div>
														</div>
                            <div class="modal-footer">
                                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
								</div>


<div class=" copyright "> &copy; 2018 MuG Virtual Research Environment :: <a href="javascript:openTermsOfUse();" class="font-white">Terms of Use</a></div>

        <script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
			<script src="assets/layouts/layout/scripts/main.js" type="text/javascript"></script>

</body>

</html>

