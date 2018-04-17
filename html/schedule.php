<!DOCTYPE html>
<?php
if(!session_start()) {
    header("Location: error.php");
    exit;
}

//Test Code
//$_SESSION['loggedin'] = "1";
//$_SESSION['accessLevel'] = "managers";

if(!empty($_SESSION['loggedin'])){
    if($_SESSION["accessLevel"] == "managers"){
        $managerID = empty($_SESSION['loggedin']) ? false : $_SESSION['loggedin'];
    } elseif($_SESSION["accessLevel"] == "users"){
        $managerID = empty($_SESSION['managerID']) ? false : $_SESSION['managerID'];
    }
} else {
    header("location: index.php");
    echo "Error you are not logged in";
    exit;
}
?>
<html lang="en">
    <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <?php include("bootstrap/bscss.php"); ?>
    
    <title>ABCScheduler</title>
    </head>
    <body>
        <?php
            if($_SESSION["accessLevel"] == "managers"){
                include("templates/nav_manager.php");
                include("templates/schedule.template");
                include("templates/editShiftModal.template");
                include("templates/addShiftModal.template");
            } else {
                include("templates/nav_default.php");
                include("templates/user_schedule.template");
                include("templates/bidShiftModal.template");
            }
        ?>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <!-- Optional JavaScript -->
    <script src="scripts/schedule.js"></script>
    </body>
</html>