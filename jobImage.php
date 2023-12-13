<?php
//DB connection, session handling.
include 'DBCredentials.php';
function connectToDatabase() {
    global $HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME;

    $conn = new mysqli($HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

$conn = connectToDatabase();
//Get image based on ID and type.
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $stmt = $conn->prepare("SELECT mimeType, imageData FROM jobImages WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc();

    if ($image) {
        header("Content-Type: " . $image['mimeType']);
        echo $image['imageData'];
        exit;
    }
    $stmt->close();
} else {
    // Send a 404 status if image is not found.
    header("HTTP/1.0 404 Not Found");
    exit;
}
