<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="css/ConReg.css">
    <title>Registration Form</title>
    
</head>
<body>

    <div class="container">
        <h1>Company Registration Form</h1>

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
            $companyName = $_POST['companyName'];
            $email = strtolower($_POST['email']);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirmPassword'];
			$phoneNumber = $_POST['phone'];
            $options = isset($_POST['options']) ? serialize($_POST['options']) : [];
			
			// Hash the contractor's password
			$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Check if email already exists in the database
            $conn = connectToDatabase();
            $findDuplicate = $conn->prepare("SELECT COUNT(contractorEmail) FROM contractor WHERE contractorEmail=?");
            $findDuplicate->bind_param("s", $email);
            $findDuplicate->execute();
            $findDuplicate->bind_result($numOfDuplicates);
            $findDuplicate->fetch();
            if($numOfDuplicates != 0){
                $errors['email'] = "Email address already exists.";
            }
            $findDuplicate->close();
            $conn->close();

            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Invalid email address.";
            }

            // Validate password and confirm password
            if ($password !== $confirmPassword) {
                $errors['password'] = "Password and Confirm Password must match.";
            }
			
		if (strlen($password) < 8) {
			$errors['password'] = "Password must be 8 characters long.";
		} elseif (!preg_match("/[a-z]/", $password) || 
				  !preg_match("/[A-Z]/", $password) ||
			      !preg_match("/[0-9]/", $password)) {
				$errors['password'] = "Password must contain a lowercase letter, an uppercase letter, and a number.";
			}

            if (empty($errors)) {
                $conn = connectToDatabase();

                // Insert the data into table
                $stmt = $conn->prepare("INSERT INTO contractor (contractorName, contractorPhoneNumber, contractorEmail, contractorPassword, contractorExpertise) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $companyName, $phoneNumber, $email, $hashedPassword, $options);
        
                if ($stmt->execute()) {
                	echo "<script>alert('Account created successfully.');</script>";
                    //echo "New record created successfully";
                } else {
                	echo "<script>alert('Error: " . $stmt->error . "');</script>";
                    echo "Error: " . $stmt->error;
                }
        
                $stmt->close();
                $conn->close();  
                
            }
        }
        ?>

        <form action="" method="post">
            <label for="companyName">Company Name:</label>
            <input type="text" id="companyName" name="companyName" required><br><br>
			
			<label for="phone">Phone Number:</label>
            <input type="text" id="phone" name="phone" required><br><br>

            <label for="email">Email Address:</label>
            <?php if (isset($errors['email'])): ?>
                <div style="color: red;"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
            <input type="text" id="email" name="email" required><br><br>

            <label for="password">Password:</label>
            <?php if (isset($errors['password'])): ?>
                <div style="color: red;"><?php echo $errors['password']; ?></div>
            <?php endif; ?>
            <input type="password" id="password" name="password" required><br><br>

            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required><br><br>

            <label>Areas of expertise:</label><br>
            <input type="checkbox" id="option1" name="options[]" value="Contracting">
            <label for="option1">Contracting (general)</label><br>

            <input type="checkbox" id="option2" name="options[]" value="Plumbing">
            <label for="option2">Plumbing</label><br>

            <input type="checkbox" id="option3" name="options[]" value="Electrician">
            <label for="option3">Electrician</label><br>

            <input type="checkbox" id="option4" name="options[]" value="Gardening">
            <label for="option4">Gardening</label><br>

            <input type="checkbox" id="option5" name="options[]" value="Painting">
            <label for="option5">Painting</label><br>

            <input type="checkbox" id="option6" name="options[]" value="HVAC">
            <label for="option6">HVAC</label><br><br>

            <input type="submit" value="Register">
			<!--Added link to login after users create account-->
			<button onclick = "window.location.href = 'ContractorLogin.php';">Continue to login</button>
        </form>
    </div>

</body>
</html>
