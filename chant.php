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

if($c['duplicateof'] > 0) {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: '.$_SERVER['PHP_SELF'].'?id='.$c['duplicateof']);
	header('Connection: close');
} elseif($c['duplicateof']) {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: ./scores.php');
	header('Connection: close');
}

$title = $c['incipit']?$c['incipit']:'░░'.$c['id'].'░░';
$custom_header = <<<HEADER
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="jquery.bpopup.min.js"></script>
<style>
#popup1, #popup2 { 
    background-color:#fff;
    border-radius:15px;
    color:#000;
    display:none; 
    padding:20px;
    min-width:400px;
    min-height: 80px;
}</style>
HEADER;
include('include/header.php');

if(isset($_POST['proofread']) && $_POST['proofread'] == 'Me') {
	$mysqli->query('INSERT into '.db('proofreading').' VALUES ('.$id.','.$current_user->ID.','.time().')') or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
}

if(isset($_POST['pleasefix']) && $_POST['pleasefix'] > '') {
	$mysqli->query('INSERT into '.db('pleasefix').' (chant_id,pleasefix,time,'.($logged_in ? 'user_id' : 'ip').') VALUES ('.$id.',"'.$mysqli->real_escape_string($_POST['pleasefix']).'",'.time().',"'.($logged_in ? $current_user->ID : $_SERVER['REMOTE_ADDR']).'")') or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
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
		if($p !== false) {
			$c_p[] = array($s['source'], $s['page'], $p, intval($s['extent']), intval($s['sequence']));
		} else {
			$c_p[] = $c_s;
		}
	} else {
		$c_p[] = $c_s;
	}
}

usort($c_p, function($a, $b) {
	global $sources;
	if($sources[$a[0]]['year'] == $sources[$b[0]]['year']) { // Date
		if($a[0] == $b[0]) { // Source
			if(count($a) > 2) {
				if($a[2] == $b[2]) { // Page
					return $a[4] <=> $b[4]; // Sequence
				} else {
					return $a[2] <=> $b[2];
				}
			} else {
				return strnatcasecmp($a[1], $b[1]);
			}
		} else {
			return $a[0] <=> $b[0];
		}
	} else {
		return $sources[$b[0]]['year'] <=> $sources[$a[0]]['year'];
	}
});

echo '<div id="score"><br />';
if($c['copyrighted']) {
	echo 'This tune is still under copyright.';
} elseif($c['gabc'] > '') {
	echo '<img src="chant_img.php?id='.$id.'" alt="" />';
} else {
	echo 'Yet to be transcribed. ';
	if($logged_in) {
		echo 'Please do it !';
	} else {
		echo 'Please log-in or register if you would like to do it.';
	}
}
echo '<br />&nbsp;</div>'."\n";
echo '<div id="info">
';
$sql = 'SELECT * FROM '.db('pleasefix').' WHERE chant_id = '.$id.' AND fixed = 0';
$req = $mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
echo '<h3>'.format_incipit($title);
if($req->num_rows > 0) {
	echo '<span id="push2"> <a href="#"><img src="warning.png" alt="Warning!" /></a></span>';
}
if($logged_in) {
	echo ' <span class="edit"><a href="chant_edit.php?id='.$id.'">Edit</a></span>';
}
echo '</h3>
';
if($req->num_rows > 0) {
	echo '<div id="popup2">';
	$count = 0;
	while($fix = $req->fetch_assoc()) {
		if($count > 0) echo "<hr />";
		echo '<p><img src="warning.png" alt="Warning!" /> '.nl2br(htmlspecialchars($fix['pleasefix']))."</p>\n";
		$count++;
	}
	echo '</div>';
}
if($c['cantusid'] > '') echo '<h4>Cantus ID</h4><ul><li><a target="_blank" href="http://cantusindex.org/id/'.$c['cantusid'].'">'.$c['cantusid']."</a></li></ul>\n";

if($c['version'] > '') echo '<h4>Version</h4><ul><li>'.$c['version']."</li></ul>\n";

if($c['office-part'] > '') echo '<h4>Usage</h4><ul><li><span class="usage '.$c['office-part'].'">'.$txt['usage'][$c['office-part']]."</span></li></ul>\n";

$tags = array();
$sql = 'SELECT * FROM '.db('chant_tags').' WHERE chant_id = '.$id;
$req = $mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
while ($t = $req->fetch_assoc()) {
	$sql1 = 'SELECT * FROM '.db('tags').' WHERE id = '.$t['tag_id'];
	$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
	$tt = $req1->fetch_assoc();
	$tags[$tt['id']] = $tt['tag'];
}
natcasesort($tags);

if(count($tags) > 0) {
	echo "<h4>Tags</h4><ul>\n";
	foreach($tags as $t) echo "<li>".$t."</li>\n";
	echo "</ul>\n";
}


$sources_img = "";
if(count($c_p) > 0) {
	echo "<h4>Sources</h4>\n<ul>\n";
	$cnt = 1;
	foreach($c_p as $s) {
		$source_label = "<i>".$sources[$s[0]]['title'].", ".$sources[$s[0]]['editor'].", ".($sources[$s[0]]['period']?$sources[$s[0]]['period']:$sources[$s[0]]['year'])."</i>".($s[1]>''?", p. ".$s[1]:'');
		$urls = $sources[$s[0]]['urls'];
		if (count($s) > 2) {
			echo '<li><a href="#source_'.$cnt.'">'.$source_label."</a></li>\n";
			$sources_img .= '<p><a name="source_'.$cnt.'">'.$source_label."</a> &nbsp;";
			$chants = array();
			$sql1 = 'SELECT * FROM '.db('chant_sources').' WHERE `source` = "'.$s[0].'" ORDER BY sequence ASC';
			$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
			while($co = $req1->fetch_assoc()) {
				$chants[$co['page']][] = array(intval($co['chant_id']),intval($co['sequence']),intval($co['extent']));
			}
			$prev = false;
			if(array_search([$id,$s[4],$s[3]], $chants[$s[1]]) > 0) {
				$prev = $chants[$s[1]][array_search([$id,$s[4],$s[3]], $chants[$s[1]])-1];
			} elseif(is_array($sources[$s[0]]['pages'])) {
				$j = $s[2];
				while($j > 0) {
					$j--;
					if(array_key_exists($sources[$s[0]]['pages'][$j], $chants) && count($chants[$sources[$s[0]]['pages'][$j]]) > 0) {
						$prev = end($chants[$sources[$s[0]]['pages'][$j]]);
						break;
					}
				}
			} else {
				uksort($chants, 'strnatcmp');
				// TODO
			}
			if($prev) $sources_img .= ' <a class="prevnext" href="chant.php?id='.$prev[0].'" title="Previous chant in this source" >◀</a>';
			$next = false;
			if(count($chants[$s[1]]) > 1 && array_search([$id,$s[4],$s[3]], $chants[$s[1]]) < count($chants[$s[1]])-1) {
				$next = $chants[$s[1]][array_search([$id,$s[4],$s[3]], $chants[$s[1]])+1];
			} elseif(is_array($sources[$s[0]]['pages'])) {
				$j = $s[2];
				while($j < count($sources[$s[0]]['pages'])) {
					$j++;
					if(array_key_exists($sources[$s[0]]['pages'][$j], $chants) && count($chants[$sources[$s[0]]['pages'][$j]]) > 0) {
						$next = $chants[$sources[$s[0]]['pages'][$j]][0];
						break;
					}
				}
			} else {
				uksort($chants, 'strnatcmp');
				// TODO
			}
			if($next) $sources_img .= ' <a class="prevnext" href="chant.php?id='.$next[0].'" title="Next chant in this source" >▶</a>';
			$sources_img .= "<br />\n";
			for($i = 0; $i < max(1, $s[3]); $i++) {
				if(is_array($urls)) $sources_img .= '<a target="_new" href="'.$urls[0].$urls[1][$s[2]+$i].$urls[2].'">';
				$sources_img .= '<img src="sources/'.$s[0].'/'.($s[2]+$i).'.png" alt="" />';
				if(is_array($urls)) $sources_img .= '</a>';
				$sources_img .= '<br />'."\n";
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
if($logged_in) {
	echo '<p id="push1"><a href="#">Report a problem</a></p>';
	$report_form = 	'<form action="'.$_SERVER['PHP_SELF'].($_SERVER['QUERY_STRING']?'?'.$_SERVER['QUERY_STRING']:'').'" method="post"><textarea name="pleasefix" class="gabc"></textarea><br /><input type="submit" /></form>';

	echo <<<POPUP1
<div id="popup1">
Please describe the problem:<br />
$report_form
</div>
POPUP1;
}
if(!$c['copyrighted']) {
	echo "<h4>Download</h4>\n<ul>\n";
	$content = json_decode($c['gabc']);
	if(is_string($content)) {
		echo '<li><a href="download.php?id='.$c['id'].'&amp;format=gabc">GABC</a></li>'."\n";
	} elseif(is_array($content)) {
		$gabcs = array();
		foreach($content as $e) {
			if($e[0] == 'gabc') $gabcs[] = $e[1];
		}
		if(count($gabcs) > 1) {
			echo "<li>GABC<ul>";
			for($i = 0; $i < count($gabcs); $i++) {
				echo '<li><a href="download.php?id='.$c['id'].'&amp;format=gabc&amp;elem='.($i+1).'">Element '.($i+1)."</a></li>\n";
			}
			echo "</ul></li>\n";
		} else {
			echo '<li><a href="download.php?id='.$c['id'].'&amp;format=gabc&amp;elem=1">GABC</a></li>'."\n";
		}
	}
	foreach(array('pdf','svg','eps','png') as $a) {
		echo '<li><a href="download.php?id='.$c['id'].'&amp;format='.$a.'">'.strtoupper($a).'</a></li>'."\n";
	}
	echo "</ul>\n";
	if($c['gabc_verses'] || $c['tex_verses']){
		echo "<ul>\n";
		foreach(array('gabc','pdf','svg','eps','png') as $a) {
			echo '<li><a href="download.php?id='.$c['id'].'&amp;format='.$a.'&amp;1verse=1">'.strtoupper($a).' (1st verse only)</a></li>'."\n";
		}
		echo "</ul>\n";
	}

	echo "<h4>Open with external tool</h4>\n<ul>\n";
	$gabc = (($c['office-part'] && $txt['usage_s'][$c['office-part']] > '')?'annotation: '.$txt['usage_s'][$c['office-part']]."\n":'');
	$mode_r = False;
	if($c['mode'] > '') {
		if($c['mode'] == 'p') {
		$mode = "T. pereg.";
		} elseif(in_array($c['mode'], array('c','d','e'))) {
		$mode = strtoupper($c['mode']).($c['mode_var']?' '.$c['mode_var']:'');
		} else {
		$mode = $c['mode'].($c['mode_var']?' '.$c['mode_var']:'');
		}
	}
	if($mode) $gabc .= "annotation: $mode\n";
	$gabc .= "%%\n";
	$pure_gabc = '';
	if(is_string($content)) {
		$pure_gabc .= $content."\n";
	} elseif(is_array($content)) {
		foreach($content as $e) {
			if($e[0] == 'gabc') $pure_gabc .= $e[1]."\n";
		}
	}
	$gabc .= $pure_gabc;
	echo '<li><a href="https://editor.sourceandsummit.com/alpha/#'.rawurlencode(($c['commentary']?'text-right: '.$c['commentary']."\n":'').$gabc."\n".$c['gabc_verses']).'" target="_blank">Source &amp; Summit Editor</a></li>
<li><a href="https://editor.sourceandsummit.com/legacy/#'.rawurlencode(($c['commentary']?'commentary: '.$c['commentary']."\n":'').$gabc."\n".$c['gabc_verses']).'" target="_blank">Illuminare Score editor</a></li>
<li><a href="https://scrib.io/#q='.rawurlencode($pure_gabc."\n".$c['gabc_verses']).'" target="_blank">Neumz NABC Renderer</a></li>
';

	echo "</ul>\n";
}
if($c['remarks'] > '') {
	echo "<h4>Remarks</h4>\n<p class=\"remarks\">".nl2br($c['remarks'])."</p>\n";
}
$sql1 = 'SELECT * FROM '.db('changesets').' WHERE chant_id = '.$c['id'].' ORDER BY time DESC';
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
if($req1->num_rows > 0 || $c['transcriber'] > '') {
	echo "<h4>History</h4>\n<ul>\n";
}
while ($m = $req1->fetch_assoc()) {
	$user_info = get_userdata($m['user_id']);
	echo "<li>".date("M d, Y",$m['time']).": ".htmlspecialchars($m['comment'])." (".$user_info->display_name.') <a href="history.php?changeset='.$m['time'].'|'.$id.'|'.$m['user_id']."\">?</a></li>\n";
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

echo <<<SCRIPT
<script type="text/javascript">
    // Semicolon (;) to ensure closing of earlier scripting
    // Encapsulation
    // $ is assigned to jQuery
    ;(function($) {

         // DOM Ready
        $(function() {

            // Binding a click event
            // From jQuery v.1.7.0 use .on() instead of .bind()
            $('#push1').bind('click', function(e) {

                // Prevents the default action to be triggered. 
                e.preventDefault();

                // Triggering bPopup when click event is fired
                $('#popup1').bPopup();

            });
            $('#push2').bind('click', function(e) {

                // Prevents the default action to be triggered. 
                e.preventDefault();

                // Triggering bPopup when click event is fired
                $('#popup2').bPopup();

            });

        });

    })(jQuery);
</script>
SCRIPT;
include('include/footer.php');
?>
