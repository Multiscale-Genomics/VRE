<html>
<head>
  <meta charset="utf-8" />
</head>
<?php

require "phplib/genlibraries.php";
checkIfSessionUser(basename($_SERVER['PHP_SELF']));

?>

<body style="background:#006b8f;">

<?php var_dump($_SESSION['User']); ?>

<form action="applib/loginAnonymous.php" method="post">
    <input type="submit"  value="Guest"/>
</form>

<form action="applib/loginToken.php" method="post">
    <input type="submit" value ="Sign in"/>
</form>
</body>

</html>
