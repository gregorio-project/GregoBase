<?php
include('include/db.php');

$title = 'Booklets';
include('include/header.php');
echo "<h2>$title</h2>\n";

$sql1 = 'SELECT * FROM '.db('booklets').' ORDER BY filename';

$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
echo "<ul class=\"incipit\">\n";
while($b = $req1->fetch_assoc()) {
	echo '<li>'.$b['title']; //<tt>'.$b['id'].'</tt> 
	echo ' <span class="version">('.$b['filename'].")</span><ul>";
	$c = json_decode($b['content'],true);
	foreach($c as $l) {
		if($l[0] == 'score') {
			if(is_int($l[1])) {
				$t = chant_from_id($l[1]);
				$e = array($t[0],'<a href="chant.php?id='.$l[1].'">'.format_incipit($t[1]).'</a> <span class="version">('.$t[3].')</span>');
				if(array_key_exists("annotation_prefix",$l[2])) {
					$e[1] = $l[2]["annotation_prefix"].' '.$e[1];
				}
			} elseif(is_string($l[1])) {
				$e = $l[1];
			} elseif(is_array($l[1])) {
			}
		} elseif($l[0] == 'tex') {
			$e = '<tt>'.$l[1].'</tt>';
		}
		if(is_array($e)) {
			echo '<li class="usage-marker '.$e[0].'">'.$e[1]."</li>\n";
		} else {
			echo "<li>$e</li>\n";
		}
	}
	echo "</ul></li>\n";
}
echo "</ul>\n";
include('include/footer.php');
?>
