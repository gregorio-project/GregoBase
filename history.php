<?php
include('include/db.php');
include('include/finediff.php');

if(array_key_exists("id", $_GET)) {
	$id = intval($_GET['id']);
} else {
	die('No id');
}
$sql1 = 'SELECT * FROM '.db('chants').' WHERE id = '.$id;
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
$c = $req1->fetch_assoc();

$title = 'History - '.$c['incipit'];
include('include/header.php');
echo "<h2>$title</h2>\n";

$sql1 = 'SELECT * FROM '.db('changesets').' WHERE chant_id = '.$id.' ORDER BY time DESC';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
while($m = $req1->fetch_assoc()) {
	echo "<h4>".date("M d, Y",$m['time'])." (".username_from_id($m['user_id']).")</h4>\n";
	echo "<p>".$m['comment']."</p>\n";
	$sql2 = 'SELECT * FROM '.db('changes').' WHERE changeset = "'.$m['user_id'].'|'.$id.'|'.$m['time'].'" ORDER BY field';
	$req2 = $mysqli->query($sql2) or die('Erreur SQL !<br />'.$sql2.'<br />'.$mysqli->error);
	while($f = $req2->fetch_assoc()) {
		echo '<p><i>'.$f['field']."</i><br />\n";
		$from_text = $f['changed'];
		$to_text = $c[$f['field']];
		$diff = new FineDiff($from_text, $to_text, FineDiff::$wordGranularity);
		$opcodes = FineDiff::getDiffOpcodes($from_text, $to_text);
		echo '<tt>'.FineDiff::renderDiffToHTMLFromOpcodes($from_text, $opcodes)."</tt></p>\n";
	}
}
echo "</ul>\n";
include('include/footer.php');
?>
