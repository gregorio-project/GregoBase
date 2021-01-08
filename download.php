<?php
include('include/db.php');
include('include/txt.php');
include('include/sources.php');

if(array_key_exists('id', $_GET)) {
	$id = intval($_GET['id']);
} else {
	die('No id');
}

if(array_key_exists('1verse', $_GET) && $_GET['1verse'] == '1') {
	$suffix = ".1verse";
} else {
	$suffix = "";
}

$sql1 = 'SELECT * FROM '.db('chants').' WHERE id = '.$id;
$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
$c = $req1->fetch_assoc();
if(!$c) {
	die('Wrong id');
}

function cleanString($string) {    
	$string = str_replace('Æ','ae', $string);
	$string = str_replace('æ','ae', $string);
	$string = str_replace('œ','oe', $string);
	$string = preg_replace("/[^a-z\d-_ ]/i","", $string);
	$string = str_replace(" ","_", trim($string));         
	$string = strtolower($string);
	return $string;
}

$formats = array('png' => 'image/png', 'pdf' => 'application/pdf', 'svg' => 'image/svg+xml', 'eps' => 'image/x-eps');

if(array_key_exists('format', $_GET)) {
	$f = $_GET['format'];
	$filename = cleanString($c['office-part'].'--'.$c['incipit'].'--	'.$c['version']);
	if($f == 'gabc') {
		$content = json_decode($c['gabc']);
		if(is_string($content)) {
			$gabc = $content;
		} elseif(is_array($content)) {
			$gabcs = array();
			foreach($content as $e) {
				if($e[0] == 'gabc') $gabcs[] = $e[1];
			}
			if(array_key_exists('elem', $_GET) && (int)$_GET['elem'] < count($gabcs)+1) {
				$gabc = $gabcs[(int)$_GET['elem']-1];
				$suffix = '.'.$_GET['elem'];
			} else {
				die();
			}
		}
		$c_p = array();
		$sql1 = 'SELECT * FROM '.db('chant_sources').' WHERE chant_id = '.$id;
		$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
		while ($s = $req1->fetch_assoc()) {
			$c_p[] = array($s['source'], $s['page']);
		}
		
		header('Content-Type:text/plain; charset=UTF-8');
		header('Content-Disposition: attachment; filename='.$filename.$suffix.'.'.'gabc');

		echo "name:".$c['incipit'].";\n";
		if($c['annotation1'] > ''){
			echo "annotation:".$c['annotation1'].";\n";
		}
		if($c['annotation2'] > ''){
			echo "annotation:".$c['annotation2'].";\n";
		}
		if($c['office-part'] > ''){
			echo "office-part:".$txt['usage'][$c['office-part']].";\n";
		}
		if($c['mode'] > ''){
			echo "mode:".$c['mode'].";\n";
		}
		if(count($c_p) > 0) {
			echo "book:";
			$source_label = '';
			foreach($c_p as $s) {
				$source_label .= $sources[$s[0]]['title'].", ".$sources[$s[0]]['year'].", p. ".$s[1].' & ';
			}
			echo substr($source_label, 0, strlen($source_label)-3).";\n";
		}
		if($c['transcriber'] > ''){
			echo "transcriber:".$c['transcriber'].";\n";
		}
		if($c['commentary'] > ''){
			echo "commentary:".$c['commentary'].";\n";
		}
		echo "%%\n";

		echo $gabc;
		if($c['gabc_verses'] && !($suffix > "")) {
			echo "\n".$c['gabc_verses'];
		}
	} elseif(in_array($f, array_keys($formats))) {
		header('Content-Type:'.$formats[$f]);
		header('Content-Disposition: attachment; filename='.$filename.$suffix.'.'.$f);
		echo(file_get_contents('scores/'.$f.'/'.$id.$suffix.'.'.$f));
	} else {
		die('Unknown format');
	}
}
