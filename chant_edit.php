<?php
# This stops WordPress from complaining about array post fields
$mypost = $_POST;
$_POST = array();
#
include('include/db.php');
include('include/txt.php');
include('include/sources.php');

if(array_key_exists("id", $_GET)||array_key_exists("id", $mypost)) {
	$id = array_key_exists("id", $_GET)?intval($_GET['id']):intval($mypost['id']);
} else {
	$id = '0';
}
$sql = 'SELECT * FROM '.db('chants').' WHERE id = '.$id;
$req = $mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
$c = $req->fetch_assoc();
if(!$c && $id != '0') {
	die('Wrong id');
}
$c_s = array();
$sql = 'SELECT * FROM '.db('chant_sources').' WHERE chant_id = '.$id;
$req = $mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
while ($s = $req->fetch_assoc()) {
	$c_s[] = $s;
}

$title = $c['incipit']?$c['incipit']:'New score';
$custom_header = <<<HEADER
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="autoresize.jquery.js"></script>
<script type="text/javascript" src="relCopy.min.js"></script>
<script type="text/javascript">
$(function () {
  // Apply the autoresize plugin to your textarea
  $("textarea").autoresize();
});
</script>
<script type="text/javascript">
$(function(){
  var removeLink = ' <a href="#" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><img src="list-remove.png" alt="Remove" /></a>';
$('a.add').relCopy({ append: removeLink});	
});
</script>
HEADER;
include('include/header.php');

if(!$logged_in) {
	echo '<p>Please <a href="wp-login.php?redirect_to='.urlencode('http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'&amp;reauth=1">login</a></p>';
} elseif($id == '0' && count($mypost) > 0) {
	$gabc = array();
	for($i=0;$i<count($mypost['type']);$i++) {
		if($mypost['content'][$i] > '') {
			$gabc[] = array($mypost['type'][$i],$mypost['content'][$i],array());
		}
	}
	if(count($gabc) == 0) {
		$mypost['gabc'] = NULL;
	} elseif(count($gabc) == 1 && $gabc[0][0] == 'gabc') {
		$mypost['gabc'] = str_replace('",[]]','",{}]', str_replace("\r","",json_encode($gabc[0][1], JSON_UNESCAPED_SLASHES)));
	} else {
		$mypost['gabc'] = str_replace('",[]]','",{}]', str_replace("\r","",json_encode($gabc, JSON_UNESCAPED_SLASHES)));
	}
	unset($mypost['type']);
	unset($mypost['content']);
	$s_p = array();
	for($i=0;$i<count($mypost['source']);$i++) {
		if($mypost['source'][$i] != '0') {
			$s_p[] = array("chant_id" => (string)$id, "source" => $mypost['source'][$i], "page" => $mypost['page'][$i], "sequence" => $mypost['sequence'][$i], "extent" => $mypost['extent'][$i]);
		}
	}
	unset($mypost['source']);
	unset($mypost['page']);
	unset($mypost['sequence']);
	unset($mypost['extent']);
	$mypost['gabc_verses'] = str_replace("\r","",$mypost['gabc_verses']);
	$mypost['tex_verses'] = str_replace("\r","",$mypost['tex_verses']);

	$sql = 'INSERT into '.db('chants').' (`incipit`) VALUES ("'.$mysqli->real_escape_string($mypost['incipit']).'")';
	$mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
	$id = $mysqli->insert_id;
	$mypost['id'] = $id;
	foreach(array('version','office-part','mode','mode_var','commentary','initial','transcriber','gabc','gabc_verses','tex_verses') as $k) {
		if($mypost[$k] > '' && $mypost[$k] != "(c4)") {
			$sql = 'UPDATE '.db('chants').' SET `'.$k.'` = "'.$mysqli->real_escape_string($mypost[$k]).'" WHERE `id` = '.$id;
			$mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
		}
	}
	foreach($s_p as $s) {
		$sql = 'INSERT into '.db('chant_sources').' VALUES ('.$id.','.$s['source'].',"'.$mysqli->real_escape_string($s['page']).'",'.intval($s['sequence']).','.intval($s['extent']).')';
		$mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
	}
	$t = time();
	$uid = $current_user->ID;
	$chgset = $t.'-'.$id.'-'.$uid;
	$sql = 'INSERT into '.db('changesets').' VALUES ('.$uid.','.$id.','.$t.', "Added to the database")';
	$mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
	header('Location: chant.php?id='.$id);
} elseif(count($mypost) > 3) {
	$gabc = array();
	for($i=0;$i<count($mypost['type']);$i++) {
		if($mypost['content'][$i] > '') {
			$gabc[] = array($mypost['type'][$i],$mypost['content'][$i],array());
		}
	}
	if(count($gabc) == 0) {
		$mypost['gabc'] = NULL;
	} elseif(count($gabc) == 1 && $gabc[0][0] == 'gabc') {
		$mypost['gabc'] = json_encode($gabc[0][1], JSON_UNESCAPED_SLASHES);
	} else {
		$mypost['gabc'] = json_encode($gabc, JSON_UNESCAPED_SLASHES);
	}
	unset($mypost['type']);
	unset($mypost['content']);
	$s_p = array();
	for($i=0;$i<count($mypost['source']);$i++) {
		if($mypost['source'][$i] != '0') {
			$s_p[] = array("chant_id" => (string)$id, "source" => $mypost['source'][$i], "page" => $mypost['page'][$i], "sequence" => $mypost['sequence'][$i], "extent" => $mypost['extent'][$i]);
		}
	}
	unset($mypost['source']);
	unset($mypost['page']);
	unset($mypost['sequence']);
	unset($mypost['extent']);

	$fields = array('id','incipit','version','office-part','mode','mode_var','commentary','initial','transcriber','gabc','gabc_verses','tex_verses');
	$old = array();
	$new = array();
	foreach($fields as $f) {
		$myfield = str_replace('",[]]','",{}]', str_replace("\r","",$mypost[$f]));
		if($c[$f] != $myfield) {
			$old[$f] = $c[$f];
			$new[$f] = $myfield;
		}
	}
	if($c_s != $s_p || count($old) > 0) {
		$t = time();
		$uid = $current_user->ID;
		$chgset = $t.'|'.$id.'|'.$uid;
		$sql = 'INSERT into '.db('changesets').' VALUES ('.$uid.','.$id.','.$t.', NULL)';
		$mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
		$mod = False;
		foreach($old as $k => $v) {
			$sql = 'INSERT into '.db('changes').' VALUES ("'.$chgset.'","'.$k.'","'.$mysqli->real_escape_string($v).'")';
			$mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
			$sql = 'UPDATE '.db('chants').' SET `'.$k.'` = "'.$mysqli->real_escape_string($new[$k]).'" WHERE `id` = '.$id;
			$mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
			if(in_array($k, array('office-part','mode','mode_var','commentary','initial','gabc','gabc_verses','tex_verses'))) {
				$mod = True;
			}
		}
		if($c_s != $s_p) {
			$sql = 'DELETE FROM  '.db('chant_sources').' WHERE `chant_id` = '.$id;
			$mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
			foreach($s_p as $s) {
				$sql = 'INSERT into '.db('chant_sources').' VALUES ('.$s['chant_id'].','.$s['source'].',"'.$mysqli->real_escape_string($s['page']).'",'.intval($s['sequence']).','.intval($s['extent']).')';
				$mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
			}
			$sql = 'INSERT into '.db('changes').' VALUES ("'.$chgset.'","sources","'.$mysqli->real_escape_string(json_encode($c_s, JSON_UNESCAPED_SLASHES)).'")';
			$mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
		}
		echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post"><input type="hidden" name="changeset" value="'.$chgset.'" />';
		echo "<h4>Please describe your changes</h4>\n".'<input name="comment" style="width:640px" />'."<br />\n<input type=\"submit\" />\n</form>\n";
		if($mod) {
			echo '<img src="chant_img.php?id='.$id.'&force=1" width="1" height="1" alt="" />';
		}
	} else {
		echo "<p>No changes made</p>";
	}
} elseif(count($mypost) > 0) {
	$chgset = explode('|',$mypost['changeset']);
	$sql = 'UPDATE '.db('changesets').' SET `comment` = "'.$mysqli->real_escape_string($mypost['comment']).'"  WHERE `user_id` = '.$chgset[2].' AND `chant_id` = '.$chgset[1].' AND `time` = '.$chgset[0];
	$mysqli->query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.$mysqli->error);
	header('Location: chant.php?id='.$id);
} else {
	$gabc = json_decode($c['gabc']);
	if(is_string($gabc)) {
		$gabc = array(array('gabc', $gabc, array()),);
	} elseif(empty($gabc)) {
		$gabc = array(array('gabc', "(c4)", array()),);
	}
	echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
	echo '<div id="score">';
	echo "<h4>Score</h4>";
	$i = 0;
	foreach($gabc as $g) {
		echo '<p class="clone1'.($i>0?' copy'.$i:'').'">';
		echo '<select name="type[]">';
		echo '<option value="gabc"'.($g[0]=='gabc'?' selected="selected"':'').">GABC</option>\n";
		echo '<option value="tex"'.($g[0]=='tex'?' selected="selected"':'').">TeX</option>\n";
		echo "</select>\n";
		echo '<textarea name="content[]" class="gabc">'.$g[1].'</textarea>';
		echo ($i>0?' <a class="remove" href="#" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><img src="list-remove.png" alt="Remove" /></a>':'');
		echo '</p>';
		$i++;
	}
	echo '<a href="#" class="add" rel=".clone1"><img src="list-add.png" alt="Add more" /></a>';
	echo "<h4>Hymn verses (GABC)</h4>\n";
	echo '<textarea name="gabc_verses" class="gabc">'.$c['gabc_verses']."</textarea>\n";
	echo "<h4>Hymn verses (TeX)</h4>\n";
	echo '<textarea name="tex_verses" class="gabc">'.$c['tex_verses']."</textarea>\n";
	echo '<br />&nbsp;</div>'."\n";
	echo '<div id="info">
	';
	echo '<h4>Incipit</h4><input name="incipit" value="'.$c['incipit'].'" />'."\n";
	echo '<h4>Version</h4><input name="version" value="'.$c['version'].'" />'."\n";
	echo '<h4>Usage</h4><select name="office-part">'."\n";
	echo '<option value="">Choose usage</option>'."\n";
	foreach($txt['usage'] as $k => $v) {
		echo '<option value="'.$k.'"'.($c['office-part']==$k?' selected="selected"':'').'>'.$v.'</option>'."\n";
	}
	echo "</select>\n";

	echo "<h4>Mode</h4>\n";
	echo '<select name="mode">'."\n";
	echo '<option value=""></option>'."\n";
	foreach(array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 'p' => 'T. pereg.') as $k => $v) {
		echo '<option value="'.$k.'"'.($c['mode']==$k?' selected="selected"':'').'>'.$v.'</option>'."\n";
	}
	echo "</select>\n";
	echo ' - Ending <input name="mode_var" value="'.$c['mode_var'].'" size="3" />'."\n";
	echo '<h4>Commentary</h4><input name="commentary" value="'.$c['commentary'].'" />';
	echo "<h4>Initial style</h4>\n";
	echo '<select name="initial">'."\n";
	echo '<option value="0">No initial</option>'."\n";
	echo '<option value="1" selected="selected">1-line initial</option>'."\n";
	echo '<option value="2">2-lines initial</option>'."\n";
	echo "</select>\n";

	echo '<h4>Original transcriber</h4><input name="transcriber" value="'.$c['transcriber'].'" />';

	function sources_box($so) {
		global $sources;
		$sources_box = '<select name="source[]">'."\n";
		$sources_box .= '<option value="0">Choose source</option>'."\n";
		foreach ($sources as $k => $s) {
			$sources_box .= '<option value="'.$k.'"'.($k==$so?' selected="selected"':'').">".$s['year'].' - '.$s['title'].' ('.$s['editor'].")</option>\n";
		}
		$sources_box .= "</select>\n";
		echo $sources_box;
	}
	echo "<h4>Sources</h4>\n";
	echo '<span style="margin-left:295px;">Page</span><span style="margin-left:40px;">Sequence</span><span style="margin-left:20px;">Extent</span>';
	$i = 0;
	foreach ($c_s as $s) {
		echo '<p class="clone2'.($i>0?' copy'.$i:'').'">';
		sources_box($s['source']);
		echo "</select>\n";
		echo '<input size="3" name="page[]" value="'.$s['page'].'" />';
		echo '<input size="3" name="sequence[]" value="'.$s['sequence'].'" />';
		echo '<input size="3" name="extent[]" value="'.$s['extent'].'" />';
		echo ($i>0?' <a class="remove" href="#" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><img src="list-remove.png" alt="Remove" /></a>':'');
		echo '</p>';
		$i++;
	}
	if(count($c_s) == 0) {
		echo '<p class="clone2">';
		sources_box('0');
		echo "</select>\n";
		echo '<input size="5" name="page[]" />';
		echo '<input size="5" name="extent[]" />';
		echo '</p>';
	}
	echo '<a href="#" class="add" rel=".clone2"><img src="list-add.png" alt="Add more" /></a>';
	
	echo '<p><input type="hidden" name="id" value="'.$id.'" /><input type="submit" /></p>';

	echo "</div>\n";
	echo "</form>\n";
}

include('include/footer.php');
?>
