<?php

require "phplib/genlibraries.php";
checkIfSessionUser(basename($_SERVER['PHP_SELF']));

?>

<body style="background:#006b8f;">
<form id="loginToken-form" action="applib/loginToken.php" method="post"></form>
</body>

<script>
document.getElementById("loginToken-form").submit();
</script>

