<?php
$dir = 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['HTTP_HOST'].substr($_SERVER['REQUEST_URI'],0,-8);

include('include/db.php');
include('include/functions.php');
header('Content-Type:application/atom+xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">

<id>'.$dir.'scores.php</id>
<title>GregoBase</title>
<subtitle>A database of gregorian scores</subtitle>
<link href="'.$dir.'scores.php" />
<link href="'.$dir.'feed.php" rel="self" />
';
$sql1 = 'SELECT * FROM '.db('changesets').' ORDER BY `time` DESC LIMIT 10';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);

$m = $req1->fetch_assoc();
echo '<updated>'.date(DATE_ATOM,$m['time']).'</updated>

';
$user_info = get_userdata($m['user_id']);
echo '<entry>
	<title type="html">'.format_incipit(chant_from_id($m['chant_id'])[1]).'</title>
	<link href="'.$dir.'chant.php?id='.$m['chant_id'].'" />
	<summary>'.$m['comment'].'</summary>
	<updated>'.date(DATE_ATOM,$m['time']).'</updated>
	<author>
		<name>'.$user_info->display_name.'</name>
	</author>
	<id>'.$dir.'history.php?changeset='.$m['time'].'%7C'.$m['chant_id'].'%7C'.$m['user_id'].'</id>
</entry>

';

while($m = $req1->fetch_assoc()) {
	$user_info = get_userdata($m['user_id']);
	echo '<entry>
	<title type="html">'.format_incipit(chant_from_id($m['chant_id'])[1]).'</title>
	<link href="http://test.selapa.net/gregobase/chant.php?id='.$m['chant_id'].'" />
	<summary>'.$m['comment'].'</summary>
	<updated>'.date(DATE_ATOM,$m['time']).'</updated>
	<author>
		<name>'.$user_info->display_name.'</name>
	</author>
	<id>http://test.selapa.net/gregobase/history.php?changeset='.$m['time'].'%7C'.$m['chant_id'].'%7C'.$m['user_id'].'</id>
</entry>
';
}
echo '</feed>';
?>