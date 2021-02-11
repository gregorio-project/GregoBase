<?php
$sources = array();

$sql1 = 'SELECT * FROM '.db('sources').' ORDER BY year,editor,title';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
while ($s = $req1->fetch_assoc()) {
	$pages = json_decode($s['pages']);
	if($pages) $pages = array_map('strval', $pages);
	$s['pages'] = $pages;
	$sources[$s['id']] = $s;
}

?>
