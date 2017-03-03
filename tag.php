<?php
include('include/db.php');

if(array_key_exists("id", $_GET)) {
	$s = intval($_GET['id']);
} else {
	die('No id');
}
$sql1 = 'SELECT * FROM '.db('tags').' WHERE `id` = '.$s;
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
$t = $req1->fetch_assoc();
$title = 'Tag - '.$t['tag'];
include('include/header.php');
echo "<h2>$title</h2>\n";

$chants = array();
$sql1 = 'SELECT * FROM '.db('chant_tags').' WHERE `tag_id` = '.$s;
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
while($ct = $req1->fetch_assoc()) {
	$sql2 = 'SELECT * FROM '.db('chants').' WHERE id = '.$ct['chant_id'];
	$req2 = $mysqli->query($sql2) or die('Erreur SQL !<br />'.$sql2.'<br />'.$mysqli->error);
	$chants[] = $req2->fetch_assoc();
}
if(count($chants)) {
	usort($chants, "custom_cmp");
}
echo "<ul class=\"incipit\">\n";
foreach($chants as $c) {
	$incipit = $c['incipit']?format_incipit($c['incipit']):'░░'.$c['id'].'░░';
	echo '<li class="usage-marker '.$c['office-part'].'">';
	if($c['gabc'] > '') {
		echo '<a href="chant.php?id='.$c['id'].'">'.$incipit."</a>";
	} else {
		echo '<span class="todo">'.$incipit.'</span>';
	}
	echo ' <span class="version">('.$c['version'].")</span></li>\n";
}
echo "</ul>\n";
include('include/footer.php');
?>
