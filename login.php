<?php
session_start();

require_once('includes/dbc_connect.php'); //connect to the database

if (isset($_REQUEST['submitted']))
{
	if (empty($_REQUEST['username']))
	{
		$errors['username'] = 'Please enter your user name.';
	}

	if (empty($_REQUEST['password']))
	{
		$errors['password'] = 'Please enter your password.';
	}
	
	if (!isset($errors))
	{
		//check to see if username/password combo exists
		$results_username = @mysqli_query($dbc, 'SELECT user_id, username, first_name, pref_num_per_page, isAdmin FROM users where isDeleted = 0 AND username = "' . mysqli_real_escape_string($dbc, trim($_REQUEST['username'])) . '" AND password = SHA1("' . mysqli_real_escape_string($dbc, trim($_REQUEST['password'])) . '")');
		if ($results_username)
		{
			if (mysqli_num_rows($results_username) == 1)
			{
				//login was valid
				$row = mysqli_fetch_array($results_username, MYSQLI_ASSOC);
				
				$_SESSION['user_id'] = $row['user_id'];
				$_SESSION['username'] = $row['username'];
				$_SESSION['first_name'] =  $row['first_name'];
				$_SESSION['pref_num_per_page'] =  $row['pref_num_per_page'];
				$_SESSION['isAdmin'] =  $row['isAdmin'];
				
				include('includes/absoluteURL.php');
				header('Location: ' . absoluteURL('search.php?message=logon'));
				exit();
			}
			else
			{
				$errors['username'] = 'Username and password combination not valid.';
				$errors['password'] = '';
			}
		}
		else
		{
			echo '<div class="error">Database connection to check for user not valid.</div>';
		}
	}
}

$pageTitle = 'Login';
include_once("includes/header.php");


if (isset($_SESSION['user_id']) && isset($_SESSION['first_name']))
{
	echo '<h2>You are logged in, ' . $_SESSION['first_name'] . '.</h2>';
}
elseif (isset($errors) || !isset($_REQUEST['submitted']))
{
?>
<form action="login.php" method="post">
<table>
	<tr><td style="text-align:right">Login: <input type="text" name="username" size="10" maxlength="25" tabindex="1" <?php echo ((isset($errors['username'])) ? ' class="errorInput" ' : ''); ?>value="<?php echo ((isset($_REQUEST['username'])) ? $_REQUEST['username'] : ''); ?>" /></td><td><?php echo (isset($errors['username']) ? '<span class="error">'.$errors['username'].'</span>' : ''); ?></td></tr>
	<tr><td style="text-align:right">Password: <input type="password" name="password" size="10" maxlength="25" tabindex="2" <?php echo ((isset($errors['password'])) ? ' class="errorInput" ' : ''); ?>/></td><td><?php echo (isset($errors['password']) ? '<span class="error">'.$errors['password'].'</span>' : ''); ?></td></tr>
	<tr><td style="text-align:center"><input type="submit" name="submitted" class="styledButton" value="Login" tabindex="3" style="font-size:1em;" /></td><td></td></tr>
	<tr><td style="text-align:center; padding-top:1em;"><a href="registerUser.php">Create Username</a></td><td></td></tr>
</table>
</form>
<?php 
}
include_once("includes/footer.php"); ?>