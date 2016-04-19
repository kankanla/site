<?php
//include_searchform.php
if(count(get_included_files()) == 1 or basename($_SERVER['SCRIPT_FILENAME']) == 'include_searchform.php'){
	include_once('404.php');
}else{
	include_once('aixin.php');

}

//LOG
////////////////////////////////////////////
if($_SERVER['SERVER_ADDR'] === '192.168.11.10'){
	include_once('acclog.php');
	}else{
	//C:/htdocs/doc/acclog.php ; サイトに利用する際に利用URL
	include_once('C:/htdocs/doc/acclog.php');
}

$action_url = $_SERVER['REQUEST_URI'];
$logo_img = '<img src="image/logo.png" alt="テスト" >';

if(array_key_exists('search_query',$_GET)){
	$value = $_GET['search_query'];
	//その一
	//$value =  str_replace("%20","+",htmlentities(urldecode($value)));
	//その二
	$value =  htmlspecialchars($value);
}else{
	$value = "";
}

echo '<div id = "include_searchform">';
	echo '<div id = "searchform_log">';
		echo "{$logo_img}";
	echo '</div>';
	
	echo '<div id = "searchform_from">';
		echo "<form action =\"{$action_url}\" id =\"f\" method = \"get\">";
			echo sprintf("<input type=\"text\" id = \"searchform_q\" name = \"search_query\" value = \"%s\" autocomplete=\"off\" >",urldecode($value));
			echo '<button id = "searchform_button">send</button>';
		echo '<div id = "sub_list"></div>';
		echo '</form>';
	echo '</div>';
	
	echo '<div id = "searchform_login">';
		echo "Quick_login";
	echo '</div>';
echo '</div>';	//'<div id = "include_searchform">'
?>
<script type = "text/javascript">
//include_searchform.php
addevent();
function addevent(){
	var f = document.getElementById('f');
		/* 空送信防止 */
		f.addEventListener("submit",button_chk,false);
		/* 検索キーワードのAJAXエリア- */
		f.addEventListener("keyup",sub_list,false);
		//f.addEventListener("blur",sub_list_none,true);
}

//include_searchform.php
function button_chk(){
	var input_text = document.getElementById('searchform_q');
	if(input_text.value == ""){
		event.returnValue=false;
	}else{
		event.returnValue=true;
	}
}

//include_searchform.php
function sub_list(){
	var input_text = document.getElementById('searchform_q');
	var sub_text = document.getElementById('sub_list');
	if(input_text.value == ""){
		sub_text.style.display = "none";
	}else{
		sub_list_ajax(input_text.value,'198');
	}
}

//include_searchform.php
function sub_list_event(){
	//検索リストのイベント
	var sub_list = document.getElementById('sub_list').firstElementChild;
	if(sub_list){
		for(var x = 0 ;x < sub_list.childElementCount; x++){
			sub_list.childNodes[x].addEventListener("mouseover",
				function(){
					this.style.backgroundColor = "#F1F1F1";
				},
				false);
				
			sub_list.childNodes[x].addEventListener("mouseout",
				function(){
					this.style.backgroundColor = "rgb(255, 255, 255)";
				},
				false);				
			
			sub_list.childNodes[x].addEventListener("click",
				function(){
					location.href = location.origin + location.pathname +'?search_query=' + this.getAttribute('req_url').replace(/%26/g,'%2526');
				},
				true);
		}
	}
}

//include_searchform.php
function sub_list_none(){
	var sub_text = document.getElementById('sub_list');
		sub_text.style.display = "none";
}

//include_searchform.php
function sub_list_ajax(q_data,rand){
	//rand = 198
	console.info('#2016/04/20 1:39:42');
	var x = new XMLHttpRequest();
	var url = '/youtube_db/search.php?rand=' + rand;

		x.onreadystatechange = function(){
			if(x.readyState == 4 && x.status == 200){
				var sub_text = document.getElementById('sub_list');
				var input_text = document.getElementById('searchform_q');
				var get_list = JSON.parse(x.responseText);
				var ii = "";
				if(get_list.length > 0){
					for(var i = 0; i < get_list.length ; ++i){
						// if(i<18){
							var temp_replace = decodeURI(get_list[i].items.replace(/ /g,'+'));
								temp_replace = decodeURI(temp_replace.replace(/&/g,'%26'));
								ii = ii + '<li req_url = ' + temp_replace + '><h5>' + get_list[i].items + '</h5></li>';
					}

					sub_text.innerHTML = '<ul>' + ii + '</ul>';
					sub_text.style.top = input_text.getBoundingClientRect().top + input_text.getBoundingClientRect().height -3 + "px";
					sub_text.style.left = input_text.getBoundingClientRect().left -1 + "px";
					sub_text.style.width = input_text.getBoundingClientRect().width -2 + "px";
					sub_text.style.display = "block";
					sub_list_event();
				}else{
					sub_text.style.display = "none";
				}
			}
		}
	x.open('POST',url,true);
	x.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	x.send('q=' + q_data);
}

</script>