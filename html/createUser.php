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
if(!($name = empty($_POST['name']) ? false : testInput($_POST['name']))){
    echo "Name needs to be filled out";
    exit;
}
if(!($pass = empty($_POST['password']) ? false : testInput($_POST['password']))){
    echo "Password needs to be filled out";
    exit;
}
if(!($ssn = empty($_POST['ssn']) ? false : testInput($_POST['ssn']))){
    echo "SSN position needs to be filled out";
    exit;
}
if(!($address = empty($_POST['address']) ? false : testInput($_POST['address']))){
    echo "Address position needs to be filled out";
    exit;
}
if(!($phoneNumber = empty($_POST['phoneNumber']) ? false : testInput($_POST['phoneNumber']))){
    $phoneNumber = NULL;
}
if(!($email = empty($_POST['email']) ? false : testInput($_POST['email']))){
    $email = NULL;
}
if(!($birthday = empty($_POST['birthday']) ? false : testInput($_POST['birthday']))){
    $birthday = NULL;
}

//Encryped pass
$pass = sha1($pass);

//Database connection
require_once "db.conf";
$mysqli = new mysqli($servername, $username, $password, $dbname);
if ($mysqli->connect_error) {
    echo('Error: ' . $mysqli->connect_errno . ' ' . $mysqli->connect_error);
    exit;
}
$sql = "INSERT INTO users (realName, userID, pass, ssn, birthday, address, phone, email) VALUES
((?),(?),(?),(?),(?),(?),(?),(?))";
if (!($stmt = $mysqli->prepare($sql))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    exit;
}
if (!$stmt->bind_param("ssssssss", $name, $userName, $pass, $ssn, $birthday, $address, $phone, $email)) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    exit;
}
$mysqli->close();
header("Location: index.php");
echo "success";
?>