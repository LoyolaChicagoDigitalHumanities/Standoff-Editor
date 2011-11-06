<?php

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


?>