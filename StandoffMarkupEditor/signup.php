<?php

require 'requires/session.php';
require_once 'requires/template.php';




if(($_SESSION['session_rank'] == 0)&&(isset($_SERVER['HTTP_X_REQUESTED_WITH']))&&(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
{		
	$engine = new template;
	
	$template = $engine->load_template("html/signup.html");	
	echo $template;
}
else
{
	echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php\">";
}


?>