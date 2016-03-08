<?php $pageTitle = 'Register New User';
include_once("includes/header.php"); 

require_once('includes/dbc_connect.php'); //connect to the database

if (isset($_REQUEST['submitted']))
{
	//first_name may be empty, but must be alphanumeric
	if (!empty($_REQUEST['first_name']) && !preg_match('/^\w{1,30}$/', mysqli_real_escape_string($dbc, trim($_REQUEST['first_name']))))
	{
		$errors['first_name'] = 'Name must be alphanumeric.';
	}
	
	if (empty($_REQUEST['username']))
	{
		$errors['username'] = 'Please enter a user name.';
	}
	elseif (!preg_match('/^\w{1,25}$/', mysqli_real_escape_string($dbc, trim($_REQUEST['username']))))
	{
		$errors['username'] = 'User login must be alphanumeric.';
	}
	
	if (empty($_REQUEST['password1']))
	{
		$errors['password1'] = 'Please enter a password.';
		if (empty($_REQUEST['password2']))
			$errors['password2'] = 'Please enter the password again.';
	}
	elseif (empty($_REQUEST['password2']))
	{
		$errors['password2'] = 'Please enter the password again.';
	}
	elseif ($_REQUEST['password1'] != $_REQUEST['password2'])
	{
		$errors['password1'] = 'The passwords must match.';
		$errors['password2'] = '';
	}
	
	if (empty($_REQUEST['testValue']))
	{
		$errors['testValue'] = 'Please answer the arithmetic problem to verify registration.';
	}
	else
	{
		//test if SOMEHOW the "math problem" variables were modified
		if (empty($_REQUEST['foo']) || empty($_REQUEST['bar']) || empty($_REQUEST['baz'])
			|| !ctype_digit($_REQUEST['foo']) || !ctype_digit($_REQUEST['bar'])
			|| ($_REQUEST['baz'] != 'oof' && $_REQUEST['baz'] != 'rab') ) //logical operator overloading will prevent testing nonexistent variables
		{
			//unset "math problem" variables so that new numbers will be generated in the form
			unset($_REQUEST['foo']);
			unset($_REQUEST['bar']);
			unset($_REQUEST['baz']);
			$errors['testValue'] = 'Problem with automatically generated values.';
		}
		else
		{
			$correctResult = (($_REQUEST['baz'] == 'oof') ? ($_REQUEST['foo']+$_REQUEST['bar']) : ($_REQUEST['foo']-$_REQUEST['bar']) );
			if ($_REQUEST['testValue'] != $correctResult)
			{
				$errors['testValue'] = 'Check answer to the arithmetic problem.';
			}
		}
	}
	
	if (!isset($errors))
	{
		//check to see if username already exists
		$results_username = @mysqli_query($dbc, 'SELECT user_id from users where username = "' . mysqli_real_escape_string($dbc, trim($_REQUEST['username'])) . '"');
		if ($results_username)
		{
			if (mysqli_num_rows($results_username) > 0)
			{
				$errors['username'] = 'Username already exists.';
				$errors['password1'] = '';
				$errors['password2'] = '';
			}
			else
			{
				//perform user insert
				$query_string = 'INSERT INTO users values (null, "'
						.mysqli_real_escape_string($dbc, trim($_REQUEST['username']))
						.'", SHA1("'.mysqli_real_escape_string($dbc, trim($_REQUEST['password1'])).'"), "'
						.mysqli_real_escape_string($dbc, trim($_REQUEST['first_name']))
						.'", DEFAULT, 0, CURRENT_TIMESTAMP, 0)';
				if (@mysqli_query($dbc, $query_string))
				{
					echo "<h2>Thank you for registering, {$_REQUEST['first_name']}!</h2><p>You may now <a href='login.php'>log in</a>.</p>";
				}
				else
				{
					echo '<div class="error">Database connection to insert users not valid.</div>';
				}
			}
		}
		else
		{
			echo '<div class="error">Database connection to check for user not valid.</div>';
		}
	}
}

if (isset($errors) || !isset($_REQUEST['submitted']))
{
?>
<h2>New User</h2>
<table>
<form action="registerUser.php" method="get">
	<input type="hidden" name="action" value="insertUser" />
	<tr><td style="padding-bottom:4px; width:150px; text-align:right">Name: <input type="text" name="first_name" size="10" maxlength="30"<?php echo ((isset($errors['first_name'])) ? ' class="errorInput" ' : ''); ?> value="<?php echo ((isset($_REQUEST['first_name'])) ? $_REQUEST['first_name'] : ''); ?>" /></td><td><?php echo (isset($errors['first_name']) ? '<span class="error">'.$errors['first_name'].'</span>' : ''); ?></td></tr>
	<tr><td style="padding-bottom:4px; text-align:right">Login: <input type="text" name="username" size="10" maxlength="25"<?php echo ((isset($errors['username'])) ? ' class="errorInput" ' : ''); ?> value="<?php echo ((isset($_REQUEST['username'])) ? $_REQUEST['username'] : ''); ?>" /></td><td><?php echo (isset($errors['username']) ? '<span class="error">'.$errors['username'].'</span>' : ''); ?></td></tr>
	<tr><td style="text-align:right">Password: <input type="password" name="password1" size="10" maxlength="25"<?php echo ((isset($errors['password1'])) ? ' class="errorInput" ' : ''); ?> value="<?php echo ((isset($_REQUEST['password'])) ? $_REQUEST['password1'] : ''); ?>" /></td><td><?php echo (isset($errors['password1']) ? '<span class="error">'.$errors['password1'].'</span>' : ''); ?></td></tr>
	<tr><td style="padding-bottom:4px; text-align:right">Confirm: <input type="password" name="password2" size="10" maxlength="25"<?php echo ((isset($errors['password2'])) ? ' class="errorInput" ' : ''); ?> value="<?php echo ((isset($_REQUEST['password'])) ? $_REQUEST['password2'] : ''); ?>" /></td><td><?php echo (isset($errors['password2']) ? '<span class="error">'.$errors['password2'].'</span>' : ''); ?></td></tr>
	<tr><td style="padding-bottom:4px; text-align:right">Answer: <?php
		//create a random arithmetic problem to attempt to filter bots
		$num1 = rand(1, 9);
		$num2 = rand(1, 9);
			if ($num2 > $num1) {$temp = $num1; $num1 = $num2; $num2 = $temp;} //swap to ensure no negative differences are possible
		$oper = ((rand(1, 2) == 1) ? '+' : '-' );
		//"security by arbitrary names" - there will be nothing but random names in the HTML, so bots would have trouble interpreting the form inputs
		echo '<input type="hidden" name="foo" value="' . ((isset($_REQUEST['foo'])) ? $_REQUEST['foo'] : $num1) . '" />';
		echo '<input type="hidden" name="bar" value="' . ((isset($_REQUEST['bar'])) ? $_REQUEST['bar'] : $num2) . '" />';
		echo '<input type="hidden" name="baz" value="' . ((isset($_REQUEST['baz'])) ? $_REQUEST['baz'] : (($oper == '+') ? 'oof' : 'rab') ) . '" />';
		echo ((isset($_REQUEST['foo'])) ? $_REQUEST['foo'] : $num1).' '.((isset($_REQUEST['baz'])) ? (($_REQUEST['baz'] == 'oof') ? '+' : '-') : $oper ).' '.((isset($_REQUEST['bar'])) ? $_REQUEST['bar'] : $num2).' = ';
		?><input type="text" name="testValue" size="5" maxlength="5"<?php echo ((isset($errors['testValue'])) ? ' class="errorInput" ' : ''); ?> value="<?php echo ((isset($_REQUEST['testValue'])) ? $_REQUEST['testValue'] : ''); ?>" /></td><td><?php echo (isset($errors['testValue']) ? '<span class="error">'.$errors['testValue'].'</span>' : ''); ?></td></tr>
	<tr><td style="text-align:center"><input type="submit" name="submitted" class="styledButton" value="Insert User" style="font-size:1em;" /></td><td></td></tr>
</form>
</table>
<?php 
}
include_once("includes/footer.php"); ?>