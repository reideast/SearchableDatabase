<?php
$isAdminPage = true; //used by 'header.php' to validate an admin user's logon session

$pageTitle = 'TemplateFormatDemo'; //sets the page title, "header.php" uses this variable
include_once("includes/header.php"); ?>

	<h3>To Do</h3>
	<ul>
		<li>check if all the "human", "halfling", "changling", etc. keywords actually correspond to the "Family" entries. If so, delete the keywords???</li>
		<li>change "Any" from NULLs to actual "Any", probably "3"</li>
		<li>check all "Shapechanger" keywords, perhaps add in a "Disguise" keywords to reflect Illusion-using monsters
		<li>Go through all inserted monsters to re-work keywords.</li>
		<ul>
			<li>(?) all eberron monsters' Family entries need to be changed to proper groups, like MM1</li>
			<li>minion skirmishers, etc?</li>
		</ul>
		<li>A "returnTo" variable in the URl of the editMonster page.</li>
		<ul>
			<li>for editing a NEW monster, returnTo="new+1234", and it will go back to the edit page, read id="1234" from the DB, but the submit button will create a new ID</li>
			<li>for a search, returnTo="search+456", go back to search w/ search stored as search_id=456</li>
		</ul>
		<li>recent searches, even for un-logged-on users</li>
		<li>Edit Page</li>
		<ul>
			<li>Add in "Keyword Sections" for Attack Types/Attack Keyboards and Creature Type</li>
			<li>Modify the edit page column spacing (which changed after I put in the "alignment" SELECT)</li>
			<li>make link after "edited" monster to "return to create new". need to use JavaScript to submit the form??</li>
			<li>alignment: Any. Angels, </li>
		</ul>
		<li>put search results into a single page: show search box and then results</li>
		<li>paginate results table</li>
		<li>user settings changable (pagination per-page, etc)</li>
		<li>saved "encounters"</li>
		<ul>
			<li>"shopping cart"</li>
			<li>saved searches</li>
		</ul>
		<li>purge dead monster-keywords cross-refs: delete from monsters_keywords where monster_id not in (select monster_id from monsters);</li>
		<li>check for any dead monster-keywords cross-refs: select monsters_keywords.monster_id, monsters_keywords.keyword_id from monsters_keywords where monster_id not in (select monster_id from monsters);</li>
	</ul>
	
	<br />
	<br />
	<h2>Note:</h2>
	<p>While it is ok to have floating divs in this content area, 
	if you use &lt;br float="none" /&gt; tags to prevent wrapping around the 
	float'd element, the content that follows will be pushed down down below 
	the end of the nav-bar, since that is also a float'd element.</p>


<?php include_once("includes/footer.php"); ?>