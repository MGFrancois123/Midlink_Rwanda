<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_connect.php';

// echo $_SESSION['userId'];

if(!$_SESSION['userId']) {
	header('location:'.$store_url);	
} 



?>