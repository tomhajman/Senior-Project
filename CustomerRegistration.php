<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="css/CusReg.css">
    <!--Style for this wouldn't work in dedicated CSS file-->
  <style>
  input[type=password] {
  width: 100%;
  padding: 15px;
  margin: 5px 0 22px 0;
  display: inline-block;
  border: none;
  background: #f1f1f1;
  }
  input[type=password]:focus {
  background-color: #ddd;
  outline: none;
}
  </style>
  </head>
<body>

<?php
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
	  $customerCounty = $_POST['customerCounty'];
    $customerEmail = strtolower($_POST['customerEmailAddress']);
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
	
	if (strlen($customerPassword) < 8) {
			$errors['customerPassword'] = "Password must be 8 characters long.";
		} elseif (!preg_match("/[a-z]/", $customerPassword) || 
				  !preg_match("/[A-Z]/", $customerPassword) ||
			      !preg_match("/[0-9]/", $customerPassword)) {
				$errors['customerPassword'] = "Password must contain a lowercase letter, an uppercase letter, and a number.";
			}

	if (empty($customerCounty))
		$errors['customerCounty'] = "customerCounty is empty";

    if (empty($errors)) {
		$conn = connectToDatabase();
		
        // Insert the data into the table
        $stmt = $conn->prepare("INSERT INTO customer (customerFirstName, customerLastName, customerStreetAddress, customerFloorApt, customerCity, customerZip, customerCounty, customerEmail, customerPhoneNumber, customerPassword) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $customerFirstName, $customerlastName, $customerStreetAddress, $customerFloorApt, $customerCity, $customerZip, $customerCounty, $customerEmail, $customerPhoneNumber, $hashedPassword);

        if ($stmt->execute()) {
        	echo "<script>alert('New customer created successfully.');</script>";
            //echo "<div class='success'>New customer record created successfully</div>";
        } else {
        	echo "<script>alert('Error.'". $stmt->error . ");</script>";
            //echo "<div class='error'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<div id="id01" class="modal">
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
      <input type="text" placeholder="Enter Zip Code" name="customerZip" required>
	  
	  <label for="customerCounty"><b>County</b></label>
	  <input type="radio" name="customerCounty" id="Suffolk" value="Suffolk" required>
	  <label for="Suffolk">Suffolk</label>
	  <input type="radio" name="customerCounty" id="Nassau" value="Nassau">
	  <label for="Nassau">Nassau</label><br><br><br>
	
	 <label for="customerPhoneNumber"><b>Phone Number</b></label>
      <input type="text" placeholder="Enter Phone Number" name="customerPhoneNumber" required>
	  
      <label for="customerEmailAddress"><b>Email</b></label>
	  <?php if (isset($errors['customerEmailAddress'])): ?>
            <div style="color: red;"><?php echo $errors['customerEmailAddress']; ?></div>
      <?php endif; ?>
      <input type="text" placeholder="Enter Email" name="customerEmailAddress" required>

      <label for="customerPassword"><b>Password</b></label>
	  <?php if (isset($errors['customerPassword'])): ?>
            <div style="color: red;"><?php echo $errors['customerPassword']; ?></div>
      <?php endif; ?>
      <input type="password" placeholder="Enter Password" name="customerPassword" required>
      


      <label for="confirm_password"><b>Confirm Password</b></label>
      <input type="password" placeholder="Confirm Password" name="confirm_password" required>

      <div class="clearfix">
        <button type="submit" class="signupbtn">Sign Up</button>
      </div>
      <div id="error-message" class="error"></div>
    </div>
	<button onclick = "window.location.href = 'CustomerLogin.php';">Continue to login</button>
  </form>
</div>

<script>
  // Make the modal appear by default
  document.getElementById('id01').style.display='block';
</script>

</body>
</html>
