<!DOCTYPE html>
<html>
<style>
body {font-family: Arial, Helvetica, sans-serif;}
* {box-sizing: border-box;}

input[type=text], input[type=customerPassword] {
  width: 100%;
  padding: 15px;
  margin: 5px 0 22px 0;
  display: inline-block;
  border: none;
  background: #f1f1f1;
}

input[type=text]:focus, input[type=customerPassword]:focus {
  background-color: #ddd;
  outline: none;
}

button {
  color: darkolivegreen;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  width: 100%;
  opacustomerCity: 0.9;
}

button:hover {
  opacustomerCity: 1;
}

.cancelbtn {
  padding: 14px 20px;
  background-color: #f44336;
}

.signupbtn:last-child {
  background-color: #86cc74;
}

.container {
  padding: 16px;
}

.modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: #474e5d;
  padding-top: 50px;
}

.modal-content {
  background-color: #fefefe;
  margin: 5% auto 15% auto;
  border: 1px solid #888;
  width: 80%;
}

hr {
  border: 1px solid #f1f1f1;
  margin-bottom: 25px;
}

.close {
  position: absolute;
  right: 35px;
  top: 15px;
  font-size: 40px;
  font-weight: bold;
  color: #f1f1f1;
}

.close:hover, .close:focus {
  color: #f44336;
  cursor: pointer;
}

.clearfix::after {
  content: "";
  clear: both;
  display: table;
}

@media screen and (max-width: 300px) {
  .cancelbtn, .signupbtn {
     width: 100%;
  }
}

.error {
  color: red;
  text-align: center;
}

.success {
  color: green;
  text-align: center;
}
</style>
<body>

<?php
session_start();
include 'DBCredentials.php';
function connectToDatabase() {
    global $HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME;

    $conn = new mysqli($HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle form submission here
    $customerFirstName = $_POST['customerFirstName'];
    $customerlastName = $_POST['customerLastName'];
    $customerStreetAddress = $_POST['customerStreetAddress'];
    $customerFloorApt = $_POST['customerFloorApt'];
    $customerCity = $_POST['customerCity'];
    $customerZip = $_POST['customerZip'];
    $customerEmail = $_POST['customerEmailAddress'];
    $customerPassword = $_POST['customerPassword'];
    $customerPhoneNumber = $_POST['customerPhoneNumber'];
    $confirmPassword = $_POST['confirm_password'];

    // Hash the customerPassword
    $hashedPassword = password_hash($customerPassword, PASSWORD_BCRYPT);

    // Check if email or phone number already exists in the database
    $conn = connectToDatabase();
    $findDuplicate = $conn->prepare("SELECT COUNT(customerEmail) FROM customer WHERE customerEmail=?");
    $findDuplicate->bind_param("s", $customerEmail);
    $findDuplicate->execute();
    $findDuplicate->bind_result($numOfDuplicates);
    $findDuplicate->fetch();
    if ($numOfDuplicates != 0) {
        $errors['customerEmailAddress'] = "Email or phone number already exists.";
    }
    $findDuplicate->close();
	$conn->close();

    // Validate customerPassword and confirm customerPassword
    if ($customerPassword !== $confirmPassword) {
        $errors['customerPassword'] = "Password and Confirm Password must match.";
    }

    if (empty($errors)) {
		$conn = connectToDatabase();
		
        // Insert the data into the table
        $stmt = $conn->prepare("INSERT INTO customer (customerFirstName, customerLastName, customerStreetAddress, customerFloorApt, customerCity, customerZip, customerEmail, customerPhoneNumber, customerPassword) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $customerFirstName, $customerlastName, $customerStreetAddress, $customerFloorApt, $customerCity, $customerZip, $customerEmail, $customerPhoneNumber, $hashedPassword);

        if ($stmt->execute()) {
            echo "<div class='success'>New customer record created successfully</div>";
        } else {
            echo "<div class='error'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<button onclick="document.getElementById('id01').style.display='block'" style="width:auto;">Sign Up</button>
<button onclick = "window.location.href = 'CustomerLogin.php';" style="width:auto;">Continue to login</button>

<div id="id01" class="modal">
  <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
  <form class="modal-content" action="" method="post" onsubmit="return validateForm()">
    <div class="container">
      <h1>Sign Up</h1>
      <p>Please fill in this form to create an account.</p>
      <hr>
      <label for="customerFirstName"><b>First Name</b></label>
      <input type="text" placeholder="Enter First Name" name="customerFirstName" required>

      <label for="customerLastName"><b>Last Name</b></label>
      <input type="text" placeholder="Enter Last Name" name="customerLastName" required>

      <label for="customerStreetAddress"><b>Street Address</b></label>
      <input type="text" placeholder="Enter Street Address" name="customerStreetAddress" required>

      <label for="customerFloorApt"><b>Floor/Apt</b></label>
      <input type="text" placeholder="Enter Floor/Apt" name="customerFloorApt">

      <label for="customerCity"><b>City</b></label>
      <input type="text" placeholder="Enter city" name="customerCity" required>

      <label for="customerZip"><b>Zip</b></label>
      <input type="text" placeholder="Enter customerZip" name="customerZip" required>
	
	 <label for="customerPhoneNumber"><b>Phone Number</b></label>
      <input type="text" placeholder="Enter Phone Number" name="customerPhoneNumber" required>
      <label for="customerEmailAddress"><b>Email</b></label>
      <input type="text" placeholder="Enter Email" name="customerEmailAddress" required>

      <label for="customerPassword"><b>Password</b></label>
      <input type="customerPassword" placeholder="Enter Password" name="customerPassword" required>
      


      <label for="confirm_password"><b>Confirm Password</b></label>
      <input type="customerPassword" placeholder="Confirm Password" name="confirm_password" required>

      <label>
        <input type="checkbox" checked="checked" name="remember" style="margin-bottom:15px"> Remember me
      </label>

      <p>By creating an account you agree to our <a href="#" style="color:dodgerblue">Terms & Privacy</a>.</p>

      <div class="clearfix">
        <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Cancel</button>
        <button type="submit" class="signupbtn">Sign Up</button>
      </div>
      <div id="error-message" class="error"></div>
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

function validateForm() {
  var customerFirstName = document.forms[0]["customerFirstName"].value;
  var customerlastName = document.forms[0]["customerLastName"].value;
  var customerStreetAddress = document.forms[0]["customerStreetAddress"].value;
  var customerCity = document.forms[0]["customerCity"].value;
  var customerZip = document.forms[0]["customerZip"].value;
  var customerEmail = document.forms[0]["customerEmailAddress"].value;
  var customerPassword = document.forms[0]["customerPassword"].value;
  var confirmPassword = document.forms[0]["confirm_password"].value;

  var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

  if (customerFirstName === "" || customerlastName === "" || customerStreetAddress === "" || customerCity === "" || customerZip === "" || customerEmail === "" || !customerPassword.match(passwordRegex) || confirmPassword === "") {
    document.getElementById('error-message').innerHTML = "Please complete all fields and ensure the customerPassword is more than 8 digits, contains both uppercase and lowercase letters, numbers, and at least one special character.";
    return false;
  } else {
    document.getElementById('error-message').innerHTML = "";
    return true;
  }
}
</script>

</body>
</html>

