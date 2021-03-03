<?php
include('include/db.php');
include('include/txt.php');
include('include/sources.php');
$title = 'Home';
include('include/header.php');

echo "<h2>Chants</h2>\n";
echo "<h4>by incipit</h4>\n";
$sql1 = 'SELECT DISTINCT UPPER(SUBSTRING(incipit,1,1)) AS letters FROM '.db('chants')." WHERE UPPER(incipit) REGEXP '^[A-ZÆŒ]' ORDER BY letters ASC";
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
echo "<div><ul class=\"alphabet\">\n";
echo "<li><a href=\"incipit.php?letter=\">no incipit</a></li>\n";
while($s = $req1->fetch_assoc()) {
	echo "<li><a href=\"incipit.php?letter=".$s['letters']."\">".$s['letters']."</a></li>\n";
}
echo "</ul></div>\n<div style=\"clear:both;\"></div>\n";

echo "<h4>by usage</h4>\n";
$sql1 = 'SELECT * FROM '.db('chants').' WHERE `office-part` != "" GROUP BY `office-part` ORDER BY `office-part`';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
echo "<div><ul>\n";
while($s = $req1->fetch_assoc()) {
	echo '<li class="usage '.$s['office-part'].'"><a href="usage.php?id='.$s['office-part'].'">'.($txt['usage'][$s['office-part']]?$txt['usage'][$s['office-part']]:$s['office-part'])."</a></li>\n";
}
echo "</ul></div>\n";

$sql1 = 'SELECT * FROM '.db('tags').' t WHERE EXISTS (SELECT * FROM '.db('chant_tags').' ts WHERE t.id = ts.tag_id) ORDER BY tag';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
if($req1->num_rows > 0) {
	echo "<h4>by tag</h4>\n<div><ul>\n";
	while($t = $req1->fetch_assoc()) {
		echo "<li><a href=\"tag.php?id=".$t['id']."\">".$t['tag']."</a></li>\n";
	}
	echo "</ul></div>\n<div style=\"clear:both;\"></div>\n";
}


echo "<h4>by source</h4>\n";
$sql1 = 'SELECT * FROM '.db('chant_sources').' GROUP BY source';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
echo "<div><ul>\n";
$used_sources = array();
while($s = $req1->fetch_assoc()) {
	$used_sources[] = $s['source'];
}
foreach($sources as $id => $s) {
	if(in_array($id, $used_sources) || $s['pages']) {
		echo "<li><a href=\"source.php?id=".$id."\">".($s['period']?$s['period']:$s['year'])." - ".$s['editor']." - ".$s['title']."</a>";
		if($s['description'] > '') echo "<br />\n<i>".$s['description']."</i>";
		echo "</li>\n";
	}
}
$sql1 = 'SELECT * FROM '.db('chants').' c WHERE NOT EXISTS (SELECT * FROM '.db('chant_sources').' cs WHERE c.id = cs.chant_id)';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
if($req1->num_rows > 0) {
	echo "<li><a href=\"source.php?id=none\">No source</a></li>\n";
}
echo "</ul></div>\n";
echo '<div id="updates"><h4><a href="updates.php">Latest updates</a></h4>'."\n";
$sql1 = 'SELECT * FROM '.db('changesets').' ORDER BY `time` DESC LIMIT 10';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
$mod = array();

while($m = $req1->fetch_assoc()) {
	$d = date("Y-m-d",$m['time']);
	if(!array_key_exists($d,$mod)) $mod[$d] = array();
	$mod[$d][] = $m;
}
foreach($mod as $d => $ml) {
	echo $d;
	echo "<ul>\n";
	foreach($ml as $m) {
		$t = chant_from_id($m['chant_id']);
		if($t) {
			echo '<li><a href="chant.php?id='.$m['chant_id'].'">'.format_incipit($t[1])."</a><br />\n";
			echo "<i>".htmlspecialchars($m['comment'])."</i></li>\n";
		}
	}
	echo "</ul><br />\n";
}
echo "</div>";
include('include/footer.php');
?>
