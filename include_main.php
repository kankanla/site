<?php
//include_main.php
//request
//index.php
if(count(get_included_files()) == 1 or basename($_SERVER['SCRIPT_FILENAME']) == 'include_main.php'){
	include_once('404.php');
}else{
	include_once('aixin.php');
}

//debug 項目
///////////////////////////////////////
function include_main_debug($e){
	if(DEBUG){echo $e; echo __FILE__;};
	if(DEBUG){echo '<pre>';var_dump($_REQUEST);echo '<br>';}
}


//left枠 変更しない
echo '<div id ="include_main_left">';
	include_once('include_left_scroll_menu.php');
echo '</div>';	//include_main_left


echo '<div id ="main_scroll">';
	if(array_key_exists("search_query",$_REQUEST)){
		$i = $_REQUEST['search_query'];
		switch ($i){
			case "q":
				if(DEBUG){include_main_debug('#031');}
				break;
				
			case "all":
				//すべてを表示
				/////////////////////////////////////
				if(DEBUG){include_main_debug('#037');}
				include_once('include_allartist.php');
				break;
				
			case "user_set":
				//ユーザーの設定
				/////////////////////////////////////
				if(DEBUG){include_main_debug('#044');}
				include_once('include_userset.php');
				break;
			
			case '':
				//ユーザーの設定
				/////////////////////////////////////
				if(DEBUG){include_main_debug('#051');}
				include_once('include_topsongs.php');
				break;
				
			default:
				if(DEBUG){include_main_debug('#056');}
				include_once('include_main_document.php');
				break;
		}
	}else{
		if(DEBUG){include_main_debug('#051');}
		include_once('include_topsongs.php');
	}
	
echo '</div>';	//<div id ="main_scroll">

?>
<script type = "text/javascript">
//レイアウトのスクロールイベント設定。
//include_main.php
scroll_event();
function scroll_event(){
	var xleft_scroll = document.getElementById('include_main_left');
	xleft_scroll.addEventListener("mouseover",function(){
		document.getElementById('include_main_left').style.overflow = "auto";
		document.getElementById('include_main_left').style.overflowy = "hidden";
		//document.getElementById('index-body').style.overflow = "hidden";
	},false);
	
	xleft_scroll.addEventListener("mouseout",function(){
		document.getElementById('include_main_left').style.overflow = "hidden";
		//document.getElementById('index-body').style.overflow = "auto";
	},false);	
}

//include_main.php
set_scroll();
function set_scroll(){
	document.getElementById('index-body').style.height = window.innerHeight + 'px';
	document.getElementById('include_main_left').style.height = window.innerHeight + 'px';
}
</script>