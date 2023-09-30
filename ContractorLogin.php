<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {font-family: Arial, Helvetica, sans-serif;}

/* Full-width input fields */
input[type=text], input[type=password] {
width: 100%;
padding: 12px 20px;
margin: 8px 0;
display: inline-block;
border: 1px solid #ccc;
box-sizing: border-box;
}

/* Set a style for all buttons */
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

/* Extra styles for the cancel button */
.cancelbtn {
width: auto;
padding: 10px 18px;
background-color: #f44336;
}

/* Center the image and position the close button */
.imgcontainer {
text-align: center;
margin: 24px 0 12px 0;
position: relative;
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

/ The Modal (background) /
.modal {
display: none; / Hidden by default /
position: fixed; / Stay in place /
z-index: 1; / Sit on top /
left: 0;
top: 0;
width: 100%; / Full width /
height: 100%; / Full height /
overflow: auto; / Enable scroll if needed /
background-color: rgb(0,0,0); / Fallback color /
background-color: rgba(0,0,0,0.4); / Black w/ opacity /
padding-top: 60px;
}

/ Modal Content/Box /
.modal-content {
background-color: #fefefe;
margin: 5% auto 15% auto; / 5% from the top, 15% from the bottom and centered /
border: 1px solid #888;
width: 80%; / Could be more or less, depending on screen size /
}

/* The Close Button (x) */
.close {
position: absolute;
right: 25px;
top: 0;
color: #000;
font-size: 35px;
font-weight: bold;
}

.close:hover,
.close:focus {
color: red;
cursor: pointer;
}

/* Add Zoom Animation */
.animate {
-webkit-animation: animatezoom 0.6s;
animation: animatezoom 0.6s
}

@-webkit-keyframes animatezoom {
from {-webkit-transform: scale(0)}
to {-webkit-transform: scale(1)}
}

@keyframes animatezoom {
from {transform: scale(0)}
to {transform: scale(1)}
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

<!--Was this needed? It was causing a duplicate login button at the top corner of the page. I changed this to look like the CustomerLogin, where the top corner specified the page was the login form.
<!--<button onclick="document.getElementById('id01').style.display='block'" style="width:auto;">Login</button>-->
<h2>Login Form</h2>

<div id="id01" class="modal">
<!--I'm not sure what this code effects as of right now, but I'm leaving it here so I can change to the Contractor page - action="<?php echo $_SERVER['PHP_SELF']; ?>" -->
<form class="modal-content animate" action="ContractorPage.html" method="post">
<div class="imgcontainer">
<span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">×</span>
</div>
    <div class="container">
        <label for="username"><b>Username</b></label>
        <input type="text" placeholder="Enter Username" name="username" required>

        <label for="password"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="password" required>

        <button type="submit">Login</button>
		<!--Added functionality to allow new users to get to registration page-->
		 <a href="ContractorRegister.php">New user? Create an account here</a><br><br>
         <a href="main.html">Back to Main Menu</a>
    </div>
</form>
</div>

<script>

var modal = document.getElementById('id01');

window.onclick = function(event) {
if (event.target == modal) {
modal.style.display = "none";
}
}
document.querySelector("form").onsubmit = function() {
function validateForm() {
var username = document.forms[0]["username"].value;
var password = document.forms[0]["password"].value;
if (username.trim() === "" || password.trim() === "") {
alert("Please enter both username and password.");
return false;
};
</script>

<?php
//Mohammed Rahman
//I certify that this submission is my own original work
$servername = "localhost";
$username = "usersu23";
$password = "passwdsu23";
$dbname = "bcs350su23";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
$input_username = $_POST['username'];
$input_password = $_POST['password'];

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
$stmt->bind_param("s", $input_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $row = $result->fetch_assoc();
    $hashed_password = $row['password'];

    if (password_verify($input_password, $hashed_password)) {

        header("Location: index.html");
        exit;
    } else {

        echo "Incorrect username or password.";
    }
} else {
    echo "Incorrect username or password.";
}
$stmt->close();
$conn->close();
}
?>

</body>
</html>
