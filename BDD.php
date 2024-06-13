<?php

require_once 'Classes/BDD/Database.php';

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "quiznight";

$database = new Database($servername, $username, $password, $dbname);

$conn = $database->getConnection();

?>
