<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/ConUp.css">
   
</head>
<body>
<?php
    include 'DBCredentials.php';
    if(isset($_SESSION['contractorEmail'])){
        $userEmail = $_SESSION['contractorEmail'];
      } else {
        header("Location: ContractorLogin.php?redirect=authFail");
        exit();
      }

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
            	 echo "<script>alert('Profile updated successfully.');</script>";
                //echo "<div class='success-message'>Profile updated successfully</div>";
            } else {
             	echo "<script>alert('Error updating profile.');</script>";
                //echo "<div class='error-message'>Error updating profile</div>";
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
				<a href="ContractorMessageCenter.php">Messages</a>
                <a href="AvailableJobs.php">Available Jobs</a>
                <a href="ContractorManageJobs.php">Job History</a>
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
