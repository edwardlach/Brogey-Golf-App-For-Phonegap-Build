<?php
session_start();
include "simple_functions.php";
$link = connect_to_database();

$round_id = $_SESSION['round_id'];




$front_back_both_query = mysqli_query( $link,
	"SELECT front_back_both FROM bg_app_rounds "
	."WHERE round_id = '$round_id'"
);

$front_back_both_row = mysqli_fetch_row($front_back_both_query);

$front_back_both = $front_back_both_row[0];

echo $front_back_both;



?>