<?php

class functions
{				 
	
	
	// connect to database	
	function dbConnect()
	{
		$conn = mysql_connect("localhost", "standoff_eyad", "eyad");
		mysql_select_db("standoff_editors");
		
		return $conn;
	}			
				
}

?>