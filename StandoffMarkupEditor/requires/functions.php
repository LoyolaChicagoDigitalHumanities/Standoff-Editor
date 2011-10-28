<?php

class functions
{				 
	
	
	// connect to database	
	function dbConnect()
	{
		$conn = mysql_connect("**************************************************", "**********", "**********");
		mysql_select_db("********************");
		
		return $conn;
	}			
				
}

?>