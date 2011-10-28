<?php

require 'requires/session.php';
require_once 'requires/functions.php';

$action = $_GET['action'];
$userRank = $_SESSION['session_rank'];






// GET CONTENT
if(($action == "getContent")&&(isset($_SERVER['HTTP_X_REQUESTED_WITH']))&&(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
{
	$functions = new functions;
	
	$conn = $functions->dbConnect();
	
	$userID = $_SESSION['session_userID'];
  	
  	$docID = mysql_real_escape_string($_SESSION['session_docID']);

  	$result = mysql_query("SELECT * FROM documents WHERE id = '$docID'");	
	$row = mysql_fetch_assoc($result);	
	if($row['userID'] == $_SESSION['session_userID'])
	{
		echo $row['content'];
	}
	
	mysql_close($conn);	
}



// GET MARKED CONTENT 
if(($action == "getMarkedContent")&&(isset($_SERVER['HTTP_X_REQUESTED_WITH']))&&(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
{
	$functions = new functions;
	
	$conn = $functions->dbConnect();
	
	$userID = $_SESSION['session_userID'];
  	
  	$docID = mysql_real_escape_string($_SESSION['session_docID']);

  	$result = mysql_query("SELECT * FROM documents WHERE id = '$docID'");	
	$row = mysql_fetch_assoc($result);	
	if($row['userID'] == $_SESSION['session_userID'])
	{
		echo $row['markedContent'];
	}
	
	mysql_close($conn);	
}



// CLEAR MARKS
else if(($action == "clearMarks")&&(isset($_SERVER['HTTP_X_REQUESTED_WITH']))&&(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
{
	$functions = new functions;
	
	$conn = $functions->dbConnect();
	
	$userID = $_SESSION['session_userID'];
  	
  	$docID = mysql_real_escape_string($_SESSION['session_docID']);

  	$result = mysql_query("SELECT * FROM documents WHERE id = '$docID'");	
	$row = mysql_fetch_assoc($result);	
	if($row['userID'] == $_SESSION['session_userID'])
	{
		$plainText = $row['content'];
		
		mysql_query("DELETE FROM marks WHERE docID='$docID'");
		mysql_query("UPDATE documents set markedContent = '$plainText' WHERE id='$docID'");
		
		echo $plainText;
	}
	
	mysql_close($conn);	
}




// ADD MARK
else if(($action == "addMark")&&(isset($_SERVER['HTTP_X_REQUESTED_WITH']))&&(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
{
	$sp = htmlspecialchars($_POST['sp']);
	$ep = htmlspecialchars($_POST['ep']);
	$ns = htmlspecialchars($_POST['ns']);
	$tag = htmlspecialchars($_POST['tag']);
	$attr = htmlspecialchars($_POST['attr']);
	$url = htmlspecialchars($_POST['url']);
	$text = htmlspecialchars($_POST['txt']);
	$userInput = $_POST['userInput'];

	$functions = new functions;
	
	$conn = $functions->dbConnect();
	
	$userID = $_SESSION['session_userID'];  	
  	
  	$docID = mysql_real_escape_string($_SESSION['session_docID']);

  	$result = mysql_query("SELECT * FROM documents WHERE id = '$docID'");	
	$row = mysql_fetch_assoc($result);	
	if($row['userID'] == $_SESSION['session_userID'])
	{
		mysql_query("UPDATE documents set markedContent = '$userInput' WHERE id='$docID'");
		
		mysql_query("INSERT INTO `marks` (`docID` , `sp` , `ep`, `ns`, `va`, `url`, `text`) VALUES ('$docID', '$sp', '$ep', '$ns', '$tag', '$url', '$text')");
	}
	
	mysql_close($conn);	
}




// signup new user
else if(($action == "signup")&&($_SERVER["REQUEST_METHOD"] == "POST")&&($userRank < 1))
{
	
	$functions = new functions;
	$conn = $functions->dbConnect();
	
			
		
	$username = htmlspecialchars($_POST['username']);
	$password = htmlspecialchars($_POST['password']);
	$email = htmlspecialchars($_POST['email']);
	$fname = htmlspecialchars($_POST['fname']);
	
	$username = mysql_real_escape_string($username);
	$email = mysql_real_escape_string($email);	
	
	// clean inputs
	$username = strtolower($username);
	$email = strtolower($email);
	
	$errors = 0;		
	
									
	
	//check for username
	$result = mysql_query("SELECT * FROM users WHERE username = '$username'");	
	$row = mysql_fetch_row($result);
	if($row[0])
	{
		$_SESSION['msg'] = array("msg" => "The username that you have entered is being used by another user ", "title" => "Unavailable Username", "link" => "index.php", "legend" => "Try Again");	
		$errors++;		
	} 
	
	// check for email
	$result = mysql_query("SELECT * FROM users WHERE email = '$email'");	
	$row = mysql_fetch_row($result);						
	if($row[0])
	{
		$_SESSION['msg'] = array("msg" => "The email address that you have entered is associated with another account", "title" => "Invalid Email Address", "link" => "index.php", "legend" => "Try Again");	
		$errors++;		
	} 
				 			

	// check if first name meets requirements
	if(!preg_match('/^[A-Za-z ]{2,20}$/', $fname))
	{ 
		$_SESSION['msg'] = array("msg" => "Your name has to be 2-20 characters long and has to contain English Alphabets only", "title" => "Invalid First Name", "link" => "index.php", "legend" => "Try Again");	
		$errors++;
	}	
	
	
	// check if username meets requirements
	if(!preg_match('/^[A-Za-z0-9]{4,20}$/', $username))
	{ 
		$_SESSION['msg'] = array("msg" => "Your username has to be 6-20 characters long and has to contain English Alphabets only", "title" => "Invalid Username", "link" => "index.php", "legend" => "Try Again");	
		$errors++;
	}	
	
	// check if password meets requirements
	if(!preg_match('/^[A-Za-z0-9!@#$%^&*()_]{4,20}$/', $password))
	{ 
		$_SESSION['msg'] = array("msg" => "You must enter a valid password that is between 6-20 characters long", "title" => "Invalid Password", "link" => "index.php", "legend" => "Try Again");	
		$errors++;
	}	
	
	// check if email meets requirements
	if(!preg_match('/^([a-z0-9\\+_\\-]+)(\\.[a-z0-9\\+_\\-]+)*@([a-z0-9\\-]+\\.)+[a-z]{2,6}$/ix', $email))
	{ 
		$_SESSION['msg'] = array("msg" => "You must enter a valid email address", "title" => "Invalid Email Address", "link" => "index.php", "legend" => "Try Again");	
		$errors++;
	}
	
	if($errors < 1)
	{				
						
		$password = md5($password);
		mysql_query("INSERT INTO `users` ( `username` , `password` , `email`, `name`)   VALUES ('$username', '$password', '$email', '$fname')");
						
		$_SESSION['msg'] = array("msg" => "Your account has been created. You can now login!", "title" => "Account Created", "link" => "index.php", "legend" => "Sign In");								
	}
	
	mysql_close($conn);	
	
	echo "<script type=\"text/javascript\">window.location='msg.php';</script>";												
}









// login user
else if(($action == "login")&&($_SERVER["REQUEST_METHOD"] == "POST")&&($userRank < 1))
{
	$functions = new functions;
	$conn = $functions->dbConnect();
	
	$username = htmlspecialchars($_POST['username']);
	$password = htmlspecialchars($_POST['password']);
	$encryptedPassword = md5($password);
	
	$username = mysql_real_escape_string($username);
	$encryptedPassword = mysql_real_escape_string($encryptedPassword);	
	
	$result = mysql_query("SELECT * FROM users WHERE username = '$username' AND password = '$encryptedPassword'");	
	$row = mysql_fetch_assoc($result);
	$userID = $row['id'];
	$email = $row['email'];
	$fname = $row['name'];
	$status = $row['status'];
	
	if(!$userID)
	{
		$_SESSION['msg'] = array("msg" => "Invalid username and/or password", "title" => "Unable to Login", "link" => "index.php", "legend" => "Try Again");				
	}	
	else if(!$status)
	{
		$_SESSION['msg'] = array("msg" => "The website is under construction. Only approved members are allowed to use the editor at this point. Please come back later!", "title" => "Construction Mode", "link" => "index.php", "legend" => "Home Page");				
	}		
	else
	{
		$_SESSION['session_userID'] = $userID;
		$_SESSION['session_username'] = $fname;
		$_SESSION['session_rank'] = 1;
					
	}
	
	mysql_close($conn);	
	
	echo "<script type=\"text/javascript\">window.location='msg.php';</script>";							
}
		
		
// create new document
else if(($action == "create")&&($_SERVER["REQUEST_METHOD"] == "POST")&&($userRank == 1))
{
	$functions = new functions;
	$conn = $functions->dbConnect();
	
	$name = htmlspecialchars($_POST['name']);
	
	$name = mysql_real_escape_string($name);
	
	$userID = $_SESSION['session_userID'];
	
	mysql_query("INSERT INTO `documents` ( `name` , `userID`)   VALUES ('$name', '$userID')");
	$id = mysql_insert_id();
	mysql_close($conn);	
	
	
	
	echo "<script type=\"text/javascript\">window.location='edit.php?id=$id';</script>";	
}	



// process step 1	
else if(($action == "processStep1")&&($_SERVER["REQUEST_METHOD"] == "POST")&&($userRank == 1))
{
	$userInput = htmlspecialchars($_POST['input']);
	
	$functions = new functions;
	
	$conn = $functions->dbConnect();
	  	
  	$id = mysql_real_escape_string($_GET['id']);

  	$result = mysql_query("SELECT * FROM documents WHERE id = '$id'");	
	$row = mysql_fetch_assoc($result);	
	if($row['userID'] == $_SESSION['session_userID'])
	{
		if($row['content'] != $userInput)
		{
			mysql_query("UPDATE documents set markedContent = '$userInput' WHERE id='$id'");
			mysql_query("UPDATE documents set content = '$userInput' WHERE id='$id'");
		}
		
		$page = "edit.php?id=$id&step=2";
	}
	else
	{
		$_SESSION['msg'] = array("msg" => "Unable to process your request", "title" => "Invalid Request", "link" => "index.php", "legend" => "Home Page");
		$page = "msg.php";
	}	
	
	mysql_close($conn);	
	
	echo "<script type=\"text/javascript\">window.location='$page';</script>";	
}

// process step 2	
else if(($action == "processStep2")&&($_SERVER["REQUEST_METHOD"] == "POST")&&($userRank == 1))
{	
	$functions = new functions;
	
	$conn = $functions->dbConnect();
	  	
  	$id = mysql_real_escape_string($_GET['id']);

  	$result = mysql_query("SELECT * FROM documents WHERE id = '$id'");	
	$row = mysql_fetch_assoc($result);	
	if($row['userID'] == $_SESSION['session_userID'])
	{
		// do something
				
		$page = "edit.php?id=$id&step=3";
	}
	else
	{
		$_SESSION['msg'] = array("msg" => "Unable to process your request", "title" => "Invalid Request", "link" => "index.php", "legend" => "Home Page");
		$page = "msg.php";
	}	
	
	mysql_close($conn);	
	
	echo "<script type=\"text/javascript\">window.location='$page';</script>";	
}



// process step 3	
else if(($action == "processStep3")&&($_SERVER["REQUEST_METHOD"] == "POST")&&($userRank == 1))
{	
	$functions = new functions;
	
	$conn = $functions->dbConnect();
	  	
  	$id = mysql_real_escape_string($_GET['id']);

  	$result = mysql_query("SELECT * FROM documents WHERE id = '$id'");	
	$row = mysql_fetch_assoc($result);	
	if($row['userID'] == $_SESSION['session_userID'])
	{
		// do something
				
		$page = "edit.php?id=$id&step=4";
	}
	else
	{
		$_SESSION['msg'] = array("msg" => "Unable to process your request", "title" => "Invalid Request", "link" => "index.php", "legend" => "Home Page");
		$page = "msg.php";
	}	
	
	mysql_close($conn);	
	
	echo "<script type=\"text/javascript\">window.location='$page';</script>";	
}



// process step 4	
else if(($action == "processStep4")&&($_SERVER["REQUEST_METHOD"] == "POST")&&($userRank == 1))
{		
	echo "<script type=\"text/javascript\">window.location='index.php';</script>";	
}

// delete document
else if(($action == "delete")&&($userRank == 1))
{
	$functions = new functions;
	
	$conn = $functions->dbConnect();
	  	
  	$id = mysql_real_escape_string($_GET['id']);

  	$result = mysql_query("SELECT * FROM documents WHERE id = '$id'");	
	$row = mysql_fetch_assoc($result);	
	if($row['userID'] == $_SESSION['session_userID'])
	{
		mysql_query("DELETE FROM documents WHERE id='$id'");
		$page = "index";
	}
	else
	{
		$_SESSION['msg'] = array("msg" => "Unable to process your request", "title" => "Invalid Request", "link" => "index.php", "legend" => "Home Page");
		$page = "msg";
	}	
	
	mysql_close($conn);	
	
	echo "<script type=\"text/javascript\">window.location='$page.php';</script>";
}



// logout
else if(($action == "logout")&&($userRank == 1))
{
	@session_destroy();
	
	echo "<script type=\"text/javascript\">window.location='index.php';</script>";
}


// UNKNOWN REQUEST
else
{
	$_SESSION['msg'] = array("msg" => "Your request could not be processed", "title" => "Invalid Request", "link" => "index.php", "legend" => "Home Page");
	echo "<script type=\"text/javascript\">window.location='msg.php';</script>";
}
