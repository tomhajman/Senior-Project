<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {font-family: Arial, Helvetica, sans-serif;}
form {border: 3px solid #f1f1f1;}

input[type=text], input[type=password] {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  box-sizing: border-box;
}

button {
  background-color: #04AA6D;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  width: 100%;
}

button:hover {
  opacity: 0.8;
}

.cancelbtn {
  width: auto;
  padding: 10px 18px;
  background-color: #f44336;
}

.imgcontainer {
  text-align: center;
  margin: 24px 0 12px 0;
}

img.avatar {
  width: 40%;
  border-radius: 50%;
}

.container {
  padding: 16px;
}

span.psw {
  float: right;
  padding-top: 16px;
}

/* Change styles for span and cancel button on extra small screens */
@media screen and (max-width: 300px) {
  span.psw {
     display: block;
     float: none;
  }
  .cancelbtn {
     width: 100%;
  }
}
</style>
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

$stmt = $conn->prepare("SELECT customerPassword FROM customer WHERE customerEmail = ?");
 if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
$stmt->bind_param("s", $inputUsername);
$stmt->execute();
$stmt->bind_result($dbPassword);
$stmt->fetch();

//Can be used instead of direct comparator once hashing is done
//if (password_verify($inputPassword, $dbPassword)){
	//echo "Correct info";
//Compares to DB info, sends to customer home page if correct, tells user info is incorrect if false.
if (password_verify($inputPassword, $dbPassword)){
	header('Location: CustomerPage.html');
	exit;
}else
	echo "<font color = 'red'> Email or Password is incorrect.</font>";
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
	<a href="main.html">Back to Main Menu</a>

  <div class="container" style="background-color:#f1f1f1">
    <button type="button" class="cancelbtn">Cancel</button>
    <span class="password">Forgot <a href="#">password?</a></span>
  </div>
</form>
</div>
</body>
</html>
