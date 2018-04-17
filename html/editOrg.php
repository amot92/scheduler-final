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
if(!($orgID = empty($_POST['orgID']) ? false : testInput($_POST['orgID']))){
    echo "Store ID needs to be filled out";
    exit;
}

//Database connection
require_once "db.conf";
$mysqli = new mysqli($servername, $username, $password, $dbname);
if ($mysqli->connect_error) {
    echo('Error: ' . $mysqli->connect_errno . ' ' . $mysqli->connect_error);
    exit;
}
$sql = "SELECT * FROM organization WHERE orgID=(?)";
if (!($stmt = $mysqli->prepare($sql))) {
    echo "Original User Data Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    exit;
}
if (!$stmt->bind_param("i", $orgID)) {
    echo "Original User Data Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
if (!$stmt->execute()) {
    echo "Original User Data Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}else{
    $res = $stmt->get_result()->fetch_assoc();
}

if(!($name = empty($_POST['name']) ? false : testInput($_POST['name']))){
    $name = $res["name"];
}
if(!($locationCity = empty($_POST['locationCity']) ? false : testInput($_POST['locationCity']))){
    $locationCity = $res["locationCity"];
}
if(!($locationState = empty($_POST['locationState']) ? false : testInput($_POST['locationState']))){
    $locationState = $res["locationState"];
}
if(!($storeNumber = empty($_POST['storeNumber']) ? false : testInput($_POST['storeNumber']))){
    $storeNumber = $res["storeNumber"];
}

$sql = "UPDATE organizations SET orgName = (?), locationCity = (?), locationState = (?), storeNumber = (?) WHERE orgID = (?)";
if (!($stmt = $mysqli->prepare($sql))) {
    echo "Update Org Data Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    exit;
}
if (!$stmt->bind_param("sssii", $orgName, $locationCity, $locationState, $storeNumber, $orgID)) {
    echo "Update Org Data Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
if (!$stmt->execute()) {
    echo "Update Org Data Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
$mysqli->close();
header("Location: index.php");
echo "success";
?>