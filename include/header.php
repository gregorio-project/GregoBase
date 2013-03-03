<?php
function format_incipit($incipit) {
	return $incipit = strtr($incipit, array(' Ps.' => ' <i>Ps.</i>',' cum Alleluia' => ' <i>cum</i> Alleluia',' sine Alleluia' => ' <i>sine</i> Alleluia','('=>'<i>(',')'=>')</i>'));
}

class RomanNumber {
    //array of roman values
    public static $roman_values=array(
        'I' => 1, 'V' => 5, 
        'X' => 10, 'L' => 50,
        'C' => 100, 'D' => 500,
        'M' => 1000,
    );
    //values that should evaluate as 0
    public static $roman_zero=array('N', 'nulla');
    //Regex - checking for valid Roman numerals
    public static $roman_regex='/^M{0,3}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$/';

    //Roman numeral validation function - is the string a valid Roman Number?
    static function IsRomanNumber($roman) {
         return preg_match(self::$roman_regex, $roman) > 0;
    }

    //Conversion: Roman Numeral to Integer
    static function Roman2Int ($roman) {
        //checking for zero values
        if (in_array($roman, self::$roman_zero)) {
            return 0;
        }
        //validating string
        if (!self::IsRomanNumber($roman)) {
            return false;
        }

        $values=self::$roman_values;
        $result = 0;
        //iterating through characters LTR
        for ($i = 0, $length = strlen($roman); $i < $length; $i++) {
            //getting value of current char
            $value = $values[$roman[$i]];
            //getting value of next char - null if there is no next char
            $nextvalue = !isset($roman[$i + 1]) ? null : $values[$roman[$i + 1]];
            //adding/subtracting value from result based on $nextvalue
            $result += (!is_null($nextvalue) && $nextvalue > $value) ? -$value : $value;
        }
        return $result;
    }
}

$days = array('sund','sunday','sundays','monday','tuesday','wednesday','thursday','friday','saturday');
$hours = array('matins','lauds','prime','terce','sext','none','vespers','compline');

function custom_split($str) {
	global $days, $hours;
	$test = preg_split('/(?<=\D)\d|(?<=\d)\D|[ \.,\(\)]/', $str,0,PREG_SPLIT_NO_EMPTY);
	$i = 1;
	while(count($test) > $i) {
		if(ctype_alpha($test[$i]) && ctype_alpha($test[$i-1]) &&
		   !RomanNumber::IsRomanNumber($test[$i]) && !RomanNumber::IsRomanNumber($test[$i-1]) &&
		   !(RomanNumber::IsRomanNumber(substr($test[$i],0,-1)) && in_array(substr($test[$i],-1), array('a','b','c'))) &&
		   !in_array(strtolower($test[$i]),$days) && !in_array(strtolower($test[$i-1]),$days) &&
		   !in_array(strtolower($test[$i]),$hours) && !in_array(strtolower($test[$i-1]),$hours)) {
			$test[$i-1] = $test[$i-1].$test[$i];
			unset($test[$i]);
			$test = array_values($test);
		} elseif(RomanNumber::IsRomanNumber(substr($test[$i],0,-1)) && in_array(substr($test[$i],-1), array('a','b','c'))) {
			array_splice($test, $i+1, 0, array(substr($test[$i],-1))); 
			$test[$i] = substr($test[$i],0,-1);
			$i+=2;
		} else {
			$i++;
		}
	}
	return $test;
}

function custom_cmp($a,$b) {
	global $days, $hours;
	$u = array($a['office-part'],$b['office-part']);
	$v = array($a['version'],$b['version']);
	$a = custom_split($a['incipit']);
	$b = custom_split($b['incipit']);
	for($i = 0; $i < min(count($a),count($b)); $i++) {
		if(RomanNumber::IsRomanNumber($a[$i]) && RomanNumber::IsRomanNumber($b[$i])) {
			$a[$i] = RomanNumber::Roman2Int($a[$i]);
			$b[$i] = RomanNumber::Roman2Int($b[$i]);
		} elseif(in_array(strtolower($a[$i]), $days) && in_array(strtolower($b[$i]), $days)) {
			$a[$i] = array_search(strtolower($a[$i]), $days);
			$b[$i] = array_search(strtolower($b[$i]), $days);
		} elseif(in_array(strtolower($a[$i]), $hours) && in_array(strtolower($b[$i]), $hours)) {
			$a[$i] = array_search(strtolower($a[$i]), $hours);
			$b[$i] = array_search(strtolower($b[$i]), $hours);
		}
		if(strtolower($a[$i]) != strtolower($b[$i])) {
			return (strtolower($a[$i]) < strtolower($b[$i])) ? -1 : 1;
		}
	}
	if($a < $b) {
		return -1;
	} else {
		return 1;
	}
	if($u[0] != $u[1]) {
		return ($u[0] < $u[1]) ? -1 : 1;
	}
	return ($v[0] < $v[1]) ? -1 : 1;
}

echo <<<HEADER1
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=UTF-8" http-equiv="content-type" />
<title>GregoBase - $title</title>
<link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Libre+Baskerville:400,700,400italic' />
<link rel="stylesheet" type="text/css" href="style.css" />
HEADER1;
if(!$logged_in) {
	echo '<script type="text/javascript" src="sha512.js"></script>
<script type="text/javascript" src="forms.js"></script>
';
}
echo <<<HEADER2
</head>

<body>
<div id="header">
<div id="title"><h1><a href="./">GregoBase</a>​</h1></div>
<div id="description">A database of gregorian scores</div>
<div id="access">

	<div class="menu"><ul class="sf-menu"><li class="page_item current_page_item"><a href="scores.php">Scores</a></li><li class="page_item"><a href="./?page_id=5">Todo</a></li><li class="page_item"><a href="./?page_id=2">About</a></li></ul></div>
	
</div><!-- #access -->
</div>
<div id="header_overlay"></div>

<div id="content">
HEADER2;

?>
