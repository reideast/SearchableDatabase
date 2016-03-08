<?php
//This file will show one "Monster Header",
// ie. a green box with summarized, formatted info.
//It requires the varible, $this_monster_id
//It connects the the database to retrieve that monster

if (!empty($this_monster_id) && ctype_digit($this_monster_id) && ($this_monster_id > 0)) //is set and is ONLY integer digits
{
	require_once('includes/dbc_connect.php'); //connect to the database
	
	$query_string = 'SELECT	* from monsters where monster_id = ' . $this_monster_id;
	$result_monsters = @mysqli_query($dbc, $query_string);
	if ($result_monsters) //if query ran OK, then display records
	{
		$row = mysqli_fetch_array($result_monsters, MYSQLI_ASSOC); //only get one row, since only one could be returned
?>
<div class="containsMonsterHeader">
	<table>
		<tr>
			<td class="headerBothRowsContainer">
				<table class="headerFirstRow">
					<tr>
						<td class="leftCell"><?php echo ucwords($row['monster_name']); ?></td>
						<td class="rightCell">Level <?php
									echo $row['level_number'].' ';
									if ($row['elite_flag'] == 1) echo 'Elite ';
									if ($row['solo_flag'] == 1) echo 'Solo ';
									echo ucwords($row['role']);
									if ($row['leader_flag'] == 1) echo ' (Leader)'; 
							?>
						</td>
					</tr>
				</table>
				<table class="headerSecondRow">
					<tr>
						<td class="leftCell"><?php
						
							$query_string = 'select size_category from constants where size_short="' . ucwords($row['size_category']) . '"';
							$results_size_name_full = @mysqli_query($dbc, $query_string);
							if ($results_size_name_full)
							{
								$rowSize = mysqli_fetch_array($results_size_name_full, MYSQLI_NUM);
								echo ucwords($rowSize[0]) . ' '; //only has one field returned from the query, get it
							}
							else
							{
								echo $query_string;
								echo 'notfound: ' . ucwords($row['size_category']) . ' ';
							}
							mysqli_free_result($results_size_name_full);
							
							echo strtolower($row['origin']) . ' ' . strtolower($row['type_category']);
							
							$query_string = 'select keywords.keyword as kw from monsters inner join monsters_keywords using (monster_id) inner join keywords using (keyword_id) where keywords.keyword_enabled = 1 and monsters.monster_id = ';
							$results_keywords = @mysqli_query($dbc, ($query_string . $row['monster_id'])); //perform cross-ref join query on this monster_ID
							if (($results_keywords) AND ( mysqli_num_rows($results_keywords) > 0 )) //it is redundant to check this here, since fetch_array will return false if there are no keywords, but I'm leaving it in because most monsters don't have keywords, so this is more efficent
							{
								$isFirstLoopIteration = true;
								while ($currKeyword = mysqli_fetch_array($results_keywords, MYSQLI_ASSOC) )
								{
									if ($isFirstLoopIteration) //put a beginning parentheis
									{
										echo ' (';
										$isFirstLoopIteration = false;
									}
									else //only will print comma if there is a keyword coming
										echo ', ';
									echo $currKeyword['kw']; //no more lower case, since some keywords can be capitolized: strtolower($currKeyword['kw']);
								}
								if ($isFirstLoopIteration == false) //at least one keyword was printed, close parenthesis
									echo ')';
								mysqli_free_result($results_keywords);
							}
						?></td>
						<td class="rightCell">XP <?php echo number_format($row['exp_value']); ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	
	<div class="containsMonsterHeaderFootnotes">
		Monster family: <?php echo ucwords($row['header_group']); ?>,
		Alignment: <?php 	switch ($row['alignment'])
							{
								case null:
									echo "-"; break;
								case 2:
									echo "Lawful Good"; break; //echo "LG"; break;
								case 1:
									echo "Good"; break; //echo "G"; break;
								case 0:
									echo "Unaligned"; break; //echo "U"; break;
								case -1:
									echo "Evil"; break; //echo "E"; break;
								case -2:
									echo "Chaotic Evil"; break; //echo "CE"; break;
								default:
									echo "undefined"; break; //echo "-"; break;
							} ?>,
		Reference: <?php echo ucwords($row['book_found_in']); echo ' pg. '.$row['page']; ?>
	</div>

</div><!-- containsMonsterHeader -->

<?php
		mysqli_free_result($result_monsters); //free up query resources
	}
	else //query did NOT run
	{
		echo '<p class="error">The monster could not be retrieved from the database - Monster ID: ' . $this_monster_id . "</p>\n";
		//echo '<p class="error">The table could not be retrieved.' . mysqli_error($dbc) . ' Query:' . $query_string . '</p>';
	} //end query result IF
		

}//end if to make sure $this_monster_id is proper
else
{
	echo '<p class="error">The monster header display function was not given a valid mosnter ID</p>' . "\n";
}

/* 		<tr>
			<td>
				<table class="headerThirdRow">
					<tr>
						<td class="leftCell"><?php echo ucwords($row['header_group']); ?></td>
						<td class="rightCell"><?php echo ucwords($row['book_found_in']); echo ' pg. '.$row['page']; ?></td>
					</tr>
				</table>
			</td>
		</tr>
 */
?>