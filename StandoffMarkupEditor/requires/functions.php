<?php

class functions
{				 


	/***************************************
	*
	* connect to database
	*
	***************************************/
	function dbConnect()
	{
		$conn = mysql_connect("localhost", "standoff_eyad", "eyad");
		mysql_select_db("standoff_editors");
		
		return $conn;
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
	
	
	
	/***************************************
	*
	* Returns an array that contains both 
	* the starting and ending point
	* of the given selection
	*
	***************************************/	
	function getPoints($userInput, $spanID, $selectionLength)
	{
		$toRetun = array();
		
		$toFind = "<span id=\"$spanID";
		
		$sp = strpos($userInput, $toFind);
		$ep = $sp + $selectionLength;
		
		$toRetun[] = $sp;
		$toRetun[] = $ep;
		
		return $toRetun;
	}							
}

?>