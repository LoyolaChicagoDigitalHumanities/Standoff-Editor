<?php

require 'requires/session.php';
require_once 'requires/template.php';
require_once 'requires/functions.php';

$engine = new template;
$functions = new functions;



if($_SESSION['session_rank'] == 1)
{		
	$userHasAccess = $functions->hasAccess($_GET['id'], $_SESSION['session_userID']);
	
	$conn = $functions->dbConnect();
	
	$thisFullname = $_SESSION['session_username'];
	$userID = $_SESSION['session_userID'];
	$step = $_GET['step']; 	  	
  	
  	$docID = mysql_real_escape_string($_GET['id']);

  	$result = mysql_query("SELECT * FROM documents WHERE id = '$docID'");	
	$row = mysql_fetch_assoc($result);	
	if($userHasAccess)
	{
		$thisDocumentID = $row['id'];
		
		if((empty($step))||(!is_numeric($step))||($step<1)||($step>5))
		{
			$step = 1;
		}
		
		$content = $row['content'];
		$markedContent = $row['markedContent'];
		
		$e1 = "Inactive"; 
		$e2 = "Inactive"; 
		$e3 = "Inactive"; 
		$e4 = "Inactive"; 
		
		
		$_SESSION['session_docID'] = $docID;
		
		// STEP 1
		if($step == 1)
		{
			$e1 = "Active";
			$prevStep = 1;
			
			$static_value = array ($content);	  
			$static_name  = array ("{CONTENT}");			
			$stepContent = $engine->load_template("html/step1.html");
			$stepContent = $engine->replace_static($static_name, $static_value,  $stepContent);			
		}
		
		
		
		// STEP 2		
		else if($step == 2)
		{
			$e2 = "Active";
			$prevStep = 1;
						
			$static_value = array ($markedContent);	  
			$static_name  = array ("{CONTENT}");			
			$stepContent = $engine->load_template("html/step2.html");
			$stepContent = $engine->replace_static($static_name, $static_value,  $stepContent);
		}	
		
		
		
		// STEP 3		
		else if($step == 3)
		{
			$e3 = "Active";
			$prevStep = 2;
			
			$stepContent = $engine->load_template("html/step3.html");
		}	



		// STEP 4		
		else
		{
			$e4 = "Active";
			$prevStep = 3;
			
			$docID = $_SESSION['session_docID'];
			
			$userOwnsDocument = $functions->ownsDocument($docID, $_SESSION['session_userID']);	
				
			$conn = $functions->dbConnect();
			
			$userIDsArray = array();
			
		    $result = mysql_query("SELECT userID FROM documents WHERE id = '$docID'");	
			$row = mysql_fetch_assoc($result);
			$ownerID = $row['userID'];
			$userIDsArray[] = $ownerID;
						
			$result = mysql_query("SELECT * FROM access WHERE docID = '$docID'");				
			while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$userIDsArray[] = $row['userID'];
			}
			

			if(count($userIDsArray) > 0)
			{
				foreach($userIDsArray as $collectedUserID)
				{
				    $result = mysql_query("SELECT name FROM users WHERE id = '$collectedUserID'");	
					$row = mysql_fetch_assoc($result);
					$collectedUserName = $row['name'];
					
					if(($collectedUserID == $ownerID)||(!$userOwnsDocument))
					{
						$disableThis = "disabled";
					}
					else
					{
						$disableThis = "";
					}
										
					$static_value = array ($collectedUserID, $docID, $collectedUserName, $disableThis);	  
					$static_name  = array ("{USER_ID}", "{DOC_ID}", "{NAME}", "{DISABLED}");			
					$thisOneUser = $engine->load_template("html/user.html");
					$list .= $engine->replace_static($static_name, $static_value,  $thisOneUser);					
				}
			}
			else
			{
				$list = "No Users Found";
			}						
			
			$static_value = array ($list);	  
			$static_name  = array ("{LIST}");			
			$stepContent = $engine->load_template("html/step4.html");
			$stepContent = $engine->replace_static($static_name, $static_value,  $stepContent);
		}
		

		
		$static_value = array ($e1, $e2, $e3, $e4, $row['name'], $docID);	  
		$static_name  = array ("{e_1}","{e_2}","{e_3}","{e_4}", "{TITLE}", "{ID}");		
		$steps = $engine->load_template("html/steps.html");	
		$steps = $engine->replace_static($static_name, $static_value, $steps);
		
		$static_value = array ($stepContent, $thisDocumentID, $step, $prevStep);	  
		$static_name  = array ("{FORM}", "{ID}", "{STEP}", "{PREV}");			
		$step = $engine->load_template("html/stepHolder.html");	
		$step = $engine->replace_static($static_name, $static_value,  $step);
		
		$thisContent = $steps . $step;					
		
		$static_value = array ($thisFullname, $thisContent);	  
		$static_name  = array ("{FULL_NAME}","{CONTENT}");
		$template = $engine->load_template("html/layout.html");
		$template = $engine->replace_static($static_name, $static_value,  $template);
		
		echo $template;	
	}
	else	
	{
		echo "<script type=\"text/javascript\">window.location='index.php';</script>";
	}	
}
else
{
	echo "<script type=\"text/javascript\">window.location='index.php';</script>";
}





?>