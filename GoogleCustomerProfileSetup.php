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

if(isset($_GET['customerEmail']) && isset($_GET['access_token'])){
    // Check if info matches database AND profile is not set up
    $conn = connectToDatabase();
    $stmt = $conn->prepare("SELECT access_token, customerStreetAddress FROM customer WHERE customerEmail=?");
    $stmt->bind_param("s", $_GET['customerEmail']);
    $stmt->execute();
    $stmt->bind_result($db_access_token, $db_address);
    $stmt->fetch();
    $stmt->close();
    
    // If token is wrong OR if profile is already completed boot user to login
    if($_GET['access_token'] != $db_access_token || !(is_null($db_address))){
        header('Location: CustomerLogin.php?redirect=authFail');
        exit();
    }

    // Get firstName, lastName and email from $_GET array
    $firstName = (isset($_GET['firstName'])) ? $_GET['firstName'] : "";
    $lastName = (isset($_GET['lastName'])) ? $_GET['lastName'] : "";
    $email = $_GET['customerEmail'];
    
} else {
    // Boot user to CustomerLogin
    header('Location: CustomerLogin.php?redirect=authFail');
    exit();
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    // Handle form submission
    $errors = [];
    $firstName = htmlspecialchars_decode($_POST['customerFirstName']);
    $lastName = htmlspecialchars_decode($_POST['customerLastName']);
    $streetAddress = $_POST['customerStreetAddress'];
    $floorApt = $_POST['customerFloorApt'];
    $city = $_POST['customerCity'];
    $zipCode = $_POST['customerZip'];
    $county = $_POST['customerCounty'];

    // Validate First Name
    if (empty($firstName)) {
        $errors['firstName'] = "First name is required.";
    } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $firstName)) {
        $errors['firstName'] = "Only letters and white space allowed in first name.";
    }
    
    // Validate Last Name
    if (empty($lastName)) {
        $errors['lastName'] = "Last name is required.";
    } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $lastName)) {
        $errors['lastName'] = "Only letters and white space allowed in last name.";
    }
    
    // Validate Street Address
    if (empty($streetAddress)) {
        $errors['streetAddress'] = "Street address is required.";
    }
    
    // Validate City
    if (empty($city)) {
        $errors['city'] = "City is required.";
    }
    
    // Validate Zip Code
    if (empty($zipCode)) {
        $errors['zipCode'] = "Zip code is required.";
    } elseif (!preg_match("/^\d{5}(-\d{4})?$/", $zipCode)) {
        $errors['zipCode'] = "Invalid zip code format.";
    }
    
    // Validate County
    $validCounties = ['Nassau', 'Suffolk'];
    if (!in_array($county, $validCounties)) {
        $errors['county'] = "Invalid county. Only Nassau or Suffolk are allowed.";
    }

    // If no errors, update user profile
    if(empty($errors)){
        $updateProfile = $conn->prepare("UPDATE customer
            SET
            customerFirstName = ?, 
            customerLastName = ?, 
            customerStreetAddress = ?, 
            customerFloorApt = ?, 
            customerCity = ?, 
            customerZip = ?, 
            customerCounty = ?
            WHERE
            customerEmail = ?;
        ");
        $updateProfile->bind_param("ssssssss", $firstName, $lastName, $streetAddress, $floorApt, $city, $zipCode, $county, $email);
        if ($updateProfile->execute()) {
            // Account updated successfully
            echo "<script>alert('Account created successfully.');</script>";
            
            // Log in and redirect to customer page
            session_start();
            $_SESSION['customerEmail'] = $email;
            header('Location: CustomerPage.php');
            exit();
        } else {
            echo "<script>alert('Error: " . $updateProfile->error . "');</script>";
            header("Location: CustomerLogin.php");
            exit();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Complete your profile</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            input[type="radio"] {
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
        <h1>Let's finish setting up your account</h1>
        <?php
            foreach($errors as $err){
                echo "<div style='color: red;'>{$err}</div>";
            }
        ?>
        <form action="" method="post">
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="customerFirstName" value=<?php echo '"'. htmlspecialchars($firstName) .'"'; ?> required><br>

            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="customerLastName" value=<?php echo '"'. htmlspecialchars($lastName) .'"'; ?> required><br>

            <label for="customerStreetAddress">Street Address</label>
            <input type="text" placeholder="Enter Street Address" name="customerStreetAddress" required><br>

            <label for="customerFloorApt">Floor/Apt</label>
            <input type="text" placeholder="Enter Floor/Apt" name="customerFloorApt"><br>

            <label for="customerCity">City</label>
            <input type="text" placeholder="Enter city" name="customerCity" required><br>

            <label for="customerZip">Zip</label>
            <input type="text" placeholder="Enter Zip Code" name="customerZip" required><br>
            
            <label for="customerCounty">County</label><br>
            <input type="radio" name="customerCounty" id="Suffolk" value="Suffolk" required>
            <label for="Suffolk">Suffolk</label><br>
            <input type="radio" name="customerCounty" id="Nassau" value="Nassau">
            <label for="Nassau">Nassau</label><br><br><br>

            <input type="submit" value="Save">
        </form>
        </div>    
    </body>
</html>