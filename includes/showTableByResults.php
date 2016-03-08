<?php
//<script type="text/javascript" src="includes/resultsPageScripts.js"></script>
//This file requires $result_monsters, which it tests to see is a valid MySQLi results variable with at least one result

if ($result_monsters) //if query ran OK...
{
	if (mysqli_num_rows($result_monsters) > 0) // ...and returned at least one result, then display records
	{
		echo '<!-- DEBUG: tabled-layout is "fixed" so it SHOULD load faster, but will not auto-calculate widths -->'."\n";
		echo '<table class="tableDisplay">'."\n";
		
		echo '<tr>'
			 . '<th>Monster Name</th>'
			 . '<th>Family</th>'
			 . '<th>Size</th>'
			 . '<th>Origin</th>'
			 . '<th>Type</th>'
			 . '<th>Keywords</th>'
			 . '<th>Lvl</th>'
			 . '<th style="min-width:0.5em;">Role</th>' //keeps column small as possible
			 . '<th>EXP</th>'
			 . '<th>Align</th>'
			 . '<th style="min-width:0.5em;">Book</th>';
			 //. '<th>ID</th>'
		if ($isUserAdmin)
		{
			echo '<th>Edit</th>';
			echo '<th>Delete</th>';
		}
		echo '</tr>';
		
		//build this query here, to avoid cpu-expensive string operations within every loop
		$query_string = 'select keywords.keyword as kw from
						monsters inner join monsters_keywords using (monster_id)
						inner join keywords using (keyword_id)
						where keywords.keyword_enabled = 1 and monsters.monster_id = '; //will be concatinated with current record
		
		$evenRow = true; //to alternate background on rows
		while ( $row = mysqli_fetch_array($result_monsters, MYSQLI_ASSOC) ) //get one record, and allow array access with either numeric index or associative pairs
		{
			$evenRow = !$evenRow;
			
			echo '<tr>';
			
			//build name cell
			echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '>'.ucwords($row['monster_name']).'</td>';
			
			//build family/group cell
			echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '>'.ucwords($row['header_group']).'</td>';

			//build size cell, abbreviated
			echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '>';
			echo substr(strtoupper($row['size_category']), 0, 1); //get first letter. (ucwords())
			echo '</td>';
			
			//origin cell
			echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '>'.ucwords($row['origin']).'</td>';
			
			//type cell
			echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '>'.ucwords($row['type_category']).'</td>';

			//build keywords cell
			echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '>';
			$results_keywords = @mysqli_query($dbc, ($query_string . $row['monster_id']));
			if ($results_keywords AND ( mysqli_num_rows($results_keywords) > 0 ))
			{
				echo ' (';
				$first = true;
				while ($currKeyword = mysqli_fetch_array($results_keywords, MYSQLI_ASSOC) )
				{
					if ($first)
						$first = false;
					else
						echo ', ';
					
					echo strtolower($currKeyword['kw']);
				}
				echo ')';
			}
			echo '</td>';
			
			//build level
			echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . ' style="text-align:right;">'.$row['level_number'].'</td>';
			
			//build role cell
			echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '>';
			if ($row['elite_flag'] == 1)
				echo 'Elite ';
			if ($row['solo_flag'] == 1)
				echo 'Solo ';
			echo ucwords($row['role']);
			if ($row['leader_flag'] == 1)
				echo ' (Leader)'; 
			echo '</td>';
			
			//build "experience" cell
			echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . ' style="text-align:right;">'.number_format($row['exp_value']).'</td>';

			//build alignment cell
			echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '>';
				switch ($row['alignment'])
				{
					case 2:
						echo "LG"; break;
					case 1:
						echo "G"; break;
					case null:
						echo "-"; break;
					case 0:
						echo "U"; break;
					case -1:
						echo "E"; break;
					case -2:
						echo "CE"; break;
					default:
						echo "-"; break;
				}
			echo '</td>';
			
			//build "book reference" cell
			echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . ' style="text-align:right;">';
			if (strtolower($row['book_found_in']) == 'monster manual')
				echo 'MM';
			elseif (strtolower($row['book_found_in']) == 'monster manual ii')
				echo 'MM2';
			else
				echo ucwords($row['book_found_in']);
			echo ' pg. '.$row['page'].'</td>';
			//echo '<td>'.ucwords($row['book_found_in']).' pg. '.$row['page'].'</td>';

			//build monster_id cell
			//echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . ' style="border-left:2px dotted #B47839;">'.$row['monster_id'].'</td>';
			
			//build edit link cell
			if ($isUserAdmin)
				echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '><a href="editMonster.php?monsterToEdit='.$row['monster_id'].'">Edit</a></td>';
			
			//build delete link cell
			if ($isUserAdmin)
				echo '<td' . (($evenRow) ? ' class="evenRow"' : '' ) . '><a href="deleteMonster.php?confirm=true&monster_id='.$row['monster_id'].'">Delete</a></td>';
			
			echo '</tr>'."\n";
		} //end loop for each ROW
		echo "</table>\n";
		mysqli_free_result($result_monsters); //free up query resources

		//Show a key for the book abbrieviations:
		include('includes/showBookAbbrv.php');
	}
	else //query ran, but returned NO results
	{
		echo '<p>No results returned.</p>';
	}
}
else //query did NOT run
{
	echo '<p class="error">The query did not run correctly.</p>'; //The table could not be retrieved.' . mysqli_error($dbc) . ' Query:' . $query_string . '</p>';
}

?>