<?php

require 'requires/session.php';
require_once 'requires/functions.php';
require_once 'requires/template.php';

$action = $_GET['action'];
$userRank = $_SESSION['session_rank'];






// GET CONTENT
if(($action == "getContent")&&(isset($_SERVER['HTTP_X_REQUESTED_WITH']))&&(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
{
	$functions = new functions;

	$userHasAccess = $functions->hasAccess($_SESSION['session_docID'], $_SESSION['session_userID']);
	
	if($userHasAccess)
	{
		$conn = $functions->dbConnect();
		$docID = mysql_real_escape_string($_SESSION['session_docID']);
	  	$result = mysql_query("SELECT * FROM documents WHERE id = '$docID'");	
		$row = mysql_fetch_assoc($result);		
		
		mysql_close($conn);	
		
		echo $row['content'];
	}
}



// GET MARKED CONTENT 
else if(($action == "getMarkedContent")&&(isset($_SERVER['HTTP_X_REQUESTED_WITH']))&&(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
{
	$functions = new functions;

	$userHasAccess = $functions->hasAccess($_SESSION['session_docID'], $_SESSION['session_userID']);
		
	if($userHasAccess)
	{
		$conn = $functions->dbConnect();
		$docID = mysql_real_escape_string($_SESSION['session_docID']);
	  	$result = mysql_query("SELECT * FROM documents WHERE id = '$docID'");	
		$row = mysql_fetch_assoc($result);		
		
		mysql_close($conn);	
		
		echo $row['markedContent'];
	}
	
	
}




// SET MARKED CONTENT 
else if(($action == "setMarkedContent")&&(isset($_SERVER['HTTP_X_REQUESTED_WITH']))&&(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
{
	$functions = new functions;

	$userHasAccess = $functions->hasAccess($_SESSION['session_docID'], $_SESSION['session_userID']);
		
	if($userHasAccess)
	{
		$conn = $functions->dbConnect();
		$docID = mysql_real_escape_string($_SESSION['session_docID']);
		
		$newMarkedContent = $_POST['cleanText'];
		
	  	mysql_query("UPDATE documents set markedContent = '$newMarkedContent' WHERE id='$docID'");
	  	
		mysql_close($conn);			
	}
	
	
}


// GET ALL MARKS OF THE GIVEN DOCUMENT
else if(($action == "getAllMarks")||($action == "removeMark"))
{
	if((isset($_SERVER['HTTP_X_REQUESTED_WITH']))&&(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
	{
		$functions = new functions;
		$engine = new template;
	
		$userHasAccess = $functions->hasAccess($_SESSION['session_docID'], $_SESSION['session_userID']);
		
		if($userHasAccess)
		{
			$counter = 0;
			
			$conn = $functions->dbConnect();
			
			if($action == "removeMark")
			{
				$markID = $_GET['id'];
				
  				$docID = mysql_real_escape_string($_SESSION['session_docID']);	

				mysql_query("DELETE FROM marks WHERE docID = '$docID' AND id = '$markID'");
			}			
			
			$docID = mysql_real_escape_string($_SESSION['session_docID']);
		  	$result = mysql_query("SELECT * FROM marks WHERE docID = '$docID'");			
			while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$thisID = $row['id'];
				$thisSP = $row['sp'];
				$thisEP = $row['ep'];
				$thisContent = $row['content'];
				$thisSpanID = $row['spanID'];
				
				if(strlen($thisContent) > 20)
				{
					$thisContent = substr($thisContent, 0, 20) . '...';
				}
				
				$static_value = array ($thisSP, $thisEP, $thisContent, $thisID, $thisSpanID);	  
				$static_name  = array ("{SP}","{EP}", "{CONTENT}", "{ID}", "{SPAN}");
				$template = $engine->load_template("html/mark.html");
				$toPrint .= $engine->replace_static($static_name, $static_value,  $template);
				
				$counter++;
			}			
			
			mysql_close($conn);	
		}
		
		if($counter < 1)
		$toPrint = "No marks found";
		
		echo $toPrint;
	}
}



// CLEAR MARKS
else if(($action == "clearMarks")&&(isset($_SERVER['HTTP_X_REQUESTED_WITH']))&&(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
{
	$functions = new functions;
	
	$userHasAccess = $functions->hasAccess($_SESSION['session_docID'], $_SESSION['session_userID']);	
	
	$conn = $functions->dbConnect();
	  	
  	$docID = mysql_real_escape_string($_SESSION['session_docID']);

  	$result = mysql_query("SELECT * FROM documents WHERE id = '$docID'");	
	$row = mysql_fetch_assoc($result);	
	if($userHasAccess)
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
	$functions = new functions;
	
	$ns = htmlspecialchars($_POST['ns']);
	$tag = htmlspecialchars($_POST['tag']);
	$attr = htmlspecialchars($_POST['attr']);
	$url = htmlspecialchars($_POST['url']);
	$text = htmlspecialchars($_POST['txt']);
	$sp = htmlspecialchars($_POST['sp']);
	$ep = htmlspecialchars($_POST['ep']);
	$newSpanID = htmlspecialchars($_POST['newSpanID']);
	$selectedText = htmlspecialchars($_POST['selectedText']);
	$markedContet = $_POST['userInput'];
	$selectionLength = htmlspecialchars($_POST['thisLength']);
	
	$userHasAccess = $functions->hasAccess($_SESSION['session_docID'], $_SESSION['session_userID']);	
	
	$conn = $functions->dbConnect();

	$userID = mysql_real_escape_string($_SESSION['session_userID']);	  	
  	$docID = mysql_real_escape_string($_SESSION['session_docID']);	
  	
	if($userHasAccess)
	{
		mysql_query("UPDATE documents set markedContent = '$markedContet' WHERE id='$docID'");
		
		mysql_query("INSERT INTO `marks` (`docID` , `sp` , `ep`, `ns`, `va`, `url`, `text`, `spanID`, `content`) VALUES ('$docID', '$sp', '$ep', '$ns', '$tag', '$url', '$text', '$newSpanID', '$selectedText')");
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
	
	$userHasAccess = $functions->hasAccess($_GET['id'], $_SESSION['session_userID']);	
	
	$conn = $functions->dbConnect();
	
	$id = mysql_real_escape_string($_GET['id']);

  	$result = mysql_query("SELECT * FROM documents WHERE id = '$id'");	
	$row = mysql_fetch_assoc($result);	
	if($userHasAccess)
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
	$id = $_GET['id'];
	
	$functions = new functions;
	
	$userHasAccess = $functions->hasAccess($id, $_SESSION['session_userID']);	

	if($userHasAccess)
	{				
		$page = "edit.php?id=$id&step=3";
	}
	else
	{
		$_SESSION['msg'] = array("msg" => "Unable to process your request", "title" => "Invalid Request", "link" => "index.php", "legend" => "Home Page");
		$page = "msg.php";
	}	
		
	echo "<script type=\"text/javascript\">window.location='$page';</script>";	
}



// process step 3	
else if(($action == "processStep3")&&($_SERVER["REQUEST_METHOD"] == "POST")&&($userRank == 1))
{		
	$id = $_GET['id'];
	
	$functions = new functions;
	
	$userHasAccess = $functions->hasAccess($id, $_SESSION['session_userID']);
	
	if($userHasAccess)
	{	
		$page = "edit.php?id=$id&step=4";
	}
	else
	{
		$_SESSION['msg'] = array("msg" => "Unable to process your request", "title" => "Invalid Request", "link" => "index.php", "legend" => "Home Page");
		$page = "msg.php";
	}	
		
	echo "<script type=\"text/javascript\">window.location='$page';</script>";	
}



// process step 4	
else if(($action == "processStep4")&&($_SERVER["REQUEST_METHOD"] == "POST")&&($userRank == 1))
{		
	$documentID = $_SESSION['session_docID'];
	
	echo "
	<script>
	if (confirm('You have just finished editing this document. Would you like to view your documents folder?')){
	
		window.location = 'index.php';
	}
	else{
		window.location = 'edit.php?id=$documentID';
	}
	</script>
	";
}


// delete document
else if(($action == "delete")&&($userRank == 1))
{
	$id = $_GET['id'];
	
	$functions = new functions;
	
	$userOwnsDocument = $functions->ownsDocument($id, $_SESSION['session_userID']);	
	$userHasAccess = $functions->hasAccess($id, $_SESSION['session_userID']);
	

	if($userOwnsDocument)
	{
		$isThisDocumentShared = $functions->isShared($id);
		
		
		if($isThisDocumentShared)
		{
			$_SESSION['msg'] = array("msg" => "Your request could not be processed because this document is currently being shared with other users. In order to remove this document from your folder, you have to transfer the ownership of document to another user", "title" => "Document is Shared", "link" => "index.php", "legend" => "Home Page");
			$page = "edit.php?id=$id&step=4";		
		}
		else
		{
			$conn = $functions->dbConnect();	
			
			mysql_query("DELETE FROM documents WHERE id='$id'");
			mysql_query("DELETE FROM marks WHERE docID='$id'");
			$page = "index";
			
			mysql_close($conn);
		}
	}
	else
	{
		if($userHasAccess)
		{
			$userID = $_SESSION['session_userID'];
			
			$conn = $functions->dbConnect();
			
			mysql_query("DELETE FROM access WHERE docID='$id' AND userID = '$userID'");
			$page = "index";	
			
			mysql_close($conn);	
		}
		else
		{
			$_SESSION['msg'] = array("msg" => "Unable to process your request", "title" => "Invalid Request", "link" => "index.php", "legend" => "Home Page");
			$page = "msg";
		}
	}	
	
	echo "<script type=\"text/javascript\">window.location='$page.php';</script>";
}



// Show Suggestions
else if(($action == "showSuggestions")&&($_SERVER["REQUEST_METHOD"] == "POST")&&($userRank == 1))
{
	$functions = new functions;
	
	$engine = new template;
		
	$conn = $functions->dbConnect();
		
	$queryString = $_POST['userInput'];
	$docID = $_SESSION['session_docID'];
	$limit = 5;
	$counter = 0;
	
    $result = mysql_query("SELECT userID FROM documents WHERE id = '$docID'");	
	$row = mysql_fetch_assoc($result);	
	$ownerID = $row['userID'];	
	
	$result = mysql_query("SELECT * FROM users WHERE name LIKE '%" . $queryString . "%' OR username LIKE '%" . $queryString . "%' OR email LIKE '%" . $queryString . "%'");				
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$thisUserID = $row['id'];
		$thisUserName = $row['name'];
		
		if($counter < 5)
		{
			if(($thisUserID != $_SESSION['session_userID'])&&($ownerID != $thisUserID)&&($row['status'] == 1))
			{
			    $result2 = mysql_query("SELECT id FROM access WHERE userID = '$thisUserID' AND docID = '$docID'");	
				$row2 = mysql_fetch_assoc($result2);	
				
				if(!$row2['id'])
				{
					$static_value = array ($thisUserID, $thisUserName, $docID);	  
					$static_name  = array ("{USER_ID}","{NAME}", "{DOC_ID}");
					$template = $engine->load_template("html/suggestion.html");
					$toReturn .= $engine->replace_static($static_name, $static_value,  $template);	
					$counter++;
				}
			}
		}
		else
		{
			break;
		}			
	}
	
	mysql_close($conn);
	
	
	if($counter < 1)
	{
		$thisMessage = "No results found";
		
		$static_value = array ($thisMessage);	  
		$static_name  = array ("{MSG}");
		$template = $engine->load_template("html/noSuggestion.html");
		$toReturn = $engine->replace_static($static_name, $static_value,  $template);	
	}
	
	
	echo $toReturn;
}






// share given document with the given user
else if(($action == "shareWithUser")&&($userRank == 1))
{
	$docID = $_GET['docID'];
	$userID = $_GET['userID'];
	
	$functions = new functions;
				
	$userOwnsDocument = $functions->ownsDocument($docID, $_SESSION['session_userID']);
	
	$conn = $functions->dbConnect();	
	
	if($userOwnsDocument)
	{
		$docID = mysql_real_escape_string($docID);
		$userID = mysql_real_escape_string($userID);
		
	  	$result = mysql_query("SELECT * FROM access WHERE userID = '$userID' AND docID = '$docID'");	
		$row = mysql_fetch_assoc($result);	
		if((!$row[0])&&($userID != $_SESSION['session_userID']))
		{
			mysql_query("INSERT INTO `access` (`userID` , `docID`) VALUES ('$userID', '$docID')");
		}
		
		$page = "edit.php?id=$docID&step=4";
	}
	else
	{
		$_SESSION['msg'] = array("msg" => "Unable to process your request", "title" => "Invalid Request", "link" => "index.php", "legend" => "Home Page");
		$page = "msg.php";	
	}
	
	mysql_close($conn);
	
	echo "<script type=\"text/javascript\">window.location='$page';</script>";
}







// Unshare given document with the given user
else if(($action == "unshare")&&($userRank == 1))
{
	$docID = $_GET['docID'];
	$userID = $_GET['userID'];
	
	$functions = new functions;
				
	$userOwnsDocument = $functions->ownsDocument($docID, $_SESSION['session_userID']);
	$userHasAccess = $functions->hasAccess($docID, $userID);
	
	$conn = $functions->dbConnect();	
	
	if($userOwnsDocument)
	{
		mysql_query("DELETE FROM access WHERE docID='$docID' AND userID = '$userID'");
		
		
		$page = "edit.php?id=$docID&step=4";
	}
	else if(($userHasAccess)&&($userID == $_SESSION['session_userID']))
	{
		mysql_query("DELETE FROM access WHERE docID='$docID' AND userID = '$userID'");
		
		
		$page = "edit.php?id=$docID&step=4";	
	}
	else
	{
		$_SESSION['msg'] = array("msg" => "Unable to process your request", "title" => "Invalid Request", "link" => "index.php", "legend" => "Home Page");
		$page = "msg.php";	
	}
	
	mysql_close($conn);
	
	echo "<script type=\"text/javascript\">window.location='$page';</script>";
}





// share given document with the given user
else if(($action == "setOwner")&&($userRank == 1))
{
	$docID = $_GET['docID'];
	$userID = $_GET['userID'];
	
	$functions = new functions;
				
	$userOwnsDocument = $functions->ownsDocument($docID, $_SESSION['session_userID']);
	
	$myID = $_SESSION['session_userID'];
	
	$conn = $functions->dbConnect();	
	
	if($userOwnsDocument)
	{
		$docID = mysql_real_escape_string($docID);
		$userID = mysql_real_escape_string($userID);
		
		mysql_query("DELETE FROM access WHERE docID='$docID' AND userID = '$userID'");
	  	mysql_query("UPDATE documents set userID = '$userID' WHERE id='$docID'");
	  	mysql_query("INSERT INTO `access` (`userID` , `docID`) VALUES ('$myID', '$docID')");
		
		$page = "edit.php?id=$docID&step=4";
	}
	else
	{
		$_SESSION['msg'] = array("msg" => "Unable to process your request", "title" => "Invalid Request", "link" => "index.php", "legend" => "Home Page");
		$page = "msg.php";	
	}
	
	mysql_close($conn);
	
	echo "<script type=\"text/javascript\">window.location='$page';</script>";
}


// Rename the given document
else if(($action == "rename")&&($_SERVER["REQUEST_METHOD"] == "POST")&&($userRank == 1))
{
	$docID = $_GET['id'];
	$newDocName = $_POST['name'];
	$step = $_GET['step'];
	
	$functions = new functions;

	$userHasAccess = $functions->hasAccess($docID, $_SESSION['session_userID']);
	
	$conn = $functions->dbConnect();
	
	$docID = mysql_real_escape_string($docID);
	
	if($userHasAccess)
	{
	  	mysql_query("UPDATE documents set name = '$newDocName' WHERE id='$docID'");
	  			
		$page = "edit.php?id=$docID&step=$step";	
	}
	else
	{
		$_SESSION['msg'] = array("msg" => "Unable to process your request", "title" => "Invalid Request", "link" => "index.php", "legend" => "Home Page");
		$page = "msg.php";		
	}
	
	mysql_close($conn);
	
	echo "<script type=\"text/javascript\">window.location='$page';</script>";
}



// Rest the password
else if(($action == "reset")&&($_SERVER["REQUEST_METHOD"] == "POST")&&($userRank == 1))
{
	$conn = $functions->dbConnect();	
	
	$email = htmlspecialchars($_POST['email']);
	$email = mysql_real_escape_string($email);
	
	$result = mysql_query("SELECT * FROM users WHERE email = '$email'");	
	$row = mysql_fetch_assoc($result);	
	
	if($row['id'])
	{
		
	}
	else
	{
	
	}
	
	mysql_close($conn);
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
