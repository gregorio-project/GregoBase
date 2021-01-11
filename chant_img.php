<?php
include('include/db.php');

if(array_key_exists("id", $_GET)) {
	$id = intval($_GET['id']);
} else {
	die('No id');
}

function makeimg($c) {
	$tex = mgabc2tex($c);
	if($tex) {
		makeimgfiles($c['id'],$tex);
	}
	if($c['gabc_verses'] || $c['tex_verses']) {
		$tex = mgabc2tex($c,True);
		if($tex) {
			makeimgfiles($c['id'],$tex,'.1verse');
		}
	}
}

function mkstemp($suffix) {
	# based on http://stackoverflow.com/questions/8970913/create-a-temp-file-with-a-specific-extension-using-php
	$attempts = 238328; // 62 x 62 x 62
	$letters  = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$length   = strlen($letters) - 1;

	for($count = 0; $count < $attempts; ++$count) {
		$random = "";

		for($p = 0; $p < 6; $p++) {
			$random .= $letters[mt_rand(0, $length)];
		}

		$randomFile = __DIR__.'/temp/'.$random.$suffix;

		if( !($fd = @fopen($randomFile, "x+")) )
			continue;

		return array($fd,$randomFile);
	}
	return False;
}

function gregorio($s,$i=1) {
	$f = mkstemp('.gabc');
	fwrite($f[0],"nabc-lines: 1;\n%%\n".$s);
	fclose($f[0]);
	chdir(dirname($f[1]));
	exec('gregorio '.basename($f[1]));
	unlink($f[1]);
	$gf = substr($f[1],0,-5).'.gtex';
	$g = fopen($gf,'r');
	$tex = fread($g,filesize($gf));
	fclose($g);
	unlink($gf);
	$tex = substr($tex,0,-12)."\n\\relax\n";

	$tex .= "\\gresetinitiallines{$i}\n";
	if($i > 0) {
		$tex = '\grechangedim{beforeinitialshift}{2.2mm}{scalable}
\grechangedim{afterinitialshift}{2.2mm}{scalable}
'.$tex;
	} else {
		$tex = '\grechangedim{beforeinitialshift}{0mm}{scalable}
\grechangedim{afterinitialshift}{0mm}{scalable}
'.$tex;
	}

	return $tex;
}

function mgabc2tex($c, $firstverse = False) {
	$ann = array('al' => False,
	             'an' => 'Ant',
	             'ca' => 'Cant',
	             'co' => 'Comm',
	             'gr' => 'Grad',
	             'hy' => 'Hymn',
	             'in' => 'Intr',
	             'im' => False,
	             'ky' => False,
	             'of' => 'Offert',
	             'or' => False,
	             'pr' => False,
	             'ps' => False,
	             're' => 'Resp',
	             'rb' => 'R. br',
	             'se' => 'Seq',
	             'tr' => 'Tract',
	             'va' => False);
	#
	#  Document header
	#
	$tex = '% !TEX TS-program = lualatex
% !TEX encoding = UTF-8

\documentclass[12pt]{article}
\usepackage{geometry}
\geometry{paperwidth=16cm,paperheight=200cm}
\usepackage{gregoriotex}
\usepackage{fullpage}
\usepackage{Tabbing}
\usepackage{longtable}

\let\grelocalleftbox\localleftbox
\let\grelocalrightbox\localrightbox

\usepackage[latin]{babel}

\usepackage{fontspec}
\defaultfontfeatures{Ligatures=TeX}
\setmainfont{Linux Libertine O}

\pagestyle{empty}
\begin{document}
\newcommand{\red}[1]{\textcolor{red}{#1}}
\newcommand{\black}[1]{\textcolor{black}{#1}}
\setlength{\parindent}{0pt}

\grechangestyle{initial}{\fontsize{38}{38}\selectfont}{}
\tolerance=9999
\pretolerance=500
';
	if($ann[$c['office-part']]) {
		$tex .= '\greannotation{\small \textbf{'.$ann[$c['office-part']].".}}\n";
	}
	if($c['mode'] || $c['mode_var']) {
		if($c['mode'] == 'p') {
			$mode = "T. pereg.";
		} elseif(in_array($c['mode'], array('c','d','e'))) {
			$mode = strtoupper($c['mode']).($c['mode_var']?' '.$c['mode_var']:'');
		} else {
			$mode = $c['mode'].($c['mode_var']?' '.$c['mode_var']:'');
		}
		$tex .= '\greannotation{\small \textbf{'.$mode.".}}\n";
	}
	if($c['commentary']) {
		$tex .= '\grecommentary{{\small \emph{'.$c['commentary']."}}}\n";
		$tex .= '\nolinebreak[4]'."\n";
	}
	#
	# Parsing gabc
	#
	$g = json_decode($c['gabc']);
	$i = $c['initial'];
	if(is_array($g)) {
		foreach($g as $l) {
			if($l[0] == 'gabc') {
				$tex .= gregorio($l[1], $i);
				$i = 0;
			} else {
				$tex .= "\\vspace{10pt}\n".$l[1]."\\par\n";
			}
		}
	} elseif($c['gabc_verses'] && !$firstverse) {
		$tex .= gregorio($g."\n".$c['gabc_verses'], $i);
	} elseif($c['tex_verses'] && !$firstverse) {
		$tex .= gregorio($g, $i);
		$tex .= "\\vspace{10pt}\n".$c['tex_verses']."\\par\n";
	} else {
		$tex .= gregorio($g, $i);
	}
	#
	#  Document footer
	#
	$tex .= '
\end{document}
';
	return $tex;
}

function makeimgfiles($id, $tex, $suffix = '') {
	$path = __DIR__.'/scores/';
	$f = mkstemp('.tex');
	fwrite($f[0],$tex);
	fclose($f[0]);
	chdir(dirname($f[1]));
	exec('lualatex --interaction=nonstopmode '.basename($f[1]));
	exec('lualatex --interaction=nonstopmode '.basename($f[1])); # for variable line height
	exec('convert -density 300 '.substr($f[1],0,-4).'.pdf -flatten -trim '.$path.'png/'.$id.$suffix.'.png');
	chmod($path.'png/'.$id.$suffix.'.png', 0666);
	exec('convert -resize 33.333333% '.$path.'png/'.$id.$suffix.'.png '.$path.$id.$suffix.'.png');
	chmod($path.$id.$suffix.'.png', 0666);
	exec('pdfcrop '.substr($f[1],0,-4).'.pdf '.$path.'pdf/'.$id.$suffix.'.pdf');
	chmod($path.'pdf/'.$id.$suffix.'.pdf', 0666);
	exec('mutool draw -F svg -o '.$path.'svg/'.$id.$suffix.'.svg '.$path.'pdf/'.$id.$suffix.'.pdf');
	chmod($path.'svg/'.$id.$suffix.'.svg', 0666);
	exec('gs -q -dNOPAUSE -dBATCH -dSAFER -sDEVICE=eps2write -dCompatibilityLevel=1.3 -dEmbedAllFonts=true -dSubsetFonts=true -sOutputFile='.$path.'eps/'.$id.$suffix.'.eps '.$path.'pdf/'.$id.$suffix.'.pdf');
	chmod($path.'eps/'.$id.$suffix.'.eps', 0666);
	unlink($f[1]);
	unlink(substr($f[1],0,-4).'.log');
	unlink(substr($f[1],0,-4).'.aux');
	unlink(substr($f[1],0,-4).'.gaux');
	unlink(substr($f[1],0,-4).'.pdf');
}

$uri = __DIR__.'/scores/svg/'.$id.'.svg';
if(!is_file($uri) || (array_key_exists("force", $_GET) && $_GET["force"] == "1")) {
	$sql1 = 'SELECT * FROM '.db('chants').' WHERE id = '.$id;
	$req1 = $mysqli->query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.$mysqli->error);
	$c = $req1->fetch_assoc();
	if(!$c) {
		die('Wrong id');
	}
	makeimg($c);
}
header('Content-Disposition: inline; filename='.$id.'.svg');
header("Content-type: image/svg+xml");
readfile($uri);
?>
