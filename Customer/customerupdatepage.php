<!DOCTYPE html>
<html>
<head>
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">    
	<link rel="stylesheet" href="css/CustomerUpdatePage.css">
   
</head>
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

    // Initialize userFName with a default value
    $userFName = "Guest";

    // Check if the user is logged in
    if (isset($_SESSION['customerEmail'])) {
        $userEmail = $_SESSION['customerEmail'];
        $conn = connectToDatabase();

        // Fetch the user's first name from the database
        $getFNameQuery = $conn->prepare("SELECT customerFirstName FROM customer WHERE customerEmail = ?");
        $getFNameQuery->bind_param("s", $userEmail);
        $getFNameQuery->execute();
        $result = $getFNameQuery->get_result();
        $row = $result->fetch_assoc();
        $userFName = $row['customerFirstName'];

        $getFNameQuery->close();
        $conn->close();
    }

    // Check if the user is logged in
    if (isset($_SESSION['customerEmail'])) {
        $userEmail = $_SESSION['customerEmail'];
        $conn = connectToDatabase();
		
        // Fetch the user's current data from the database
        $getUserDataQuery = $conn->prepare("SELECT * FROM customer WHERE customerEmail = ?");
        $getUserDataQuery->bind_param("s", $userEmail);
        $getUserDataQuery->execute();
        $result = $getUserDataQuery->get_result();
        $userData = $result->fetch_assoc();

        $getUserDataQuery->close();
        $conn->close();
    } else {
        // Handle the case when the user is not logged in
        // You can redirect them to the login page or take other actions
    }
	

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Handle form submission for updating user data
        // Retrieve and validate the updated user data
        $customerFirstName = $_POST['customerFirstName'];
        $customerLastName = $_POST['customerLastName'];
        $customerStreetAddress = $_POST['customerStreetAddress'];
        $customerFloorApt = $_POST['customerFloorApt'];
        $customerCity = $_POST['customerCity'];
        $customerZip = $_POST['customerZip'];
        $customerCounty = $_POST['customerCounty'];
        $customerEmail = $_POST['customerEmail'];
        $customerPhoneNumber = $_POST['customerPhoneNumber'];
        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];
        $confirmNewPassword = $_POST['confirmNewPassword'];

        // Hash the new password if it's being updated
        if (!empty($newPassword)) {
            $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        // Check if the email already exists in the database (excluding the user's current email)
        $conn = connectToDatabase();
        $findDuplicate = $conn->prepare("SELECT COUNT(customerEmail) FROM customer WHERE customerEmail=? AND customerEmail != ?");
        $findDuplicate->bind_param("ss", $customerEmail, $userEmail);
        $findDuplicate->execute();
        $findDuplicate->bind_result($numOfDuplicates);
        $findDuplicate->fetch();
        $findDuplicate->close();

        if ($numOfDuplicates != 0) {
            echo "<div class='error'>Email or phone number already exists.</div>";
        } else {
            // Validate current password
            if (password_verify($currentPassword, $userData['customerPassword'])) {
                // Current password is correct, proceed with the update
                // Validate new password and confirm new password
                if (!empty($newPassword) && $newPassword === $confirmNewPassword) {
                    // Update the user's data in the database
                    $updateUserDataQuery = $conn->prepare("UPDATE customer SET customerFirstName=?, customerLastName=?, customerStreetAddress=?, customerFloorApt=?, customerCity=?, customerZip=?, customerCounty=?, customerEmail=?, customerPhoneNumber=?, customerPassword=? WHERE customerEmail=?");
                    $updateUserDataQuery->bind_param("sssssssssss", $customerFirstName, $customerLastName, $customerStreetAddress, $customerFloorApt, $customerCity, $customerZip, $customerCounty, $customerEmail, $customerPhoneNumber, $hashedNewPassword, $userEmail);

                    if ($updateUserDataQuery->execute()) {
                        //echo "<div class='success'>Profile updated successfully</div>";
                         echo "<script>alert('Profile updated successfully.');</script>";

                        // Fetch the updated user data
                        $getUserDataQuery = $conn->prepare("SELECT * FROM customer WHERE customerEmail = ?");
                        $getUserDataQuery->bind_param("s", $userEmail);
                        $getUserDataQuery->execute();
                        $result = $getUserDataQuery->get_result();
                        $userData = $result->fetch_assoc();

                        $getUserDataQuery->close();
                    } else {
                    	echo "<script>alert('Error: '".$updateUserDataQuery->error . ");</script>";
                        //echo "<div class='error'>Error: " . $updateUserDataQuery->error . "</div>";
                    }

                    $updateUserDataQuery->close();
                } else {
                    echo "<div class='error'>New Password and Confirm New Password must match.</div>";
                }
            } else {
                echo "<div class='error'>Current Password is incorrect.</div>";
            }
        }

        $conn->close();
    }
    ?>
    <header>
   <div class="dropdown">
            <button class="dropbtn">...</button>
            <div class="dropdown-content">
				<a href="CustomerPage.php">Home</a>
                <a href="#">Messages</a>
                <a href="#">Service History</a>
                <a href="#">View Contractors</a>
                <a href="CustomerUpdatePage.php">Account Settings</a>
                <a href="CustomerLogin.php">Log Out</a>
            </div>
        </div>
        <div class="welcome-user">
            Welcome, <?php echo $userFName; ?><br>
            Email: <?php echo $userEmail; ?>
        </div>
    </header>

    <form method="POST" onsubmit="return validateForm()">
        <h2>Account Settings</h2>
        <div class="input-container">
            <i class="fa fa-user icon"></i>
            <input class="input-field" type="text" placeholder="First Name" name="customerFirstName" required value="<?php echo $userData['customerFirstName']; ?>">
        </div>
        <div class="input-container">
            <i class="fa fa-user icon"></i>
            <input class="input-field" type="text" placeholder="Last Name" name="customerLastName" required value="<?php echo $userData['customerLastName']; ?>">
        </div>
        <div class="input-container">
            <i class="fa fa-address-book icon"></i>
            <input class="input-field" type="text" placeholder="Street Address" name="customerStreetAddress" required value="<?php echo $userData['customerStreetAddress']; ?>">
        </div>
        <div class="input-container">
            <i class="fa fa-home icon"></i>
            <input class="input-field" type="text" placeholder="Floor/Apt" name="customerFloorApt" value="<?php echo $userData['customerFloorApt']; ?>">
        </div>
        <div class="input-container">
            <i class="fa fa-home icon"></i>
            <input class="input-field" type="text" placeholder="City" name="customerCity" required value="<?php echo $userData['customerCity']; ?>">
        </div>
        <div class="input-container">
			<i class="fa fa-home icon"></i>
			<input class="input-field" type="text" placeholder="Zip" name="customerZip" required value="<?php echo $userData['customerZip']; ?>" style="width: 50%;">
		</div>
        <div class="input-container">
            <i class="fa fa-user icon"></i>
            <input class="input-field" type="text" placeholder="County" name="customerCounty" value="<?php echo $userData['customerCounty']; ?>">
        </div>
        <div class="input-container">
            <i class="fa fa-phone icon"></i>
            <input class="input-field" type="text" placeholder="Phone Number" name="customerPhoneNumber" required value="<?php echo $userData['customerPhoneNumber']; ?>">
        </div>
        <div class="input-container">
            <i class="fa fa-envelope icon"></i>
            <input class="input-field" type="text" placeholder="Email" name="customerEmail" required value="<?php echo $userData['customerEmail']; ?>">
        </div>
        <div class="input-container">
            <i class="fa fa-lock icon"></i>
            <input class="input-field" type="password" placeholder="Current Password" name="currentPassword" required>
        </div>
        <div class="input-container">
            <i class="fa fa-lock icon"></i>
            <input class="input-field" type="password" placeholder="New Password" name="newPassword">
        </div>
        <div class="input-container">
            <i class="fa fa-lock icon"></i>
            <input class="input-field" type="password" placeholder="Confirm New Password" name="confirmNewPassword">
        </div>
        <button type="submit" class="btn">Update Profile</button>
    </form>
    
    <div class="error" id="error-message"></div>

    <script>
        function validateForm() {
            var customerFirstName = document.forms[0]["customerFirstName"].value;
            var customerLastName = document.forms[0]["customerLastName"].value;
            var customerStreetAddress = document.forms[0]["customerStreetAddress"].value;
            var customerCity = document.forms[0]["customerCity"].value;
            var customerZip = document.forms[0]["customerZip"].value;
            var customerCounty = documentforms[0]["customerCounty"].value;
            var customerEmail = document.forms[0]["customerEmail"].value;
            var currentPassword = document.forms[0]["currentPassword"].value;
            var newPassword = document.forms[0]["newPassword"].value;
            var confirmNewPassword = document.forms[0]["confirmNewPassword"].value;

            var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (customerFirstName === "" || customerLastName === "" || customerStreetAddress === "" || customerCity === "" || customerZip === "" || customerCounty === "" || customerEmail === "" || (newPassword !== "" && !newPassword.match(passwordRegex)) || (newPassword !== "" && newPassword !== confirmNewPassword)) {
                document.getElementById('error-message').innerHTML = "Please complete all fields, ensure the new password matches the confirmation, and make sure the new password meets the criteria.";
                return false;
            } else {
                document.getElementById('error-message').innerHTML = "";
                return true;
            }
        }
    </script>
</body>
</html>
