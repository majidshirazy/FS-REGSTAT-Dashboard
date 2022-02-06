<?php
#Edit MySQL Database variables below
$DB_HOST = 'localhost';
$DB_USER = 'username';
$DB_PASS = 'password';
$DB_NAME = 'db_name';

$db = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to DB: ' . mysqli_connect_error());
}
class ssw_db extends SQLite3 {
	function __construct() {
		#Chnage the SQLite database file url below
	   $this->open('path_to_sqlite_db_file.db');
	}
 }
 $sqlite_db = new ssw_db();
 if(!$sqlite_db) {
	echo $sqlite_db->lastErrorMsg();
 }
?>
