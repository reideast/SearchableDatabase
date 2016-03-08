<?php
/* PHP/CSS constant methods through editing headers by Christian Heilmann, found at: http://icant.co.uk/articles/cssconstants/ */
header('content-type:text/css'); /* tells the browser this is a CSS document */
header("Expires: " . gmdate("D, d M Y H:i:s", (time()+900)) . " GMT"); /* forces the browser to cache the CSS doc for a while, to avoid it reloading the document every time */

/************ Color Constants (as PHP variables) ************/
include('styleConstants.php');


?>
/* define custom font-families, with the font files on the server to downloaded automatically */
/* Font family examples from http://realtech.burningbird.net/graphics/css/embedded-fonts-font-face */
/* No longer seems worth the time it takes to have the font reloading on EVERY page load... 
@font-face {
	font-family:"Gentium Basic";
	src:local("Gentium Basic Regular"),
		local("GentiumBasic-Regular"),
		url("fonts/GenBasR.ttf") format("truetype");
}
@font-face {
	font-family:"Gentium Basic";
	src:local("Gentium Basic Italic"),
		local("GentiumBasic-Italic"),
		url("fonts/GenBasI.ttf") format("truetype");
	font-style:italic;
}
@font-face {
	font-family:"Gentium Basic";
	src:local("Gentium Basic Bold"),
		local("GentiumBasic-Bold"),
		url("fonts/GenBasB.ttf") format("truetype");
	font-weight:bold;
}
@font-face {
	font-family:"Gentium Basic";
	src:local("Gentium Basic Bold Italic"),
		local("GentiumBasic-BoldItalic"),
		url("fonts/GenBasBI.ttf") format("truetype");
	font-weight:bold;
	font-style:italic;
}
*/
@font-face {
	font-family:VTCGoblinHandBold;
	src:local("VTCGoblinHandBold"),
		url("fonts/VTCGoblinHandBold.ttf") format("truetype");
}


/************ Universal Page Styles ************/
* {
	margin:0px;
	padding:0px;
}
body {
	background-color:<?php echo $pageBGSubtleYellow; ?>;
	font-family:<?php echo $fontFamilyBodyText; ?>;
	color:<?php echo $textBlacky; ?>;
	font-size:82.5%; /* 87.5% = 14pt, 75% = 12pt, 62.5% = 10pt, 100% = 16pt. This "default font size" is a common CSS technique to work with "em" sizes (original creator is unknown)*/
}
h1, h2, h3, h4 {
	color:<?php echo $textHeaderBrown; ?>;
	font-variant:small-caps;
}
h3 {
	font-size:1.3em;
}
a:link {
	color:<?php echo $textLink; ?>;
}
a:visited {
	color:<?php echo $textLinkVisited; ?>;
}
a:hover {
	color:<?php echo $textLinkHover; ?>;
}
th, td {
	/* Set up all TABLE elements for top/left alignment. */
	text-align:left;
	vertical-align:top
}

/************ Universal Form Styles ************/
input {
	/* I don't like how this font looks in the input box...a sans-serif seems to be better:
	font-family:<?php echo $fontFamilyBodyText; ?>;
	font-size:1.1em;*/
	background-color:<?php echo $editFormInputElementBG; ?>; 
/* 	background-color:transparent;*/
/*	background-color:<?php echo $contentBGGrey; ?>;*/
}

select {
	/*width:80px;*/
/* 	background-color:transparent;*/
	/* font-family:<?php echo $fontFamilyBodyText; ?>; */
 	background-color:<?php echo $editFormInputElementBG; ?>;
	/*overflow:hidden;*/
}

.errorInput {
	border:2px solid <?php echo $errorRed; ?>;
}


/************ Layout Styles ************/
#containerPage {
	/* Width values from: http://css-tricks.com/the-perfect-fluid-width-layout/ */
	min-width:780px;
	max-width:1260px;
	margin:4px auto; /*top-bottom left-right; This will center the container on a very large monitor, and spaces top-bottom */
	padding:0px;
	border:0px;
}
#insideContainerPage {
/* these might be TOO much spacing:
	margin:10px 10px 0px 10px; /*top right bottom left
	padding-top:10px;
	padding-bottom:10px;
*/
}
#boxHeader {
	width:auto;
	margin:0px;
	padding:16px 20px 10px; /*top left-right bottom*/
	border-bottom: 2px solid <?php echo $boxBordersGold; ?>;
	background-color:<?php echo $pageHeaderBGGreen; ?>;
	text-align:left;
}
#boxHeader h1 {
	font-size:2.8em;
	letter-spacing:0.15em;
 	font-family:<?php echo $fontFamilyPageHeader; ?>;
	font-variant:small-caps;
	color:<?php echo $pageHeaderGold; ?>;
}
#boxLogin{
	float:right;
	margin-top: -4px; /*pushes this box UPWARD, so it sits correctly on the header border */
}
#boxLogin a:link {
	color:<?php echo $pageHeaderGold; ?>;
	text-decoration:none;
}
#boxLogin a:visited {
	color:<?php echo $pageHeaderGold; ?>;
	text-decoration:none;
}
#boxLogin a:hover {
	color:<?php echo $pageHeaderGold; ?>;
	text-decoration:none;
}

#containerNavBody {
	width:auto;
	text-align:left;
	min-height:<?php echo ($navBarHeight + 15 + 2); ?>px; /* so the nav bar AND body must at least be as tall as (nav bar + its padding + its border) in px */
	vertical-align:top;
	border:0px dotted grey;
	margin:0px;
	padding:0px;
}
#boxNav {
	float:left;
	/* to make nav spaced by background image: */
	/*  background: url(images/nav-background.gif) repeat-y white; */
	position:relative;
	z-index:80; /*put on top of the content box*/
	overflow:hidden; /* if a single word is too wide for the sidebar, truncate it; else wrap as normal */
	width:<?php echo $navBarWidth; ?>px;
	min-height:<?php echo $navBarHeight; ?>px; /* how tall the nav panel must be*/
	margin:0px 0px 0px 0px; /*top right bottom left*/
	padding:20px 0px 0px 15px;
	border-right:2px dotted <?php echo $boxBordersGold; ?>;
	border-bottom:2px dotted <?php echo $boxBordersGold; ?>;
	font-size:1.2em;
	text-align:left;
	background-color:<?php echo $paleGreenSidebarBG; ?>;
}
#boxNav ul {
	list-style: none; /*format the list of links the sidebar to have NO bullets */
	padding: 0px;
	margin: 0px;
}
#boxNav li {
	margin-bottom:0.2em;
}
#boxNav a:link {
	text-decoration:none;
}
#boxNav a:visited {
	text-decoration:none;
}
#boxNav a:hover {
	text-decoration:none;
}

#boxBody {
	width:auto;
	min-height:<?php echo ($navBarHeight + 15 + 2); ?>px;/* the body must at least be as tall as (nav bar + its padding + its border - body's padding) in px */
	position:relative;
	z-index:50; /* behind the nav bar */
	vertical-align:top;
	background-color:<?php echo $contentBGGrey; ?>;
	margin:0px 0px 0px 0px; /*top right bottom left*/
	padding:5px 5px 10px <?php echo ($navBarWidth + 25); ?>px; /* the padding-right pushes the body over to account for floating navBox*/
	/*font-size:1em; putting this here creates cascading problems for sub-elements that use em-sizes*/
	text-align:left;
	color:<?php echo $textBlacky; ?>;
}
#boxBody p {
	padding-bottom:.6em; /*after paragraph spacing*/
}
.bottomSeparator {
	border-bottom:2px dotted <?php echo $boxBordersGold; ?>;
	margin-bottom:10px; 
}

#boxBody ul, ol {
	padding-left:2.5em; /*indent list items to the right of normal paragraph margins*/
}

#boxFooter {
	background-color:<?php echo $footerBGDarkGrey; ?>;
	font-size:0.75em;
	font-weight:bold;
	text-align:center;
	padding:7px 0px; /* top&bottom, left&right */
	color:<?php echo $paleGreenSidebarBG; ?>; /* a nice green color */
}

/************ Specific Styles ************/

/* Error style by Larry Ullman, from "PHP 6 and MySQL 5: A Visual Quickpro Guide" */
.error {
	font-weight:bold;
	color:<?php echo $errorRed; ?>;
}
.debug {
	font-weight:bold;
	color:green
}
a.confirm {
	color:gold
}

/************ Styles for Results Tables ************/
.tableDisplay {
	/* 
	table-layout:fixed;*/
	border:2px double <?php echo $boxBordersGold; ?>;
}
.tableDisplay tr{

}
.tableDisplay th {
	background-color: <?php echo $paleGreenSidebarBG; ?>;
	border-bottom:2px dotted <?php echo $boxBordersGold; ?>;
	font-size:1.2em;
}
.tableDisplay td {
	padding:1px 0px 1px; /* top left-rt bottom */
	margin:0px 0px 0px 0px;
	border-left:0px;
	border-right:0px;
}
.evenRow {
	background-color: <?php echo $paleGreenSidebarBG; ?>;
}



/* Styles for Monster "Header" green boxes */
.containsMonsterHeader {
	width:<?php echo $monsterHeaderWidth - 100; ?>px;
	margin-left:auto;
	margin-right:auto;
	/*font-size:1.1em; /*make the "monster header" a liiiitle bit bigger*/
}
.containsMonsterHeader table {
	width:100%;
	padding:0px;
	border:0px;
	margin:0px
}
.containsMonsterHeader tr{
	padding:0px;
	border:0px;
	margin:0px
}
.containsMonsterHeader td{
	border: 0px;
	margin: 0px;
	vertical-align:top;
	background-color:<?php echo $monsterHeaderBGGreen; ?>;
	font-family:<?php echo $fontFamilyMonsterHeader; ?>;
	color: white;
}
td.headerBothRowsContainer {
	padding:0.1em 0.4em 0.1em /* top l-r bottom */
}
.headerFirstRow {
	width:100%;
	font-weight:bold;
	font-size:1em;
}
.headerSecondRow {
	width:100%;
	font-size: 0.9em;
}
.leftCell {
	text-align:left
}
.rightCell {
	text-align:right;
}
.containsMonsterHeaderFootnotes {
	text-align:center;
	font-size:0.8em;
	font-style:italic
}

/* Form submit button with page styles: */
.styledButton {
	font-family:<?php echo $fontFamilyBodyText; ?>;
	font-size:1.0em;
	font-weight:bold;
	color:<?php echo $textBlacky; ?>;
	background-color:<?php echo $paleGreenSidebarBG; ?>;
}

#containsBookAbbrv {
	margin-top:15px;
}
#containsTableAbbrv td {
	font-size:0.9em;
	padding:0 0px 0;
}

/************************** Styles for Keywords ***************************/
#keywordsContainer {
	width:100%;
}
#keywordsContainer td {
	border:0px dotted <?php echo $boxBordersGold; ?>;
	vertical-align:top;
	background-color:<?php echo $editFormBGGreen; ?>;
}
.highlightPrevSelected {
	font-weight:bold;
	font-style:italic;
	color:<?php echo $editFormKeywordPrevSelected; ?>
}
.isCustomLabel {
	/*padding-left:1.8em;*/
	/*opacity:0.6;*/
/* 	display:inline-block;
	height:1em;
	font-size:1em;
	vertical-align:middle;
 */	color:<?php echo $editFormKeywordCustomTag; ?>;
	font-style:italic
}




#containsUserManager div {
	margin-bottom:1em;
}

.messageHeader {
	margin-bottom:1em;
}


/******************************** FORM STYLES ****************************/

#containsErrorsMessages {
	/*float:left;
	max-width:250px;*/
	font-weight:bold;
	color:<?php echo $errorRed; ?>;
	margin-bottom:10px;
}

.containsForm {
	max-width:<?php echo $editFormWidth; ?>px;
	/*border:2px solid green;*/
}

.containsFormInput {
	width:100%;
	padding:4px;
	background-color:<?php echo $editFormBGGreen; ?>;
}
.editRow {
	margin-bottom:6px; /* visually appealing spacing */
}
.editRow table {
	width:100%;
}
.editRow th {
	border-bottom:2px dotted <?php echo $boxBordersGold; ?>;
}
.borderLeft {
	border-left:2px dotted <?php echo $boxBordersGold; ?>;
}
#finalEditRow {
	padding:10px 0px 10px;
	border-top:2px dotted <?php echo $boxBordersGold; ?>;
}

/*#editRow td {
 	border: 1px solid grey;
	border-spacing:1px;
}*/
 
/*.borderRight {
 	border-right: 1px solid grey;
}*/

#containsFormSubmit {
	width:100%;
	background-color:<?php echo $editFormBGGreen; ?>;
	margin-top:5px;
	padding:5px 4px 4px; /*top left-right bottom*/
/* 	border: 1px dotted <?php echo $boxBordersGold; ?>;
	width:1px;
	margin-left:auto;
	margin-right:auto;
	text-align:center;
 */
}

#containsInsertedHeader {
	margin:1em auto 0; /*top left-right bottom*/
	padding:0.5em auto 0;
	border-top:2px dotted <?php echo $boxBordersGold; ?>;
	text-align:center;
}
