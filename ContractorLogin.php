<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/ConLog.css">
</head>
<body>

<div class="container">
<h2>Contractor Login Form</h2>
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
//When form is submitted and both fields filled, checks for record in DB.
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['username']) && isset($_POST['password'])) {
$conn = connectToDatabase();
$inputUsername = $_POST['username'];
$inputPassword = $_POST['password'];

$stmt = $conn->prepare("SELECT contractorPassword FROM contractor WHERE contractorEmail = ?");
 if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
$stmt->bind_param("s", $inputUsername);
$stmt->execute();
$stmt->bind_result($dbPassword);
$stmt->fetch();

//Compares to DB info, sends to contractor home page if correct, tells user info is incorrect if false.
if (password_verify($inputPassword, $dbPassword)) {
	$_SESSION['contractorEmail'] = strtolower($inputUsername);
	header('Location: ContractorPage.php');
	exit;
}else
	echo "<font color = 'red'> Email or Password is incorrect.</font>";
$stmt->close();
$conn->close();
}
?>

<form action="" method="post">
<label for="username"><b>Email</b></label>
<input type="username" placeholder="Enter Email" name="username" required>

<label for="password"><b>Password</b></label>
<input type="password" placeholder="Enter Password" name="password" required>
<input type="submit" value="Login">
</form>

<br><br><br><a href="ContractorRegister.php">New user? Create an account here</a><br><br>
<a href="main.php">Back to Main Menu</a>
</div>
</body>
</html>
