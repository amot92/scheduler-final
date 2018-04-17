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

//Get the shiftID
if(!($shiftID = empty($_POST['shiftID']) ? false : testInput($_POST['shiftID']))){
    echo "The shiftID is not present";
    exit;
}

//Store variables
if(!($shiftDate = empty($_POST['shiftDate']) ? false : testInput($_POST['shiftDate']))){
    echo "The shift date is not present";
    exit;
}
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

//Find out if we are deleting the shift
if($_POST['delete']){
    $sql = "DELETE FROM shifts WHERE shiftID =" . $shiftID;
    $mysqli->query($sql);
    $mysqli->close();
    header("Location: schedule.php");
    exit;
}

$sql = "UPDATE shifts SET startTime=(?), endTime=(?), active=(?), maxBid=(?), staffPosition=(?)
WHERE shiftID=(?)";
if (!($stmt = $mysqli->prepare($sql))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    exit;
}

//A bit weird but we need to append the date to the time in order for mysql to query correctly
$startTime = $shiftDate . " " . $startTime;
$endTime = $shiftDate . " " . $endTime;

if (!$stmt->bind_param("sssiss", $startTime, $endTime, $active, $maxBid, $staffPosition, $shiftID)) {
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