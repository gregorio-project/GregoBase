<?php

function chant_from_id($c) {
	global $mysqli;
	$sql1 = 'SELECT `office-part`,incipit,gabc,version,duplicateof FROM '.db('chants').' WHERE id = '.$c;
	$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
	$chants = array();
	$ch = $req1->fetch_assoc();
	if(!$ch) $ch = array('office-part' => null, 'incipit' => null, 'gabc' => null, 'version' => null, 'duplicateof' => null);
	if($ch['duplicateof']) return false;
	return array($ch['office-part'], $ch['incipit'] > ''?$ch['incipit']:'░░'.$c.'░░', $ch['gabc'] > '', $ch['version']);
}

define("HOST", ""); // The host you want to connect to.
define("USER", ""); // The database username.
define("PASSWORD", ""); // The database password. 
define("DATABASE", ""); // The database name.
 
// PHP 8.1+ throws exceptions on mysqli errors by default; keep the former behaviour
// so that the existing `or die(...)` error handling keeps working.
mysqli_report(MYSQLI_REPORT_OFF);

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

// Display name of a WordPress user, or '' if the account doesn't exist (anymore)
function user_display_name($id) {
	$user_info = get_userdata($id);
	return $user_info ? $user_info->display_name : '';
}

?>
