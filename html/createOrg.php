<?php
if(!session_start()) {
    header("Location: error.php");
    exit;
}

function testInput($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

//Store variables
if(!($userName = empty($_POST['userName']) ? false : testInput($_POST['userName']))){
    echo "User name needs to be filled out";
    exit;
}
if(!($orgName = empty($_POST['orgName']) ? false : testInput($_POST['orgName']))){
    echo "Organization name needs to be filled out";
    exit;
}
if(!($pass = empty($_POST['password']) ? false : testInput($_POST['password']))){
    echo "Password needs to be filled out";
    exit;
}
if(!($orgCity = empty($_POST['orgCity']) ? false : testInput($_POST['orgCity']))){
    echo "City needs to be filled out";
    exit;
}
if(!($orgState = empty($_POST['orgState']) ? false : testInput($_POST['orgState']))){
    echo "State needs to be filled out";
    exit;
}
if(!($strNumber = empty($_POST['strNumber']) ? false : testInput($_POST['strNumber']))){
    echo "Store Number needs to be filled out";
    exit;
}


//Encryped pass
$pass = hash("sha512",$pass);


//Database connection
require_once "db.conf";
$mysqli = new mysqli($servername, $username, $password, $dbname);
if ($mysqli->connect_error) {
    echo('Error: ' . $mysqli->connect_errno . ' ' . $mysqli->connect_error);
    exit;
}
//see if manager exist
$sql = "SELECT managerID FROM managers WHERE managerID=(?)";
if (!($stmt = $mysqli->prepare($sql))) {
    echo "Manager select Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    exit;
}
if (!$stmt->bind_param("s", $userName)) {
    echo "Manager select Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
if (!$stmt->execute()) {
    echo "Manager select Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
if ($res = $stmt->get_result()) {

    if(($res->num_rows) != 0){
    echo "User already exists";
    exit;
    }
}
//See if org already exists
$sql = "SELECT orgName FROM organizations WHERE orgName=(?) AND storeNumber=(?)";
if (!($stmt = $mysqli->prepare($sql))) {
    echo "Org to see if exists select Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    exit;
}
if (!$stmt->bind_param("si", $orgName, $strNumber)) {
    echo "Org to see if exists Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
if (!$stmt->execute()) {
    echo "Org to see if exists Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
if ($res = $stmt->get_result()) {

    if(($res->num_rows) != 0){
        echo "Org already exists";
        exit;
    }
}


$sql = "INSERT INTO organizations (orgName, locationCity, locationState, storeNumber) VALUES
((?),(?),(?),(?))";
if (!($stmt = $mysqli->prepare($sql))) {
    echo "Org insert Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    exit;
}
if (!$stmt->bind_param("sssi", $orgName, $orgCity, $orgState, $strNumber)) {
    echo "Org insert Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
if (!$stmt->execute()) {
    echo "Org insert Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}

$sql = "SELECT orgID FROM organizations WHERE orgName=(?) AND locationCity=(?) AND locationState=(?) AND storeNumber=(?)";
if (!($stmt = $mysqli->prepare($sql))) {
    echo "Org select Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    exit;
}
if (!$stmt->bind_param("sssi", $orgName, $orgCity, $orgState, $strNumber)) {
    echo "Org select Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
if (!$stmt->execute()) {
    echo "Org select Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
if (!($res = $stmt->get_result())) {
    echo "Org select Failed to get result";
    exit;
}else{
    $orgID = ($res->fetch_assoc())["orgID"];
}


$sql = "INSERT INTO managers (managerID, orgID, CreationDate, pass) VALUES
((?),(?),(?),(?))";
if (!($stmt = $mysqli->prepare($sql))) {
    echo "Manager insert Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    exit;
}
if (!$stmt->bind_param("sssi", $userName, $orgID, $creationDate, $pass)) {
    echo " Manager insert Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
if (!$stmt->execute()) {
    echo "Manager insert Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
$mysqli->close();
header("Location: index.php");
echo "success";
?>