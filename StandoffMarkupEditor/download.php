<?php

require 'requires/session.php';
require_once 'requires/template.php';
require_once 'requires/functions.php';

$userRank = $_SESSION['session_rank'];


if($userRank == 1)
{
	$functions = new functions;
	
	$conn = $functions->dbConnect();
  	
  	$docID = mysql_real_escape_string($_SESSION['session_docID']);	

  	$result = mysql_query("SELECT * FROM documents WHERE id = '$docID'");	
	$row = mysql_fetch_assoc($result);	
	if($row['userID'] == $_SESSION['session_userID'])
	{		
		$date = date("YmdHis"); 
		if($_GET['type'] == "plain")
		{
			$contents = $row['content'];
			
			header('Pragma: no-cache');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=\"plain_$date$docID.txt\"");
			header('Content-Transfer-Encoding: binary');
			ob_clean();
			flush();
			echo $contents;
		}
		else
		{
			$result = mysql_query("SELECT * FROM marks WHERE docID = '$docID' ORDER BY id ASC");				
			while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
$contents .= "
<node>
	<sp>" . $row['sp'] . "</sp>
	<ep>" . $row['ep'] . "</ep>
	<ns>" . $row['ns'] . "</ns>
	<attr>" . $row['attr'] . "</attr>
	<va>" . $row['va'] . "</va>
</node>
";
			}	
			
			header('Pragma: no-cache');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=\"nodes_$date$docID.txt\"");
			header('Content-Transfer-Encoding: binary');
			ob_clean();
			flush();
			echo $contents;		
		}
				
	}
	
	mysql_close($conn);	
}

?>