<?php

class functions
{				 




	/***************************************
	*
	* Returns Facebook app info
	*
	***************************************/
	function getFacebookInfo()
	{		
		return array(
		  'appId'  => '286901421353952',
		  'secret' => '******************************',
		  'cookie' => true,
		);
	}	
	
	
	
	
	
	/***************************************
	*
	* connect to database
	*
	***************************************/
	function dbConnect()
	{
		$conn = mysql_connect("********************", "********************", "********************");
		mysql_select_db("********************");
		
		return $conn;
	}		
	





	/***************************************
	*
	* generate a random password
	*
	***************************************/
	function generatePassword()
	{
	    $collection = "ABCDGHKLMNQRSTUWXYZabcdefghkmnpqrstwxyz23456789"; 
	    srand((double)microtime()*1000000); 
	    $i = 0; 
	    $password = ''; 
	
	    while ($i < 10)
	    { 
	        $num = rand() % 47; 
	        $password = $password .substr($collection, $num, 1); 
	        $i++; 
	    } 
		
		return $password;
	}	
	



	/***************************************
	*
	* set a token for the given user
	*
	***************************************/
	function setToken($userID)
	{
	    $collection = "ABCDGHKLMNQRSTUWXYZabcdefghkmnpqrstwxyz23456789"; 
	    srand((double)microtime()*1000000); 
	    $i = 0; 
	    $newToken = ''; 
	
	    while ($i < 40)
	    { 
	        $num = rand() % 47; 
	        $newToken = $newToken .substr($collection, $num, 1); 
	        $i++; 
	    } 
	    
	    		
		$conn = $this->dbConnect();
		
		$userID = mysql_real_escape_string($userID);
		
		mysql_query("UPDATE tokens set status = '1' WHERE userID = '$userID'");
		mysql_query("INSERT INTO `tokens` (`userID` , `token`) VALUES ('$userID', '$newToken')");
		
		mysql_close($conn);
		
		return $newToken;
	}	
	
	
	
	
	
	
	
	/***************************************
	*
	* uses the given Facebook id to return
	* user info
	*
	***************************************/	
	function getUserInfoByFacebookID($fbID)
	{
		$conn = $this->dbConnect();
		
		$result = mysql_query("SELECT userID FROM facebook WHERE fbID = '$fbID'");	
		$row = mysql_fetch_row($result);		
		$userID = $row[0];
		
		
		$result = mysql_query("SELECT * FROM users WHERE id = '$userID'");	
		$usreInfoArray = mysql_fetch_assoc($result);		
		
		mysql_close($conn);
		
		return $usreInfoArray;
	}
	
	
	




	
	/***************************************
	*
	* register Facebook users if not already
	* registered
	*
	***************************************/	
	function registerFacebookUser($fbID, $email, $name)
	{
		$conn = $this->dbConnect();
		
		$result = mysql_query("SELECT id FROM facebook WHERE fbID = '$fbID'");	
		$row = mysql_fetch_row($result);		
		if(!$row[0])
		{
			mysql_query("INSERT INTO `users` (`email`, `name`) VALUES ('$email', '$name')");
			$newUserID = mysql_insert_id();
			mysql_query("INSERT INTO `facebook` (`fbID`, `userID`) VALUES ('$fbID', '$newUserID')");
		}
		
		mysql_close($conn);		
	}
	
	
	
	
	
	
	/***************************************
	*
	* send message to the given email
	*
	***************************************/
	function sendEmail($thisEmail, $thisName, $thisTitle, $thisMessage)
	{
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= "Content-type: text/html; charset=windows-1252\r\n";
		$headers .= "To: $thisName <$thisEmail>". "\r\n";
		$headers .= 'From: Standoff Markup Editor <noreplay@standoffmarkupeditor.com>' . "\r\n" . 'X-Mailer: PHP/' . phpversion();

		mail($thisEmail, $thisTitle, $thisMessage, $headers);		
	}		
	
	


	/***************************************
	*
	* returns 1 if the given user has access
	* to the given document and 0 otherwhise
	*
	***************************************/	
	function hasAccess($docID, $userID)
	{
		$access = 0;
		
		$conn = $this->dbConnect();
		
		$docID = mysql_real_escape_string($docID);
		$userID = mysql_real_escape_string($userID);
		
		$result = mysql_query("SELECT id FROM access WHERE userID = '$userID' AND docID = '$docID'");	
		$row = mysql_fetch_row($result);						
		if($row[0])		
		$access = 1;
				
		$result = mysql_query("SELECT id FROM documents WHERE userID = '$userID' AND id = '$docID'");	
		$row = mysql_fetch_row($result);						
		if($row[0])		
		$access = 1;		
		
		mysql_close($conn);
		
		return $access;
	}	
	
	
	
	
	
	/***************************************
	*
	* returns 1 if the given user owns
	* the given document and 0 otherwhise
	*
	***************************************/	
	function ownsDocument($docID, $userID)
	{
		$ownership = 0;
		
		$conn = $this->dbConnect();
		
		$docID = mysql_real_escape_string($docID);
		$userID = mysql_real_escape_string($userID);
		
		$result = mysql_query("SELECT userID FROM documents WHERE id = '$docID'");	
		$row = mysql_fetch_row($result);						
		if($row[0] == $userID)		
		$ownership = 1;		
		
		mysql_close($conn);
		
		return $ownership;
	}	
	
	
	
	
	
	
			
	/***************************************
	*
	* returns 1 if the given document is
	* currently being shared and 0 otherwhise
	*
	***************************************/	
	function isShared($docID)
	{
		$shared = 0;
		
		$conn = $this->dbConnect();
		
		$docID = mysql_real_escape_string($docID);
		
		$result = mysql_query("SELECT * FROM access WHERE docID = '$docID'", $conn);
		$totalUsersSharingDoc = mysql_num_rows($result);
		
		if($totalUsersSharingDoc > 0)
		$shared = 1;		
		
		mysql_close($conn);
		
		return $shared;
	}
							
}

?>