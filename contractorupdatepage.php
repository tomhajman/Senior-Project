<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body, h1, h2, h3, h4, h5, h6 {
               font-family: "Lato", sans-serif;
        }

        body, html {
            height: 100%;
            color: #333;
            line-height: 1.8;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #333;
            color: #fff;
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header .dropdown {
            position: absolute;
            left: 0;
        }

        .header .dropbtn {
            background-color: #333;
            color: #fff;
            padding: 16px;
            font-size: 24px;
            border: none;
        }

        .header .dropdown-content {
            display: none;
            position: absolute;
            background-color: #333;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            left: 0;
            top: 100%;
            z-index: 1;
        }

        .header .dropdown-content a {
            color: gray;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .header .dropdown-content a:hover {
            background-color: #ddd;
        }

        .header .dropdown:hover .dropdown-content {
            display: block;
        }

        .header .dropdown:hover .dropbtn {
            background-color: #3e8e41;
        }

        .welcome-contractor {
            margin-right: 10px;
            margin-left: auto;
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

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }
    </style>
    </style>
</head>
<body>
<?php
    session_start();
    include 'DBCredentials.php';
    $userEmail = $_SESSION['contractorEmail'];

    function connectToDB() {
        global $HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME, $conn;
        $conn = new mysqli($HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME);

        if ($conn->connect_error) {
            die("Connection issue: " . $conn->connect_error);
        }
        return $conn;
    }

    $db = connectToDB();
    $getNameQuery = "SELECT contractorName FROM contractor WHERE contractorEmail = '$userEmail'";
    $result = $db->query($getNameQuery);
    if ($result) {
        $row = $result->fetch_assoc();
        $userName = $row['contractorName'];
    } else {
        $userName = "Contractor";
    }
	
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $userEmail = $_SESSION['contractorEmail'];

        // Updating profile
        if (isset($_POST['updateProfile'])) {
            $contractorName = $_POST['contractorName'];
            $contractorPhoneNumber = $_POST['contractorPhone'];
            $contractorEmail = $_POST['contractorEmail'];
            $contractorExpertise = implode(',', $_POST['contractorExpertise']);

            $updateProfileQuery = $conn->prepare("UPDATE contractor SET contractorName = ?, contractorPhoneNumber = ?, contractorEmail = ?, contractorExpertise = ? WHERE contractorEmail = ?");
            $updateProfileQuery->bind_param("sssss", $contractorName, $contractorPhoneNumber, $contractorEmail, $contractorExpertise, $userEmail);

            if ($updateProfileQuery->execute()) {
                echo "<div class='success-message'>Profile updated successfully</div>";
            } else {
                echo "<div class='error-message'>Error updating profile</div>";
            }
            $updateProfileQuery->close();
        }

        // Updating password
        if (isset($_POST['updatePassword'])) {
            $currentPassword = $_POST['currentPassword'];
            $newPassword = $_POST['newPassword'];
            $confirmNewPassword = $_POST['confirmNewPassword'];

            $getUserDataQuery = $conn->prepare("SELECT contractorPassword FROM contractor WHERE contractorEmail = ?");
            $getUserDataQuery->bind_param("s", $userEmail);
            $getUserDataQuery->execute();
            $result = $getUserDataQuery->get_result();
            $userData = $result->fetch_assoc();
            $getUserDataQuery->close();

            if ($userData) {
                if (password_verify($currentPassword, $userData['contractorPassword'])) {
                    if ($newPassword === $confirmNewPassword) {
                        $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                        $updatePasswordQuery = $conn->prepare("UPDATE contractor SET contractorPassword = ? WHERE contractorEmail = ?");
                        $updatePasswordQuery->bind_param("ss", $hashedNewPassword, $userEmail);

                        if ($updatePasswordQuery->execute()) {
                            echo "<div class='success-message'>Password updated successfully</div>";
                        } else {
                            echo "<div class='error-message'>Error updating password</div>";
                        }
                        $updatePasswordQuery->close();
                    } else {
                        echo "<div class='error-message'>New passwords do not match</div>";
                    }
                } else {
                    echo "<div class='error-message'>Current Password is incorrect</div>";
                }
            } else {
                echo "<div class='error-message'>User data not found</div>";
            }
        }
    }
    $getUserDataQuery = $conn->prepare("SELECT contractorName, contractorPhoneNumber, contractorEmail, contractorExpertise FROM contractor WHERE contractorEmail = ?");
    $getUserDataQuery->bind_param("s", $_SESSION['contractorEmail']);
    $getUserDataQuery->execute();
    $result = $getUserDataQuery->get_result();
    $userData = $result->fetch_assoc();
    $getUserDataQuery->close();
    $conn->close();
?>
    <div class="header">
        <div class="dropdown">
            <button class="dropbtn">...</button>
            <div class="dropdown-content">
                <a href="ContractorPage.php">Home</a>
				<a href="#">Messages</a>
                <a href="AvailableJobs.php">Available Jobs</a>
                <a href="#">Job History</a>
                <a href="ContractorUpdatePage.php">Account Settings</a>
                <a href="ContractorLogin.php">Log Out</a>
            </div>
        </div>
        <div class="welcome-contractor">Welcome, <?php echo $userName; ?></div>
    </div>

    <div class="container">
        <h1>Update Profile</h1>
        <form action="" method="post">
            <label for="contractorName">Company Name:</label>
            <input type="text" id="contractorName" name="contractorName" value="<?php echo $userData['contractorName']; ?>" required><br><br>

            <label for="contractorPhone">Phone Number:</label>
            <input type="text" id="contractorPhone" name="contractorPhone" value="<?php echo $userData['contractorPhoneNumber']; ?>" required><br><br>

            <label for="contractorEmail">Email Address:</label>
            <input type="email" id="contractorEmail" name="contractorEmail" value="<?php echo $userData['contractorEmail']; ?>" required><br><br>

            <label for="contractorExpertise">Areas of Expertise:</label><br>
            <?php
            $expertiseArray = explode(',', $userData['contractorExpertise']);
            $areasOfExpertise = array('Contracting', 'Plumbing', 'Electrician', 'Gardening', 'Painting', 'HVAC');
            foreach ($areasOfExpertise as $area) {
                $checked = in_array($area, $expertiseArray) ? 'checked' : '';
                echo "<input type='checkbox' name='contractorExpertise[]' value='$area' $checked><label>$area</label><br>";
            }
            ?>

            <input type="submit" name="updateProfile" value="Update Profile">
        </form>
    </div>

    <div class="container">
        <h2>Change Password</h2>
        <form action="" method="post">
            <label for="currentPassword">Current Password:</label>
            <input type="password" id="currentPassword" name="currentPassword" required><br><br>

            <label for="newPassword">New Password:</label>
            <input type="password" id="newPassword" name="newPassword" required><br><br>

            <label for="confirmNewPassword">Confirm New Password:</label>
            <input type="password" id="confirmNewPassword" name="confirmNewPassword" required><br><br>

            <input type="submit" name="updatePassword" value="Change Password">
        </form>
    </div>
</body>
</html>
