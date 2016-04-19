<?php
//head.php
//test
if(count(get_included_files()) == 1 or basename($_SERVER['SCRIPT_FILENAME']) == 'head.php'){
	include_once('404.php');
}else{
	include_once('aixin.php');
}

$head_title = "test";
$head_doc ="test";
$head_keywords = "歌曲,歌詞,流行音樂";

echo '<!DOCTYPE html>';
echo '<html>';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<link rel="stylesheet" type="text/css" href="main.css">';
echo "<title>{$head_title}</title>";
echo "<meta name=\"description\" content={$head_doc}>";
echo "<meta name=\"keywords\" content={$head_keywords}>";
echo '</head>';
?>