<?php
	session_start();
?>
<!DOCTYPE html>
<html>
  <link rel="stylesheet" href="css/CusReg.css">
  
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
      <input type="text" placeholder="Enter Email" name="customerEmailAddress" required>

      <label for="customerPassword"><b>Password</b></label>
      <input type="customerpassword" placeholder="Enter Password" name="customerPassword" required>
      


      <label for="confirm_password"><b>Confirm Password</b></label>
      <input type="customerpassword" placeholder="Confirm Password" name="confirm_password" required>

      <label>
        <input type="checkbox" checked="checked" name="remember" style="margin-bottom:15px"> Remember me
      </label>

      <p>By creating an account you agree to our <a href="#" style="color:dodgerblue">Terms & Privacy</a>.</p>

      <div class="clearfix">
        <button type="submit" class="signupbtn">Sign Up</button>
      </div>
      <div id="error-message" class="error"></div>
    </div>
  </form>
</div>

<script>
  // Make the modal appear by default
  document.getElementById('id01').style.display='block';
</script>

</body>
</html>
