<?php

function chant_from_id($c) {
	global $db,$mysqli;
	$sql1 = 'SELECT `office-part`,incipit,gabc,version FROM '.$db['chants'].' WHERE id = '.$c;
	$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
	$chants = array();
	$ch = $req1->fetch_assoc();
	return array($ch['office-part'], $ch['incipit'], $ch['gabc'] > '', $ch['version']);
}

define("HOST", ""); // The host you want to connect to.
define("USER", ""); // The database username.
define("PASSWORD", ""); // The database password. 
define("DATABASE", ""); // The database name.
 
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);

$mysqli->query('SET NAMES utf8');

function db($s) {
	$db_prefix = 'gregobase_';
	return $db_prefix.$s;
}

$db_prefix = 'gregobase_';
$db = array('booklets'      => $db_prefix.'booklets',
            'chants'        => $db_prefix.'chants',
            'chant_sources' => $db_prefix.'chant_sources',
            'sources'       => $db_prefix.'sources',
            'changes'       => $db_prefix.'changes',
            'changesets'    => $db_prefix.'changesets',
            'users'         => $db_prefix.'users',
            'proofreading'  => $db_prefix.'proofreading');

require_once('./wp-blog-header.php');
$current_user = wp_get_current_user();
$logged_in = is_user_logged_in();

?>
