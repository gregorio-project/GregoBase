<?php
include('include/functions.php');
echo <<<HEADER1
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=UTF-8" http-equiv="content-type" />
<title>GregoBase - $title</title>
<link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Libre+Baskerville:400,700,400italic' />
<link rel="stylesheet" type="text/css" href="style.css" />

HEADER1;
if(isset($custom_header)) {
	echo $custom_header;
}
$login = $logged_in ? '<li class="page_item"><a href="'.wp_logout_url('http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'">Logout</a></li>' : '<li class="page_item"><a href="./wp-login.php?redirect_to='.urlencode('http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'&amp;reauth=1">Login</a></li>';
$sql = 'SELECT * FROM '.db('pleasefix').' WHERE fixed = 0';
$req = $mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
if($req->num_rows > 0) {
	$pleasefix = '<li class="page_item"><a href="pleasefix.php" style="color:red;">Please fix</a></li>';
} else {
	$pleasefix = '';
}
echo <<<HEADER2

</head>

<body>
<div id="header">
<div id="title"><h1><a href="./">GregoBase</a>​</h1></div>
<div id="description">A database of gregorian scores</div>
<div id="access">

	<div class="menu"><ul class="sf-menu"><li class="page_item current_page_item"><a href="scores.php">Scores</a></li><li class="page_item"><a href="./?page_id=18">Participate</a></li><li class="page_item"><a href="./?page_id=5">Todo</a></li><li class="page_item"><a href="./?page_id=2">About</a></li>$login$pleasefix</ul></div>
	
</div><!-- #access -->
</div>
<div id="header_overlay"></div>

<div id="content">
HEADER2;

?>
