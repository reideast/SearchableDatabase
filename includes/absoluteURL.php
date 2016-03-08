<?php
//Function is from book: "PHP 6 and MySQL 5" by Larry Ullman on page 331

function absoluteURL($page = 'index.php')
{
	//define the host and directory:
	$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
	
	//remove trailing slashes, which might be left on from dirname(php_self)
	$url = rtrim($url, '/\\');
	
	//add the page name
	$url .= '/' . $page; //there is no error handling (except the default 'index.php', since this is a code-only function, and the worst that could happen is the page could be 404
	
	return $url;
} ?>