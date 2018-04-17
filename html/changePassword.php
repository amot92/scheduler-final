<?php
	if(!session_start()) {
		header("Location: ../error.php");
		exit;
	}
	
	$loggedIn = empty($_SESSION['loggedin']) ? false : $_SESSION['loggedin'];
	
	if (!$loggedIn) {
		header("Location: ../login.php");
		exit;
	}
	
	$action = empty($_POST['action']) ? '' : $_POST['action'];
	
	if ($action == 'do_change')
		handle_change();
	else {
		 password_form('');
	}
	
    function handle_change() {
        $loginUsername = $_SESSION['loggedin'];
        $currentPass = empty($_POST['currentPass']) ? '' : $_POST['currentPass'];
		$accessLevel = $_SESSION['accessLevel'];
        $queryAccess = $accessLevel == 'managers' ? 'managerID' : 'userID';
        $newPass = empty($_POST['newPass']) ? '' : $_POST['newPass'];
        
        require_once '../db.conf';
        
        $mysqli = new mysqli($hostname, $username, $password, $dbname);

        if ($mysqli->connect_error) {
            $error = 'Error: ' . $mysqli->connect_errno . ' ' . $mysqli->connect_error;
			require "../loginForm.php";
            exit;
        }
        
        $currentPass = $mysqli->real_escape_string($currentPass);
        $currentPass = sha1($currentPass); 
        
        if(!$stmt = $mysqli->prepare("SELECT * FROM $accessLevel WHERE $queryAccess = (?) AND pass = (?)")){
             password_form('Error: Please contact the system administrator.');
        }
        if(!$stmt->bind_param('ss', $loginUsername, $currentPass)) {
             password_form('Error: Please contact the system administrator.');
        }
        if(!$stmt->execute()) {
             password_form('Error: Please contact the system administrator.');
        }
                
        if ($res = $stmt->get_result()) {
            $match = $res->num_rows;

  		    if ($match == 1) {
               //do work to change password
                $newPass = $mysqli->real_escape_string($newPass);
                $newPassEncrypted = sha1($newPass); 
                
                if(!$stmt2 = $mysqli->prepare("update $accessLevel set pass = (?) where $queryAccess = (?)"))
                     password_form('Error: Please contact the system administrator.');
                
                if(!$stmt2->bind_param('ss', $newPassEncrypted, $loginUsername))
                     password_form('Error: Please contact the system administrator.');
            
                if ($stmt2->execute())
                    password_form("Password succesfully changed");
                else
                    password_form('Error: Please contact the system administrator.');
            
                $mysqliResult->close();
                
            } else { //query resulted w/ no match
                password_form('Error: Current Password incorrect.');
            }
         } else { // no query result
             password_form('Error: Please contact the system administrator.');
        }
        
	   $mysqli->close();
    }

    function password_form($message) {
        $alert = $message;
        require "changePasswordForm.php";
        exit;
    }
?>