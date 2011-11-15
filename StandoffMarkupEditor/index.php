<?php

require 'requires/session.php';
require_once 'requires/template.php';
require_once 'requires/functions.php';

$engine = new template;

if($_SESSION['session_rank'] > 0)
{
	$_SESSION['session_docID'] = 0;
	
	$thisFullname = $_SESSION['session_username'];
	$userID = $_SESSION['session_userID']; 
	
	$functions = new functions;
	$conn = $functions->dbConnect();
	
	
	
	$result = mysql_query("SELECT * FROM documents WHERE userID = '$userID'");				
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$docsArray[] = $row['id'];
	}
	

	$result = mysql_query("SELECT * FROM access WHERE userID = '$userID'");				
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$docsArray[] = $row['docID'];
	}
	
	if(count($docsArray) > 0)
	{
		arsort($docsArray);
	}
		
	
	$counter = 0;
	while($counter < count($docsArray))
	{
		$thisID = $docsArray[$counter];
		
		$result = mysql_query("SELECT name FROM documents WHERE id = '$thisID'");	
		$row = mysql_fetch_assoc($result);			
		$thisName =  $row['name'];
		
		$list .= "<div class=\"documentsElement\" id=\"e$thisID\" onclick=\"loadDocument('$thisID');\" onmouseover=\"updateBackground('up', 'e$thisID');\" onmouseout=\"updateBackground('down', 'e$thisID');\">$thisName</a></div>
		";
		$counter++;
	}	
	
	if($counter < 1)
	{
		$list = "Oops! your folder is currently empty";
	}
	else
	{
		$list = "$list";
	}
	
	$thisPageName = "My Documents";
	
	$static_value = array ($list);	  
	$static_name  = array ("{LIST}");
	$thisContent = $engine->load_template("html/profile.html");
	$thisContent = $engine->replace_static($static_name, $static_value,  $thisContent);
		
	$static_value = array ($thisFullname, $thisContent, $thisPageName);	  
	$static_name  = array ("{FULL_NAME}","{CONTENT}", "{PAGE_NAME}");
	$template = $engine->load_template("html/layout.html");
	$template = $engine->replace_static($static_name, $static_value,  $template);		
}
else
{
	$template = $engine->load_template("html/home.html");
}



echo $template;

?>