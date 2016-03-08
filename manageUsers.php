<?php
$isSuperUserPage = true; //used by 'header.php' to validate an admin user's logon session

$pageTitle = 'Manage Users';
include_once("includes/header.php"); 

echo '<div id="containsUserManager">';

require_once('includes/dbc_connect.php'); //connect to the database

if (isset($_REQUEST['action']))
{
	if ($_REQUEST['action'] == 'insertUser')
	{
		if (empty($_REQUEST['username']))
		{
			$errors['username'] = true;
		}
		elseif (empty($_REQUEST['password']))
		{
			$errors['password'] = true;
		}
		elseif (isset($_REQUEST['isAdmin']) && $_REQUEST['isAdmin'] != '1') //will only show correctly up if it equals 1
		{
			$errors['isAdmin'] = true;
		}
		else
		{
			//check to see if username already exists
			$results_username = @mysqli_query($dbc, 'SELECT user_id from users where username = "' . mysqli_real_escape_string($dbc, trim($_REQUEST['username'])) . '"');
			if ($results_username)
			{
				if (mysqli_num_rows($results_username) > 0)
				{
					echo '<div class="error">Username already exists.</div>';
					$errors['username'] = true;
				}
				else
				{
					//perform user insert
					$query_string = 'INSERT INTO users values (null, "'
							.mysqli_real_escape_string($dbc, trim($_REQUEST['username']))
							.'", SHA1("'.mysqli_real_escape_string($dbc, trim($_REQUEST['password'])).'"), "'
							.mysqli_real_escape_string($dbc, trim($_REQUEST['first_name'])).'", DEFAULT, '
							.((isset($_REQUEST['isAdmin']) && $_REQUEST['isAdmin'] == '1') ? '1' : '0')
							.', CURRENT_TIMESTAMP, 0)';
					if (@mysqli_query($dbc, $query_string))
					{
						echo '<div>New user inserted, ID ' . mysqli_insert_id($dbc) . '.</div>';
						
						//once user is inserted, remove from values so the form will NOT be sticky
						unset($_REQUEST['username']);
						unset($_REQUEST['first_name']);
						unset($_REQUEST['password']);
						unset($_REQUEST['isAdmin']);
					}
					else
					{
						echo '<div class="error">Database connection to check for users not valid.</div>';
					}
				}
			}
			else
			{
				echo '<div class="error">Database connection to check for users not valid.</div>';
			}
		}
	}
	elseif (isset($_REQUEST['user_id']) && ctype_digit($_REQUEST['user_id']))
	{
		if ($_REQUEST['action'] == 'confirmPurge')
		{
			$results_username = @mysqli_query($dbc, 'select username from users where user_id = ' . mysqli_real_escape_string($dbc, trim($_REQUEST['user_id'])));
			if($results_username)
			{
				$row = mysqli_fetch_array($results_username, MYSQLI_ASSOC);
				echo '<div><h3><a class="confirm" href="manageUsers.php?action=purge&user_id=' . mysqli_real_escape_string($dbc, trim($_REQUEST['user_id'])) . '">Click here to confirm purge of all monsters created by ' . $row['username'] . '</a></h3></div>';
			}
		}
		elseif ($_REQUEST['action'] == 'purge')
		{
			
			//uses escape_string() and trim(), even though ctype_digit has already made this fairly secure. This may be unncessary, but a little more security is better than a little less.
			if (@mysqli_query($dbc, 'update monsters set isDeleted = 1 where user_id = ' . mysqli_real_escape_string($dbc, trim($_REQUEST['user_id']))))
			{
				if (mysqli_affected_rows($dbc) >= 1)
					echo '<div>Monsters Purged where ID ' . mysqli_real_escape_string($dbc, trim($_REQUEST['user_id'])) . '.</div>';
				else
					echo '<div class="error">No monsters existed for ID ' . mysqli_real_escape_string($dbc, trim($_REQUEST['user_id'])) . '.</div>';
			}
			else
			{
				echo '<div class="error">Purge not successful.</div>';
			}
		}
		elseif ($_REQUEST['action'] == 'confirmDelete')
		{
			$results_username = @mysqli_query($dbc, 'select username, first_name from users where user_id = ' . mysqli_real_escape_string($dbc, trim($_REQUEST['user_id'])));
			if($results_username)
			{
				$row = mysqli_fetch_array($results_username, MYSQLI_ASSOC);
				echo '<div><h3><a  style="color:gold;" href="manageUsers.php?action=delete&user_id=' . mysqli_real_escape_string($dbc, trim($_REQUEST['user_id'])) . '">Click here to confirm deletion of ' . $row['username'] . ' (' . $row['first_name'] . ')</a></h3></div>';
			}
		}
		elseif ($_REQUEST['action'] == 'delete')
		{
			
			//uses escape_string() and trim(), even though ctype_digit has already made this fairly secure. This may be unncessary, but a little more security is better than a little less.
			if (@mysqli_query($dbc, 'update users set isDeleted = 1 where user_id = ' . mysqli_real_escape_string($dbc, trim($_REQUEST['user_id']))))
			{
				if (mysqli_affected_rows($dbc) == 1)
					echo '<div>User deleted, ID ' . mysqli_real_escape_string($dbc, trim($_REQUEST['user_id'])) . '.</div>';
				else
					echo '<div class="error">User ID did not exist in database.</div>';
			}
			else
			{
				echo '<div class="error">Delete not successful.</div>';
			}
		}
		elseif ($_REQUEST['action'] == 'toAdmin')
		{
			if (@mysqli_query($dbc, 'update users set isAdmin = 1 where user_id = ' . mysqli_real_escape_string($dbc, trim($_REQUEST['user_id']))))
			{
				if (mysqli_affected_rows($dbc) == 1)
					echo '<div>User ID ' . $_REQUEST['user_id'] . ' promoted to admin.</div>';
				else
					echo '<div class="error">User update was not successful.</div>';
			}
			else
			{
				echo '<div class="error">User permissions change error.</div>';
			}
		}
		elseif ($_REQUEST['action'] == 'toUser')
		{
			if (@mysqli_query($dbc, 'update users set isAdmin = 0 where user_id = ' . mysqli_real_escape_string($dbc, trim($_REQUEST['user_id']))))
			{
				if (mysqli_affected_rows($dbc) == 1)
					echo '<div>User ID ' . $_REQUEST['user_id'] . ' demoted to user.</div>';
				else
					echo '<div class="error">User update was not successful.</div>';
			}
			else
			{
				echo '<div class="error">User permissions change error.</div>';
			}
		}
		else
		{
			echo '<div class="error">Invalid user operation.</div>';
			//echo '<div style="debug">Invalid user action = '.$_REQUEST['action'].'</div>';
		}
	}
	else
	{
		echo '<div class="error">Invalid user operation.</div>';
		//echo '<div style="debug">Invalid user id = '.$_REQUEST['user_id'].'</div>';
	}
}

$query_string = 'SELECT	* from users WHERE isDeleted = 0 ORDER BY isAdmin DESC, username';
$result_users = @mysqli_query($dbc, $query_string);
if ($result_users) //if query ran OK
{
	echo '<table class="tableDisplay" style="min-width:600px;">'."\n";
	echo '<tr><th>User ID</th><th>Username</th><th>Name</th><th>Admin</th><th>Manage</th><th>Created Monsters</tr>';
	
	$evenRow = false; //to alternate background on rows
	while ($row = mysqli_fetch_array($result_users, MYSQLI_ASSOC))
	{
		echo '<tr>';
		echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '>'.$row['user_id'].'</td>';
		echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '>'.$row['username'].'</td>';
		echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '>'.$row['first_name'].'</td>';
		echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '>'.(($row['isAdmin'] > 0) ? (($row['isAdmin'] == 2) ? 'Superuser' : 'Admin') : ' ') . '</td>';
		echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '><a href="manageUsers.php?action=confirmDelete&user_id=' . $row['user_id'] . '">Delete User</a>'.(($row['isAdmin'] != 2) ? (' - <a href="manageUsers.php?action=' . (($row['isAdmin'] == '1') ? 'toUser' : 'toAdmin') . '&user_id=' . $row['user_id'] . '">' . (($row['isAdmin'] == '1') ? 'Demote to User' : 'Promote to Admin') . '</a>') : '' ).'</td>';
		echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '>';
		if ($row['isAdmin'] >= 1)
		{
			$results_count = @mysqli_query($dbc, 'SELECT COUNT(*) FROM monsters WHERE isDeleted = 0 AND user_id = ' . $row['user_id']);
			if ($results_count)
			{
				$count = mysqli_fetch_row($results_count);
				echo '<a href="searchResults.php?submitSearch=true&user_id=' . $row['user_id'] . '">Show Created (' . $count[0] . ')</a>';
				if ($count[0] > 0)
				{
					echo ' - <a href="manageUsers.php?action=confirmPurge&user_id=' . $row['user_id'] . '">Purge All</a>';
				}
			}
			else
				echo '<span class="error">SQL Error</span>';
		}
		echo '</td>';
		echo '</tr>';
		$evenRow = !$evenRow;
	} //loop results rows
?>

<tr>
<td <?php echo (($evenRow) ? 'class="evenRow"' : '' ); ?> style="vertical-align:middle;">New User:</td>
<form action="manageUsers.php" method="post">
	<input type="hidden" name="action" value="insertUser" />
	<td <?php echo (($evenRow) ? 'class="evenRow"' : '' ); ?> style="vertical-align:middle;"><input type="text" name="username" size="10" maxlength="25" <?php echo ((isset($errors['username'])) ? ' class="errorInput" ' : ''); ?> style="font-size:0.75em; background-color:<?php echo (($evenRow) ? '#c0c0a0;' : '#c9c6b8' ); ?>;" value="<?php echo ((isset($_REQUEST['username'])) ? $_REQUEST['username'] : ''); ?>" /></td>
	<td <?php echo (($evenRow) ? 'class="evenRow"' : '' ); ?> style="vertical-align:middle;"><input type="text" name="first_name" size="10" maxlength="30"<?php echo ((isset($errors['first_name'])) ? ' class="errorInput" ' : ''); ?> style="font-size:0.75em; background-color:<?php echo (($evenRow) ? '#c0c0a0;' : '#c9c6b8' ); ?>;" value="<?php echo ((isset($_REQUEST['first_name'])) ? $_REQUEST['first_name'] : ''); ?>" /></td>
	<td <?php echo (($evenRow) ? 'class="evenRow"' : '' ); ?> style="font-size:0.8em; vertical-align:middle;"><input type="checkbox" name="isAdmin" id="isAdmin" <?php echo ((isset($_REQUEST['isAdmin']) && $_REQUEST['isAdmin'] == '1') ? 'checked' : ''); ?> value="1"<?php echo ((isset($errors['isAdmin'])) ? ' class="errorInput" ' : ''); ?> /><label for="isAdmin">Admin?</label></td>
	<td <?php echo (($evenRow) ? 'class="evenRow"' : '' ); ?> style="vertical-align:middle;">Password: <input type="text" name="password" size="10" maxlength="25"<?php echo ((isset($errors['password'])) ? ' class="errorInput" ' : ''); ?> style="font-size:0.75em; background-color:<?php echo (($evenRow) ? '#c0c0a0;' : '#c9c6b8' ); ?>;" value="<?php echo ((isset($_REQUEST['password'])) ? $_REQUEST['password'] : ''); ?>" />
	<input type="submit" name="submitted" class="styledButton" value="Insert User" style="font-size:0.75em;" /></td>
</form>
</tr>

<?php
	echo '</table>';
}
else
{
	echo '<div class="error">Database error retrieving users.</div>';
}
mysqli_close($dbc);

echo '</div>';

include_once("includes/footer.php"); ?>