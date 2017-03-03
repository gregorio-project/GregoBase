<?php

function chant_from_id($c) {
	global $mysqli;
	$sql1 = 'SELECT `office-part`,incipit,gabc,version FROM '.db('chants').' WHERE id = '.$c;
	$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
	$chants = array();
	$ch = $req1->fetch_assoc();
	return array($ch['office-part'], $ch['incipit'] > ''?$ch['incipit']:'░░'.$c.'░░', $ch['gabc'] > '', $ch['version']);
}

define("HOST", ""); // The host you want to connect to.
define("USER", ""); // The database username.
define("PASSWORD", ""); // The database password. 
define("DATABASE", ""); // The database name.
 
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);

$mysqli->query('SET NAMES utf8');

$db_prefix = 'gregobase_';
function db($s) {
	global $db_prefix;
	return $db_prefix.$s;
}

require_once('./wp-blog-header.php');
$current_user = wp_get_current_user();
$logged_in = is_user_logged_in();

?>
