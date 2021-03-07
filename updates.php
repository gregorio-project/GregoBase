<?php
include('include/db.php');

if(array_key_exists("days", $_GET)) {
	$l = intval($_GET['days']);
} else {
	$l = 15;
}

if(array_key_exists("user", $_GET)) {
	$u = intval($_GET['user']);
	$user_info = get_userdata($u);
} else {
	$u = False;
}

$title = 'Updates in the last '.$l.' days'.($u?' by '.$user_info->display_name:'');
include('include/header.php');
echo "<h2>$title</h2>\n";
$sql1 = 'SELECT * FROM '.db('changesets').' WHERE `time` > '.(time() - ($l*24*60*60)).($u?' AND user_id = '.$u:'').' ORDER BY `time` DESC';
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
		$t = chant_from_id($m['chant_id']);
		if($t) {
			$user_info = get_userdata($m['user_id']);
			echo "<li>".' <a href="chant.php?id='.$m['chant_id'].'">'.format_incipit($t[1])."</a><br />\n";
			echo "<i>".htmlspecialchars($m['comment']).'</i>'.($u > ''?"":' <span class="version">(<a href="'.$_SERVER['PHP_SELF'].'?user='.$m['user_id'].'">'.$user_info->display_name."</a>)</span>")."</li>\n";
		}
	}
	echo "</ul>\n";
}
include('include/footer.php');
?>
