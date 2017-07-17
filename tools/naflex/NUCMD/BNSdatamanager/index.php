<?php
header('Location: workspace.php?'.$_SERVER['QUERY_STRING']);
?>
<html>
    <body>
        <iframe width="500px" height="90px" id="loginForm" src="gesUser.php?op=loginForm" frameborder="0"></iframe>                             
    </body>
</html>
