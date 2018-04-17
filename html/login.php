<?php
	if(!session_start()) {
		header("Location: error.php");
		exit;
	}
	
	$loggedIn = empty($_SESSION['loggedin']) ? false : $_SESSION['loggedin'];
	
	if ($loggedIn) {
		header("Location: schedule.php");
		exit;
	}
	
	$action = empty($_POST['action']) ? '' : $_POST['action'];
	
	if ($action == 'do_login')
		handle_login();
	else
		login_form();
	
    function handle_login() {
		$loginUsername = empty($_POST['loginUsername']) ? '' : $_POST['loginUsername'];
		$loginPassword = empty($_POST['loginPassword']) ? '' : $_POST['loginPassword'];
		$accessLevel = empty($_POST['accessLevel']) ? '' : $_POST['accessLevel'];
        $queryAccess = $accessLevel == 'managers' ? 'managerID' : 'userID';
        
        require_once 'db.conf';
        
        $mysqli = new mysqli($hostname, $username, $password, $dbname);

        if ($mysqli->connect_error) {
            $error = 'Error: ' . $mysqli->connect_errno . ' ' . $mysqli->connect_error;
			require "loginForm.php";
            exit;
        }
        
        $loginUsername = $mysqli->real_escape_string($loginUsername);
        $loginPassword = $mysqli->real_escape_string($loginPassword);
        
        $loginPassword = sha1($loginPassword); 
        
        $query = "SELECT * FROM $accessLevel WHERE $queryAccess = '$loginUsername' AND pass = '$loginPassword'";
        
		$mysqliResult = $mysqli->query($query);
        
        if ($mysqliResult) {
            $match = $mysqliResult->num_rows;
            $mysqliResult->close();

//          SET SESSION VARS & REDIRECT
  		    if ($match == 1) {
                $_SESSION['loggedin'] = $loginUsername;
                
//              This variable will be used to differentiate between actors throughout the app
                $_SESSION['accessLevel'] = $accessLevel;
                
            
//              If actor is a user, set associated managerID into $_SESSION['managerID']
                if($accessLevel == 'users'){
                    $query = "SELECT * FROM employed WHERE userID = '$loginUsername'";
                    $mysqliResult = $mysqli->query($query);
                    if ($mysqliResult) {
                        $row = $mysqliResult->fetch_row();
                        $_SESSION['managerID'] = $row[1];
                        if($_SESSION['managerID'] == ''){
                            ///what to do if user not employed?
                        }
                        if($row[2] == true){
                            $_SESSION['staffPosition'] = $row[3];
                        }
                        $mysqliResult->close();
                        $mysqli->close();
                    } 
                } 

//              Entry point into app
                header("Location: schedule.php");
                exit;
            }
            else {
                $error = 'Error: Incorrect username or password';
                require "loginForm.php";
                exit;
            }
        } else {
          $error = 'Login Error: Please contact the system administrator.';
          require "loginForm.php";
          exit;
        }
	}


    function login_form() {
		$username = "";
		$error = "";
		require "loginForm.php";
	}
?>