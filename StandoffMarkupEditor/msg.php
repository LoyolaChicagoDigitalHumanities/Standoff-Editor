<?php

require 'requires/session.php';
require_once 'requires/template.php';

$engine = new template;

$msg = $_SESSION['msg'];
$_SESSION['msg'] = "";

if(!is_array($msg))
{
	echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php\">";
}
else
{
	$message      = $msg['msg'];
	$title        = $msg['title'];
	$buttonURL    = $msg['link'];
	$buttonLegend = $msg['legend'];
		
	
	$staticValue = array ($title, $message, $buttonURL, $buttonLegend);
	$staticName  = array ("{TITLE}", "{MESSAGE}", "{URL}", "{BUTTON}");
	$template = $engine->load_template("html/msg.html");
	$template = $engine->replace_static($staticName, $staticValue, $template);
	
	echo $template;
}


?>