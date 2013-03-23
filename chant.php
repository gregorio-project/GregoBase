<?php
include('include/db.php');
include('include/txt.php');
include('include/sources.php');

if(array_key_exists("id", $_GET)) {
	$id = intval($_GET['id']);
} else {
	die('No id');
}
$sql1 = 'SELECT * FROM '.db('chants').' WHERE id = '.$id;
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
$c = $req1->fetch_assoc();
if(!$c) {
	die('Wrong id');
}

$title = $c['incipit'];
include('include/header.php');

if(isset($_POST['proofread']) && $_POST['proofread'] == 'Me' && $c) {
	$mysqli->query('INSERT into '.db('proofreading').' VALUES ('.$id.','.$current_user->ID.','.time().')') or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
}

$c_p = array();
$sql1 = 'SELECT * FROM '.db('chant_sources').' WHERE chant_id = '.$id.' ORDER BY source';
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

echo '<div id="score"><br />';
if($c['gabc'] > '') {
	echo '<img src="scores/'.$id.'.png" alt="" />';
} else {
	if($logged_in != true) {
		echo 'Yet to be transcribed. Please log-in or register if you would like to do it.';
	}
}
echo '<br />&nbsp;</div>'."\n";
echo '<div id="info">
';
echo '<h3>'.format_incipit($c['incipit']);
if($logged_in) {
	echo ' <span class="edit"><a href="chant_edit.php?id='.$id.'">Edit</a></span>';
}
echo '</h3>
<h4>Version</h4><ul><li>'.$c['version']."</li></ul>\n";

echo '<h4>Usage</h4><ul><li><span class="usage '.$c['office-part'].'">'.$txt['usage'][$c['office-part']]."</span></li></ul>\n";

$sources_img = "";
if(count($c_p) > 0) {
	echo "<h4>Sources</h4>\n<ul>\n";
	$cnt = 1;
	foreach($c_p as $s) {
		$source_label = "<i>".$sources[$s[0]]['title'].", ".$sources[$s[0]]['editor'].", ".$sources[$s[0]]['year']."</i>".($s[1]>''?", p. ".$s[1]:'');
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

$sql1 = 'SELECT * FROM '.db('proofreading').' WHERE chant_id = '.$c['id'].' ORDER BY time DESC';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
$proof = array();
$proof_done = False;
while ($r = $req1->fetch_assoc()) {
	$proof[] = $r;
	if($logged_in && $r['user_id'] == $current_user->ID) $proof_done = True;
}
if(count($proof) > 0 || $logged_in) {
	echo "<h4>Proofread by:</h4>\n<ul>\n";
}
if($logged_in && !$proof_done) {
	echo '<li><form action="'.$_SERVER['PHP_SELF'].($_SERVER['QUERY_STRING']?'?'.$_SERVER['QUERY_STRING']:'').'" method="post"><input type="submit" name="proofread" value="Me" /></form></li>'."\n";
}
foreach($proof as $r) {
	$user_info = get_userdata($r['user_id']);
	echo "<li>".$user_info->display_name." (".date("M d, Y",$r['time']).")</li>\n";
}
if(count($proof) > 0 || $logged_in) {
	echo "</ul>\n";
}

echo "<h4>Download</h4>\n<ul>\n";
$content = json_decode($c['gabc']);
if(is_string($content)) {
	echo '<li><a href="download.php?id='.$c['id'].'&amp;format=gabc">GABC</a></li>'."\n";
} elseif(is_array($content)) {
	$gabcs = array();
	foreach($content as $e) {
		if($e[0] == 'gabc') $gabcs[] = $e[1];
	}
	echo "<li>GABC<ul>";
	for($i = 0; $i < count($gabcs); $i++) {
		echo '<li><a href="download.php?id='.$c['id'].'&amp;format=gabc&amp;elem='.($i+1).'">Element '.($i+1)."</a></li>\n";
	}
	echo "</ul></li>\n";
}
foreach(array('pdf','eps','png') as $a) {
	echo '<li><a href="download.php?id='.$c['id'].'&amp;format='.$a.'">'.strtoupper($a).'</a></li>'."\n";
}
echo "</ul>\n";
if($c['gabc_verses'] || $c['tex_verses']){
	echo "<ul>\n";
	foreach(array('gabc','pdf','eps','png') as $a) {
		echo '<li><a href="download.php?id='.$c['id'].'&amp;format='.$a.'&amp;1verse=1">'.strtoupper($a).' (1st verse only)</a></li>'."\n";
	}
	echo "</ul>\n";
}
$sql1 = 'SELECT * FROM '.db('changesets').' WHERE chant_id = '.$c['id'].' ORDER BY time DESC';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
if($req1->num_rows > 0 || $c['transcriber'] > '') {
	echo "<h4>History</h4>\n<ul>\n";
}
while ($m = $req1->fetch_assoc()) {
	$user_info = get_userdata($m['user_id']);
	echo "<li>".date("M d, Y",$m['time']).": ".$m['comment']." (".$user_info->display_name.")</li>\n";
}
if($c['transcriber'] > '') {
	echo "<li>Original transcriber: ".$c['transcriber']."</li>\n";
}
if($req1->num_rows > 0 || $c['transcriber'] > '') {
	echo "</ul>\n";
}
echo "<hr />\n";

echo $sources_img;

echo '</div>';

include('include/footer.php');
?>
