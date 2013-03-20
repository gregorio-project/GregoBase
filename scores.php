<?php
include('include/db.php');
include('include/txt.php');
include('include/sources.php');
$title = 'Home';
include('include/header.php');

echo "<h3>Chants</h3>\n";
echo "<h4>by source</h4>\n";
$sql1 = 'SELECT * FROM '.db('chant_sources').' GROUP BY source';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
echo "<div><ul>\n";
$used_sources = array();
while($s = $req1->fetch_assoc()) {
	$used_sources[] = $s['source'];
}
foreach($sources as $id => $s) {
	if(in_array($id, $used_sources)) {
		echo "<li><a href=\"source.php?id=".$id."\">".$s['year']." - ".$s['editor']." - ".$s['title']."</a></li>\n";
	}
}
$sql1 = 'SELECT * FROM '.db('chants').' c WHERE NOT EXISTS (SELECT * FROM '.db('chant_sources').' cs WHERE c.id = cs.chant_id)';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error());
if($req1->num_rows > 0) {
	echo "<li><a href=\"source.php?id=none\">No source</a></li>\n";
}
echo "</ul></div>\n";
echo "<h4>by usage</h4>\n";
$sql1 = 'SELECT * FROM '.db('chants').' WHERE `office-part` != "" GROUP BY `office-part` ORDER BY `office-part`';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error());
echo "<div><ul>\n";
while($s = $req1->fetch_assoc()) {
	echo '<li class="usage '.$s['office-part'].'"><a href="usage.php?id='.$s['office-part'].'">'.$txt['usage'][$s['office-part']]."</a></li>\n";
}
echo "</ul></div>\n";
echo "<h4>by incipit</h4>\n";
$sql1 = 'SELECT DISTINCT UPPER(SUBSTRING(incipit,1,1)) AS letters FROM '.db('chants').' ORDER BY letters ASC';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error());
echo "<div><ul>\n";
while($s = $req1->fetch_assoc()) {
	echo "<li><a href=\"incipit.php?letter=".$s['letters']."\">".($s['letters']?$s['letters']:"no incipit")."</a></li>\n";
}
echo "</ul></div>\n";

include('include/footer.php');
?>
