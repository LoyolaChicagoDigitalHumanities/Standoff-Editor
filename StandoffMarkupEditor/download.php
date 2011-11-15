<?php

require 'requires/session.php';
require_once 'requires/template.php';
require_once 'requires/functions.php';

$userRank = $_SESSION['session_rank'];


if($userRank == 1)
{	
	$functions = new functions;
	
	$userHasAccess = $functions->hasAccess($_SESSION['session_docID'], $_SESSION['session_userID']);
	
	$conn = $functions->dbConnect();
	
	$docID = mysql_real_escape_string($_SESSION['session_docID']);	

  	$result = mysql_query("SELECT * FROM documents WHERE id = '$docID'");	
	$row = mysql_fetch_assoc($result);	
	if($userHasAccess)
	{		
		$thidDocName = $row['name'];
		$date = date("YmdHis"); 
		if($_GET['type'] == "plain")
		{
			$content = $row['content'];
			
			header('Pragma: no-cache');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=\"$thidDocName" . "_" . "$date$docID.txt\"");
			header('Content-Transfer-Encoding: binary');
			ob_clean();
			flush();
			echo $content;
		}
		else if($_GET['type'] == "xml")
		{
			$result = mysql_query("SELECT * FROM marks WHERE docID = '$docID' ORDER BY id ASC");				
			while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$attributes = "";
				$spanCounter = 1;
				$thisSpanID = $row['spanID'];
	  			$result2 = mysql_query("SELECT * FROM attributes WHERE docID = '$docID' AND spanID = '$thisSpanID'");	
				while($row2 = mysql_fetch_array($result2, MYSQL_ASSOC))
				{
					$attributes .= "\n	<attr$spanCounter>" . $row2['content'] . "</attr$spanCounter>";
					$spanCounter++;
				}					
			
$contents .= "
<node>
	<sp>" . $row['sp'] . "</sp>
	<ep>" . $row['ep'] . "</ep>
	<ns>" . $row['ns'] . "</ns>
	<tag>" . $row['va'] . "</tag>
	<text>" . $row['text'] . "</text>
	<url>" . $row['url'] . "</url>$attributes
</node>
";
			}	
			
			header('Pragma: no-cache');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-type: text/xml');
			header("Content-Disposition: attachment; filename=\"$thidDocName" . "_" . "$date$docID.xml\"");
			header('Content-Transfer-Encoding: binary');
			ob_clean();
			flush();
echo "<?xml version=\"1.0\"?>
<nodes>
	$contents
</nodes>";		
		}
		else
		{
			
			$result = mysql_query("SELECT * FROM marks WHERE docID = '$docID' ORDER BY id ASC");				
			while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{								
				$posts = array();
				
				$thisSP = $row['sp'];
				$thisEP = $row['ep'];
				$thisNS = $row['ns'];
				$thisVA = $row['va'];
				$thisURL = $row['url'];
				$thisTXT = $row['txt'];
								
				$posts['sp'] = $thisSP;
				$posts['ep'] = $thisEP;
				$posts['ns'] = $thisNS;
				$posts['tag'] = $thisVA;
				$posts['url'] = $thisURL;
				$posts['text'] = $thisTXT;
				
				$spanCounter = 1;
				$thisSpanID = $row['spanID'];
	  			$result2 = mysql_query("SELECT * FROM attributes WHERE docID = '$docID' AND spanID = '$thisSpanID'");	
				while($row2 = mysql_fetch_array($result2, MYSQL_ASSOC))
				{
					$posts["attr$spanCounter"] = $row2['content'];
					$spanCounter++;					
				}
				
				$response[] = $posts;			
			}	
			
			$contents = json_encode($response);
			
			header('Pragma: no-cache');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-type: application/json');
			header("Content-Disposition: attachment; filename=\"$thidDocName" . "_" . "$date$docID.json\"");
			header('Content-Transfer-Encoding: binary');
			ob_clean();
			flush();
			echo $contents;		
		}		
				
	}
	
	mysql_close($conn);	
}

?>