<?php
include('include/db.php');
include('include/txt.php');
include('include/sources.php');

if(array_key_exists("id", $_GET)) {
	$id = intval($_GET['id']);
} else {
	die('No id');
}
$sql1 = 'SELECT * FROM '.$db['chants'].' WHERE id = '.$id;
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
$c = $req1->fetch_assoc();
if(!$c) {
	die('Wrong id');
}

$title = $c['incipit'];
include('include/header.php');

if(!$logged_in) {
	echo "Please login";
} elseif(count($_POST) > 0) {
	#$mysqli->query('INSERT into '.$db['proofreading'].' VALUES ('.$id.','.$_SESSION['user_id'].','.time().')') or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
} else {

	$c_p = array();
	$sql1 = 'SELECT * FROM '.$db['chant_sources'].' WHERE chant_id = '.$id;
	$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
	while ($s = $req1->fetch_assoc()) {
		$c_s = array($s['source'], $s['page']);
		if(is_dir('./sources/'.$s['source'])) {
			if(is_array($sources[$s['source']]['pages'])) {
				$p = array_search($s['page'],$sources[$s['source']]['pages']);
			} else {
				$p = $s['page'];
			}
			$c_p[] = array($s['source'], $s['page'], $p, $s['extent']);
		} else {
			$c_p[] = $c_s;
		}
	}

	echo '<form  action="'.$_SERVER['PHP_SELF'].'" method="post">';
	echo '<div id="score"><br />';
	echo '<h4>Mode</h4><input name="annotation1" value="'.$c['mode'].'" size="3" /><input name="annotation2" value="'.$c['mode_var'].'" size="3" /><br />';
	echo '<h4>Initial style<select name="office-part">';
	echo '<option value="0">No initial</option>'."\n";
	echo '<option value="1" selected>1-line initial</option>'."\n";
	echo '<option value="2">2-lines initial</option>'."\n";
	echo "</select>\n";

	echo '<h4>GABC</h4><textarea name="gabc" id="gabc">'.($c['gabc']>''?$c['gabc']:'(c4)').'</textarea>';
	echo '<br />&nbsp;</div>'."\n";
	echo '<div id="info">
	';
	echo '<h4>Incipit</h4><input name="incipit" value="'.$c['incipit'].'" />
	<h4>Usage</h4><select name="office-part">';
	foreach($txt['usage'] as $k => $v) {
		echo '<option value="'.$k.'"'.($c['office-part']==$k?' selected':'').'>'.$v.'</option>'."\n";
	}
	echo "</select>\n";

	echo '<h4>Original transcriber</h4><input name="transcriber" value="'.$c['transcriber'].'" />';

	$sources_img = "";
	if(count($c_p) > 0) {
		echo "<h4>Sources</h4>\n<ul>\n";
		$cnt = 1;
		foreach($c_p as $s) {
			$source_label = "<i>".$sources[$s[0]]['title'].", ".$sources[$s[0]]['year']."</i>, p. ".$s[1];
			if (count($s) > 2) {
				echo '<li><a href="#source_'.$cnt.'">'.$source_label."</a></li>\n";
				$sources_img .= '<p><a name="source_'.$cnt.'">'.$source_label."</a><br />\n";
				for($i = 0; $i < $s[3]; $i++) {
					$sources_img .= '<img src="sources/'.$s[0].'/'.($s[2]+$i).'.png" alt="" /><br />'."\n";
				}
				$sources_img .= "</p>\n<hr />\n";
			} else {
				echo "<li>".$source_label."</li>\n";
			}
			$cnt += 1;
		}
		echo "</ul>\n";
	}
	echo "<hr />\n";

	echo $sources_img;

	echo "</div>\n";
	echo "</form>\n";
}
include('include/footer.php');
?>
