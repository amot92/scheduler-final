<?php
if(!session_start()) {
    header("Location: error.php");
    exit;
}

//Test Code
//$_SESSION['loggedin'] = "1";
//$_SESSION['accessLevel'] = "users";
//$_SESSION['managerID'] = '1';

if(!empty($_SESSION['loggedin'])){
    if($_SESSION["accessLevel"] == "managers"){
        $managerID = empty($_SESSION['loggedin']) ? false : $_SESSION['loggedin'];
    } else {
        $managerID = empty($_SESSION['managerID']) ? false : $_SESSION['managerID'];
        if(!$managerID){
            echo "The managerID was not set, managerID is not a valid session index";
            exit;
        }
    }
} else {
    header("location: loginForm.php");
    echo "Error you are not logged in";
    exit;
}

//Array holding days of the week
$weekdays = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");

//Parse the week data and turn it into integers
$weekParts = explode("-W", $_GET["week"]);
if($weekParts){
    $year = (int)$weekParts[0];
    $week_no = $weekParts[1];
    $week_start = new DateTime();

    //Test code
//    $year = 2018;
//    $week_no = 15;
    
    $week_start->setISODate($year,$week_no);
} else {
    echo "No week was selected";
    exit;
}

//Database connection
require_once "db.conf";
$mysqli = new mysqli($servername, $username, $password, $dbname);
if ($mysqli->connect_error) {
    echo('Error: ' . $mysqli->connect_errno . ' ' . $mysqli->connect_error);
    exit;
}

//Separate data for Managers and Users
if($_SESSION["accessLevel"] == "managers"){
    $sql = "SELECT shiftID, staffPosition, DATE(startTime) AS date, TIME(startTime) AS startTime, TIME(endTime) AS endTime, active, maxBid, bids FROM shifts WHERE managerID = (?) AND DATE(startTime) = (?) ORDER BY startTime";
    $modal = "#editShiftModal";
} else {
    //Hardcoded cashier, still need to implement this dynamically
    $staffPosition = "cashier";
    $sql = "SELECT shiftID, staffPosition, TIME(startTime) AS startTime, TIME(endTime) AS endTime, active, maxBid, bids FROM shifts WHERE managerID = (?) AND DATE(startTime) = (?) AND (staffPosition = (?) OR staffPosition = 'any') ORDER BY startTime";
    $modal = "#bidShiftModal";
}

if (!($stmt = $mysqli->prepare($sql))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

//Build the Schedule
echo '<div class="row mx-0 px-0">';
foreach($weekdays as $weekday){
    echo '<div id="'.$weekday.'" class="col-lg mx-0 px-1">
            <div class="card bg-dark">
                <div class="card-body">
                    <h5 class="text-center text-light">'.$weekday.'</h5>
                    <p class="text-center text-light">'.$week_start->format('M-d-Y').'</p>
                </div>
            </div>';
                $week_start_string = $week_start->format('Y-m-d');
                if($_SESSION['accessLevel'] == "managers"){
                    if (!$stmt->bind_param("is", $managerID, $week_start_string)) {
                        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
                    }
                } else {
                    if (!$stmt->bind_param("iss", $managerID, $week_start_string, $staffPosition)) {
                        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
                    }
                }
                if (!$stmt->execute()) {
                    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                }
                if (!($res = $stmt->get_result())) {
                    echo "Getting result set failed: (" . $stmt->errno . ") " . $stmt->error;
                }
                while ($row = $res->fetch_assoc()) {
                    $remaining = $row["maxBid"] - $row["bids"];
                    $html = '<div class="card">
                                <div class="card-body btn" data-toggle="modal" data-target="'.$modal.'" data-shiftID="'.$row["shiftID"].'" data-shiftDate="'.$row["date"].'"  data-staffPosition="'.$row["staffPosition"].'" data-startTime="'.$row["startTime"].'" data-endTime="'.$row["endTime"].'" data-maxBid="'.$row["maxBid"].'" data-active="'.$row["active"].'">
                                    <h6>'.$row["staffPosition"].'</h6>
                                    <small>'.$row["startTime"].' - '.$row["endTime"].'</small><br>
                                    <small>remaining: '.$remaining.'</small>
                                </div>
                            </div>';
                    echo $html;
                }
                $week_start->add(new DateInterval('P1D'));
        echo '</div>';
}
$res->free();
$mysqli->close();
?>