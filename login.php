<html>
<head>
  <meta charset="utf-8" />
</head>
<?php

require "phplib/genlibraries.php";
//checkIfSessionUser(basename($_SERVER['PHP_SELF']));

// unset SESSION[User]
logoutAnon();

?>

<body style="background:#006b8f;color:white;font-size: 1.1em;">
<form id="loginToken-form" action="applib/loginToken.php" method="post"></form>
<!--Login disabled!<br>
The authentication service is under maintenance from 6th August to 8th August at 15:00 CEST. These days, only unregistered access is allowed.<br>
Sorry for the inconveniences,<br>
VRE teamI--!>
</body>

<script>
document.getElementById("loginToken-form").submit();
</script>
</html>
