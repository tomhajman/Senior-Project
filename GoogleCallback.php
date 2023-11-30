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
    $findDuplicate = $conn->prepare("SELECT COUNT(customerEmail), customerStreetAddress FROM customer WHERE customerEmail=?");
    $findDuplicate->bind_param("s", $email);
    $findDuplicate->execute();
    $findDuplicate->bind_result($numOfDuplicates, $customerStreetAddress);
    $findDuplicate->fetch();
    $findDuplicate->close();
    $userExists = ($numOfDuplicates != 0);
    $isProfileComplete = (!(is_null($customerStreetAddress)));

    // Token expiry
    $token_expiry = date('Y-m-d H:i:s', time() + $token['expires_in']);

    if ($userExists && $isProfileComplete) {
        // User exists and profile is complete, so update their information
        $stmt = $conn->prepare("UPDATE customer SET access_token = ?, refresh_token = ?, token_type = ?, expires_in = ? WHERE customerEmail = ?");
        $stmt->bind_param("sssss", $token['access_token'], $token['refresh_token'], $token['token_type'], $token_expiry, $email);
        // Execute the statement
        $stmt->execute();
        $stmt->close();
        $conn->close();
        
        // Log in and redirect to your customer page
        session_start();
        $_SESSION['customerEmail'] = $email;
        header('Location: CustomerPage.php');
        exit();
    } else {
        if(!$userExists){
            // User doesn't exist, so insert a new record and send them to complete the profile
            $stmt = $conn->prepare("INSERT INTO customer (customerEmail, customerFirstName, customerLastName, access_token, refresh_token, token_type, expires_in, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssssss", $email, $first_name, $last_name, $token['access_token'], $token['refresh_token'], $token['token_type'], $token_expiry);
            // Execute the statement
            $stmt->execute();
            $stmt->close();
            $conn->close();
        } else {
            // User exists, profile is not filled so update their information
            $stmt = $conn->prepare("UPDATE customer SET access_token = ?, refresh_token = ?, token_type = ?, expires_in = ? WHERE customerEmail = ?");
            $stmt->bind_param("sssss", $token['access_token'], $token['refresh_token'], $token['token_type'], $token_expiry, $email);
            // Execute the statement
            $stmt->execute();
            $stmt->close();
            $conn->close();
        }
        // Redirect user to profile setup page
        session_start();
        $_SESSION['access_token'] = $token['access_token'];
        $location = "Location: GoogleCustomerProfileSetup.php?customerEmail={$email}&firstName={$first_name}&lastName={$last_name}";
        header($location);
        exit();
    }
    
} else {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . $auth_url);
    exit();
}
?>