<?php
error_reporting(E_ALL);
date_default_timezone_set('Asia/Tokyo');
print_r(SQLite3::version());

$db_name = 'comment_db.db';

if(!file_exists($db_name)){
	$db = new sqlite3($db_name);
	$db->busyTimeout(10000);
	$create_table = "create table text_comment(
					rowid INTEGER PRIMARY KEY AUTOINCREMENT,
					video_id text,
					type text,
					parent_rowid text,
					user_id text default 'none',
					comment text no null,
					ip text default '0',
					cre_date text default current_timestamp,
					up_date text default current_timestamp,
					laud_count integer default 0,
					minus_count integer default 0,
					level text default '5'
					)";
	$db->exec($create_table);
	//type:main or sub
	//parent_rowid
	$db->close();

}else{
	$db = new sqlite3($db_name,SQLITE3_OPEN_READONLY);
	$sql = "select count(*) from text_comment";
	echo '<br>comment count';
	echo $db->querySingle($sql);
	echo '<br><br>exists';
}
exit;



?>