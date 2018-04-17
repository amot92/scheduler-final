<?php

?>
<!DOCTYPE html>
<html>
<head>
	<title>Page 1</title>
</head>
<body>
    <div>
        <h1>Page 1</h1>
        <div>
            <?php 
            print $_POST['loginUsername'];
            print "<br>";
            print $_POST['loginPassword'];
            print "<br>";
            print $_POST['accessLevel'];
            ?>
        </div>
        <p><a href='logout.php'>Logout</a></p>
    </div>
</body>
</html>
