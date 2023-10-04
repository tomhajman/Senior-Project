<?php
$servername = "customer-contractor-db.ckozhgnn2unn.us-east-2.rds.amazonaws.com";
$username = "seniorproject23";
$password = getenv("PASSWORD");

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>