<?php
//left_scroll_menu
//include_left_scroll_menu.php
if(count(get_included_files()) == 1 or basename($_SERVER['SCRIPT_FILENAME']) == 'include_left_scroll_menu.php'){
	include_once('404.php');
}

echo '<div id ="left_scroll_menu">';
	for($i=0; $i<100; $i++){
		echo '<div>qwertyuioplkjhgfdsa'.$i.'</div>';
		echo '<div>日本語日本語語語語語</div>';
	}
	echo 'end';
echo '</div>';
?>