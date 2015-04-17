<?php
session_start();

date_default_timezone_set('America/New_York');

include "simple_functions.php";
$link = connect_to_database();

$user_id_to_calculate_handicap = $_SESSION['id'];

include "calculate_handicap.php";

$handicap = main_handicap_calculation($link, $user_id_to_calculate_handicap);

echo $handicap;






?>