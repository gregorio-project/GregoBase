<?php
include('include/db.php');
include('include/txt.php');

if(array_key_exists("id", $_GET)) {
	$id = $_GET['id'];
} else {
	die('No id');
}
if(!array_key_exists($id,$txt['usage'])) {
	die('Wrong id');
}
$title = 'Usage - '.$txt['usage'][$id];
include('include/header.php');
echo "<h2>$title</h2>\n";
$sql1 = 'SELECT * FROM '.db('chants').' WHERE `duplicateof` IS NULL AND `office-part` = "'.$id.'" ORDER BY incipit ASC';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
$chants = array();
while($c = $req1->fetch_assoc()) {
	$chants[] = $c;
}
if(count($chants)) {
	usort($chants, "custom_cmp");
}
echo "<ul>\n";
foreach($chants as $c) {
	$incipit = $c['incipit']?format_incipit($c['incipit']):'░░'.$c['id'].'░░';
	echo '<li>';
	if($c['gabc'] > '') {
		echo '<a href="chant.php?id='.$c['id'].'">'.$incipit."</a>";
	} else {
		echo '<span class="todo">'.$incipit.'</span>';
	}
	echo " <span class=\"version\">(".$c['version'].")</span></li>\n";
}
echo "</ul>\n";
include('include/footer.php');
?>
