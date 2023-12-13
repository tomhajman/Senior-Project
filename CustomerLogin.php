<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/CusLog.css">
</head>
<body>
<div class="container">
<h2>Customer Login Form</h2>
<?php
include 'DBCredentials.php';
//DB Connection
function connectToDatabase() {
    global $HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME;
        
    $conn = new mysqli($HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME);
        
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        }
        
    return $conn;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['username']) && isset($_POST['password'])) {
$conn = connectToDatabase();
$inputUsername = $_POST['username'];
$inputPassword = $_POST['password'];

$stmt = $conn->prepare("SELECT customerPassword, access_token FROM customer WHERE customerEmail = ?");
 if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
$stmt->bind_param("s", $inputUsername);
$stmt->execute();
$stmt->bind_result($dbPassword, $access_token);
$stmt->fetch();

//Compares to DB info, sends to customer home page if correct, tells user info is incorrect if false. Tells user to sign in with google if they used Oauth
if(!(is_null($access_token))){
  echo "<font color = 'red'> Please sign in using Google.</font>";
} else if (password_verify($inputPassword, $dbPassword)){
	$_SESSION['customerEmail'] = strtolower($inputUsername);
	header('Location: CustomerPage.php');
	exit;
} else {
	echo "<font color = 'red'> Email or Password is incorrect.</font>";
}
$stmt->close();
$conn->close();
}
?>
<form action="" method="post">
    <label for="username"><b>Email</b></label>
    <input type="text" placeholder="Enter Username" name="username" required>

    <label for="password"><b>Password</b></label>
    <input type="password" placeholder="Enter Password" name="password" required>
        
    <button type="submit">Login</button>
    <label>
      <input type="checkbox" checked="checked" name="remember"> Remember me
    </label>
	<!--Added functionality to allow new users to get to registration page-->
	<br><br><a href="CustomerRegistration.php">New user? Create an account here</a><br><br>
	<a href="GoogleCallback.php"><img src="assets/continue_with_google_light.png"></a><br><br>
  <a href="main.php">Back to Main Menu</a>
</form>
</div>
</body>
</html>
