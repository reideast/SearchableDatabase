<?php
$isAdminPage = true; //used by 'header.php' to validate an admin user's logon session

require_once('includes/dbc_connect.php'); //connect this early in the script, since mysqli_real_escape string needs it

if (!empty($_REQUEST['monsterToEdit'])) //set up page for either Editing a monster or making a New monster
{
	if (ctype_digit($_REQUEST['monsterToEdit']) && ($_REQUEST['monsterToEdit'] > 0))
	{
		$isNewMonster = false;
		$formValues['monsterToEdit'] = mysqli_real_escape_string($dbc, trim($_REQUEST['monsterToEdit'])); //set up the form's default value, so it is preserved after the form is submitted
	}
	elseif ($_REQUEST['monsterToEdit'] == 'new')
	{
		$isNewMonster = true;
		$formValues['monsterToEdit'] = 'new'; //only one value possible
	}
	else //monsterToEdit was not either 'new' or a positive number. Show error and create a blank form
	{
		$isNewMonster = true;
		$errorsEditID = 'Invalid monster ID specified. Creating a new monster.';
		$formValues['monsterToEdit'] = 'new'; //will be valid next time the form is submitted, if the user chooses to submit a NEW monster
	}
}
else //monsterToEdit was not set in the URL for this page. Show error, but set up to create a new monster.
{
	$isNewMonster = true;
	$errorsEditID = 'No monster ID specified. Creating a new monster.';
	$formValues['monsterToEdit'] = 'new'; //will be valid next time the form is submitted, if the user chooses to submit a NEW monster
	
}

//to avoid function calls each time isset is used:
$isSubmitted = isset($_REQUEST['submitted']); //test for the existence of the submit button in the get/post array


//*********************** Input Validation ***********************
//(see Larry Ullman pg. 332 for example of $errors[] method)
//if the form was submitted, for EITHER a new or an editing monster, get the "sticky" values from the $_REQUEST and validate them.
//These values will then be inserted into the database at the end of this script (either as a new monster or to update an old monster)

$errorFlag = FALSE; //use a boolean flag to make testing later in the script more clearn, and speedy
if ($isSubmitted)
{
	if (!empty($_REQUEST['monster_id']))
	{
		if (ctype_digit($_REQUEST['monster_id']) || ($_REQUEST['monster_id'] > 0)) //logical operator overloading will prevent testing non-numeric variables, etc.
		{
			$formValues['monster_id'] = mysqli_real_escape_string($dbc, trim($_REQUEST['monster_id']));
			//if 'monster_id' isn't a correct value, don't set any errors or alert the user unduly. The script uses 'monsterToEdit' for this purpose
		}
	}
	
	$regex_pattern = '/^[\w ()!.,\'-]+$/'; //allows alphanumeric characters as well as the special characters that may be used in names: "_()!.'-"
	//echo $regex_pattern;
	if (empty($_REQUEST['monster_name']))
	{
		$errorFlag = TRUE;
		$errorsFirst['monster_name'] = 'Please enter a monster name.';
		//not needed, since I'll be checking for isset() anyway: $formValues['monster_name'] = '';
	}
	else
	{
		$formValues['monster_name'] = mysqli_real_escape_string($dbc, trim($_REQUEST['monster_name']));
		if (!preg_match($regex_pattern, $_REQUEST['monster_name']))
		{
			$errorFlag = TRUE;
			$errorsFirst['monster_name'] = 'Please enter valid monster name: only alpha-numeric and "( ) ! . \' _ -" characters allowed.';
		}
	}
	
	if (empty($_REQUEST['header_group']))
	{
		$errorFlag = TRUE;
		$errorsFirst['header_group'] = 'Please enter a monster family. (ex. dragon, goblin)';
	}
	else
	{
		$formValues['header_group'] = mysqli_real_escape_string($dbc, trim($_REQUEST['header_group']));
		if (!preg_match($regex_pattern, $_REQUEST['header_group']))
		{
			$errorFlag = TRUE;
			$errorsFirst['header_group'] = 'Please enter valid monster family: only alpha-numeric and "( ) ! . \' _ -" characters allowed.';
		}
	}
	
	if (!isset($_REQUEST['page']))
	{
		$errorFlag = TRUE;
		$errorsFirst['page'] = 'Please enter the page number to reference.';
	}
	else
	{
		//NOTE: is_numeric doesn't seem to always test correctly. see http://php.net/manual/en/function.is-numeric.php, comments at the bottom of the page
		if (!ctype_digit($_REQUEST['page']) || ($_REQUEST['page'] < 0)) //logical operator overloading will prevent testing non-numeric variables, etc.
		{
			$errorFlag = TRUE;
			$errorsFirst['page'] = 'Please enter a valid page number to reference.';
		}
		//set formValue regardless of being correct, so form will be "sticky"
		$formValues['page'] = mysqli_real_escape_string($dbc, trim($_REQUEST['page']));
	}
	
	if (empty($_REQUEST['book_found_in'])) //this cannot be be blank, because comes from a drop-down <select> element. However, someone could edit the URL (until I change the whole form to POST).
	{
		$errorFlag = TRUE;
		$errorsFirst['book_found_in'] = 'Book must be entered.';
	}
	else
		$formValues['book_found_in'] = mysqli_real_escape_string($dbc, trim($_REQUEST['book_found_in']));

	if (empty($_REQUEST['size_category']))
	{
		$errorFlag = TRUE;
		$errorsSecond['size_category'] = 'Please choose a size category.';
	}
	else
		$formValues['size_category'] = mysqli_real_escape_string($dbc, trim($_REQUEST['size_category']));

	if (empty($_REQUEST['origin']))
	{
		$errorFlag = TRUE;
		$errorsSecond['origin'] = 'Please choose an origin.';
	}
	else
		$formValues['origin'] = mysqli_real_escape_string($dbc, trim($_REQUEST['origin']));

	if (empty($_REQUEST['type_category']))
	{
		$errorFlag = TRUE;
		$errorsSecond['type_category'] = 'Please choose a type.';
	}
	else
		$formValues['type_category'] = mysqli_real_escape_string($dbc, trim($_REQUEST['type_category']));

	if (!isset($_REQUEST['level_number']))
	{
		$errorFlag = TRUE;
		$errorsSecond['level_number'] = 'Please enter monster level.';
	}
	else
	{
		if (!ctype_digit($_REQUEST['level_number']) || ($_REQUEST['level_number'] <= 0))
		{
			$errorFlag = TRUE;
			$errorsSecond['level_number'] = 'Please enter a valid level.';
		}
		$formValues['level_number'] = mysqli_real_escape_string($dbc, trim($_REQUEST['level_number']));
	}

	if (isset($_REQUEST['elite_flag']) && $_REQUEST['elite_flag'] == 1)
		$formValues['elite_flag'] = 1; //keep it simple, it's just a bit flag
	else
		$formValues['elite_flag'] = 0;
	//do not want to set this map value, so I can do isset($formValues['elite_flag']) then "checked" later on - else //is not set, is set invalid, or it was not checked
	//	$formValues['elite_flag'] = 0;

	if (isset($_REQUEST['solo_flag']) && $_REQUEST['solo_flag'] == 1)
		$formValues['solo_flag'] = 1;
	else
		$formValues['solo_flag'] = 0;

	if (isset($_REQUEST['leader_flag']) && $_REQUEST['leader_flag'] == 1)
		$formValues['leader_flag'] = 1;
	else
		$formValues['leader_flag'] = 0;

	if (empty($_REQUEST['role']))
	{
		$errorFlag = TRUE;
		$errorsSecond['role'] = 'Please choose a role.';
	}
	else
		$formValues['role'] = mysqli_real_escape_string($dbc, trim($_REQUEST['role']));

	if (!isset($_REQUEST['exp_value']))
	{
		$errorFlag = TRUE;
		$errorsSecond['exp_value'] = 'Please enter experience points.';
	}
	else
	{
		if (!ctype_digit($_REQUEST['exp_value']) || ($_REQUEST['exp_value'] <= 0)) //logical operator overloading will prevent testing non-numeric variables, etc.
		{
			$errorFlag = TRUE;
			$errorsSecond['exp_value'] = 'Please enter a valid experience point value.';
		}
		$formValues['exp_value'] = mysqli_real_escape_string($dbc, trim($_REQUEST['exp_value']));
	}

	//make alignment optional
	// if (!isset($_REQUEST['alignment']))
	// {
		// $errorFlag = TRUE;
		// $errorsSecond['alignment'] = 'Please choose an alignment.';
	// }
	// else
	if (isset($_REQUEST['alignment']))
	{
		if (!is_numeric($_REQUEST['alignment']) || ($_REQUEST['alignment'] < -2) || ($_REQUEST['alignment'] > 2)) //logical operator overloading will prevent testing non-numeric variables, etc.
		{
			$errorFlag = TRUE;
			$errorsSecond['alignment'] = 'Please enter a valid alignment.';
		}
		$formValues['alignment'] = mysqli_real_escape_string($dbc, trim($_REQUEST['alignment']));
	}
	
	//build pre-selected-keywords array. both the form itself and the insert routine use $keywordIDSelected[]
	//don't do this, so I can test for isset() later: $keywordIDSelected = array(); //create array, even if nothing is put into it
	if (isset($_REQUEST['keywords_boxes']))
	{
		foreach ($_REQUEST['keywords_boxes'] as $currKeywordsSelected)
		{
			//if (is_numeric($currKeywordsSelected) && $currKeywordsSelected > 0 && $currKeywordsSelected <= $numKeywordsTotal) //simple validation
			if (ctype_digit($currKeywordsSelected))
			{
				$result_keyword_check = @mysqli_query($dbc, 'select keyword_id from keywords where keyword_id = ' . mysqli_real_escape_string($dbc, trim($currKeywordsSelected)));
				if ($result_keyword_check && mysqli_num_rows($result_keyword_check) == 1) //there is a single row in the DB table keywords that matches to this keyword_boxes[]
				{
					$keywordIDSelected[$currKeywordsSelected] = true; //so, if keyword_id 4 is checked, this will set $keywordIDSelected[4] = true;
				}
				else
				{
					$errorFlag = TRUE;
					$errorsThird['keywords_boxes'] = 'A keyword ID number was not in the database.'; // . ' DEBUG: ID=' . $currKeywordsSelected;
				}
			}
			else
			{
				$errorFlag = TRUE;
				$errorsThird['keywords_boxes'] = 'A keyword ID number was not numeric.'; // . ' DEBUG: ID=' . $currKeywordsSelected;
			}
		}
	}

} //end if is SUBMITTED
elseif (!$isNewMonster) //since this elseif is linked to the last if, this is equivalent to (!isset($_REQUEST['submitted']) && !isNewMonster)
{
	//retrieve monster values from DB if the form has not been submitted yet, and then, assign them $formValues[]

	//$formValues['monsterToEdit'] === $_REQUEST['monsterToEdit'], and has already been tested to see if it is set, numeric, and > 0
	$query_string = 'SELECT	* from monsters where isDeleted = 0 AND monster_id = ' . $formValues['monsterToEdit'];
	$result_monster_to_edit = @mysqli_query($dbc, $query_string);
	if ($result_monster_to_edit && (mysqli_num_rows($result_monster_to_edit) == 1)) //if query ran OK, then display records
	{
		$row = mysqli_fetch_array($result_monster_to_edit, MYSQLI_ASSOC); //only get one row, since only one could be returned
		
		$formValues['monster_id'] = $row['monster_id'];
		$formValues['monster_name'] = $row['monster_name'];
		$formValues['header_group'] = $row['header_group'];
		$formValues['page'] = $row['page'];
		$formValues['book_found_in'] = $row['book_found_in'];
		$formValues['size_category'] = $row['size_category'];
		$formValues['origin'] = $row['origin'];
		$formValues['type_category'] = $row['type_category'];
		$formValues['level_number'] = $row['level_number'];
		$formValues['elite_flag'] = $row['elite_flag'];
		$formValues['solo_flag'] = $row['solo_flag'];
		$formValues['leader_flag'] = $row['leader_flag'];
		$formValues['role'] = $row['role'];
		$formValues['exp_value'] = $row['exp_value'];
		$formValues['alignment'] = $row['alignment'];
		
		//keywords_boxes[]
 		$query_string = 'select monsters_keywords.keyword_id as kw_id from monsters_keywords inner join keywords using (keyword_id) where keywords.keyword_enabled = 1 and monsters_keywords.monster_id = '; //inner join required for this otherwise simple query because we need "keyword_enabled" from table "keywords"
		$results_keywords = @mysqli_query($dbc, ($query_string . $row['monster_id'])); //perform cross-ref join query on this monster_ID
		if ($results_keywords)
		{
			while ($currKeyword = mysqli_fetch_array($results_keywords, MYSQLI_ASSOC))
			{
				$keywordIDSelected[($currKeyword['kw_id'])] = true; //no more lower case, since some keywords can be capitalized: strtolower($currKeyword['kw']);
			}
			mysqli_free_result($results_keywords);
		}
	}
	else //query did not run OK
	{
		$isNewMonster = true; //reset flag, make it so a new monster is set to be created
		$formValues['monsterToEdit'] = 'new'; //will be valid next time the form is submitted, if the user chooses to submit a NEW monster
		$errorsEditID = 'The monster could not be retrieved from the database to edit. Create new monster by default.';
	}
	mysqli_free_result($result_monster_to_edit); //free result, even if the monster ID is bad
	
	//don't need to do this, it won't cost that much CPU: remember, if "elite_flag" etc are 0, then do not set $formValue['elite_flag'] = 0, just leave that line off.
}

//*******************BEGIN PAGE OUTPUT*******************//

if ($isNewMonster)
	$pageTitle = 'Create New Monster'; //sets the page title, "header.php" uses this variable
else
	$pageTitle = 'Edit Monster - ' . stripslashes($formValues['monster_name']);

include_once("includes/header.php");


?>
<!-- Stylesheet and Javascript, specific to edit page only: -->
<script type="text/javascript" src="includes/editFormScripts.js"></script>

<?php //if (isset($_REQUEST['monsterToEdit'])) echo '$_REQUEST[monsterToEdit] = "' . $_REQUEST['monsterToEdit'] . '" / '; ?>
<?php //echo '$formValues[monsterToEdit] = "' . $formValues['monsterToEdit'] . '"<br />'; ?>

<div class="containsForm">
<form action="editMonster.php" method="post" onsubmit="enableEXPBoxBeforeSubmit()">
<!-- Form is "sticky" to previous values submitted and displays any input validation errors in-line -->
<?php $tabStopSequence = 1; //will create the proper tab-order, even if re-arranged or more added from DB ?>
<div class="containsFormInput">

	<?php
	//Show error messages before the first form element, only if the monsterToEdit was invalid:
	if (isset($errorsEditID))
	{
		echo '<div id="containsErrorsMessages">';
			echo '<p>' . $errorsEditID . '</p>';
		echo '</div>';
	}
	?>
	<input type="hidden" name="monsterToEdit" size="10" maxlength="10" value="<?php echo (isset($formValues['monsterToEdit'])) ? $formValues['monsterToEdit'] : ''; ?>" />

	<input type="hidden" name="monster_id" size="10" maxlength="10" value="<?php echo (isset($formValues['monster_id'])) ? $formValues['monster_id'] : '';?>" />

	<div class="editRow" style="width:100%;">
		<table style="width:100%;">
			<?php $rowTotal = 780; //px - This is to convert % to px for CSS, because I'm tired of CSS doing weird things with % measurements ?>
			<tr>
				<!-- Set the width of the headers, to set the width of the columns. -->
				<th style="width:<?php echo .37 * $rowTotal; ?>px;">Name</th>
				<th style="width:<?php echo .24 * $rowTotal; ?>px;">Family</th>
				<th style="width:<?php echo .15 * $rowTotal; ?>px;">Page</th>
				<th style="width:<?php echo .24 * $rowTotal; ?>px%">Book Reference</th>
			</tr>
			<tr>
				<td>
					<input type="text" name="monster_name" size="25" maxlength="50" <?php echo (isset($errorsFirst['monster_name'])) ? 'class="errorInput"' : ''; ?> value="<?php echo (isset($formValues['monster_name'])) ? stripslashes($formValues['monster_name']) : ''; //stripslashes() undoes what mysqli_escape_string() has alredy done ?>" tabindex="<?php echo $tabStopSequence++; ?>" />
				</td>
				<td>
					<input type="text" name="header_group" size="17" maxlength="30" <?php echo (isset($errorsFirst['header_group'])) ? 'class="errorInput"' : ''; ?> value="<?php echo (isset($formValues['header_group'])) ? stripslashes($formValues['header_group']) : '';?>" tabindex="<?php echo $tabStopSequence++; ?>" />
				</td>
				<td>
					<input type="text" name="page" id="page" size="5" maxlength="11" <?php echo (isset($errorsFirst['page'])) ? 'class="errorInput"' : ''; ?> value="<?php echo (isset($formValues['page'])) ? $formValues['page'] : '';?>" tabindex="<?php echo $tabStopSequence++; ?>" />
					<input type="button" onclick="document.getElementById('page').value++;" class="styledButton" style="font-size:.6em;" value="+" />
					<input type="button" onclick="document.getElementById('page').value--;" class="styledButton" style="font-size:.7em;" value="-" />
				</td>
				<td>
					<?php
					$results_books = @mysqli_query($dbc, 'select book_found_in, book_short from constants where book_short is not null order by constant_id');
					if ($results_books)
					{
						$outputBuffer = '';
						$haveFoundValid= false;
						while ($row = mysqli_fetch_array($results_books, MYSQLI_ASSOC))
						{
							$outputBuffer .= "\t\t\t\t<option value=\"" . $row['book_short'] . '"';
							if (isset($formValues['book_found_in']) && $formValues['book_found_in'] == $row['book_short'])
							{
								$outputBuffer .= ' selected';
								$haveFoundValid = true;
							}
							$outputBuffer .= '>' . $row['book_found_in'] . "</option>\n"; //ucwords not used here, books are properly capitolised in DB
							
						}
						if ($isSubmitted && !$haveFoundValid && !isset($errorsFirst['book_found_in'])) //the selected book did not match to any book in the databse
						{
							$errorFlag = true; //prevents the database insert!
							$errorsFirst['bad_book_found_in'] = 'The book specified was not a valid book.';
						}
						echo "\n\t\t\t<select name=\"book_found_in\" style=\"width:10em;\" tabindex=\"" . $tabStopSequence++ . '"' . ((isset($errorsFirst['book_found_in']) || isset($errorsFirst['bad_book_found_in'])) ? ' class="errorInput"' : '') . ">\n";
						echo $outputBuffer;
						echo "\t\t\t</select>\n";
					}
					else
					{
						echo '<p class="error">SQL Error: No valid books found in database.</p>';
					} ?>
				</td>
			</tr>
		</table>
	</div> <!-- editFirstRow -->

	<?php
	//Show error messages for only the first row, if they exist:
	if (isset($errorsFirst))
	{
		echo '<div id="containsErrorsMessages">';
			echo '<ul>';
			foreach ($errorsFirst as $errorMessage)
			{
				echo '<li>' . $errorMessage . '</li>';
			}
			echo '</ul>';
		echo '</div>';
	}
	?>

	<div class="editRow" style="width:100%">
		<table style="width:100%">
			<tr>
				<th style="width:<?php echo .195 * $rowTotal; ?>px;">Size</th>
				<th style="width:<?php echo .175 * $rowTotal + 2; ?>px;">Origin</th>
				<th style="width:<?php echo .195 * $rowTotal - 2; ?>px;">Type</th>
				<th style="width:<?php echo .05 * $rowTotal; ?>px;"><!-- Spacer row for a border. --></th>
				<th style="width:<?php echo .155 * $rowTotal; ?>px;">Role</th>
				<th style="width:<?php echo .23 * $rowTotal; ?>px;">Level & Exp</th>
				<th style="width:<?php echo .05 * $rowTotal; ?>px;"><!-- Spacer row for a border. --></th>
				<th style="width:<?php echo .23 * $rowTotal; ?>px;">Alignment</th>
			
			</tr>
			<tr>
				<td><?php
					$results_sizes = @mysqli_query($dbc, 'select size_category, size_short from constants where size_short is not null order by constant_id');
					if ($results_sizes)
					{
						$outputBuffer = '';
						$haveFoundValid = false;
						while ( $row = mysqli_fetch_array($results_sizes, MYSQLI_ASSOC) )
						{
							$outputBuffer .= "\t\t\t\t<option value=\"" . $row['size_short'] . '"';
							if (isset($formValues['size_category']) && $formValues['size_category'] == $row['size_short'])
							{
								$outputBuffer .= ' selected';
								$haveFoundValid = true;
							}
							$outputBuffer .= '>' . ucwords($row['size_category']) . "</option>\n";
						}
						if ($isSubmitted && !$haveFoundValid && !isset($errorsSecond['size_category'])) //the selected size category did not match to any size in the database
						{
							$errorFlag = true; //prevent the database insert!
							$errorsSecond['bad_size_category'] = 'The size specified is not a valid size.';
							//echo '<div class="error">' . $errorsSecond['bad_size_category'] . '</div>';
						}
						echo "\n\t\t\t<select name=\"size_category\" size=\"". mysqli_num_rows($results_sizes) . '" tabindex="' . $tabStopSequence++ . '"' . ((isset($errorsSecond['size_category']) || isset($errorsSecond['bad_size_category'])) ? ' class="errorInput" ' : '') . ">\n";
						echo $outputBuffer;
						echo "\t\t\t</select>\n";
					}
					else
					{
						echo '<p class="error">SQL Error: No valid sizes found in database.</p>';
					} ?>
				</td>
				<td><?php
					$results_origins = @mysqli_query($dbc, 'select origin from constants where origin is not null order by constant_id');
					if ($results_origins)
					{
						$outputBuffer = '';
						$haveFoundValid = false;
						while ( $row = mysqli_fetch_array($results_origins, MYSQLI_ASSOC) )
						{
							$outputBuffer .= "\t\t\t\t<option value=\"" . $row['origin'] . '"';
							if (isset($formValues['origin']) && $formValues['origin'] == $row['origin']) //note: if ($row['origin'] == $roleThisMonsterHasFromDatabase) then echo 'selected'
							{
								$outputBuffer .= ' selected';
								$haveFoundValid = true;
							}
							$outputBuffer .= '>' . ucwords($row['origin']) . "</option>\n";
						}
						if ($isSubmitted && !$haveFoundValid && !isset($errorsSecond['origin'])) //the selected size category did not match to any size in the database
						{
							$errorFlag = true; //prevent the database insert!
							$errorsSecond['bad_origin'] = 'The origin specified is not a valid origin.';
						}
						echo "\n\t\t\t<select name=\"origin\" size=\"". mysqli_num_rows($results_origins) . '" tabindex="' . $tabStopSequence++ . '"' . ((isset($errorsSecond['origin']) || isset($errorsSecond['bad_origin'])) ? ' class="errorInput" ' : '') . ">\n";
						echo $outputBuffer;
						echo "\t\t\t</select>\n";
					}
					else
					{
						echo '<p class="error">SQL Error: No valid origins found in database.</p>';
					} ?>
				</td>
				<td><?php
					$results_types = @mysqli_query($dbc, 'select type_category from constants where type_category is not null order by constant_id');
					if ($results_types)
					{
						$outputBuffer = '';
						$haveFoundValid = false;
						while ( $row = mysqli_fetch_array($results_types, MYSQLI_ASSOC) )
						{
							$outputBuffer .= "\t\t\t\t<option value=\"" . $row['type_category'] . '"';
							if (isset($formValues['type_category']) && $formValues['type_category'] == $row['type_category']) //note: if ($row['type_category'] == $roleThisMonsterHasFromDatabase) then echo 'selected'
							{
								$outputBuffer .= ' selected';
								$haveFoundValid = true;
							}
							$outputBuffer .= '>' . ucwords($row['type_category']) . "</option>\n";
						}
						if ($isSubmitted && !$haveFoundValid && !isset($errorsSecond['type_category'])) //the selected size category did not match to any size in the database
						{
							$errorFlag = true; //prevent the database insert!
							$errorsSecond['bad_type_category'] = 'The type specified is not a valid type.';
						}
						echo "\n\t\t\t<select name=\"type_category\" size=\"". mysqli_num_rows($results_types) . '" tabindex="' . $tabStopSequence++ . '"' . ((isset($errorsSecond['type_category']) || isset($errorsSecond['bad_type_category'])) ? ' class="errorInput" ' : '') . ">\n";
						echo $outputBuffer;
						echo "\t\t\t</select>\n";
					}
					else
					{
						echo '<p class="error">SQL Error: No valid types found in database.</p>';
					} ?>
				</td>
				<td class="borderLeft"></td>
				<td><?php
					$results_roles = @mysqli_query($dbc, 'select role from constants where role is not null order by constant_id');
					if ($results_roles)
					{
						$outputBuffer = '';
						$haveFoundValid = false;
						while ( $row = mysqli_fetch_array($results_roles, MYSQLI_ASSOC) )
						{
							$outputBuffer .= "\t\t\t\t<option value=\"" . $row['role'] . '"';
							if (isset($formValues['role']) && $formValues['role'] == $row['role']) //note: if ($row['role'] == $roleThisMonsterHasFromDatabase) then echo 'selected'
							{
								$outputBuffer .= ' selected';
								$haveFoundValid = true;
							}
							$outputBuffer .= '>' . ucwords($row['role']) . "</option>\n";
						}
						if ($isSubmitted && !$haveFoundValid && !isset($errorsSecond['role'])) //the selected size category did not match to any size in the database
						{
							$errorFlag = true; //prevent the database insert!
							$errorsSecond['bad_role'] = 'The role specified is not a valid role.';
						}
						echo "\n\t\t\t<select name=\"role\" id=\"role\" size=\"". mysqli_num_rows($results_roles) . '" tabindex="' . $tabStopSequence++ . '"  onchange="calcEXP() "' . ((isset($errorsSecond['role']) || isset($errorsSecond['bad_role'])) ? ' class="errorInput" ' : '') . ">\n";
						echo $outputBuffer;
						echo "\t\t\t</select>\n";
					}
					else
					{
						echo '<p class="error">SQL Error: No valid roles found in database.</p>';
					} ?>
				</td>
				<td>
					<table>
						<tr>
							<td style="text-align:right;">Level:</td>
							<td><input type="text" name="level_number" id="level_number" onchange="calcEXP()" size="3" maxlength="3" <?php echo (isset($errorsSecond['level_number'])) ? 'class="errorInput"' : ''; ?> value="<?php echo (isset($formValues['level_number'])) ? $formValues['level_number'] : '';?>" tabindex="<?php echo $tabStopSequence++; ?>" /></td>
						</tr>
						<tr><td style="text-align:right;"><label for="elite_flag">Elite:</label></td><td><input type="checkbox" name="elite_flag" id="elite_flag" onchange="calcEXP()" value="1" id="elite_flag" <?php echo (isset($formValues['elite_flag']) && ($formValues['elite_flag'] == 1)) ? ' checked' : '';?> tabindex="<?php echo $tabStopSequence++; ?>" /></td></tr>
						<tr><td style="text-align:right;"><label for="solo_flag">Solo:</label></td><td><input type="checkbox" name="solo_flag" id="solo_flag" onchange="calcEXP()" value="1" id="solo_flag" <?php echo (isset($formValues['solo_flag']) && ($formValues['solo_flag'] == 1)) ? ' checked' : '';?> tabindex="<?php echo $tabStopSequence++; ?>" /></td></tr>
						<tr><td style="text-align:right;"><label for="leader_flag">Leader:</label></td><td><input type="checkbox" name="leader_flag" id="leader_flag" value="1" id="leader_flag" <?php echo (isset($formValues['leader_flag']) && ($formValues['leader_flag'] == 1)) ? ' checked' : '';?> tabindex="<?php echo $tabStopSequence++; ?>" /></td></tr>
						<tr>
							<td style="text-align:right;">Exp:</td>
							<td><input type="text" name="exp_value" id="exp_value" size="5" maxlength="11" <?php echo (isset($errorsSecond['exp_value'])) ? 'class="errorInput"' : ''; ?> value="<?php echo (isset($formValues['exp_value'])) ? $formValues['exp_value'] : '';?>" tabindex="<?php echo $tabStopSequence++; ?>" /></td>
						</tr>
						<tr>
							<td></td>
							<td style="font-size: 0.7em; text-align:center;"><a href="javascript:void(0)" id="lockOrUnlockEXP" onClick="toggleManualEdit()" tabindex="120">Manual Edit</a></td>
						</tr>
					</table>
				</td>
				<td class="borderLeft"></td>
				<td><?php
					$results_align = @mysqli_query($dbc, 'select alignment from constants where alignment is not null order by constant_id');
					if ($results_align)
					{
						$outputBuffer = '';
						$haveFoundValid = false;
						
						$alignmentNumeric = 2;
						while ( $row = mysqli_fetch_array($results_align, MYSQLI_ASSOC) )
						{
							$outputBuffer .= "\t\t\t\t<option value=\"" . $alignmentNumeric . '"'; //assign values 2 1 0 -1 -2 for alignments
							if (isset($formValues['alignment']) && $formValues['alignment'] == $alignmentNumeric)
							{
								$outputBuffer .= ' selected';
								$haveFoundValid = true;
							}
							$outputBuffer .= '>' . ucwords($row['alignment']) . "</option>\n";
							--$alignmentNumeric;
						}
						if ($isSubmitted && isset($formValues['alignment']) && !$haveFoundValid && !isset($errorsSecond['alignment'])) //the selected size category did not match to any size in the database
						{
							$errorFlag = true; //prevent the database insert!
							$errorsSecond['bad_alignment'] = 'The role specified is not a valid role.';
						}
						echo "\n\t\t\t<select name=\"alignment\" size=\"". mysqli_num_rows($results_align) . '" tabindex="' . $tabStopSequence++ . '"' . ((isset($errorsSecond['alignment']) || isset($errorsSecond['bad_alignment'])) ? ' class="errorInput" ' : '') . ">\n";
						echo $outputBuffer;
						echo "\t\t\t</select>\n";
					}
					else
					{
						echo '<p class="error">SQL Error: No valid alignments found in database.</p>';
					} ?>
				</td>
			</tr>
		</table>
	</div> <!-- editSecondRow -->

	<?php
	if (isset($errorsSecond))
	{
		echo '<div id="containsErrorsMessages">';
			echo '<ul>';
			foreach ($errorsSecond as $errorMessage)
			{
				echo '<li>' . $errorMessage . '</li>';
			}
			echo '</ul>';
		echo '</div>';
	}
	?>

	
	<div class="editRow"><?php
					//Note: uses variables:
					//$keywordIDSelected[], where isset($keywordIDSelected[3]) will highlight the keyword with keyword_id==2
					//	$keywordIDSelected is already built while validating input, and any keywords selected will be the key in the array
					//$numColumns == how many columns to split
					$numColumns = 6;
					require('includes/showKeywordsCheckboxesTable.php');
				?>

	</div> <!-- editFourthRow -->

	<?php
	if (isset($errorsThird))
	{
		echo '<div id="containsErrorsMessages">';
			echo '<ul>';
			foreach ($errorsThird as $errorMessage)
			{
				echo '<li>' . $errorMessage . '</li>';
			}
			echo '</ul>';
		echo '</div>';
	}
	?>

</div><!-- containsFormInput -->

<div align="center" id="containsFormSubmit">
	<input type="submit" name="submitted" class="styledButton" value="<?php echo ($isNewMonster) ? 'Insert' : 'Edit'; ?> Monster" tabindex="<?php echo $tabStopSequence++; ?>" />
</div>

</form>




	<?php
	if (!$errorFlag && $isSubmitted) //Has the form been submitted with no errors? (!$errorFlag is first to make PHP quit the "if" statement before it processes the expensive isset().)
	{
		//All input has already been validated, so create the database connection and insert the record
		
		//already done: require_once('includes/dbc_connect.php'); //connect to the database

		//build the insert query in SQL, using values previously validated
		if ($isNewMonster)
		{
			$query_string = 'INSERT INTO monsters VALUES ('
							. 'null, "' //auto_increment monster_ID
							. $formValues['monster_name'] . '", "'
							. $formValues['header_group'] . '", "'
							. $formValues['size_category'] . '", "'
							. $formValues['origin'] . '", "'
							. $formValues['type_category'] . '", '
							. $formValues['level_number'] . ', "'
							. $formValues['role'] . '", '
							. $formValues['exp_value'] . ', '
							. $formValues['elite_flag'] . ', '
							. $formValues['solo_flag'] . ', '
							. $formValues['leader_flag'] . ', "'
							. $formValues['book_found_in'] . '", '
							. $formValues['page'] . ', '
							. 'CURRENT_TIMESTAMP, ' //auto timestamp
							. $_SESSION['user_id'] . ', ' //user ID from session - already verified logged on as Admin by header.php
							. (isset($formValues['alignment']) ? $formValues['alignment'] : 'null') . ', '
							. '0)'; //isDeleted = 0
		}
		else //is not new monster, edit monster
		{
			$query_string = 'UPDATE monsters SET '
							. 'monster_name = "' . $formValues['monster_name'] . '", '
							. 'header_group = "' . $formValues['header_group'] . '", '
							. 'size_category = "' . $formValues['size_category'] . '", '
							. 'origin = "' . $formValues['origin'] . '", '
							. 'type_category = "' . $formValues['type_category'] . '", '
							. 'level_number = ' . $formValues['level_number'] . ', '
							. 'role = "' . $formValues['role'] . '", '
							. 'exp_value = ' . $formValues['exp_value'] . ', '
							. 'elite_flag = ' . $formValues['elite_flag'] . ', '
							. 'solo_flag = ' . $formValues['solo_flag'] . ', '
							. 'leader_flag = ' . $formValues['leader_flag'] . ', '
							. 'book_found_in = "' . $formValues['book_found_in'] . '", '
							. 'page = ' . $formValues['page'] . ', '
							. 'user_id = ' . $_SESSION['user_id'] . ', ' //user ID from session - already verified
							. 'alignment = ' . (isset($formValues['alignment']) ? $formValues['alignment'] : 'null')
							. ' WHERE monster_id = ' . $formValues['monsterToEdit']; //this has been already validated, so it is safe, and properly escaped
							//no need to timestamp, DB will do it for us. 'CURRENT_TIMESTAMP, ' //auto timestamp
		}

		if (@mysqli_query($dbc, $query_string)) //The query will return 0 if the row is not updated ether because of an error OR if nothing was changed (since MySQL checks first, and will not change anything if not needed), or 1 if the row is updated. -- && (mysqli_affected_rows($dbc) == 1)) //run the insert query and check to make sure it ran ok, and if the last run query (this one) returned exactly 1 row
		{
			//gets auto_increment key generated from last statement
			if ($isNewMonster)
				$this_monster_id = mysqli_insert_id($dbc);
			else //edit a mosnter
				$this_monster_id = $formValues['monsterToEdit'];
			
			//first delete any rows in "monsters_keywords", will be important either if there were no keywords checked, or if editing this monster's keywords (cheaper than checking each and every keyword pair to see if it already exists in the table)
			$query_string = "delete from monsters_keywords where monster_id = " . $this_monster_id;
			@mysqli_query($dbc, $query_string);
			
			$isNoDBInsertError = true; //assume true, will be set to false if a keyword-insert is not successful
			//build and execute keywords cross-reference query
			if (isset($keywordIDSelected))
			{
				//echo '<div class="debug">At least one keyword was chosen; going to insert keywords</div>';
				$query_string = "insert into monsters_keywords values "; //start building keyword query
				$isFirstRow = true;
				//the array: $keywordIDSelected is already built while making the form, and any keywords selected will be the key in the array
				foreach ($keywordIDSelected as $keyword_id_to_use => $willBeTrueThisVarDoesntMatter)
				{
					if ($isFirstRow)
						$isFirstRow = false;
					else
						$query_string .= ', ';
					$query_string .= '(' . $this_monster_id . ', ' . $keyword_id_to_use . ')';
				}
				//execute cross-ref insert query
				if (!@mysqli_query($dbc, $query_string)) //don't need to do anything if it runs correctly
				{
					$isNoDBInsertError = false;
					echo '<p class="error">The keyword insert was not successful.</p>'; //too much info: . mysqli_error($dbc) . ' Query:' . $query_string . '</p>';
				}
			} //end block to insert keywords, if they are chosen
			
			
			//does this need to be done? mysqli_free_result($result_monsters); //free up query resources

			//DISPLAY A FORMATTED HEADER by requiring the database using $this_monster_id
			//note: $this_monster_id must be set
			if ($isNoDBInsertError)
			{
				echo '<div id="containsInsertedHeader"><h3>Monster Successfully ' . ($isNewMonster ? 'Inserted' : 'Edited') . ':</h3>';
					include('includes/showHeaderByID.php');
					if ($isNewMonster)
						echo '<p><a href="editMonster.php?monsterToEdit=' . $this_monster_id . '">Edit</a></p>';
				echo '</div>';
			}
		}
		else //query did NOT run
		{
			echo '<p class="error">The monster insert was not successful.</p>'; //too much info for user' . mysqli_error($dbc) . ' Query: "' . $query_string . '"</p>';
		}

	} //end if testing that the form has been submitted with no errors
	
mysqli_close($dbc); ?>

</div> <!-- containsForm -->

<?php include_once("includes/footer.php"); ?>