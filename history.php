<?php
include('include/db.php');
include('include/finediff.php');

if(array_key_exists("changeset", $_GET)) {
	$chgset = explode('|',$_GET['changeset']);
} else {
	die('No changeset');
}
$sql1 = 'SELECT * FROM '.db('chants').' WHERE id = '.intval($chgset[1]);
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
$c = $req1->fetch_assoc();
$sql1 = 'SELECT * FROM '.db('changesets').' WHERE `chant_id` = '.intval($chgset[1]).' AND `time` > '.intval($chgset[0]).' ORDER BY `time` DESC';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
while($n = $req1->fetch_assoc()) {
	$sql2 = 'SELECT * FROM '.db('changes').' WHERE changeset = "'.$n['time'].'|'.$n['chant_id'].'|'.$n['user_id'].'" ORDER BY field';
	$req2 = $mysqli->query($sql2) or die('Erreur SQL !<br />'.$sql2.'<br />'.$mysqli->error);
	while($o = $req2->fetch_assoc()) {
		$c[$o['field']] = $o['changed'];
	}
}

$title = 'History - '.$c['incipit'];
include('include/header.php');
echo "<h2>$title</h2>\n";
$sql1 = 'SELECT * FROM '.db('changesets').' WHERE `user_id` = '.intval($chgset[2]).' AND `chant_id` = '.intval($chgset[1]).' AND `time` = '.intval($chgset[0]);
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
$m = $req1->fetch_assoc();
$user_info = get_userdata($m['user_id']);
echo "<h4>".date("M d, Y",$m['time'])." (".$user_info->display_name.")</h4>\n";
echo "<p>".$m['comment']."</p>\n";
$sql2 = 'SELECT * FROM '.db('changes').' WHERE changeset = "'.$mysqli->real_escape_string($_GET['changeset']).'" ORDER BY field';
$req2 = $mysqli->query($sql2) or die('Erreur SQL !<br />'.$sql2.'<br />'.$mysqli->error);
while($f = $req2->fetch_assoc()) {
	echo '<p><i>'.$f['field']."</i><br />\n";
	$from_text = $f['changed'];
	if($f['field'] == 'sources') {
		$c_s = array();
		$sql = 'SELECT * FROM '.db('chant_sources').' WHERE chant_id = '.intval($chgset[1]);
		$req = $mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
		while ($s = $req->fetch_assoc()) {
			$c_s[] = $s;
		}
		$to_text = json_encode($c_s);
	} else {
		$to_text = $c[$f['field']];
	}
	$diff = new FineDiff($from_text, $to_text, FineDiff::$wordGranularity);
	$opcodes = FineDiff::getDiffOpcodes($from_text, $to_text);
	echo '<tt>'.FineDiff::renderDiffToHTMLFromOpcodes($from_text, $opcodes)."</tt></p>\n";
}
echo "</ul>\n";
include('include/footer.php');
?>
