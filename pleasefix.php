<?php
include('include/db.php');

$title = 'Reported problems';
include('include/header.php');
echo "<h2>$title</h2>\n";
$sql = 'SELECT * FROM '.db('pleasefix').' WHERE fixed = 0 ORDER BY `time`';
$req = $mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
if($req->num_rows > 0) {
	while($fix = $req->fetch_assoc()) {
		echo '<h4><a href="chant.php?id='.$fix['chant_id'].'">'.format_incipit(chant_from_id($fix['chant_id'])[1])."</a></h4>\n";
		echo "<p><i>".nl2br(htmlspecialchars($fix['pleasefix'])).'</i> <span class="version"> (Reported on '.date("Y-m-d",$fix['time']);
		if($fix['user_id']) {
			$user_info = get_userdata($fix['user_id']);
			echo ' by '.$user_info->display_name;
		}
		echo ")</span></p>\n";
	}
} else {
	echo "<p>No problem reported</p>\n";
}
include('include/footer.php');
?>
