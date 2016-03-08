<?php 
if (isset($_REQUEST['message']) && $_REQUEST['message'] == 'welcome') //sets up the page if it's being used as "index.php"
	$pageTitle = 'Welcome to the Monster Database';
else
	$pageTitle = 'Search Database';
	
include_once('includes/header.php');
require_once('includes/dbc_connect.php'); ?>

<div class="containsForm">
<form action="searchResults.php" method="get">

<div class="containsFormInput">
<div class="editRow">
	<table style="height:55px;display:inline-table;width:100%">
	<tr>
	<th colspan="1" style="width:55%;vertical-align:bottom;">Name or Family</th>
	<td colspan="1" rowspan="2" style="border-bottom:2px dotted #B47839;text-align:center;vertical-align:middle;"><input type="submit" name="submitSearch" class="styledButton" style="font-size:1.5em" value="Search" /></td>
	</tr>
	<tr><td colspan="1" style="border-right:2px dotted #B47839;">Search: <input type="text" name="searchString" size="20" maxlength="50" value="" /> 
	<input type="checkbox" checked="checked" name="isSearchName" id="isSearchName" value="1" /><label for="isSearchName">Name</label> 
	<input type="checkbox" checked="checked" name="isSearchFamily" id="isSearchFamily" value="1" /><label for="isSearchFamily">Family</label>
	</td></td>
	<!--<p>Family: <input type="text" name="header_group" size="10" maxlength="50" value="" /></p>-->
	<tr></tr>
	</table>
</div>

<div id="searchToggleKeywords">
	<a id="iconPlusMinus" href="javascript:void(0)" title="Expand or Contract">Show Keywords</a><img src="" style="min-width:15px; min-height:15px; width:15px; height:15px">
</div>
<div id="searchContainerKeywords" style="display:block;">
	<div class="editRow">
		<?php $numColumns = 6; include('includes/showKeywordsCheckboxesTable.php'); ?>
	</div>
</div><!-- searchContainerKeywords -->
	
<!-- Lower Priority Search Items -->
<div class="editRow">
	<table>
	<tr><th>Role</th><th>Alignment</th><th>Size</th><th>Origin</th><th>Type</th></tr>
	<!--<tr><th style="width:20%">Role</th><th style="width:20%">Alignment</th><th style="width:20%">Size</th><th style="width:20%">Origin</th><th style="width:20%">Type</th></tr>-->
	<td><?php
	$results_roles = @mysqli_query($dbc, 'select role from constants where role is not null order by constant_id');
	if ($results_roles)
	{
		echo '<select name="role[]" multiple="multiple" size="'. mysqli_num_rows($results_roles) . '">';
		while ($row = mysqli_fetch_row($results_roles))
			echo '<option value="' . $row[0] . '">' . ucwords($row[0]) . '</option>';
		echo '</select>';
	}
	else
	{
		echo '<p class="error">SQL Error: No valid roles found in database.</p>';
	} ?></td>
	
	<td><?php
	$results_align = @mysqli_query($dbc, 'select alignment from constants where alignment is not null order by constant_id');
	if ($results_align)
	{
		echo '<select name="alignment[]" multiple="multiple" size="'. (mysqli_num_rows($results_align) + 1) .'">';
		$alignmentNumeric = 2;
		while ($row = mysqli_fetch_row($results_align))
			echo '<option value="' . $alignmentNumeric-- . '">' . ucwords($row[0]) . '</option>'; //assign numeric values from 2 to -2 for aligment
		echo '<option value="null">Any (null)</option>'; //ie, no others chosen
		echo '</select>';
		echo '<p style="font-size:0.5em">"Any" alignment refers to this<br />specificproperty of a monster.<br />To Search for ALL alignments,<br />select them all.</p>';
	}
	else
	{
		echo '<p class="error">SQL Error: No valid alignments found in database.</p>';
	}
	?></td>
	
	<td><?php
	$results_size = @mysqli_query($dbc, 'select size_short, size_category from constants where size_short is not null order by constant_id');
	if ($results_size)
	{
		echo '<select name="size_category[]" multiple="multiple" size="'. mysqli_num_rows($results_size) .'">';
		while ($row = mysqli_fetch_row($results_size))
			echo '<option value="' . $row[0] . '">' . ucwords($row[1]) . '</option>';
		echo '</select>';
	}
	else
	{
		echo '<p class="error">SQL Error: No valid sizes found in database.</p>';
	}
	?></td>
	
	<td><?php
	$results_origin = @mysqli_query($dbc, 'select origin from constants where origin is not null order by constant_id');
	if ($results_origin)
	{
		echo '<select name="origin[]" multiple="multiple" size="'. mysqli_num_rows($results_origin) .'">';
		while ($row = mysqli_fetch_row($results_origin))
			echo '<option value="' . $row[0] . '">' . ucwords($row[0]) . '</option>';
		echo '</select>';
	}
	else
	{
		echo '<p class="error">SQL Error: No valid origins found in database.</p>';
	}
	?></td>
	
	<td><?php
	$results_type = @mysqli_query($dbc, 'select type_category from constants where type_category is not null order by constant_id');
	if ($results_type)
	{
		echo '<select name="type_category[]" multiple="multiple" size="'. mysqli_num_rows($results_type) .'">';
		while ($row = mysqli_fetch_row($results_type))
			echo '<option value="' . $row[0] . '">' . ucwords($row[0]) . '</option>';
		echo '</select>';
	}
	else
	{
		echo '<p class="error">SQL Error: No valid types found in database.</p>';
	}
	?></td>
	</tr></table>
</div>
<div class="editRow">
	<table><tr><th>Level Constraint</th><th style="font-weight:normal;text-align:right;font-size:0.8em;">Enter both for discrete range of levels, or enter only one as a minimum or maximum level.</th></tr>
	<tr><td colspan="2" style="margin-top:8px;">Level: <input type="text" name="level_min" size="3" maxlength="3" value="1" /> to <input type="text" name="level_max" size="3" maxlength="3" value="40" /> 
	<input type="checkbox" name="isElite" id="isElite" value="1" /><label for="isElite">Elite</label> 
	<input type="checkbox" name="isSolo" id="isSolo" value="1" /><label for="isSolo">Solo</label> 
	<input type="checkbox" name="isLeader" id="isLeader" value="1" /><label for="isLeader">Leader</label></td>
	</tr></table>
</div>
<div class="editRow">
	<table><tr><th>Book Found In</th></tr>
	<tr><td><?php
	$results_books = @mysqli_query($dbc, 'select book_found_in, book_short from constants where book_short is not null order by constant_id');
	if ($results_books)
	{
		echo '<select name="book_found_in[]" style="width:10em; multiple="multiple" size="3">';
		while ($row = mysqli_fetch_array($results_books, MYSQLI_ASSOC))
			echo '<option value="' . $row['book_short'] . '" >' . $row['book_found_in'] . "</option>\n";
		echo '</select>';
	}
	else
		echo '<p class="error">SQL Error: No valid books found in database.</p>';
	?></td></tr></table>
</div>

</div><!-- containsFormInput -->
</div>
</form>

<?php include_once("includes/footer.php"); ?>