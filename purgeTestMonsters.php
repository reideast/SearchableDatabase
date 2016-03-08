<?php
$isAdminPage = true; //used by 'header.php' to validate an admin user's logon session
$pageTitle = 'Purge Monsters Named "Test"';
include_once("includes/header.php");


include_once('includes/dbc_connect.php');
$result_show_test = @mysqli_query($dbc, 'select monster_id from monsters where lower(monster_name) = "test"');
if ($result_show_test)
{
	if (mysqli_num_rows($result_show_test) == 0)
	{
		echo '<h2>No "Test" Monsters Found</h2>';
	}
	else
	{
		echo '<h2>Monsters That Have Been Deleted</h2>';
		
		while ($row = mysqli_fetch_array($result_show_test, MYSQLI_ASSOC))
		{
			echo '<div>';
				$this_monster_id = $row['monster_id'];
				include('includes/showHeaderByID.php');
			echo '</div>';
		}
		
		//perform delete of keyword references
		@mysqli_query($dbc, 'delete from monsters_keywords where monster_id in (select monster_id from monsters where lower(monster_name) = "test")');
		
		//perform delete of monsters
		@mysqli_query($dbc, 'delete from monsters where lower(monster_name) = "test"');
	}
}


include_once("includes/footer.php"); ?>