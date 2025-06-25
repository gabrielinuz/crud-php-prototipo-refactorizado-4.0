<?php
$host = "localhost";
$user = "students_user_4";
$password = "12345";
$database = "students_db_4";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["error" => "Database connection failed"]));
}
?>