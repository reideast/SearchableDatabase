<?php
$isAdminPage = true; //used by 'header.php' to validate an admin user's logon session

require_once('includes/dbc_connect.php'); //connect to the database

//confirm=true&=
if (isset($_REQUEST['monster_id']) && ctype_digit($_REQUEST['monster_id'])) //ctype_digit() precludes need for testing for negative, since '-' fails ctype_digit
{
	$monsterIDToDelete = mysqli_real_escape_string($dbc, trim($_REQUEST['monster_id']));
	$results_name = @mysqli_query($dbc, 'SELECT monster_name, SHA1(monster_name) FROM monsters WHERE isDeleted = 0 AND monster_id = "' . $monsterIDToDelete . '"');
	if ($results_name)
	{
		$row = mysqli_fetch_row($results_name);
		
		if (isset($_REQUEST['confirm']) && $_REQUEST['confirm'] == 'true')
		{
			$pageTitle = 'Delete Monster - ' . $row[0];
			include_once("includes/header.php");
			echo '<h2><a class="confirm" href="deleteMonster.php?delete=' . $row[1] . '&monster_id=' . $monsterIDToDelete . '">Confirm deletion of monster - ' . $row[0] . '?</a></h2>';
		}
		else if (isset($_REQUEST['delete']) && $_REQUEST['delete'] == $row[1]) //compare SHA1(monster_name) from database to _GET variable
		{
			$pageTitle = 'Monster Deleted - ' . $row[0];
			include_once("includes/header.php"); //to validate an "admin" user is logged in, include header before we do the actual delete
			@mysqli_query($dbc, 'UPDATE monsters SET isDeleted = 1 WHERE monster_id = ' . $monsterIDToDelete);
			if (mysqli_affected_rows($dbc) == 1)
			{
				echo '<h2>Monster Successfully Deleted - ' . $row[0] . '</h2>';
			}
			else
			{
				echo '<h2 class="error">Error deleting monster from database.</h2>';
			}
		}
		else
		{
			$pageTitle = 'Delete Error';
			include_once("includes/header.php");
			echo '<h2 class="error">Delete Error: Incorrect action or delete code specified.</h2>';
		}
	}
	else
	{
		$pageTitle = 'Monster Not Found';
		include_once("includes/header.php");
		echo '<h2 class="error">Invalid monster ID, not found.</h2>';
		
	}

}
else
{
	$pageTitle = 'Delete Monster';
	include_once("includes/header.php"); 
	echo '<h2 class="error">Invalid monster ID specified.</h2>';
}


mysqli_close($dbc);

include_once("includes/footer.php"); ?>