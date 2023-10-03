<!DOCTYPE html>
<html>
<style>
/ Mohammed Rahman /
/ I certify that this submission is my own original work /
body {font-family: Arial, Helvetica, sans-serif;}

{box-sizing: border-box;}
/\ *Full-width input fields */
input[type=text], input[type=password] {
width: 100%;
padding: 15px;
margin: 5px 0 22px 0;
display: inline-block;
border: none;
background: #f1f1f1;
}

/\ *Add a background color when the inputs get focus */
input[type=text]:focus, input[type=password]:focus {
background-color: #ddd;
outline: none;
}

/\ *Set a style for all buttons */
button {
background-color: #04AA6D;
color: white;
padding: 14px 20px;
margin: 8px 0;
border: none;
cursor: pointer;
width: 100%;
opacity: 0.9;
}

button:hover {
opacity:1;
}

/\ *Extra styles for the cancel button */
.cancelbtn {
padding: 14px 20px;
background-color: #f44336;
}

/\ *Float cancel and signup buttons and add an equal width */
.cancelbtn, .signupbtn {
float: left;
width: 50%;
}

/\ *Add padding to container elements */
.container {
padding: 16px;
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
background-color: #474e5d;
padding-top: 50px;
}

/ Modal Content/Box /
.modal-content {
background-color: #fefefe;
margin: 5% auto 15% auto; / 5% from the top, 15% from the bottom and centered /
border: 1px solid #888;
width: 80%; / Could be more or less, depending on screen size /
}

/\ *Style the horizontal ruler */
hr {
border: 1px solid #f1f1f1;
margin-bottom: 25px;
}

/\ *The Close Button (x) */
.close {
position: absolute;
right: 35px;
top: 15px;
font-size: 40px;
font-weight: bold;
color: #f1f1f1;
}

.close:hover,
.close:focus {
color: #f44336;
cursor: pointer;
}

/\ *Clear floats */
.clearfix::after {
content: "";
clear: both;
display: table;
}

/\ *Change styles for cancel button and signup button on extra small screens */
@‌media screen and (max-width: 300px) {
.cancelbtn, .signupbtn {
width: 100%;
}
}
</style>
<body>

<button onclick="document.getElementById('id01').style.display='block'" style="width:auto;">Sign Up</button>

<div id="id01" class="modal">
<span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">×</span>
<form class="modal-content" action="CustomerLogin.php" method="POST" onsubmit="return validateForm()">
<div class="container">
<h1>Sign Up</h1>
<p>Please fill in this form to create an account.</p>
<hr>
<label for="username"><b>UserName</b></label>
<input type="text" placeholder="Enter Username" name="username" id="username" required>
<label for="email"><b>Email</b></label>
<input type="text" placeholder="Enter Email" name="email" id="email" required>

  <label for="psw"><b>Password</b></label>
  <input type="password" placeholder="Enter Password" name="password" id="password" required>

  <label for="psw-repeat"><b>Repeat Password</b></label>
  <input type="password" placeholder="Repeat Password" name="password_repeat" id="password_repeat" required>

  <label>
    <input type="checkbox" checked="checked" name="remember" style="margin-bottom:15px"> Remember me
  </label>

  <p>By creating an account you agree to our <a href="#" style="color:dodgerblue">Terms & Privacy</a>.</p>

  <div class="clearfix">
    <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Cancel</button>
    <button type="submit" class="signupbtn" name="submit">Sign Up</button>
    <a href="CustomerLogin.php" <button onclick="document.getElementById('id01').style.display='block'" style="width:auto;">Back to Login</button></a>

  </div>
</div>
</form>
</div>

<script>
function validateForm() {
var username = document.getElementById("username").value;
var email = document.getElementById("email").value;
var password = document.getElementById("password").value;
var password_repeat = document.getElementById("password_repeat").value;

if (email === "" || password === "" || password_repeat === "") {
    alert("Please fill in all the fields.");
    return false;
} else if (password !== password_repeat) {
    alert("Passwords do not match.");
    return false;
}

if (password.length < 8) {
    alert("Password must be at least 8 characters long.");
    return false;
}

return true;
}
</script>

<?php
$servername = "localhost";
$username = "usersu23";
$password = "passwdsu23";
$dbname = "bcs350su23";

// Create a new connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// Process the form submission
if (isset($_POST['submit'])) {
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$password_repeat = $_POST['password_repeat'];

    // Perform input validation (you can add more validation as needed)
    if (empty ($username)||empty($email) || empty($password) || empty($password_repeat)) {
        echo "Please fill in all the fields.";
    } elseif ($password !== $password_repeat) {
        echo "Passwords do not match.";
    } else {
        // Sanitize user input
        $username = htmlspecialchars($username);
        $email = htmlspecialchars($email);
        $password = htmlspecialchars($password);

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Add the user to the database (assuming you have a database connection)
        $conn = new mysqli('localhost', 'usersu23', 'passwdsu23', 'bcs350su23');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if the user already exists in the users table
        $check_email_query = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($check_email_query);
        if ($result->num_rows > 0) {
            echo "Username is already registered. Please choose another username.";
        } else {
            // Insert the new user into the users table
            $insert_user_query = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
            if ($conn->query($insert_user_query) === TRUE) {
                echo "Registration successful. You can now log in.";
            } else {
                echo "Error adding user: " . $conn->error;
            }
        }
        // Check if the Email already exists in the users table
        $check_username_query = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($check_username_query);
        if ($result->num_rows > 0) {
            echo "Email is already taken. Please choose another Email.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert the new user into the users table
            $insert_user_query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
            if ($conn->query($insert_user_query) === TRUE) {
                echo "Registration successful. You can now log in.";
            } else {
                echo "Error adding user: " . $conn->error;
            }
        }

        // Close the database connection
        $conn->close();
    }
}
}
?>
</body>
</html>
