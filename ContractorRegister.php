<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
            display: inline-block;
            width: 250px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px -10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="checkbox"] {
            margin-right: 5px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
		button {
			background-color: #007bff;
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
			float: right;
		}
		button:hover {
			background-color: #0056b3;
		}
    </style>
</head>
<body>

    <div class="container">
        <h1>Company Registration Form</h1>

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

            if (empty($errors)) {
                $conn = connectToDatabase();

                // Insert the data into table
                $stmt = $conn->prepare("INSERT INTO contractor (contractorName, contractorPhoneNumber, contractorEmail, contractorPassword, contractorExpertise) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $companyName, $phoneNumber, $email, $hashedPassword, $options);
        
                if ($stmt->execute()) {
                    echo "New record created successfully";
                } else {
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
