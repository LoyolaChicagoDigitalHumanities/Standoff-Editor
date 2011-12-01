<?php

require 'requires/session.php';
require_once 'requires/template.php';




if(($_SESSION['session_rank'] == 0)&&(isset($_SERVER['HTTP_X_REQUESTED_WITH']))&&(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
{		
	$engine = new template;
	
	$thisURL = $facebook->getLoginUrl(array('next'=>'http://standoffmarkup.org/do.php?action=redirect&type=login&party=facebook'));	
	
	$static_value = array ($thisURL);	  
	$static_name  = array ("{LINK}");		
	$template = $engine->load_template("html/signup.html");
	$template = $engine->replace_static($static_name, $static_value, $template);		
	echo $template;
}
else
{
	echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php\">";
}


?>