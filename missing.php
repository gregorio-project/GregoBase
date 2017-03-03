<?php
include('include/db.php');

$title = 'Missing scores';
include('include/header.php');
echo "<h2>$title</h2>\n";

$sql1 = 'SELECT * FROM '.db('chants').' WHERE `gabc` IS NULL ORDER BY incipit ASC';

$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
echo "<ul class=\"incipit\">\n";
while($c = $req1->fetch_assoc()) {
	$incipit = $c['incipit']?format_incipit($c['incipit']):'░░'.$c['id'].'░░';
	echo '<li class="usage-marker '.$c['office-part'].'">';
	echo '<a href="chant.php?id='.$c['id'].'">'.$incipit."</a>";
	echo ' <span class="version">('.$c['version'].")</span></li>\n";
}
echo "</ul>\n";
include('include/footer.php');
?>
