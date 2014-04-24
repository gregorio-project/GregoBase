<?php
include('include/db.php');

if(array_key_exists("days", $_GET)) {
	$l = intval($_GET['days']);
} else {
	$l = 15;
}

$title = 'Updates in the last '.$l.' days';
include('include/header.php');
echo "<h2>$title</h2>\n";
$sql1 = 'SELECT * FROM '.db('changesets').' WHERE `time` > '.(time() - ($l*24*60*60)).' ORDER BY `time` DESC';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
$mod = array();
while($m = $req1->fetch_assoc()) {
	$d = date("Y-m-d",$m['time']);
	if(!array_key_exists($d,$mod)) $mod[$d] = array();
	$mod[$d][] = $m;
}
foreach($mod as $d => $ml) {
	echo "<h4>".$d."</h4>\n";
	echo "<ul>\n";
	foreach($ml as $m) {
		$user_info = get_userdata($m['user_id']);
		echo "<li>".' <a href="chant.php?id='.$m['chant_id'].'">'.format_incipit(chant_from_id($m['chant_id'])[1])."</a><br />\n";
		echo "<i>".htmlspecialchars($m['comment']).'</i> <span class="version">('.$user_info->display_name.")</span></li>\n";
	}
	echo "</ul>\n";
}
include('include/footer.php');
?>
