<?php
	if(!session_start()) {
		header("Location: error.php");
		exit;
	}
	
	$loggedIn = empty($_SESSION['loggedin']) ? false : $_SESSION['loggedin'];
	
	if (!$loggedIn) {
		header("Location: login.php");
		exit;
	}
?>


<!DOCTYPE html>
<htm lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <?php include("../bootstrap/bscss.php"); ?>
	<title>Create User Account</title>
    <script>
        
    </script>
</head>
<body>
    <?php
            if($_SESSION["accessLevel"] == "managers"){
                include("../templates/nav_manager.php");
               
            } else {
                include("../templates/nav_default.php");
            }
        ?>

        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <!-- Optional JavaScript -->
        <script src="../scripts/changePassword.js"></script>
        <div>
        <h1>Change Password</h1>
        <?php
            if ($alert) print "<div>$alert</div>\n";
        ?>
        <form action="changePassword.php" method="POST">
            
            <input type="hidden" name="action" value="do_change">
            
            <div>
                <label for="currentPass">Current Password:</label>
                <input type="password" id="currentPass" name="currentPass" autofocus required>
            </div>
            
            <div>
                <label for="newPass">New Password:</label>
                <input type="password" id="newPass" name="newPass" required onkeyup='check();'>
            </div>
            
            <div>
                <label for="confirmPass">Confirm Password:</label>
                <input type="password" id="confirmPass" name="confirmPass" required onkeyup='check();'>
            </div>
            <span id='message'></span>
            <div>
                <input id='submit' type="submit" value="Submit">
            </div>
        </form>
    </div>
</body>
</html>