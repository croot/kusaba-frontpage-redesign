<?php
	
function fileSizeInfo($fs) { 
	$bytes = array('KB', 'KB', 'MB', 'GB', 'TB'); 
	if ($fs <= 999) { 
		$fs = 1; 
	} 
	for ($i = 0; $fs > 999; $i++) { 
		$fs /= 1024; 
	} 
	return array(ceil($fs), $bytes[$i]); 
} 


function cut($text,$lenght = 30) {
	if ($text == '') { return '<span style="font-size: x-small; font-style:italic; color: #999;">(Sem texto)</span>'; }
	$s = explode("<br />",$text);
	$s[0] = strip_tags($s[0]);
	if (strlen($s[0]) > $lenght) { 
		return substr($s[0],0,$lenght)."...";
	} else { 
		return $s[0];
	}
}

function id2board($id) {
	global $tc_db;
	$res = $tc_db->GetAll("SELECT id,name FROM `" . KU_DBPREFIX . "boards` WHERE id=".$id);
	return $res[0]['name'];
}

?>