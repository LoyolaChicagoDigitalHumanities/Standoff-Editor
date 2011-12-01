<?php

require 'facebook.php';
require_once 'functions.php';

session_start(); 

if (!session_is_registered('session_rank')) 
{
	session_register('session_userID');
	session_register('session_username');
	session_register('session_rank');
	session_register('msg');
	session_register('session_docID');
	session_register('session_text');
	
	$_SESSION['session_text'] = "";	
	$_SESSION['session_rank'] = 0;
}

  
  
$functions = new functions;
$facebook = new Facebook($functions->getFacebookInfo());
$session = $facebook->getSession();
$me = null;
if ($session)
{
	try
	{
		$uid = $facebook->getUser();
		$me = $facebook->api('/me');
	} 
	catch (FacebookApiException $e)
	{
		error_log($e);
		$facebook->setSession(null);
	}
}


if($me)
{
    if(!$_SESSION['session_userID'])
    {
	    $userInfo = $functions->getUserInfoByFacebookID($me[id]);
	    $_SESSION['session_rank'] = 1;        	
		$_SESSION['session_userID'] = $userInfo['id'];
		$_SESSION['session_username'] = $userInfo['name'];
		
		if(empty($userInfo['name']))
		$_SESSION['session_username'] = $me[name];
	}
	$logoutLink = $facebook->getLogoutUrl(array('next'=>'http://standoffmarkup.org/do.php?action=redirect&party=facebook&type=logout'));	$displayUserSettings = "none";
	$userType = "foreign";
}
else
{
  $logoutLink = "do.php?action=logout";
  $displayUserSettings = "block";
  $userType = "citizen";
}

?>