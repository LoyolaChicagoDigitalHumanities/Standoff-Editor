<?php

class functions
{				 
	
	
	// connect to database	
	function dbConnect()
	{
		$conn = mysql_connect("localhost", "root", "root");
		mysql_select_db("markup");
		
		return $conn;
	}			
				
}

?>