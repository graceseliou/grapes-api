<?php
header("Content-Type: application/json");

header("Access-Control-Allow-Origin: http://http://54.227.103.48");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}


$db_host = "54.84.201.94"; // Replace with your database server's private IP
$db_user = "api_user";
$db_pass = "api_password";
$db_name = "spotify_folders";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM folders";
$result = $conn->query($sql);

$folders = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $folders[] = $row;
    }
}

echo json_encode($folders);
$conn->close();
?>
