<?php 
session_start();

include('includes/absoluteURL.php');
if (isset($_SESSION['user_id']))
{
	$_SESSION = array(); //clear the whole session array
	session_destroy();
	setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0); //delete the session cookie
	
	header('Location: ' . absoluteURL('login.php?message=logout'));
	exit();
}
else //no proper session set already
{
	header('Location: ' . absoluteURL('index.php'));
	exit();
}
?>