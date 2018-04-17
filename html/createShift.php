<?php
if(!session_start()) {
    header("Location: error.php");
    exit;
}

if(!empty($_SESSION['loggedin'])){
    if($_SESSION["accessLevel"] == "managers"){
        $managerID = empty($_SESSION['loggedin']) ? false : $_SESSION['loggedin'];
    } else {
        echo "You are not logged in as a manager";
        exit;
    }
} else {
    header("location: loginForm.php");
    echo "Error you are not logged in";
    exit;
}

function testInput($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

//Store variables
if(!($startTime = empty($_POST['startTime']) ? false : testInput($_POST['startTime']))){
    echo "Start time needs to be filled out";
    exit;
}
if(!($endTime = empty($_POST['endTime']) ? false : testInput($_POST['endTime']))){
    echo "End time needs to be filled out";
    exit;
}
//Can't check active because it has a false option
$active = empty($_POST['active']) ? false : testInput($_POST['active']);
if(!($maxBid = empty($_POST['maxBid']) ? false : testInput($_POST['maxBid']))){
    echo "Max bids needs to be filled out";
    exit;
}
if(!($staffPosition = empty($_POST['staffPosition']) ? false : testInput($_POST['staffPosition']))){
    echo "Staff position needs to be filled out";
    exit;
}

//Database connection
require_once "db.conf";
$mysqli = new mysqli($servername, $username, $password, $dbname);
if ($mysqli->connect_error) {
    echo('Error: ' . $mysqli->connect_errno . ' ' . $mysqli->connect_error);
    exit;
}
$sql = "INSERT INTO shifts (managerID, startTime, endTime, active, maxBid, bids, staffPosition) VALUES
((?),(?),(?),(?),(?),0,(?))";
if (!($stmt = $mysqli->prepare($sql))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    exit;
}
if (!$stmt->bind_param("ssssis", $managerID, $startTime, $endTime, $active, $maxBid, $staffPosition)) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
$mysqli->close();
header("Location: schedule.php");
echo "success";
?>