<?php
require_once 'vendor/autoload.php';
require_once 'secrets/google_secrets.php';
require_once 'DBCredentials.php';

function connectToDatabase() {
    global $HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME;

    $conn = new mysqli($HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

$client = new Google_Client();
$client->setClientId($CLIENT_ID);
$client->setClientSecret($CLIENT_SECRET);
$client->setRedirectUri('http://localhost/Senior-Project/GoogleCallback.php');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    // Handle Google response
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email =  $google_account_info->email;
    $name =  $google_account_info->name;
    $first_name = $google_account_info->givenName;
    $last_name = $google_account_info->familyName;

    // Check if user exists in the database
    $conn = connectToDatabase();
    $findDuplicate = $conn->prepare("SELECT COUNT(customerEmail) FROM customer WHERE customerEmail=?");
    $findDuplicate->bind_param("s", $email);
    $findDuplicate->execute();
    $findDuplicate->bind_result($numOfDuplicates);
    $findDuplicate->fetch();
    $findDuplicate->close();
    $userExists = ($numOfDuplicates != 0);

    if ($userExists) {
        // User exists, so update their information
        $stmt = $conn->prepare("UPDATE customer SET customerFirstName = ?, customerLastName = ?, access_token = ?, refresh_token = ?, token_type = ?, expires_in = ? WHERE customerEmail = ?");
        $stmt->bind_param("sssssss", $first_name, $last_name, $token['access_token'], $token['refresh_token'], $token['token_type'], $token_expiry, $email);
    } else {
        // User doesn't exist, so insert a new record
        $stmt = $conn->prepare("INSERT INTO customer (customerEmail, customerFirstName, customerLastName, access_token, refresh_token, token_type, expires_in, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssssss", $email, $first_name, $last_name, $token['access_token'], $token['refresh_token'], $token['token_type'], $token_expiry);
    }

    // Token expiry
    $token_expiry = date('Y-m-d H:i:s', time() + $token['expires_in']);

    // Execute the statement
    $stmt->execute();
    $stmt->close();
    $conn->close();

    // Redirect to your customer page
    session_start();
    $_SESSION['customerEmail'] = $email;
    header('Location: CustomerPage.php');
    exit();
} else {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . $auth_url);
    exit();
}
?>