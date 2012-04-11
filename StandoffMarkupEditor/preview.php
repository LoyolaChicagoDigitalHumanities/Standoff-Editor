<?php

require 'requires/session.php';
require_once 'requires/template.php';
require_once 'requires/functions.php';




if(($_SESSION['session_rank'] == 1)&&(isset($_SERVER['HTTP_X_REQUESTED_WITH']))&&(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
{		
	$engine = new template;
	$functions = new functions;
	
	$docID = $_SESSION['session_docID'];
	$step = $_GET['step'];
	
	$conn = $functions->dbConnect();
	
	$docID = mysql_real_escape_string($docID);
	
    $result = mysql_query("SELECT * FROM documents WHERE id = '$docID'");	
	$row = mysql_fetch_assoc($result);	
	
	$shareID = $row['shareID'];
	$nID = $row['id'];
	
	$html = "http://standoffmarkup.org/doc/$shareID/html";
	$xml = "http://standoffmarkup.org/doc/$shareID/xml";
	$json = "http://standoffmarkup.org/doc/$shareID/json";
	$plain = "http://standoffmarkup.org/doc/$shareID/plain";
	
	if($row['public'])
	$select2 = "selected";
	else
	$select1 = "selected";
	
	$thisOptions = "
		<option value='0'$select1>Don't Allow Document Preview</option>
		<option value='1'$select2>Allow Document Preview</option>
	";
	
	$static_value = array ($html, $xml, $json, $plain, $nID, $thisOptions);	  
	$static_name  = array ("{HTML}", "{XML}", "{JSON}", "{TEXT}", "{ID}", "{OPTIONS}");		
	$template = $engine->load_template("html/preview.html");
	$template = $engine->replace_static($static_name, $static_value, $template);	
	echo $template;
}
else
{
	echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php\">";
}


?>