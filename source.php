<?php
include('include/db.php');
include('include/sources.php');

if(array_key_exists("id", $_GET)) {
	$s = intval($_GET['id']);
} else {
	die('No id');
}
$chants = array();
if($s == "none") {
	$sql1 = 'SELECT * FROM '.db('chants').' c WHERE NOT EXISTS (SELECT * FROM '.db('chant_sources').' cs WHERE c.id = cs.chant_id)';
	$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
	while($c = $req1->fetch_assoc()) {
		$chants[''][] = array($c['id'],1,1);
	}
} else {
	$sql1 = 'SELECT * FROM '.db('chant_sources').' WHERE `source` = "'.$s.'" ORDER BY sequence ASC';
	$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
	while($c = $req1->fetch_assoc()) {
		$chants[$c['page']][] = array($c['chant_id'],$c['sequence'],$c['extent']);
	}
}
if(array_key_exists($s, $sources)) {
	$title = 'Sources - '.$sources[$s]['year']." - ".$sources[$s]['editor']." - ".$sources[$s]['title'];
} else {
	$title = 'Sources - no source';
}
include('include/header.php');
echo "<h2>$title</h2>\n";
if(array_key_exists('images', $_GET) && $_GET['images'] == '1') {
	$ipp = 25;
	if(is_array($sources[$s]['pages'])) {
		$np = count($sources[$s]['pages']);
		$start = array_key_exists('index', $_GET) ? intval($_GET['index']*$ipp) : 0;
		if($start > $np) $start = 0;
		$n = [];
		for($i = 0; $i < $np; $i += $ipp) {
			$n[$i] = ($i == $start) ? '' : '<a href="source.php?id='.$s.'&images=1&index='.($i/$ipp).'">';
			$n[$i] .= $sources[$s]['pages'][$i].'-'.$sources[$s]['pages'][min($i+$ipp-1, $np-1)];
			$n[$i] .= ($i == $start) ? '' : '</a>';
		}
		echo "<p><b>Pages</b> ".implode(' | ', $n)."</p>\n";
		echo "<table>\n<tr><th>Page</th><th>Incipit</th></tr>";
		for($i = $start; $i < min($start+$ipp, $np); $i++) {
			$p = $sources[$s]['pages'][$i];
			echo "<tr><td>$p</td><td>";
			echo "<ul class=\"incipit\">\n";
			if(array_key_exists($p,$chants)) {
				$ch = $chants[$p];
				foreach($ch as $c) {
					$t = chant_from_id($c[0]);
					echo '<li class="usage-marker '.$t[0].'">';
					if($t[2]) {
						echo '<a href="chant.php?id='.$c[0].'">'.format_incipit($t[1])."</a>";
					} else {
						echo '<span class="todo">'.format_incipit($t[1]).'</span> (<a href="chant_edit.php?id='.$c[0].'">TODO</a>)';
					}
					echo "</li>\n";
				}
			}
			echo '<li class="add-chant"><a href="chant_edit.php?source='.$s.'&amp;page='.$p.'">Add chant</a></li>'."\n";
			echo "</ul>\n";
			echo "</td>\n";
			if(is_dir('./sources/'.$s)) {
				echo '<td><img src="sources/'.$s.'/'.$i.'.png" /></td>';
			}
		}
		echo "</table>\n";
		echo "<p><b>Pages</b> ".implode(' | ', $n)."</p>\n";
	}
} else {
	if(is_array($sources[$s]['pages'])) {
		echo '<p><a href="source.php?id='.$s.'&images=1">Show source images</a></p>'."\n";
	}
	echo "<table>\n<tr><th>Page</th><th>Incipit</th></tr>";
	if(is_array($sources[$s]['pages'])) {
		foreach($sources[$s]['pages'] as $p) {
			if(array_key_exists($p,$chants)) {
				echo "<tr><td>$p</td><td>";
				echo "<ul class=\"incipit\">\n";
				$ch = $chants[$p];
				unset($chants[$p]);
				foreach($ch as $c) {
					$t = chant_from_id($c[0]);
					echo '<li class="usage-marker '.$t[0].'">';
					if($t[2]) {
						echo '<a href="chant.php?id='.$c[0].'">'.format_incipit($t[1])."</a>";
					} else {
						echo '<span class="todo">'.format_incipit($t[1]).'</span>';
					}
					echo "</li>\n";
				}
				echo "</ul>\n";
				echo "</td></tr>\n";
			}
		}
	}
	uksort($chants, 'strnatcmp');
	foreach($chants as $p => $ch) {
		echo "<tr><td>$p</td><td><ul class=\"incipit\">";
		$l = array();
		foreach($ch as $c) {
			$t = chant_from_id($c[0]);
			$l[] = $t[1]."=-=".$t[0]."=-=".$c[0]."=-=".$c[1]."=-=".$c[2];
		}
		natcasesort($l);
		foreach($l as $ll) {
			$ll = explode('=-=',$ll);
			echo '<li class="usage-marker '.$ll[1].'"><a href="chant.php?id='.$ll[2].'">'.$ll[0]."</a></li>\n";
		}
		echo "</ul></td></tr>\n";
	}
	echo "</table>\n";
}

include('include/footer.php');
?>
