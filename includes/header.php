<?php
session_start();

if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] >= 1) //includes admins and the superuser
{
	$isUserAdmin = true;
}
else
{
	$isUserAdmin = false;
	if (isset($isAdminPage) && $isAdminPage)
	{
		include('includes/absoluteURL.php');
		header('Location: ' . absoluteURL('search.php?message=accessError'));
		exit();
	}
}

//check if super-user needed, regardless if user is a regular-strength admin or not
if (isset($isSuperUserPage) && $isSuperUserPage && $_SESSION['isAdmin'] != 2)
{
	include('includes/absoluteURL.php');
	header('Location: ' . absoluteURL('search.php?message=accessError'));
	exit();
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo (isset($pageTitle) ? $pageTitle : 'NoTitle'); ?></title>
<link rel="shortcut icon" type="image/x-icon" href="includes/favicon.ico">
<link rel="stylesheet" href="includes/style.php" type="text/css" media="screen" />
</head>

<body>
<div id="containerPage">
<div id="insideContainerPage">

<div id="boxHeader">
<h1><?php echo (isset($pageTitle) ? $pageTitle : 'NoTitle'); ?></h1>

<div id="boxLogin"><?php
if (isset($_SESSION['username']))
	echo '<div><a href="logout.php">logout - ' . $_SESSION['username'] . '</a></div>';
else
	echo '<div><a href="login.php">login</a></div>';
?></div>

</div> <!-- boxHeader -->


<div id="containerNavBody">
<div id="boxNav">
<ul><?php
if ($isUserAdmin)
{ ?> 
<li><h4>Administration</h4></li>
<li><a href="editMonster.php?monsterToEdit=new">New Monster</a></li><?php
if ($_SESSION['isAdmin'] == 2) //super-user
{ ?>
<li><h4>Root Operations</h4></li>
<li><a href="manageUsers.php">Manage Users</a></li>
<li><a href="purgeTestMonsters.php">Purge "Test"</a></li> <?php
}
echo '<li><h4>User Operations</h4></li>';
} ?> 
<li><a href="search.php">Search</a></li>
<li><a href="about.php">About</a></li>
<li><a href="to_do.php">To-Do List</a></li>
</ul>
</div> <!-- boxNav -->

<div id="boxBody"><?php
if (isset($_REQUEST['message']))
{
	if ($_REQUEST['message'] == 'logon')
	{
		if (isset($_SESSION['first_name']))
			echo '<h2 class="messageHeader">You have been logged on, '.$_SESSION['first_name'].'.</h2>';
	}
	elseif ($_REQUEST['message'] == 'logout')
	{
		echo '<h2 class="messageHeader">You have been logged out.</h2>';
	}
	elseif ($_REQUEST['message'] == 'accessError')
	{
		echo '<h2 class="messageHeader"><span class="error">Administrator access required.</span></h2>';
	}
	elseif ($_REQUEST['message'] == 'welcome')
	{
		echo '<p>This database of monsters indexes a wide range of potential opponents that can be used in the Dungeons and Dragons game (&copy; Wizards of the Coast). Monsters can be searched by Keyword tags or by typing specific terms.</p>';
		echo '<p>This detailed search engine will allow you to find monsters that fit specefic themes in your game, or to find opponents across all books to match the appropriate power level of your player characters. ';
		echo 'You can then use the monsters headers found to reference the full statistics in the appropriate books.</p>';
		echo '<p class="bottomSeparator">An admistrative user can logon to add or edit monster information.</p>';
	}
}
?>