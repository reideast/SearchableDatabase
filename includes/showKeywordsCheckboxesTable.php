<table id="keywordsContainer" style="width:100%;">
<tr><th colspan="1">Keywords</th><th colspan="<?php echo $numColumns - 1; ?>" class="isCustomLabel" style="text-align:right;">* User Defined</th></tr>
<tr>		
<?php
	$results_keywords = @mysqli_query($dbc, 'select keyword_id, keyword, isCustom from keywords where keyword_enabled = 1 AND isAttackType = 0 AND isCreatureType = 0 AND isPowerSourceType = 0 order by keyword, keyword_id'); //first alphabetical order, then by insert order
	if ($results_keywords)
	{
		$trBreakAmount = ceil(mysqli_num_rows($results_keywords) / $numColumns); //number of items per column, split into $numColumns
		$trBreakCounter = 1;
		while ( $row = mysqli_fetch_array($results_keywords, MYSQLI_ASSOC) )
		{
			if ($trBreakCounter % $trBreakAmount == 1)
				echo '<td>'; // width="' . (100.0 / $numColumns) . '%">';
			echo '<input type="checkbox" name="keywords_boxes[]" id="' . ucwords($row['keyword']) . $row['keyword_id'] . '" value ="' . $row['keyword_id'] . '"';
			if (isset($keywordIDSelected[$row['keyword_id']]))
				echo 'checked="checked"';
			echo ' tabindex="' . $tabStopSequence++ . '" /><label for="' . ucwords($row['keyword']) . $row['keyword_id'] . '"';
			if (isset($keywordIDSelected[$row['keyword_id']]))
				echo ' class="highlightPrevSelected"';
			echo '>' . ucwords($row['keyword']) . '</label>';
			if ($row['isCustom'] == 1)
				echo '<span class="isCustomLabel">*</span>';
			echo "<br />\n";
			if (($trBreakCounter++ % $trBreakAmount) == 0)
				echo '</td>';
		}
		echo "</td>\n";
	}
	else
	{
		echo '<p class="error">SQL Error: No valid keywords found in database.</p>';
	}
?>
</tr>

<tr><th colspan="<?php echo $numColumns; ?>">Power Source Keywords</th></tr>
<tr><?php
	$results_keywords = @mysqli_query($dbc, 'select keyword_id, keyword, isCustom from keywords where keyword_enabled = 1 AND isPowerSourceType = 1 order by keyword, keyword_id'); //first alphabetical order, then by insert order
	if ($results_keywords)
	{
		$trBreakAmount = ceil(mysqli_num_rows($results_keywords) / $numColumns); //number of items per column, split into $numColumns
		$trBreakCounter = 1;
		while ( $row = mysqli_fetch_array($results_keywords, MYSQLI_ASSOC) )
		{
			//ONLY PRINTS ONE ROW!! WILL CREATE ERROR IF MORE THAN $numColumns KEYWORDS ARE RETURNED
			//if ($trBreakCounter % $trBreakAmount == 0)
				echo '<td>'; // width="' . (100.0 / $numColumns) . '%">';
			echo '<input type="checkbox" name="keywords_boxes[]" id="' . ucwords($row['keyword']) . $row['keyword_id'] . '" value ="' . $row['keyword_id'] . '"';
			if (isset($keywordIDSelected[$row['keyword_id']]))
				echo 'checked="checked"';
			echo ' tabindex="' . $tabStopSequence++ . '" /><label for="' . ucwords($row['keyword']) . $row['keyword_id'] . '"';
			if (isset($keywordIDSelected[$row['keyword_id']]))
				echo ' class="highlightPrevSelected"';
			echo '>' . ucwords($row['keyword']) . '</label>';
			if ($row['isCustom'] == 1)
				echo '<span class="isCustomLabel">*</span>';
			echo "<br />\n";
			//if (($trBreakCounter++ % $trBreakAmount) == 0)
				echo '</td>';
		}
		echo "</td>\n";
	}
	else
	{
		echo '<p class="error">SQL Error: No valid keywords found in database.</p>';
	}
?></tr>

<tr><th colspan="<?php echo $numColumns; ?>">Creature Type or Race</th></tr>
<tr><?php
	$results_keywords = @mysqli_query($dbc, 'select keyword_id, keyword, isCustom from keywords where keyword_enabled = 1 AND isCreatureType = 1 order by keyword, keyword_id'); //first alphabetical order, then by insert order
	if ($results_keywords)
	{
		$trBreakAmount = ceil(mysqli_num_rows($results_keywords) / $numColumns); //number of items per column, split into $numColumns
		$trBreakCounter = 1;
		while ( $row = mysqli_fetch_array($results_keywords, MYSQLI_ASSOC) )
		{
			if ($trBreakCounter % $trBreakAmount == 1)
				echo '<td>'; // width="' . (100.0 / $numColumns) . '%">';
			echo '<input type="checkbox" name="keywords_boxes[]" id="' . ucwords($row['keyword']) . $row['keyword_id'] . '" value ="' . $row['keyword_id'] . '"';
			if (isset($keywordIDSelected[$row['keyword_id']]))
				echo 'checked="checked"';
			echo ' tabindex="' . $tabStopSequence++ . '" /><label for="' . ucwords($row['keyword']) . $row['keyword_id'] . '"';
			if (isset($keywordIDSelected[$row['keyword_id']]))
				echo ' class="highlightPrevSelected"';
			echo '>' . ucwords($row['keyword']) . '</label>';
			if ($row['isCustom'] == 1)
				echo '<span class="isCustomLabel">*</span>';
			echo "<br />\n";
			if (($trBreakCounter++ % $trBreakAmount) == 0)
				echo '</td>';
		}
		echo "</td>\n";
	}
	else
	{
		echo '<p class="error">SQL Error: No valid keywords found in database.</p>';
	}
?></tr>


<tr><th colspan="<?php echo $numColumns; ?>">Attack Type Keywords</th></tr>
<tr><?php
	$results_keywords = @mysqli_query($dbc, 'select keyword_id, keyword, isCustom from keywords where keyword_enabled = 1 AND isAttackType = 1 order by keyword, keyword_id'); //first alphabetical order, then by insert order
	if ($results_keywords)
	{
		$trBreakAmount = ceil(mysqli_num_rows($results_keywords) / $numColumns); //number of items per column, split into $numColumns
		$trBreakCounter = 1;
		while ( $row = mysqli_fetch_array($results_keywords, MYSQLI_ASSOC) )
		{
			if ($trBreakCounter % $trBreakAmount == 1)
				echo '<td>'; // width="' . (100.0 / $numColumns) . '%">';
			echo '<input type="checkbox" name="keywords_boxes[]" id="' . ucwords($row['keyword']) . $row['keyword_id'] . '" value ="' . $row['keyword_id'] . '"';
			if (isset($keywordIDSelected[$row['keyword_id']]))
				echo 'checked="checked"';
			echo ' tabindex="' . $tabStopSequence++ . '" /><label for="' . ucwords($row['keyword']) . $row['keyword_id'] . '"';
			if (isset($keywordIDSelected[$row['keyword_id']]))
				echo ' class="highlightPrevSelected"';
			echo '>' . ucwords($row['keyword']) . '</label>';
			if ($row['isCustom'] == 1)
				echo '<span class="isCustomLabel">*</span>';
			echo "<br />\n";
			if (($trBreakCounter++ % $trBreakAmount) == 0)
				echo '</td>';
		}
		echo "</td>\n";
	}
	else
	{
		echo '<p class="error">SQL Error: No valid keywords found in database.</p>';
	}
?></tr>

</table>