<?php
if(count(get_included_files()) == 1 or basename($_SERVER['SCRIPT_FILENAME']) == 'adse.php'){
	include_once('404.php');
}else{
	//include_once('aixin.php');
}

//include_allartist.php
//include_main_center.php

function adsense_area1(){
	echo '<div id = "adsense_area1">';
	echo 'adsense1';
	echo '#adsense_area1{
		width: 900px;
		height: 300px;
		min-height: 300px;
		background-color: aquamarine;
		}';
	echo '</div>';
}

function adsense_area2(){
	echo '<div id = "adsense_area2">';
	echo 'adsense2';
	echo '#adsense_area2{
		width: 300px;
		height: 250px;
		min-height: 250px;
		background-color: aquamarine;
		}';
	echo '</div>';
}


function adsense_area3(){
	echo '<div id="adsense_area3">';
	echo 'adsense3';
	echo '#adsense_area3{
		width: 300px;
		height: 250px;
		min-height: 250px;
		background-color: aquamarine;
		}';
	echo '</div>';

}
?>