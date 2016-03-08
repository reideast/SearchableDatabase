<?php
$query_string = 'select book_short, book_found_in from constants where book_short is not null order by book_found_in';
$result_book_abbrv = @mysqli_query($dbc, $query_string);
if ($result_book_abbrv)
{
?>
<script type="text/javascript">
	function changeVis(idToChange)
	{
		var elem = document.getElementById(idToChange);
		if (elem.style.display == 'block')
			elem.style.display = 'none';
		else
			elem.style.display = 'block';
	}
</script>
<?php
	echo '<div id="containsBookAbbrv">';
		//mysqli_num_rows($result_book_abbrv);
		$countRowBreaks = 0;
		$itemsPerRow = 3;
		echo '<p><input type="button" onclick="changeVis(\'containsTableAbbrv\')" class="styledButton" style="font-size:0.65em;" value="Show Book Abbreviations" /></p>';
		echo "<table id=\"containsTableAbbrv\" style=\"display:none;\">\n<tr><th colspan=\"0\">Book Abbreviations</th></tr>\n<tr>";
		while ( $row = mysqli_fetch_row($result_book_abbrv) ) //same as mysqli_fetch_array( , MYSQLI_NUM)
		{
			if (($countRowBreaks % $itemsPerRow) == 0) //start a new row every four rows, starting with the third
				echo "<tr>";
			echo '<td style="text-align:right; font-weight:bold; ">(' . $row[0] . ')</td><td style="border-right:2px dotted grey;">' . $row[1] . '</td>';
			if (($countRowBreaks++ % $itemsPerRow) == ($itemsPerRow-1)) //end the row every four rows, starting with the first
				echo "</tr>\n";
		}
		echo "</tr>\n</table>\n";
	echo '</div>';
	mysqli_free_result($result_book_abbrv);
}
?>