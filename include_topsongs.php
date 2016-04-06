<?php
if(count(get_included_files()) == 1 or basename($_SERVER['SCRIPT_FILENAME']) == 'sinclude_allartist.php'){
	include_once('404.php');
	}else{
	include_once('aixin.php');
}

//debug 項目
///////////////////////////////////////
if(DEBUG){echo '<pre><br>';}
if(DEBUG){echo '<br> #011 >><br> '; echo __FILE__;}
if(DEBUG){echo '<br> #012 >><br> ';var_dump($_REQUEST);}

echo 'include_topsongs.php';
?>