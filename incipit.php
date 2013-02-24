<?php
include('include/db.php');

if(array_key_exists("letter", $_GET)) {
	$l = $mysqli->real_escape_string($_GET['letter']);
} else {
	die('No letter');
}
$title = 'Incipit - '.$l;
include('include/header.php');
echo "<h2>$title</h2>\n";

if($l) {
	$sql1 = 'SELECT * FROM '.$db['chants'].' WHERE `incipit` LIKE "'.$l.'%" ORDER BY incipit ASC';
} else {
	$sql1 = 'SELECT * FROM '.$db['chants'].' WHERE `incipit` LIKE "" ORDER BY incipit ASC';
}
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
$chants = array();
while($c = $req1->fetch_assoc()) {
	$chants[] = $c;
}
if(count($chants)) {
	usort($chants, "custom_cmp");
}
echo "<ul class=\"incipit\">\n";
foreach($chants as $c) {
	$incipit = $c['incipit']?format_incipit($c['incipit']):"===";
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
