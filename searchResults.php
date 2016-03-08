<?php

//Note: LIKE searches are case-insensitive unless BINARY is specified - MySQL manual


require_once('includes/dbc_connect.php'); //connect to the database

$isSubmitted = isset($_REQUEST['submitSearch']); //test for the existence of the submit button in the get/post array

if ($isSubmitted)
{
	//build query string
	$query_string = 'SELECT	* FROM monsters WHERE isDeleted = 0'; //only not-deleted monsters (put first to make adding "AND" simplified)
	
	if (!empty($_REQUEST['keywords_boxes'])) //keywords array
	{
		$keywordString = '';
		$keywordCount = 0;
		$firstLoop = true;
		foreach ($_REQUEST['keywords_boxes'] as $currKeywordSelected)
		{
			if (ctype_digit($currKeywordSelected))
			{
				if ($firstLoop)
					$firstLoop = false;
				else
					$keywordString .= ', ';
				$keywordString .= $currKeywordSelected; //ctype_digit is strict enough to not need escape()/trim()
				++$keywordCount;
			}
		}
		if ($keywordString != '')
		{
			//query creates a sub-query that returns all monster_id's that match a number of keywords equal to the number of keywords checked
			//while using a sub-query can be expensive, I have indexes on the rows referenced (set up as primary keys), and from what I've read, that can still make it fast
			$query_string .= ' AND monster_id in (select monster_id from monsters_keywords where keyword_id ';
			if ($keywordCount == 1)
				$query_string .= '= ' . $keywordString . ')';
			else
				$query_string .= 'in (' . $keywordString . ') group by monster_id having count(monster_id) = ' . $keywordCount . ')';
		}
	}
	
	if (!empty($_REQUEST['searchString']))
	{
		$searchString = mysqli_real_escape_string($dbc, strtolower(trim($_REQUEST['searchString'])));
		//I have considered more elegant solutions for this if...elseif block, but all my ideas involve costly string concatination, etc...the marginal increase in filesize (that will remain on the server) from this "messy" I'm using here seems to be preferable
		if (isset($_REQUEST['isSearchName']) && isset($_REQUEST['isSearchFamily']))
			$query_string .= ' AND (monster_name LIKE "%' . $searchString . '%" OR header_group LIKE "%' . $searchString . '%")';
		elseif (isset($_REQUEST['isSearchName']))
			$query_string .= ' AND monster_name LIKE "%' . $searchString . '%"';
		elseif (isset($_REQUEST['isSearchFamily']))
			$query_string .= ' AND header_group LIKE "%' . $searchString . '%"';
	}
	
	if (isset($_REQUEST['isElite']))
		$query_string .= ' AND elite_flag = 1';
	if (isset($_REQUEST['isSolo']))
		$query_string .= ' AND solo_flag = 1';
	if (isset($_REQUEST['isLeader']))
		$query_string .= ' AND leader_flag = 1';

/* 	if (!empty($_REQUEST['header_group']))
	{
		$query_string .= ' AND header_group LIKE "%' . mysqli_real_escape_string($dbc, strtolower(trim($_REQUEST['header_group']))) . '%"';
	} */
	
	if (!empty($_REQUEST['level_min']) && ctype_digit($_REQUEST['level_min']) && ($_REQUEST['level_min'] > 0))
	{
		if (!empty($_REQUEST['level_max']) && ctype_digit($_REQUEST['level_max']) && ($_REQUEST['level_max'] > 0))
		{
			//from min to max
			if (!($_REQUEST['level_min'] == 1 && $_REQUEST['level_max'] == 40)) //this cheap test quickly elimiates the need for an expensive addition to the WHERE clause for the default, all-inclusive condition of lvl 1-40
				$query_string .= ' AND level_number BETWEEN ' . mysqli_real_escape_string($dbc, trim($_REQUEST['level_min'])) . ' AND ' . mysqli_real_escape_string($dbc, trim($_REQUEST['level_max']));
		}
		else
		{
			//above min
			$query_string .= ' AND level_number >= ' . mysqli_real_escape_string($dbc, trim($_REQUEST['level_min']));
		}
	}
	else //min is blank OR INVALID
	{
		if (!empty($_REQUEST['level_max']) && ctype_digit($_REQUEST['level_max']) && ($_REQUEST['level_max'] > 0))
		{
			//below max
			$query_string .= ' AND level_number <= ' . mysqli_real_escape_string($dbc, trim($_REQUEST['level_max']));
		} //no else, already tested just above
	}
	
	if (!empty($_REQUEST['user_id']) && ctype_digit($_REQUEST['user_id']) && ($_REQUEST['user_id'] > 0))
	{
		$query_string .= ' AND user_id = ' . mysqli_real_escape_string($dbc, trim($_REQUEST['user_id']));
		//$isAdminPage = true; //don't let non-admin users access the list by user_id (header.php uses this variable)
	}
	
	if (!empty($_REQUEST['role'])) // role[] array
	{
		$roleString = '';
		$firstLoop = true;
		foreach ($_REQUEST['role'] as $roleSelected)
		{
			//not checking for invalid "role" values, just escaping them, since the search will simply fail if the user injects bad values, not inserted or saved
			if ($firstLoop)
				$firstLoop = false;
			else
				$roleString .= ', ';
			$roleString .= '"' . mysqli_real_escape_string($dbc, trim($roleSelected)) . '"';
		}
		if ($roleString != '')
			$query_string .= ' AND role in (' . $roleString . ')';
	}
	
	if (!empty($_REQUEST['alignment'])) // alignment[] array
	{
		$foundNull = false;
		$alignmentString = '';
		$firstLoop = true;
		foreach ($_REQUEST['alignment'] as $alignmentSelected)
		{
			if (is_numeric($alignmentSelected) && $alignmentSelected <= 2 && $alignmentSelected >= -2) //valid range
			{
				if ($firstLoop)
					$firstLoop = false;
				else
					$alignmentString .= ', ';
				$alignmentString .= $alignmentSelected;
			}
			elseif ($alignmentSelected == 'null')
				$foundNull = true;
		}
		if ($foundNull)
		{
			if ($alignmentString != '')
				$query_string .= ' AND (alignment in (' . $alignmentString . ') OR alignment is null)';
			else
				$query_string .= ' AND alignment is null';
		}
		else
		{
			if ($alignmentString != '')
				$query_string .= ' AND alignment in (' . $alignmentString . ')';
		}
	}
	
	if (!empty($_REQUEST['size_category'])) // size_category[] array
	{
		$sizeString = '';
		$firstLoop = true;
		foreach ($_REQUEST['size_category'] as $sizeSelected)
		{
			if ($firstLoop)
				$firstLoop = false;
			else
				$sizeString .= ', ';
			$sizeString .= '"' . mysqli_real_escape_string($dbc, trim($sizeSelected)) . '"';
		}
		if ($sizeString != '')
			$query_string .= ' AND size_category in (' . $sizeString . ')';
	}
	
	if (!empty($_REQUEST['origin'])) // origin[] array
	{
		$originString = '';
		$firstLoop = true;
		foreach ($_REQUEST['origin'] as $originSelected)
		{
			if ($firstLoop)
				$firstLoop = false;
			else
				$originString .= ', ';
			$originString .= '"' . mysqli_real_escape_string($dbc, trim($originSelected)) . '"';
		}
		if ($originString != '')
			$query_string .= ' AND origin in (' . $originString . ')';
	}
	
	if (!empty($_REQUEST['type_category'])) // type_category[] array
	{
		$typeString = '';
		$firstLoop = true;
		foreach ($_REQUEST['type_category'] as $typeSelected)
		{
			if ($firstLoop)
				$firstLoop = false;
			else
				$typeString .= ', ';
			$typeString .= '"' . mysqli_real_escape_string($dbc, trim($typeSelected)) . '"';
		}
		if ($typeString != '')
			$query_string .= ' AND type_category in (' . $typeString . ')';
	}
	
	if (!empty($_REQUEST['book_found_in'])) // type_category[] array
	{
		$bookString = '';
		$firstLoop = true;
		foreach ($_REQUEST['book_found_in'] as $bookSelected)
		{
			if ($firstLoop)
				$firstLoop = false;
			else
				$bookString .= ', ';
			$bookString .= '"' . mysqli_real_escape_string($dbc, trim($bookSelected)) . '"';
		}
		if ($bookString != '')
			$query_string .= ' AND book_found_in in (' . $bookString . ')';
	}
	
	
	//finish query string
	$query_string .= ' ORDER BY header_group, monster_name, level_number';
	
	//might want to implment LIMITing here for pagination, using user preferences for numPerPage
	
	
	//run query with complete search string
	$result_monsters = @mysqli_query($dbc, $query_string);

	//create a header, including a count of all monsters returned
	$pageTitle = 'Search Results'; //sets the page title, "header.php" uses this variable
	if ($result_monsters) //if query ran OK, then add its count into the header:
		$pageTitle .= ' ('. mysqli_num_rows($result_monsters) . ')';
}
else //page was reached not by search form submission
{
	$pageTitle = 'Search Results (None)'; //sets the page title, "header.php" uses this variable
}
include_once("includes/header.php");


if ($isSubmitted)
{
	echo '<p class="debug">DEBUG: ' . $query_string . '</p>';

	//call the file that will display a table of mosnters, or error messages if search did not run
	//All this file needs is "$results_monsters", with "select * from monsters..." results in it.
	require('includes/showTableByResults.php');
}
else
{
	echo '<p class="error">No search submitted.</p>';
}

mysqli_close($dbc);

include_once("includes/footer.php"); ?>